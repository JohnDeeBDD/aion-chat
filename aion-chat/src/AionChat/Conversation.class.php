<?php

namespace AionChat;

class Conversation
{

    public static function enable_aion_conversation_cpt()
    {
        \add_action('init', [self::class, 'register_aion_conversation_cpt']);
    }

    public static function register_aion_conversation_cpt()
    {
        $labels = [
            'name'                  => _x('Aion Conversations', 'aion-chat'),
            'singular_name'         => _x('Aion Conversation', 'aion-chat'),
            'menu_name'             => _x('Aion Conversations', 'aion-chat'),
            'name_admin_bar'        => _x('Aion Conversation', 'aion-chat'),
            'add_new'               => _x('Add New', 'aion-chat'),
            'add_new_item'          => __('Add New Aion Conversation', 'aion-chat'),
            'new_item'              => __('New Aion Conversation', 'aion-chat'),
            'edit_item'             => __('Edit Aion Conversation', 'aion-chat'),
            'view_item'             => __('View Aion Conversation', 'aion-chat'),
            'all_items'             => __('All Aion Conversations', 'aion-chat'),
            'search_items'          => __('Search Aion Conversations', 'aion-chat'),
            'parent_item_colon'     => __('Parent Aion Conversations:', 'aion-chat'),
            'not_found'             => __('No Aion Conversations found.', 'aion-chat'),
            'not_found_in_trash'    => __('No Aion Conversations found in Trash.', 'aion-chat')
        ];

        $args = [
            'labels'                => $labels,
            'description'           => __('A custom post type for Aion Conversations.', 'aion-chat'),
            'public'                => true,
            'publicly_queryable'    => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => true,
            'rewrite'               => ['slug' => 'aion-conversation'],
            'capability_type'       => 'post',
            'has_archive'           => false,
            'hierarchical'          => true,
            'menu_position'         => null,
            'supports'              => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'revisions', 'page-attributes'],
            'taxonomies'            => ['category', 'post_tag']
        ];

        \register_post_type('aion-conversation', $args);
        \flush_rewrite_rules();
    }

    /**
     * Parses a conversation title string into its components.
     *
     * The expected format is "remote_post_id:initial_speaker_user_id:remote_site_url".
     * Note: The remote site URL can contain colons (e.g., "https://").
     * Note: The Aion who is responding is the post author
     *
     * @param string $titleString The conversation title string to parse.
     * @return array|null An associative array of components, or null if the format is incorrect.
     */
    public static function parseIonConversationTitle($titleString) {
        $pattern = '/^(\d+):(\d+):(.+)$/';

        if (preg_match($pattern, $titleString, $matches)) {
            if (count($matches) === 4) {
                return [
                    'remote_post_id' => $matches[1],
                    'user_id'        => $matches[2],
                    'remote_site_url' => $matches[3]
                ];
            }
        }

        error_log('parseIonConversationTitle: Incorrect format for title string: ' . $titleString);
        return null;
    }

    /**
     * Builds a conversation title string from its components.
     *
     * @param int $remote_post_id The remote post ID.
     * @param int $user_id The user ID of the initial speaker.
     * @param string $remote_site_url The URL of the remote site.
     * @return string The constructed title string.
     */
    public static function buildIonConversationTitle($remote_post_id, $user_id, $remote_site_url) {
        return sprintf('%d:%d:%s', $remote_post_id, $user_id, $remote_site_url);
    }

}
