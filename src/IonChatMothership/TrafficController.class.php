<?php

namespace IonChatMothership;

use IonChat\Exception;
use IonChat\Prompt;
use IonChat\User;

class TrafficController
{
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
        $comment_id = \wp_new_comment($comment_data);

        if ($comment_id === 0 || is_wp_error($comment_id)) {
            throw new \IonChat\Exception('Failed to insert comment.');
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
        return comment_posted($Prompt->comment_id);
        //return $Prompt;
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
            'post_type' => 'post',
            'post_status' => 'draft',
            'title' => ($Prompt->remote_post_id . $Prompt->remote_connection_domain_url),
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
                'post_title' => ($Prompt->remote_post_id . $Prompt->remote_connection_domain_url),
                'post_type' => 'post',
                'post_status' => 'draft',
                'post_author' => \IonChat\User::get_ion_user_id(),
            ));
            $Prompt->post_id = $post_id;
            return $Prompt;
        }
    }
}