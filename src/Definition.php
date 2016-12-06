<?php

namespace TaylorNetwork\MenuGenerator;

use Illuminate\Http\Request;

abstract class Definition
{
    /**
     * Variable name that will be accessible in all views
     *
     * @const string
     */
    const menuKey = null;
    
    /**
     * Menu Generator Instance
     * 
     * @var Generator
     */
    protected $generator;

    /**
     * Is the menu defined?
     * 
     * @var bool
     */
    protected $defined = false;

    /**
     * Definition constructor.
     * 
     * @param Request $request
     */
    public function __construct (Request $request = null)
    {
        if ($request === null)
        {
            $request = app(Request::class);
        }
        
        $this->generator = new Generator($request);

        $this->generator->setCallback('menu.after.open', function () {
            $this->defined = true;
        });

    }

    /**
     * Set the menu definition here 
     * 
     * @return mixed
     */
    abstract public function define ();

    /**
     * Render the menu
     * 
     * @return string
     */
    public function render ()
    {
        if (!$this->defined)
        {
            $this->define();
        }
        
        $this->generator->closeIfNotClosed();
        
        return $this->generator->renderMenu();
    }

    /**
     * Get the Generator instance
     * 
     * @return Generator
     */
    public function getGenerator () 
    {
        return $this->generator;
    }
}