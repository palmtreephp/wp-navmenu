<?php

namespace Palmtree\WordPress\NavMenu;

use Palmtree\Collection\Collection;

class NavMenuCollection extends Collection
{
    public function __construct()
    {
        add_action('after_setup_theme', function () {
            $this->registerMenus();
        });

        parent::__construct('string');
    }

    /**
     *
     */
    protected function registerMenus()
    {
        register_nav_menus($this->toArray());
    }

    /**
     * @param string $menu
     * @param array  $args
     */
    public function renderMenu($menu = '', $args = [])
    {
        $defaults = [
            'menu'       => $menu,
            'menu_class' => 'clearfix',
            'container'  => null,
            'walker'     => new BootstrapNavMenuWalker(),
        ];

        $args = wp_parse_args($args, $defaults);

        wp_nav_menu($args);
    }
}
