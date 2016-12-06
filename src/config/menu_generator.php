<?php

return [
    
    /*
     |--------------------------------------------------------------------------
     | Menus Namespace
     |--------------------------------------------------------------------------
     |
     | This is where menu definitions are stored. App\ is prepended.
     |
     */
    'namespace' => 'Menus',

    /*
     |--------------------------------------------------------------------------
     | Register Commands
     |--------------------------------------------------------------------------
     |
     | Register the artisan commands.
     |
     */
    'registerCommands' => true,

    /*
     |--------------------------------------------------------------------------
     | Share Keys As 
     |--------------------------------------------------------------------------
     |
     | The service provider will automatically make all menus accessible on 
     | all views. The menuKey constant in each menu class will replace {key}.
     | 
     | For example: 
     |      'shareKey' => '{key}Menu',
     |
     |      In a menu with key 'top', all views will have access to the
     |      top menu class definition as $topMenu. To render the menu simply
     |      call {!! $topMenu->render() !!} in the view.
     |
     */
    'shareKey' => '{key}Menu',

    /*
     |--------------------------------------------------------------------------
     | Tags and HTML Reference
     |--------------------------------------------------------------------------
     | 
     | For each tag, the associated HTML tag is used.
     |
     */
    'tags' => [
        'menu' => 'ul',
        'sub-menu-toggle' => 'li',
        'sub-menu' => 'ul',
        'item' => 'li',
        'link' => 'a',
        'icon' => 'i',
        'caret' => 'i',
    ],

    /*
     |--------------------------------------------------------------------------
     | Classes for Tags
     |--------------------------------------------------------------------------
     | 
     | Each tag can have an array of classes to add, either always or just when
     | the link is active.
     |
     */
    'classes' => [
        'menu' => [
            'always' => [ 'sidebar-menu' ],
        ],

        'sub-menu-toggle' => [
            'always' => [ 'treeview' ],
            'active' => [],
        ],

        'sub-menu' => [
            'always' => [ 'treeview-menu' ],
            'active' => [],
        ],

        'item' => [
            'always' => [],
            'active' => [ 'active' ],
        ],

        'link' => [
            'always' => [],
            'active' => [],
        ],

        'icon' => [
            'always' => [ 'fa', 'fa-fw' ],
            'active' => [],
        ],

        'caret' => [
            'always' => [ 'fa', 'fa-fw', 'fa-angle-left', 'pull-right' ],
            'active' => [],
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | Callbacks
     |--------------------------------------------------------------------------
     |
     | Define any callbacks here, or in your definition class.
     | See README for documentation.
     |
     */
    'callbacks' => [],
    
];