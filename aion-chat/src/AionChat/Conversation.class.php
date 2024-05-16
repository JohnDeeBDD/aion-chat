<?php

namespace AionChat;

class Conversation
{

    public static function enable_aion_conversation_cpt()
    {
        \add_action('init', function () {

            $labels = array(
                'name' => _x('Aion Conversations', 'aion-chat'),
                'singular_name' => _x('Aion Conversation', 'aion-chat'),
                'menu_name' => _x('Aion Conversations', 'aion-chat'),
                'name_admin_bar' => _x('Aion Conversation', 'aion-chat'),
                'add_new' => _x('Add New', 'aion-chat'),
                'add_new_item' => __('Add New Aion Conversation', 'aion-chat'),
                'new_item' => __('New Aion Conversation', 'aion-chat'),
                'edit_item' => __('Edit Aion Conversation', 'aion-chat'),
                'view_item' => __('View Aion Conversation', 'aion-chat'),
                'all_items' => __('All Aion Conversations', 'aion-chat'),
                'search_items' => __('Search Aion Conversations', 'aion-chat'),
                'parent_item_colon' => __('Parent Aion Conversations:', 'aion-chat'),
                'not_found' => __('No Aion Conversations found.', 'aion-chat'),
                'not_found_in_trash' => __('No Aion Conversations found in Trash.', 'aion-chat')
            );
            $args = array(
                'labels' => $labels,
                'description' => __('Description.', 'aion-chat'),
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
     * The expected format is "remote_post_id:initial_speaker_user_id:remote_site_url".
     * Note: The remote site URL can contain colons (e.g., "https://").
     * Note: The Aion who is responding is the post author
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

    public static function enableStubConversations()
    {
        if (isset($_GET['aion-chat-stub'])) {
            global $Servers;
            switch ($_GET['aion-chat-stub']) {
                case "dialectic":
                    \add_action("init", function(){
                        global $Servers;
                        self::buildStubConversation(
                            "voice1",
                            "This is dialectic voice 1",
                            "You are a playful friend. You are playing the game '20 Questions' with a young adult. Someone will start the game by choosing either 'animal, vegatable, or mineral'. The other player then may ask 20 yes or no questions to try and guess the selection. Play the game with your fiend.",
                            User::get_aion_assistant_user_id(),
                            (\get_site_url() . "/voice2"),
                        );
                        self::buildStubConversation(
                            "voice2",
                            "This is dialectic voice 2",
                            "You are a playful friend. You are playing the game '20 Questions' with a young adult. Someone will start the game by choosing either 'animal, vegatable, or mineral'. The other player then may ask 20 yes or no questions to try and guess the selection. Play the game with your fiend.",
                            User::get_Aion_user_id(),
                            (\get_site_url() . "/voice1"),
                        );
                    });
                    break;
                case 1:
                    echo "i equals 1";
                    break;
                case 2:
                    echo "i equals 2";
                    break;


            }
        }
    }

    public static function buildStubConversation($title, $content, $instructions, $authorID, $conversantUrl){
        $my_post = array(
            'post_title'    => $title,
            'post_content'  => $content,
            'post_status'   => 'publish',
       //     'post_author'   => User::get_aion_assistant_user_id(),
            'post_author'   => $authorID,
            'post_type'     => "aion-conversation",
        );
        $postID = \wp_insert_post( $my_post );
        \update_post_meta($postID, "aion-chat-instructions", $instructions);
       // die("x");aion-dialectic-conversant-url
        if(isset($conversantUrl)){
            \update_post_meta($postID, "aion-dialectic-conversant-url", $conversantUrl);
        }
    }
}
