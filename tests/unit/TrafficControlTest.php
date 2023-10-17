<?php

use Codeception\TestCase\WPTestCase;
use IonChat\TrafficController;
use PHPUnit\Framework\Assert;

class TrafficControlTest extends WPTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // Given: Database setup for traffic control
        TrafficController::activation_setup_db();
    }

    /**
     * @test
     * Tests linking of local and remote threads
     */
    public function it_should_link_local_and_remote_threads_correctly()
    {
        // Given: A new connection and local and remote thread IDs
        $connection_id = TrafficController::create_connection("https://fubar.com");
        $local_thread_id = 123;
        $remote_thread_id = 987;

        // When: Linking local and remote threads
        $thread_link_id = TrafficController::link_local_remote_threads($local_thread_id, $remote_thread_id, $connection_id);

        // Then: The threads should be correctly linked
        Assert::assertEquals(987, TrafficController::get_remote_thread_id($local_thread_id, $connection_id));
        Assert::assertNull(TrafficController::get_remote_thread_id(666, $connection_id));
        Assert::assertEquals(123, TrafficController::get_local_thread_id($remote_thread_id, $connection_id));
    }

    /**
     * @test
     * Tests recording and fetching of remote connection URL
     */
    public function it_should_record_and_retrieve_connection_url_successfully()
    {
        // Given: A new connection
        $id = TrafficController::create_connection("https://fubar.com");

        // When: Fetching the connection by ID and URL
        // Then: The fetched connection should match the created one
        Assert::assertEquals("https://fubar.com", TrafficController::get_connection($id));
        Assert::assertEquals($id, TrafficController::get_connection_id("https://fubar.com"));
        Assert::assertNull(TrafficController::get_connection_id("https://notfubar.com"));
    }

    /**
     * @test
     * Tests that IDs are incrementing
     */
    public function it_should_generate_unique_ids_for_new_connections()
    {
        // Given: Three new connections
        $id1 = TrafficController::create_connection("https://fubar.com");
        $id2 = TrafficController::create_connection("https://barfoo.com");
        $id3 = TrafficController::create_connection("https://shazam.com");

        // When: Comparing the IDs
        // Then: No two IDs should be the same
        Assert::assertNotEquals($id1, $id2);
        Assert::assertNotEquals($id1, $id3);
        Assert::assertNotEquals($id2, $id3);
    }
}