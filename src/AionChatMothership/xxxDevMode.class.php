<?php

namespace AionChatMothership;

class DevMode{

    public static function enable(){


        if(isset($_GET['create_stub_post'])){
            try {
                unlink("/var/www/html/wp-content/debug.log");
            }catch(Exception $e) {}

            \add_action('init', function(){
                self::createStubPost();
            });
        }
    }

    public static function createStubPost() {
        self::deleteExistingStubPosts();
        self::createNewStubPost();
    }

    private static function deleteExistingStubPosts() {
        $args = [
            'post_type' => 'aion-conversation',
            'post_status' => 'any',
            'post_title' => 'Ion Home',
            'posts_per_page' => -1
        ];
        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                \wp_delete_post(\get_the_ID(), true);
            }
        }
        \wp_reset_postdata();
    }

    private static function createNewStubPost() {
        $post_data = [
            'post_title' => 'Ion Home',
            'post_content' => "Hello! I am an Aion named Ion.",
            'post_status' => 'publish',
            'post_author' => \AionChat\User::get_ion_user_id(),
            'post_type' => 'aion-conversation',
            'comments_status'   => 'open'
        ];
        \wp_insert_post($post_data);
    }
}
