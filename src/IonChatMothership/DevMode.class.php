<?php

namespace IonChatMothership;

class DevMode{

    public function __construct(){


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
            'post_type' => 'post',
            'post_status' => 'any',
            'title' => 'Stub',
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
            'post_title' => 'Stub',
            'post_content' => 'You are a helpful assistant.',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type' => 'post'
        ];
        \wp_insert_post($post_data);
    }
}
