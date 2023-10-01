<?php
use EmailTunnel\ConnectionFinder;
use Codeception\TestCase\WPTestCase;

class ConnectionFinderTest extends WPTestCase {
    public function testFetchSingularPostByTitleAndAuthorWithValidDataForPostType() {
        // GIVEN: A test post exists with the given title and author
        $post_id = $this->factory->post->create(array(
            'post_type' => 'post',
            'post_title' => 'Test Post',
            'post_status' => 'publish',
        ));

        // WHEN: We call the method with the test data for the "post" post type
        $result = ConnectionFinder::fetchSingularPostByTitleAndAuthor('Test Post', get_post_field('post_author', $post_id), 'post');

        // THEN: It should return a valid post object with the expected title
        $this->assertInstanceOf('WP_Post', $result);
        $this->assertEquals('Test Post', $result->post_title);
    }

    public function testFetchSingularPostByTitleAndAuthorWithNoMatchingPostForPostType() {
        // GIVEN: No posts match the given title and author for the "post" post type
        // WHEN: We call the method with non-existent post data for the "post" post type
        $result = ConnectionFinder::fetchSingularPostByTitleAndAuthor('Non-Existent Post', 0, 'post');

        // THEN: It should return false, indicating no matching post
        $this->assertFalse($result);
    }

    public function testFetchSingularPostByTitleAndAuthorWithMultipleMatchingPostsForPostType() {
        // GIVEN: Multiple posts exist with the same title and author for the "post" post type
        $post_id_1 = $this->factory->post->create(array(
            'post_type' => 'post',
            'post_title' => 'Test Post',
            'post_status' => 'publish',
        ));

        $post_id_2 = $this->factory->post->create(array(
            'post_type' => 'post',
            'post_title' => 'Test Post',
            'post_status' => 'publish',
        ));

        // WHEN: We call the method with the test data for the "post" post type
        // THEN: It should throw an exception indicating more than one matching post
        $this->expectException(\Exception::class);
        ConnectionFinder::fetchSingularPostByTitleAndAuthor('Test Post', get_post_field('post_author', $post_id_1), 'post');
    }

    public function testFetchSingularPostByTitleAndAuthorWithValidDataForCustomPostType() {
        // GIVEN: A test post exists with the given title and author for the custom post type "etm-connection"
        $post_id = $this->factory->post->create(array(
            'post_type' => 'etm-connection',
            'post_title' => 'Test Connection',
            'post_status' => 'publish',
        ));

        // WHEN: We call the method with the test data for the custom post type "etm-connection"
        $result = ConnectionFinder::fetchSingularPostByTitleAndAuthor('Test Connection', get_post_field('post_author', $post_id), 'etm-connection');

        // THEN: It should return a valid post object with the expected title
        $this->assertInstanceOf('WP_Post', $result);
        $this->assertEquals('Test Connection', $result->post_title);
    }

    public function testFetchSingularPostByTitleAndAuthorWithNoMatchingPostForCustomPostType() {
        // GIVEN: No posts match the given title and author for the custom post type "etm-connection"
        // WHEN: We call the method with non-existent post data for the custom post type "etm-connection"
        $result = ConnectionFinder::fetchSingularPostByTitleAndAuthor('Non-Existent Connection', 0, 'etm-connection');

        // THEN: It should return false, indicating no matching post
        $this->assertFalse($result);
    }

    public function testFetchSingularPostByTitleAndAuthorWithMultipleMatchingPostsForCustomPostType() {
        // GIVEN: Multiple posts exist with the same title and author for the custom post type "etm-connection"
        $post_id_1 = $this->factory->post->create(array(
            'post_type' => 'etm-connection',
            'post_title' => 'Test Connection',
            'post_status' => 'publish',
        ));

        $post_id_2 = $this->factory->post->create(array(
            'post_type' => 'etm-connection',
            'post_title' => 'Test Connection',
            'post_status' => 'publish',
        ));

        // WHEN: We call the method with the test data for the custom post type "etm-connection"
        // THEN: It should throw an exception indicating more than one matching post
        $this->expectException(\Exception::class);
        ConnectionFinder::fetchSingularPostByTitleAndAuthor('Test Connection', get_post_field('post_author', $post_id_1), 'etm-connection');
    }
}
