<?php

use AionChat\Conversation;

class TrafficControllerTest extends \Codeception\TestCase\WPTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        require_once('/var/www/html/wp-content/plugins/aion-chat/src/AionChat/autoloader.php');
        require_once('/var/www/html/wp-content/plugins/aion-mother/src/AionChatMothership/autoloader.php');
    }

    /**
     * @test
     * it should be instantiable
     */
    public function itShouldBeInstantiable()
    {
        $TrafficController = new \AionChatMothership\TrafficController();
    }
}