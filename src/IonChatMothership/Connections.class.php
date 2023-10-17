<?php

namespace IonChatMothership;

class Connections
{
    public static function activate_ion_connections_cpt()
    {
        \add_action('init', function () {
            $labels = array(
                'name' => _x('Ion Connections', 'ion-chat'),
                'singular_name' => _x('Ion Connection', 'ion-chat'),
                'menu_name' => _x('Ion Connections', 'ion-chat'),
                'name_admin_bar' => _x('Ion Connection', 'ion-chat'),
                'add_new' => _x('Add New', 'ion-chat'),
                'add_new_item' => __('Add New Ion Connection', 'ion-chat'),
                'new_item' => __('New Ion Connection', 'ion-chat'),
                'edit_item' => __('Edit Ion Connection', 'ion-chat'),
                'view_item' => __('View Ion Connection', 'ion-chat'),
                'all_items' => __('All Ion Connections', 'ion-chat'),
                'search_items' => __('Search Ion Connections', 'ion-chat'),
                'parent_item_colon' => __('Parent Ion Connections:', 'ion-chat'),
                'not_found' => __('No Ion Connections found.', 'ion-chat'),
                'not_found_in_trash' => __('No Ion Connections found in Trash.', 'ion-chat')
            );

            $args = array(
                'labels' => $labels,
                'description' => __('Description.', 'ion-chat'),
                'public' => false,
                'publicly_queryable' => false,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => array('slug' => 'ion-connections'),
                'capability_type' => 'post',
                'has_archive' => false,
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
            );

            \register_post_type('ion-connections', $args);


        });
    }
}
