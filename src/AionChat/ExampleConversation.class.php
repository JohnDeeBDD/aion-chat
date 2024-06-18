<?php

namespace AionChat;

class ExampleConversation
{
    public static function enablePublishExampleConversations()
    {
        if (isset($_GET['stub'])) {
            \add_action("init", function() {
                self::doInsertExampleAionConversations();
            });
        }
    }

    public static function doInsertExampleAionConversations()
    {
        $post1ID = \wp_insert_post(
            [
                'post_title' => "Aion Chat Instructions",
                'post_content' => "You can give instructions to the Aion. Add your instructions to the post meta custom field \"aion-chat-instructions\". For instance, the custom instructions here are to say funny things. ",
                'post_status' => 'publish',
                'post_author' => User::get_aion_assistant_user_id(),
                'post_type' => "aion-conversation",
            ]
        );
        \update_post_meta($post1ID, "aion-chat-instructions", "You are a helpful assistant. However, today is joke day at the office. Please say something funny, crack a joke, or render some amusing quip after each response.");



        $post1ID = \wp_insert_post(
            [
                'post_title' => "Dialectic Voice 1",
                'post_content' => "This is dialectic voice 1",
                'post_status' => 'publish',
                'post_author' => User::get_aion_assistant_user_id(),
                'post_type' => "aion-conversation",
            ]
        );
        \update_post_meta($post1ID, "aion-chat-instructions", "You are a playful friend. You are playing the game '20 Questions' with a young adult. Someone will start the game by choosing either 'animal, vegetable, or mineral'. The other player then may ask 20 yes or no questions to try and guess the selection. Play the game with your friend.");


        $post2ID = \wp_insert_post(
            [
                'post_title' => "Dialectic Voice 2",
                'post_content' => "This is dialectic voice 2",
                'post_status' => 'publish',
                'post_author' => User::get_Aion_user_id(),
                'post_type' => "aion-conversation",
            ]
        );
        \update_post_meta($post2ID, "aion-chat-instructions", "You are a playful friend. You are playing the game '20 Questions' with a young adult. Someone will start the game by choosing either 'animal, vegetable, or mineral'. The other player then may ask 20 yes or no questions to try and guess the selection. Play the game with your friend.");
        \update_post_meta($post1ID, "aion-dialectic-conversant-url", (\get_site_url() . "/?p=" . $post2ID));
        \update_post_meta($post2ID, "aion-dialectic-conversant-url", (\get_site_url() . "/?p=" . $post1ID));

    }
}
