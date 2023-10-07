<?php

use IonChat\Prompt;

require_once('/var/www/html/wp-content/plugins/ion-chat/src/IonChat/autoloader.php');

class PromptTrafficControlTest extends \Codeception\TestCase\WPTestCase
{

    /**
     * @test
     * record remote URL and fetch it
     */
    public function recordTheIdAndFetchIt()
    {
        \IonChat\TrafficController::activation_setup_db();
        $ID = \IonChat\TrafficController::create_connection("https://fubar.com");
        \PHPUnit\Framework\assertEquals("https://fubar.com", \IonChat\TrafficController::get_connection($ID));
        \PHPUnit\Framework\assertEquals($ID, \IonChat\TrafficController::get_connection_id("https://fubar.com"));
    }

    /**
     * @test
     * id is incrementing
     */
    public function IdIsIncrimenting()
    {
        \IonChat\TrafficController::activation_setup_db();
        $ID1 = \IonChat\TrafficController::create_connection("https://fubar.com");
        $ID2 = \IonChat\TrafficController::create_connection("https://barfoo.com");
        $ID3 = \IonChat\TrafficController::create_connection("https://shazam.com");

        \PHPUnit\Framework\assertNotEquals($ID1, $ID2);
        \PHPUnit\Framework\assertNotEquals($ID1, $ID3);
        \PHPUnit\Framework\assertNotEquals($ID2, $ID3);
    }

    /**
     * @test
     * Prompt validator test
     */
    public function PromptValidatorTest()
    {
        $Prompt = Prompt::instantiate_dummy();
        $string = serialize($Prompt);

        echo '<pre>';
        var_dump($Prompt);
        echo '</pre>';

        echo '<pre>';
        var_dump($string);
        echo '</pre>';



        \PHPUnit\Framework\assertEquals(true, Prompt::isSerializedPrompt($string));
    }
}