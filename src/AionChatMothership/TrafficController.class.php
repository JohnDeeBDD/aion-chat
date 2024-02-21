<?php

namespace AionChatMothership;

use AionChat\Exception;
use AionChat\Prompt;
use AionChat\User;

class TrafficController
{
    public static function enable_comment_response(){

    }
    public static function enable_prompt_incoming(){
        \add_action('ion_prompt_incoming', '\AionChatMothership\TrafficController::prompt_incoming', 10, 1);
        \add_action('rest_api_init', function () {
            \register_rest_route(
                "aion-chat/v1",
                "ion-prompt",
                array(
                    'methods' => ['POST', 'GET'],
                    'callback' => function ($args) {
                        $Prompt = \unserialize($args["prompt"]);


                        //this function passes the prompt to ion_prompt_incoming on the next page load
                        //\wp_schedule_single_event(time(), "ion_prompt_incoming", [$Prompt]);
                        $response =  \AionChatMothership\TrafficController::prompt_incoming($Prompt);
                        \update_option("aion-chat-down-bus", $response);
                        return $response;

                        //return "Prompt received. Status 200";
                    },
                    'permission_callback' => function () {
                        return true;
                    },
                )
            );
        });
    }
    public static function publish_comment_from_Prompt(Prompt $Prompt) {
        // Prepare comment data
        $comment_data = array(
            'comment_post_ID'      => $Prompt->post_id,
            'comment_author'       => '',
            'comment_author_email' => '',
            'comment_author_url'   => '',
            'comment_content'      => $Prompt->comment_content,
            'comment_type'         => '',
            'comment_parent'       => 0,
            'user_id'              => $Prompt->user_id,
            'comment_author_IP'    => '',
            'comment_agent'        => '',
            'comment_date'         => current_time('mysql'),
            'comment_approved'     => 1,
        );

        // Insert the comment and get the comment ID
        $comment_id = \wp_insert_comment($comment_data);

        if ($comment_id === 0 || is_wp_error($comment_id)) {
            throw new \AionChat\Exception('Failed to insert comment.');
        }
        $Prompt->comment_id = $comment_id;
        return $Prompt;
    }

    public static function prompt_incoming(Prompt $Prompt){
        \error_log( print_r( $Prompt, true ) );
        $Prompt = self::force_set_local_user($Prompt);
        $Prompt = self::force_set_local_post($Prompt);
        $Prompt->remote_comment_id = $Prompt->comment_id;
        unset($Prompt->comment_id);
        $Prompt = self::check_api_key($Prompt);
        $Prompt->status = "Received and processed on mothership. Ready to send to ChatGPT";
        $Prompt = self::publish_comment_from_Prompt($Prompt);
        return TrafficController::craft_ion_response($Prompt->comment_id);
        //return $Prompt;
    }

    public static function craft_ion_response($comment_ID) {
        $comment = get_comment($comment_ID);
        $Prompt = new \AionChatMothership\Prompt();
        $Prompt->init_this_prompt($comment_ID, "created-on-mothership");
        $Prompt->send_self_to_ChatGPT();
        self::post_comment_to_post($comment->user_id, $Prompt->post_id, $Prompt->response);
        return ($Prompt->response);
    }

    public static function post_comment_to_post($user_id, $post_id, $comment_content) {
        $comment_content = str_replace('```', '###TRIPLE_BACKTICK###', $comment_content);
        $comment_data = array(
            'comment_post_ID'      => $post_id,
            'comment_author'       => "Ion",
            'comment_content'      => $comment_content,
            'comment_type'         => '',
            'comment_parent'       => 0,
            'user_id'              => \AionChat\User::get_ion_user_id(),
            'comment_date'         => current_time('mysql'),
            'comment_approved'     => 1,
        );
        $comment_id = \wp_insert_comment($comment_data);
        if ($comment_id) {
            return true;
        } else {
            throw new Exception("An error occurred while posting the comment.");
        }
    }

    private static function check_api_key(Prompt $Prompt){
        if (isset($Prompt->open_ai_api_key)) {
            return self::set_remote_api_key($Prompt);
        }
        return self::fetch_and_set_local_api_key($Prompt);
    }

    private static function set_remote_api_key(Prompt $Prompt) {
        $Prompt->remote_open_ai_api_key = $Prompt->open_ai_api_key;
        return $Prompt;
    }

    private static function fetch_and_set_local_api_key(Prompt $Prompt) {
        $localApiKey = \get_option("openai-api-key", null);
        $Prompt->open_ai_api_key = $localApiKey;
        return $Prompt;
    }

    private static function force_set_local_user(Prompt $Prompt){
        // Ensure that $Prompt->user_id is an integer
        if (!is_int($Prompt->user_id)) {
            throw new Exception("The user_id must be an integer.");
        }
        $Prompt->remote_user_id = $Prompt->user_id;
        $Prompt->user_id = User::force_return_user_id($Prompt->user_email);
        return $Prompt;
    }

    private static function force_set_local_post(Prompt $Prompt)
    {
        if (!is_int($Prompt->post_id)) {
            throw new Exception("The post_id must be an integer.");
        }

        $Prompt->remote_post_id = $Prompt->post_id;

        // Initialize variables
        $args = array(
            'post_type' => 'aion-conversation',
            'post_status' => 'draft',
            'title' => \AionChat\Conversation::buildIonConversationTitle($Prompt->remote_post_id, $Prompt->user_id, $Prompt->origin_domain_url),
            'posts_per_page' => 1,
        );

        // Query for existing ion-connection
        $query = new \WP_Query($args);

        // Check if ion-connection exists
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $Prompt->post_id = \get_the_ID();
                return $Prompt;
            }
        } else {
            // Create new ion-connection
            $post_id = \wp_insert_post(array(
                'post_title' => \AionChat\Conversation::buildIonConversationTitle($Prompt->remote_post_id, $Prompt->user_id, $Prompt->origin_domain_url),
                'post_type' => 'aion-conversation',
                'post_status' => 'draft',
                'post_author' => \AionChat\User::get_ion_user_id(),
            ));
            $Prompt->post_id = $post_id;
            return $Prompt;
        }
    }
}