<?php declare(strict_types=1);

namespace Palmtree\WordPress\NavMenu;

use Palmtree\Collection\Map;

class NavMenuCollection
{
    /** @var Map */
    private $map;

    public function __construct()
    {
        $this->map = new Map('string');

        add_action('after_setup_theme', function () {
            $this->registerMenus();
        });
    }

    public function set(string $key, string $menu): self
    {
        $this->map->set($key, $menu);

        return $this;
    }

    public function get(string $key): string
    {
        return $this->map->get($key);
    }

    /**
     * @param array|string  $args
     */
    public function renderMenu(string $menu = '', $args = [])
    {
        $defaults = [
            'theme_location' => $menu,
            'menu_class'     => 'clearfix',
            'container'      => null,
            'walker'         => new BootstrapNavMenuWalker(),
        ];

        $args = wp_parse_args($args, $defaults);

        wp_nav_menu($args);
    }

    private function registerMenus(): void
    {
        register_nav_menus($this->map->toArray());
    }
}
