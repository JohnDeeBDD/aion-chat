<?php

namespace AionDialectic;

class Questions20{

    public static function setup(){
        //if(isset($_GET['20q'])){
          // \add_action("init", function(){
               $assistant_user_id = \AionChat\User::get_aion_assistant_user_id();
               $Aion_user_id = \AionChat\User::get_Aion_user_id();
               $args = [
                   'post_title' => "Voice 1",
                    'post_content'  => "This is Aion side, who speaks first.",
                    'post_status'   => 'publish',
                    'post_author'   => \AionChat\User::get_Aion_user_id(),
                    'post_type'  =>  "aion-conversation"
                   ];
                $voice1_post_id = \wp_insert_post($args);
                \add_post_meta($voice1_post_id, "aion-chat-instructions", "You are a playful friend. You are playing the game '20 Questions' with a young adult. Someone will go first, and the should think of something that fits into the category of 'animal, vegetable, or mineral'. That person reveals the category, and the other player then can ask 20 yes or no questions to try and guess the secret. Play the game with your friend!");

               $args = [
                   'post_title' => "Voice 2",
                   'post_content'  => "This is assistant side, who speaks second.",
                   'post_status'   => 'publish',
                   'post_author'   => \AionChat\User::get_aion_assistant_user_id(),
                   'post_type'  =>  "aion-conversation"
               ];
               $voice2_post_id = \wp_insert_post($args);
               \add_post_meta($voice2_post_id, "aion-chat-instructions", "You are a playful friend. You are playing the game '20 Questions' with a young adult. Someone will go first, and the should think of something that fits into the category of 'animal, vegetable, or mineral'. That person reveals the category, and the other player then can ask 20 yes or no questions to try and guess the secret. Play the game with your friend!");

               $url_voice1_post = \get_permalink($voice1_post_id);
               $url_voice2_post = \get_permalink($voice2_post_id);

               \add_post_meta($voice1_post_id, "aion-dialectic-conversant-url", $url_voice2_post);
               \add_post_meta($voice2_post_id, "aion-dialectic-conversant-url", $url_voice1_post);
          // });
        //}
    }
}