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
            \flush_rewrite_rules();

        });
    }

    /**
     * Parses a conversation title string into its components.
     *
     * The expected format is "remote_post_id:user_id:remote_site_url".
     * Note: The remote site URL can contain colons (e.g., "https://").
     *
     * @param string $titleString The conversation title string to parse.
     * @return array|null An associative array of components, or null if the format is incorrect.
     */
    public static function parseIonConversationTitle($titleString) {
        // Define the regex pattern to extract the conversation components
        $pattern = '/^(\d+):(\d+):(.+)$/';


        // Attempt to match the pattern against the provided string
        if (preg_match($pattern, $titleString, $matches)) {
            // Check if we have the expected number of matches (full match + 3 capturing groups)
            if (count($matches) === 4) {
                // Map the matches to their respective named components
                return [
                    'remote_post_id' => $matches[1],
                    'user_id' => $matches[2],
                    'remote_site_url' => $matches[3]
                ];
            }
        }

        // If the string does not match the expected format, log the error
        error_log('parseIonConversationTitle: Incorrect format for title string: ' . $titleString);

        // Return null to indicate an error in parsing
        return null;
    }

    public static function buildIonConversationTitle($remote_post_id, $user_id, $remote_site_url) {
        return ($remote_post_id . ":" . $user_id . ":" . $remote_site_url);
    }


    public static function doCreateNewConversation($room_id, $speaker_user_id, $hearer_suer_id){}
}
