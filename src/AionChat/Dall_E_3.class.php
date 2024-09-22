<?php

namespace AionChat;

class Dall_E_3{

    public static function doHandleResponse($Prompt){
        $x = json_decode($Prompt->response['body']);
        $x = unserialize($x);
        $data = $x['data'];
        $inside = $data[0];
        $revisedPrompt = $inside['revised_prompt'];
        $attachment_id = Media::doHandleSideload($inside['url'], $Prompt->post_id, "This is a picture");
        $comment_content = "Image saved! The attachment ID is $attachment_id .";
        Conversation::set_post_comment_status($Prompt->post_id, "closed");
        $meta = [];
        $meta['image_remote_url'] = $inside['url'];
        $meta['attachment_ID'] = $attachment_id;
        return $comment_content;
    }

    public function send_up(){
        $api_route = "/wp-json/aion-chat/v1/aion-prompt";
        global $Servers;
        $AionChat_mothership_url = $Servers->mothershipURL;
        $response = wp_remote_post($AionChat_mothership_url . "/wp-json/aion-chat/v1/aion-prompt", array(
                'method' => 'POST',
                'timeout' => 60,
                'redirection' => 1,
                'httpversion' => '1.1',
                'blocking' => true,
                'headers' => array(),
                'body' => array(
                    'prompt' => serialize($this),
                )
            )
        );
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: Prompt line 147 $error_message $AionChat_mothership_url";
            die();
        }
        return ($response);
    }

}