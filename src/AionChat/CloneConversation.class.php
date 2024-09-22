<?php

namespace AionChat;

class CloneConversation{

    public static function doClone($aion_conversation_post_id){
        // Get the original post
        $original_post = \get_post($aion_conversation_post_id);

        if (!$original_post || $original_post->post_type !== 'aion-conversation') {
            return new \WP_Error('invalid_post', 'Invalid post ID or post type');
        }

        // Get post meta data
        $post_meta_data = \get_post_meta($aion_conversation_post_id);

        // Create new post array
        $new_post = array(
            'post_title'    => 'Clone of ' . $original_post->post_title,
            'post_content'  => $original_post->post_content,
            'post_status'   => $original_post->post_status,
            'post_author'   => $original_post->post_author,
            'post_type'     => $original_post->post_type,
        );

        // Insert the post into the database
        $new_post_id = \wp_insert_post($new_post);

        if (\is_wp_error($new_post_id)) {
            return $new_post_id;
        }

        // Copy all post meta
        foreach ($post_meta_data as $meta_key => $meta_values) {
            foreach ($meta_values as $meta_value) {
                \update_post_meta($new_post_id, $meta_key, \maybe_unserialize($meta_value));
            }
        }

        return $new_post_id;
    }
}
