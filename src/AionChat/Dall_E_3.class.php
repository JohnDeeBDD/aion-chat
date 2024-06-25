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
        $comment_content = "Image saved. The attachment ID is $attachment_id .";
        $meta = [];
        $meta['image_remote_url'] = $inside['url'];
        $meta['attachment_ID'] = $attachment_id;
        return $comment_content;
    }

}