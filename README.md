# Menu Generator for Laravel

A class for Laravel to easily generate HTML menus.

*Readme is a work in progress...*

## Install

Via Composer

``` bash
$ composer require taylornetwork/menu-generator
```

## Setup

Add the service provider to the providers array in `config/app.php`

``` php

'providers' => [

	TaylorNetwork\MenuGenerator\MenuGeneratorServiceProvider::class,

],

```

---

Publish config to your config directory

``` bash
$ php artisan vendor:publish
```

## Usage



## Command

This package comes with an artisan command `menu:definition` to easily generate menu definitions 

To generate a menu definition

``` bash
$ php artisan menu:definition MenuName
```

This will create `App\Menus\MenuName.php` 

The menu definitions extend the `TaylorNetwork\MenuGenerator\Definition` class which has some functionality for rendering the menu once defined.

Add all the items on your menu in the `define()` function

``` php
namespace App\Menus;

use TaylorNetwork\MenuGenerator\Definition;

class MenuName extends Definition
{
	public function define()
	{
		$this->generator->newMenu();
		$this->generator->addItem('Home', route('home'));
		$this->generator->closeMenu();
	}
}
```

Would generate a very simple menu with only a `Home` link.

See [the generator instance](#generator-instance) documentation below.

## Generator Instance

The definition class uses the `TaylorNetwork\MenuGenerator\Generator` class to generate menus. In any class that extends `TaylorNetwork\MenuGenerator\Definition` the generator instance can be accessed by the protected property `generator` or by the public function `getGenerator()`.

### Methods

**`newMenu()`**

Every menu must start with a `newMenu()` call to open the menu HTML tags.

**`addItem(string [, string, bool, string ])`**

The `addItem` method accepts a minimum of 1 parameter, the text to display on the link. See table for parameter details.

| # | Name | Description | Type | Default Value | Required |
|:-:|:-----|:------------|:----:|:-------------:|:--------:|
| 1 | Text | The text to display for the link | string | - | Yes |
| 2 | Href | The link destination URL | string | `'#'` | No |
| 3 | Show Icon | Show an icon in the link | bool | `false` | No |
| 4 | Icon | The icon to display (usually a font-awesome name ie: `'fa-dashboard'`) | string | `null` | No |

*Note: when using font-awesome, by default `fa fa-fw` is prepended to the name so it is not required, only the `fa-dashboard` is required.*

**`addSubMenu(string [, bool, string ])`**

**`closeSubMenu()`**

**`closeMenu()`**

**`closeIfNotClosed()`**

**`renderMenu()`**

**`setCallback(string, Closure)`**

Sets a callback function by a key name. See [generator callbacks](#generator-callbacks) for a list of keys.

``` php
$menuOpen = false;

$generator->setCallback('menu.after.open', function () use ($menuOpen) {
	$menuOpen = true;
});
```

### Generator Callbacks

There are a number of callback functions that the generator will call if they exist. You can set a callback by adding it to your `config/menu_generator.php` in the `callbacks` array or by using the `setCallback` method.

#### Menu Callbacks

**`menu.before.open`**

Called when `newMenu()` is called, before opening the HTML tag.

**`menu.after.open`**

Called when `newMenu()` is called, after opening the HTML tag.

**`menu.before.close`**

Called when the menu is being closed but before the closing HTML tag is added.

**`menu.after.close`**

Called after the menu is fully closed and processing is done.

**`menu.before.render`**

Called right before the menu is rendered to HTML.

#### Sub Menu Callbacks

**`sub-menu.before.open`**

Called when adding a sub menu but before processing the add.

**`sub-menu.after.open`**

Called when adding a sub menu after it has been added.

**`sub-menu.before.close`**

Called when closing a sub menu before processing the close has started.

**`sub-menu.after.close`**

Called after sub menu was closed.

#### Item Callbacks

**`item.before.add`**

Called before adding an item.

**`item.after.add`**

Called after item has been added.


## Credits

- [Sam Taylor][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-author]: https://github.com/taylornetwork
