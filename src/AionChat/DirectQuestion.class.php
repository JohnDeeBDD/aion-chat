<?php

namespace AionChat;

class DirectQuestion{


    public static function ask($question){
        global $AionChatProtocal;
        $AionChatProtocal = "remote_node";
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

        return stripslashes_deep(Comment::route_comments($comment_id));
    }
}