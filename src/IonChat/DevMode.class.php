<?php

namespace IonChat;

class DevMode{

    public static function enable(){


        if(isset($_GET['create_stub_posts'])){
            try {
                //unlink("/var/www/html/wp-content/debug.log");
            }catch(Exception $e) {}

            \add_action('init', function(){
                self::deleteExistingStubPosts();
                self::createNewStubPost();
            });

        }
    }

    private static function deleteExistingStubPosts() {
        $args = [
            'post_type' => 'aion-conversation',
            'post_status' => 'any',
            'title' => 'Ion Home',
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
            'post_author' => \IonChat\User::get_ion_user_id(),
            'post_type' => 'aion-conversation',
            'comment_status'   => 'open',
            'ping_status'   => 'open'
        ];
        \wp_insert_post($post_data);
    }

    public static function display_option($option_name) {
        // Retrieve the value of the WordPress option
        $option_value = \get_option($option_name);

        // Pretty-print the option value
        echo '<pre>';
        var_dump($option_value);
        echo '</pre>';
        die("display_option");
        // Terminate the script
        throw new \Exception("Script terminated");
    }
}
