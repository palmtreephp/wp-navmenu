<?php

namespace Palmtree\WordPress\NavMenu;

class BootstrapNavMenuWalker extends \Walker_Nav_Menu
{
    private $activeClasses = ['current-menu-item', 'current-page-ancestor'];

    /**
     * Start the element output.
     *
     * @param  string $output Passed by reference. Used to append additional content.
     * @param  object $item   Menu item data object.
     * @param  int    $depth  Depth of menu item. May be used for padding.
     * @param  array  $args   Additional strings.
     *
     * @return void
     */
    public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
    {
        $classes = ['nav-item'];

        if ($this->has_children) {
            $classes[] = 'dropdown';
        }

        $output .= sprintf('<li class="%s">', implode(' ', $classes));

        $args = (object)$args;

        $classes = ['nav-link'];

        if ($this->has_children) {
            $classes[] = 'dropdown-toggle';
        }

        $args = apply_filters('nav_menu_item_args', $args, $item, $depth);

        if ($this->isItemActive($item)) {
            $classes[] = 'active';
        }

        $attributes = 'class="' . implode(' ', $classes) . '"';

        if (!empty($item->url)) {
            $attributes .= ' href="' . esc_attr($item->url) . '"';
        }

        if ($this->has_children) {
            $attributes .= ' data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"';
        }

        $attributes = trim($attributes);

        $title = apply_filters('the_title', $item->title, $item->ID);
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $itemOutput = $args->before;
        $itemOutput .= "<a $attributes>";
        $itemOutput .= $args->link_before . $title . $args->link_after;
        $itemOutput .= '</a>';
        $itemOutput .= $args->after;

        // Since $output is called by reference we don't need to return anything.
        $output .= apply_filters(
            'walker_nav_menu_start_el',
            $itemOutput,
            $item,
            $depth,
            $args
        );
    }

    public function start_lvl(&$output, $depth = 0, $args = array())
    {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat($t, $depth);

        // Default class.
        $classes = ['dropdown-menu'];

        /**
         * Filters the CSS class(es) applied to a menu list element.
         *
         * @since 4.8.0
         *
         * @param array     $classes The CSS classes that are applied to the menu `<ul>` element.
         * @param \stdClass $args    An object of `wp_nav_menu()` arguments.
         * @param int       $depth   Depth of menu item. Used for padding.
         */
        $class_names = join(' ', apply_filters('nav_menu_submenu_css_class', $classes, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $output .= "{$n}{$indent}<ul $class_names>{$n}";
    }

    private function isItemActive($item)
    {
        $active = false;

        $page_for_posts_id = (int)get_option('page_for_posts', 0);

        if ((int)$item->object_id === $page_for_posts_id) {
            // Blog
            if (is_category() || is_tag() || is_singular('post') || is_home()) {
                $active = true;
            }
        } else {
            $queried_obj = get_queried_object();

            if ($queried_obj instanceof \WP_Post) {
                $queried_obj = get_post_type_object($queried_obj->post_type);
            }

            if (!empty($queried_obj->has_archive)) {
                $slug = $queried_obj->rewrite['slug'];

                if (is_string($queried_obj->has_archive)) {
                    $slug = $queried_obj->has_archive;
                }

                $query = new \WP_Query([
                    'post_type'      => 'page',
                    'posts_per_page' => '1',
                    'pagename'       => $slug,
                ]);

                if ($query->found_posts && (int)$query->post->ID === (int)$item->object_id) {
                    $active = true;
                }
            } else {
                // If the item classes array contains one of the WordPress active classes.
                $intersection = array_intersect($item->classes, $this->activeClasses);
                if (!empty($intersection)) {
                    $active = true;
                }
            }
        }

        return $active;
    }
}
