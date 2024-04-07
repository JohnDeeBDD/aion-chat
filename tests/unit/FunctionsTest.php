<?php


class FunctionsTest extends \Codeception\TestCase\WPTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        require_once('/var/www/html/wp-content/plugins/aion-chat/src/AionChat/autoloader.php');
    }

    public function testValidFunctionCall() {
        $expectedResult = [
            'name' => 'execute_local_command',
            'arguments' => ["command" => "ls /var/www/html"]
        ];
        $result = \AionChat\Functions::get_open_a_i_funcion_call_parameters($this->getStubFunctionResponse());
        $this->assertEquals($expectedResult, $result);
    }

    public function testInvalidString() {
        $invalidString = 'This is not a valid string for function call';
        $result = \AionChat\Functions::get_open_a_i_funcion_call_parameters($invalidString);
        $this->assertFalse($result);
    }

    private function getStubFunctionResponse(){
        $retrievedString = file_get_contents("/var/www/html/wp-content/plugins/aion-chat/tests/unit/stubFunctionCall.txt");
        return $retrievedString;
    }

}