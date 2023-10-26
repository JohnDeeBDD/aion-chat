<?php

class DebugOptionTest extends \Codeception\TestCase\WPTestCase{

    /**
     * @test
     * it should be instantiable
     */
    public function testDebugOptionClassAndMethodExist()
    {
        // Check if the class "DebugOption" exists
        $classExists = class_exists('\IonChat\DebugMode');
        $this->assertTrue($classExists, 'Class DebugOption does not exist.');

        // Check if the static method "display" exists in the class
        $methodExists = method_exists('\IonChat\DebugMode', 'display_option');
        $this->assertTrue($methodExists, 'Method display does not exist in class DebugOption.');
    }

    /**
     * @test
     * DebugOption::display should accept one argument, $option_name
     */
    public function test_method_display_arguments(){
        // Given: Initialize reflection to inspect the DebugOption::display method
        $reflection = new \ReflectionMethod('\IonChat\DebugMode', 'display_option');

        // When: Retrieve the parameters of the display_option method
        $parameters = $reflection->getParameters();

        // Then: Assert that there is exactly one parameter and it's named $option_name
        $this->assertCount(1, $parameters, 'Method display_option should accept exactly one argument.');
        $this->assertEquals('option_name', $parameters[0]->getName(), 'The argument should be named $option_name.');
    }

}