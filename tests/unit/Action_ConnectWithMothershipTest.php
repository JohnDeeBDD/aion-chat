<?php

require_once('/var/www/html/wp-content/plugins/email-tunnel/src/EmailTunnel/autoloader.php');

class Action_ConnectWithMothershipTest extends \Codeception\TestCase\WPTestCase
{

    /**
     * @test
     * Test that the function returns false if the post is less than 15 minutes old.
     */
    public function testPostIsLessThan15MinutesOld()
    {
        // Create a mock post
        $post_id = wp_insert_post(array(
            'post_title' => 'Test Post',
            'post_content' => 'This is a test post.',
            'post_status' => 'publish'
        ));

        $post = get_post($post_id);

        // Run the function against the post

        $result = \EmailTunnel\Action_ConnectWithMothership::isIt15MinutesOld($post->ID);

        // Assert that the function should return false
        $this->assertFalse($result);
    }

    /**
     * @test
     * Test that the function returns true if the post is older than 15 minutes.
     */
    public function testPostIsOlderThan15Minutes()
    {
        // Create a mock post and update the post_modified field to 30 minutes ago
        $post_id = wp_insert_post(array(
            'post_title' => 'Test Post',
            'post_content' => 'This is a test post.',
            'post_status' => 'publish'
        ));

        $this->changePostLastModifiedTime($post_id, (time() - 86400));

        // Run the function against the post
        $result = \EmailTunnel\Action_ConnectWithMothership::isIt15MinutesOld($post_id);

        // Assert that the function should return true
        $this->assertTrue($result);
    }

    /**
     * Updates the "last modified" time of a post in the WordPress database.
     *
     * @param int $postID The ID of the post to update.
     * @param int $time The Unix timestamp to set the post's "last modified" time to.
     *                  This value should be in seconds since the Unix epoch (January 1 1970 00:00:00 UTC).
     * @return bool True if the update was successful, false otherwise.
     */
    private function changePostLastModifiedTime($postID, $time)
    {
        global $wpdb;

        // Get the post object by ID
        $post = get_post($postID);

        // Return false if post not found
        if (!$post) {
            return false;
        }

        // Get the name of the posts table
        $table_name = $wpdb->prefix . 'posts';

        // Update the post_modified and post_modified_gmt fields in the database
        $result = $wpdb->update(
            $table_name,
            array(
                'post_modified' => gmdate("Y-m-d H:i:s", $time),
                'post_modified_gmt' => gmdate("Y-m-d H:i:s", $time)
            ),
            array('ID' => $postID)
        );

        // Return false if the update query fails
        if ($result === false) {
            return false;
        }

        // Set the post_modified and post_modified_gmt values in the post object
        $post->post_modified = gmdate("Y-m-d H:i:s", $time);
        $post->post_modified_gmt = gmdate("Y-m-d H:i:s", $time);

        // Clear the post cache to ensure that the updated values are used
        clean_post_cache($postID);

        // Return true upon success
        return true;
    }

    private function makePost20MinutesOld($postID) {
    // Get the current post
    $post = get_post($postID);

    // Check if the post exists
    if (!$post) {
        return;
    }

// Get the current date and time
$currentDateTime = current_time('mysql');

// Calculate the new date and time by subtracting 20 minutes
$newDateTime = date('Y-m-d H:i:s', strtotime('-20 minutes', strtotime($currentDateTime)));

// Update the post's "published on" date time
wp_update_post(array(
    'ID'           => $postID,
    'post_date'    => $newDateTime,
    'post_date_gmt' => get_gmt_from_date($newDateTime),
));
}

}
