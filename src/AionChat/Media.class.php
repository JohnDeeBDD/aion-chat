<?php

namespace AionChat;

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';


class Media{

    public static function doHandleSideload($url, $post_id, $desc){
        $attachment_id = \media_sideload_image($url, $post_id, $desc, 'id');
        \set_post_thumbnail($post_id, $attachment_id);
        $data = \wp_get_attachment_metadata( $attachment_id); // get the data structured
        $data['width'] = 680;  // change the values you need to change
        \wp_update_attachment_metadata( $attachment_id, $data );
        return $attachment_id;
    }

}