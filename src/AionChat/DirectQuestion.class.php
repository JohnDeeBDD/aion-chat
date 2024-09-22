<?php

namespace AionChat;

class DirectQuestion{


    public static function ask($question){
        global $AionChatProtocol;
        $AionChatProtocol = "remote_node";
        User::get_Aion_user_id();

        $my_post = array(
            'post_title'    => "Direct Question",
            'post_content'  => $question,
            'post_status'   => 'draft',
            'post_type'     => 'aion-conversation',
            'post_author'   => User::get_aion_assistant_user_id(),
        );

        $post_id = \wp_insert_post($my_post);
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $data = array(
            'comment_post_ID' => $post_id,
            'comment_author' => 'Aion',
            'comment_author_email' => User::get_Aion_user_email(),
            'comment_author_url' => 'https://aion.garden',
            'comment_content' => $question,
            'comment_author_IP' => '127.3.1.1',
            'comment_agent' => $agent,
            'comment_type'  => '',
            'comment_date' => date('Y-m-d H:i:s'),
            'comment_date_gmt' => date('Y-m-d H:i:s'),
            'comment_approved' => 1,
        );
        $comment_id = wp_insert_comment($data);

        return stripslashes_deep(Comment::do_on_WordPress_action_comment_post($comment_id));
    }

    public static function bool($question){
       // $response = self::ask($question . " This question is being asking in as a true or false question. Please respond with the word 'true' or 'false' only. Do not add any commentary, only respond with true or false.");
       // return filter_var($response, FILTER_VALIDATE_BOOLEAN);

        global $AionChatProtocol;
        $AionChatProtocol = "remote_node";
        User::get_Aion_user_id();

        $my_post = array(
            'post_title'    => "Direct Question",
            'post_content'  => $question,
            'post_status'   => 'draft',
            'post_type'     => 'aion-conversation',
            'post_author'   => User::get_aion_assistant_user_id(),
        );

        $post_id = \wp_insert_post($my_post);
        \update_post_meta($post_id, "aion-chat-instructions", "You are a helpful assistant. You are aiding an associate in answering some basic true of false questions and statements. Please evaluate the following question or statement as either 'true', 'false' or 'error'. Answer 'error' if you cannot evaluate the question or statement as true or false.  for not applicable if the question cannot be answered in a true or false format.");
        if(isset($_SERVER['HTTP_USER_AGENT'])){
            $agent = $_SERVER['HTTP_USER_AGENT'];
        }else{
            $agent = "default";
        }
        $data = array(
            'comment_post_ID' => $post_id,
            'comment_author' => 'Aion',
            'comment_author_email' => User::get_Aion_user_email(),
            'comment_author_url' => 'https://aion.garden',
            'comment_content' => $question,
            'comment_author_IP' => '127.3.1.1',
            'comment_agent' => $agent,
            'comment_type'  => '',
            'comment_date' => date('Y-m-d H:i:s'),
            'comment_date_gmt' => date('Y-m-d H:i:s'),
            'comment_approved' => 1,
        );
        $comment_id = wp_insert_comment($data);
        return filter_var(stripslashes_deep(Comment::do_on_WordPress_action_comment_post($comment_id)), FILTER_VALIDATE_BOOLEAN);


    }
}