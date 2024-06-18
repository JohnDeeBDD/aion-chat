<?php

class HandleJSONTest extends \Codeception\TestCase\WPTestCase {
    protected $handleJSON;

    protected function setUp(): void {
        parent::setUp();
        require_once('/var/www/html/wp-content/plugins/aion-chat/src/AionChat/autoloader.php');
        require_once('/var/www/html/wp-content/plugins/aion-mother/src/AionChatMothership/autoloader.php');
        $this->handleJSON = new \AionChatMothership\HandleJSON();
    }

    public function testDetectJSON_ValidJSON() {
        $string = 'This is some text {"key":"value"} more text.';
        $result = $this->handleJSON->detectJSON($string);
        $this->assertTrue($result, 'Failed to detect valid JSON.');
    }

    public function testDetectJSON_InvalidJSON() {
        $string = 'This is some text {key:value} more text.';
        $result = $this->handleJSON->detectJSON($string);
        $this->assertFalse($result, 'Incorrectly detected invalid JSON as valid.');
    }

    public function testDetectJSON_NoJSON() {
        $string = 'This is some text without JSON.';
        $result = $this->handleJSON->detectJSON($string);
        $this->assertFalse($result, 'Incorrectly detected JSON in a string without JSON.');
    }

    public function testRemoveNonJSON_ValidJSON() {
        $string = 'Prefix text {"key":"value"} Suffix text.';
        $expected = '{"key":"value"}';
        $result = $this->handleJSON->removeNonJSON($string);
        $this->assertEquals($expected, $result, 'Failed to extract valid JSON.');
    }

    public function testRemoveNonJSON_InvalidJSON() {
        $string = 'Prefix text {key:value} Suffix text.';
        $expected = '';
        $result = $this->handleJSON->removeNonJSON($string);
        $this->assertEquals($expected, $result, 'Extracted invalid JSON.');
    }

    public function testRemoveNonJSON_NoJSON() {
        $string = 'This is some text without JSON.';
        $expected = '';
        $result = $this->handleJSON->removeNonJSON($string);
        $this->assertEquals($expected, $result, 'Incorrectly extracted JSON from a string without JSON.');
    }
}