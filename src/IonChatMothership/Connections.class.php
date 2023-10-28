<?php

namespace IonChatMothership;

class Connections
{

    public static function enable_ion_connections_cpt()
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
                'rewrite' => array('slug' => 'ion-connection'),
                'capability_type' => 'post',
                'has_archive' => false,
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'revisions' ),
            );

            \register_post_type('ion-connection', $args);


        });
    }


    public static function slave_remote_post($remote_post_id , $remote_connection_url){
        // Step 1: Retrieve ion-connection post ID
        //$connection_id = self::force_get_ion_connection_id($remote_connection_url);


        // Initialize variables
        $args = array(
            'post_type' => 'post',
            'post_status' => 'draft',
            'title' => ($remote_post_id . $remote_connection_url),
            'posts_per_page' => 1,
        );

        // Query for existing ion-connection
        $query = new \WP_Query($args);

        // Check if ion-connection exists
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                return \get_the_ID();
            }
        } else {
            // Create new ion-connection
            $post_id = \wp_insert_post(array(
                'post_title' => ($remote_post_id . $remote_connection_url),
                'post_type' => 'post',
                'post_status' => 'draft',
                'post_author' => \IonChat\User::get_ion_user_id(),
            ));

            return $post_id;
        }

    }


    public static function force_get_ion_connection_id(string $ion_connection_title): int {
        // Initialize variables
        $args = array(
            'post_type' => 'ion-connection',
            'post_status' => 'draft',
            'title' => $ion_connection_title,
            'posts_per_page' => 1,
        );

        // Query for existing ion-connection
        $query = new \WP_Query($args);

        // Check if ion-connection exists
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                return get_the_ID();
            }
        } else {
            // Create new ion-connection
            $post_id = wp_insert_post(array(
                'post_title' => $ion_connection_title,
                'post_type' => 'ion-connection',
                'post_status' => 'draft',
                'post_author' => \IonChat\User::get_ion_user_id(),
            ));

            return $post_id;
        }
    }
}
