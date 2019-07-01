<?php
if(!defined("ABSPATH")) die;

trait menus
{
        /*
        *  class : insert_nav_menu_item : matrix
        *  This method is used to create a navbar menu item as wordpress class
        *  $settings is the matrix where all property of the class are defined:
        *  'title' is the string label of the nav menu item
        *  'class' is the array where define all css class
        *  'url' is the string URL of the nav menu item
        *  'order' is a number to identify the nav menu item
        *  'parent' is the ID of the parent item
        */
        static function insert_nav_menu_item( $settings )
        {
                /* Define and append default settings */
                $default_settings = array(
                        'title' => '',
                        'class' => array(),
                        'url' => '',
                        'order' => 100,
                        'parent' => 0
                );
                $settings += $default_settings;

                /* Create the class */
                $item = new stdClass();
                $item->ID = 1000 + $settings['order'] + $settings['parent'];
                $item->db_id = $item->ID;
                $item->title = $settings['title'];
                $item->url = $settings['url'];
                $item->menu_order = $settings['order'];
                $item->menu_item_parent = $settings['parent'];
                $item->type = 'post_type';
                $item->type_label = 'Page';
                $item->object = 'page';
                $item->object_id = '';
                $item->classes = $settings['class'];
                $item->target = '';
                $item->attr_title = '';
                $item->description = '';
                $item->xfn = '';
                $item->status = '';
                $item->post_content = '';
                $item->post_title = '';
                $item->post_excerpt = '';
                $item->post_status = 'publish';
                $item->comment_status = 'closed';
                $item->ping_status = 'closed';
                $item->post_password = '';
                $item->post_name = '';
                $item->to_ping = '';
                $item->pinged = '';
                $item->post_content_filtered = '';
                $item->post_parent = $settings['parent'];
                $item->post_type = 'nav_menu_item';
                $item->post_mime_type = '';
                $item->comment_count = 0;
                $item->filter = 'raw';

                /* Transform the stdClass in a Wordpress Post class */
                $post = new WP_Post($item);
                /* Return the post */
                return $post;
        }

}

?>