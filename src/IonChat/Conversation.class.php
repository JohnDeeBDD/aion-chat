<?php

namespace IonChat;

class Conversation
{

    public static function enable_aion_conversation_cpt()
    {
        \add_action('init', function () {

            $labels = array(
                'name' => _x('Aion Conversations', 'ion-chat'),
                'singular_name' => _x('Aion Conversation', 'ion-chat'),
                'menu_name' => _x('Aion Conversations', 'ion-chat'),
                'name_admin_bar' => _x('Aion Conversation', 'ion-chat'),
                'add_new' => _x('Add New', 'ion-chat'),
                'add_new_item' => __('Add New Aion Conversation', 'ion-chat'),
                'new_item' => __('New Aion Conversation', 'ion-chat'),
                'edit_item' => __('Edit Aion Conversation', 'ion-chat'),
                'view_item' => __('View Aion Conversation', 'ion-chat'),
                'all_items' => __('All Aion Conversations', 'ion-chat'),
                'search_items' => __('Search Aion Conversations', 'ion-chat'),
                'parent_item_colon' => __('Parent Aion Conversations:', 'ion-chat'),
                'not_found' => __('No Aion Conversations found.', 'ion-chat'),
                'not_found_in_trash' => __('No Aion Conversations found in Trash.', 'ion-chat')
            );
            $args = array(
                'labels' => $labels,
                'description' => __('Description.', 'ion-chat'),
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => array('slug' => 'aion-conversation'),
                'capability_type' => 'post',
                'has_archive' => false,
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'revisions'),
                'taxonomies' => array('category', 'post_tag')  // This line enables categories and tags for your custom post type
            );

            \register_post_type('aion-conversation', $args);
        });
    }

    public static function parseIonConversationTitle($string)
    {

    }
}
