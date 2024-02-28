<?php
require_once("/var/www/html/wp-content/plugins/aion-chat/src/AionChat/autoloader.php");
require_once("/var/www/html/wp-content/plugins/aion-mother/src/AionChatMothership/autoloader.php");
// Create users
$user_id1 = wp_create_user('Codeception', 'password', 'codeception@email.com');
$user_id2 = wp_create_user('Ion', 'password', 'jiminac@aol.com');

// Set user roles
$user1 = new WP_User($user_id1);
$user1->set_role('administrator');

$user2 = new WP_User($user_id2);
$user2->set_role('administrator');

// Create post and capture the post ID
$post_id = wp_insert_post([
    'post_title'    => 'First Chat',
    'post_content'  => 'You are a helpful assistant.',
    'post_status'   => 'publish',
    'post_author'   => $user_id1,
    'post_type'     => 'post',
]);

// Create comment
$commentdata = array(
    'comment_post_ID' => $post_id,
    'comment_author' => 'Codeception',
    'comment_author_email' => 'codeception@email.com',
    'comment_author_url' => '',
    'comment_content' => 'Hello Ion, how are you today?',
    'comment_type' => '',
    'comment_parent' => 0,
    'user_id' => $user_id1,
    'comment_author_IP' => '127.0.0.1',
    'comment_agent' => '',
    'comment_date' => current_time('mysql'),
    'comment_approved' => 1,
);

wp_insert_comment($commentdata);

