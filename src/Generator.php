<?php

namespace TaylorNetwork\MenuGenerator;

use Illuminate\Http\Request;
use TaylorNetwork\MakeHTML\MakeHTML;
use TaylorNetwork\MenuGenerator\Exceptions\GeneratorException;
use Closure;

/**
 * Class Generator
 *
 * A class for Laravel to easily generate a menu. 
 * 
 * @package TaylorNetwork\MenuGenerator\Generator
 * @see https://github.com/taylornetwork/menu-generator
 * @author Sam Taylor <sam@taylornetork.ca>
 */
class Generator
{
    use MakeHTML;
    
    /**
     * An array of menu items
     * 
     * @var array
     */
    protected $menu;

    /**
     * True if menu is open
     * 
     * @var bool
     */
    protected $menuOpen;

    /**
     * True if a sub menu is open
     * 
     * @var bool
     */
    protected $subMenuOpen;

    /**
     * A request to determine current route
     * 
     * @var Request
     */
    protected $request;

    /**
     * Tags to use for menu building
     * 
     * @var array
     */
    protected $tags = [];

    /**
     * Classes each menu tag will have, always and active cases
     * 
     * @var array
     */
    protected $classes = [];

    /**
     * Callbacks
     *
     * @var array
     */
    protected $callbacks = [];

    /**
     * HTMLGenerator External Key
     *
     * @var string
     */
    protected $HTMLExternalKey;

    /**
     * MenuGenerator constructor.
     * 
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
        $this->request = $request;
        
        $this->tags = config('menu_generator.tags', []);
        $this->classes = config('menu_generator.classes', []);
        $this->callbacks = config('menu_generator.callbacks', []);
        
        $this->HTMLExternalKey = $this->getHTMLGeneratorInstance()->getExternalKey();
    }

    /**
     * Add an item to the menu
     * 
     * @param string $text
     * @param string $href
     * @param bool $withIcon
     * @param string $icon
     * @return $this
     */
    public function addItem($text, $href = '#', $withIcon = false, $icon = null)
    {
        if ($this->menuOpen)
        {
            $this->_callback('item.before.add');
            
            $active = false;

            if ($this->request !== null && url($this->request->path()) === $href)
            {
                $active = true;
            }

            $item = $this->_open('item', $active);
            $item .= $this->_openLink($href, $active);

            if ($withIcon)
            {
                $item .= $this->_makeIcon($icon, $active);
            }

            $item .= $this->_makeText($text, $active);
            $item .= $this->_close('link');
            $item .= $this->_close('item');

            $this->_addItemToMenu($item);
            
            $this->_callback('item.after.add');

            return $this;
        }

        return $this;
    }

    /**
     * Open a sub menu
     * 
     * @param string $text
     * @param bool $withIcon
     * @param string $icon
     * @param bool $withCaret
     * @return $this
     */
    public function addSubMenu($text, $withIcon = false, $icon = null, $withCaret = true)
    {
        if ($this->menuOpen)
        {
            $this->_callback('sub-menu.before.open');
            
            $item = $this->_open('sub-menu-toggle');
            $item .= $this->_openLink('#');

            if ($withIcon)
            {
                $item .= $this->_makeIcon($icon);
            }

            $item .= $this->_makeText($text);

            if ($withCaret)
            {
                $item .= $this->_makeCaret();
            }

            $item .= $this->_close('link');

            $this->_addItemToMenu($item);

            $this->_addItemToMenu($this->_open('sub-menu'));
            
            $this->subMenuOpen = true;

            $this->_callback('sub-menu.after.open');
            
            return $this;
        }

        return $this;
    }

    /**
     * Close the open sub menu
     * 
     * @return $this
     */
    public function closeSubMenu()
    {
        $this->_callback('sub-menu.before.close');
        
        $this->_addItemToMenu($this->_close('sub-menu'));
        $this->_addItemToMenu($this->_close('sub-menu-toggle'));
        
        $this->subMenuOpen = false;

        $this->_callback('sub-menu.after.close');

        return $this;
    }

    /**
     * Start by opening a menu
     * 
     * @return $this
     */
    public function newMenu()
    {
        unset($this->menu);

        $this->_callback('menu.before.open');
        
        $this->menu = [
            $this->getHTMLGeneratorInstance()->generateTag($this->tags['menu'], [
                'class' => implode(' ', $this->classes['menu']['always']),
            ], false)
        ];
        
        $this->menuOpen = true;

        $this->_callback('menu.after.open');
        
        return $this;
    }

    /**
     * Close open menu
     * 
     * @return $this
     */
    public function closeMenu()
    {
        $this->_callback('menu.before.close');
        
        $this->_addItemToMenu($this->_close('menu'));
        $this->menuOpen = false;

        $this->_callback('menu.after.close');
        
        return $this;
    }

    /**
     * Close a menu (and sub menu) if not closed
     *
     * @return $this
     */
    public function closeIfNotClosed()
    {
        if ($this->menuOpen)
        {
            if ($this->subMenuOpen)
            {
                $this->closeSubMenu();
            }

            $this->closeMenu();
        }

        return $this;
    }

    /**
     * Render menu to usable HTML string
     *
     * @return string
     */
    public function renderMenu()
    {
        $this->closeIfNotClosed();

        $this->_callback('menu.before.render');
        
        return implode(' ', $this->menu);
    }

    /**
     * Set a callback
     * 
     * @param string $key
     * @param Closure $closure
     * @return $this
     */
    public function setCallback($key, Closure $closure)
    {
        $this->callbacks[$key] = $closure;
        return $this;
    }

    /**
     * Override default tag
     * 
     * The tag to define, 'menu', 'sub-menu', etc.
     * @param string $tag
     * 
     * The HTML tag to use
     * @param string $use
     * 
     * @return $this
     */
    public function defineTag($tag, $use)
    {
        $this->tags[$tag] = $use;
        return $this;
    }

    /**
     * Override default class for a tag
     * 
     * The tag class to override, 'menu', etc.
     * @param string $tag
     * 
     * Active or Always
     * @param string $on
     * 
     * The class to use 
     * @param string $class
     * 
     * @return $this
     * @throws GeneratorException
     */
    public function defineClass($tag, $on, $class)
    {
        $on = strtolower($on);
        
        if ($on !== 'active' || $on !== 'always')
        {
            throw new GeneratorException('Second parameter must be \'always\' or \'active\' only, \''.$on.'\' given.');
        }
        
        $this->classes[$tag][$on] = $class;
        return $this;
    }

    /*
     |--------------------------------------------------------------------------
     | Protected Functions
     |--------------------------------------------------------------------------
     */

    /**
     * Callback
     *
     * @param string $key
     * @return bool|mixed
     */
    public function _callback($key)
    {
        $callbacks = array_dot($this->callbacks);

        if (isset($callbacks[$key]))
        {
            return call_user_func($callbacks[$key]);
        }
        return false;
    }

    /**
     * Add an item to the menu
     * 
     * @param string $item
     * @return $this
     */
    protected function _addItemToMenu($item)
    {
        if (!$this->menu)
        {
            $this->newMenu();
        }
        
        $this->menu[] = $item;
        
        return $this;
    }

    /**
     * Surround text in span tags
     * 
     * @param string $text
     * @param bool $active
     * @return string
     */
    protected function _makeText($text, $active = false)
    {
        return $this->getHTMLGeneratorInstance()->generateTag('span', [ $this->HTMLExternalKey => $text ]);
    }

    /**
     * Make an icon
     * 
     * @param string $icon
     * @param bool $active
     * @return string
     */
    protected function _makeIcon($icon, $active = false)
    {
        return $this->getHTMLGeneratorInstance()->generateTag($this->tags['icon'], [
            'class' => $this->_conditionalAddActiveClass('icon', $active) . ' ' . $icon,
        ]);
    }

    /**
     * Make a caret
     * 
     * @param bool $active
     * @return string
     */
    protected function _makeCaret($active = false)
    {
        return $this->getHTMLGeneratorInstance()->generateTag($this->tags['caret'], [
            'class' => $this->_conditionalAddActiveClass('caret', $active)
        ]);
    }

    /**
     * Open a link tag
     * 
     * @param string $href
     * @param bool $active
     * @return string
     */
    protected function _openLink($href, $active = false)
    {
        return $this->getHTMLGeneratorInstance()->generateTag($this->tags['link'], [
            'href' => $href,
            'class' => $this->_conditionalAddActiveClass('link', $active)
        ], false);
    }

    /**
     * Open a tag
     * 
     * @param string $tag
     * @param bool $active
     * @return string
     */
    protected function _open($tag, $active = false)
    {
        return $this->getHTMLGeneratorInstance()->generateTag($this->tags[$tag], [
            'class' => $this->_conditionalAddActiveClass($tag, $active),
        ], false);
    }

    /**
     * Close a tag
     * 
     * @param string $tag
     * @return string
     */
    protected function _close($tag)
    {
        return $this->getHTMLGeneratorInstance()->closeTag($this->tags[$tag]);
    }

    /**
     * If the route is active, add the active class 
     * 
     * @param string $classAccessor
     * @param string $active
     * @return string
     */
    protected function _conditionalAddActiveClass($classAccessor, $active)
    {
        $classes = implode(' ', $this->classes[$classAccessor]['always']);

        if ($active)
        {
            $classes .= ' ' . implode(' ', $this->classes[$classAccessor]['active']);
        }

        return $classes;
    }
}