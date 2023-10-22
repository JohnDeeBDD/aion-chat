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


    /**
     * @test
     * DebugOption::display should pretty print option var dump and die
     */
    public function test_method_display_functionality(){
        // Given: Mock the option_name and its value
        $option_name = 'some_option';
        $option_value = 'some_value';
        update_option($option_name, $option_value);

        // When: Capture the output and terminate behavior of DebugOption::display_option
        ob_start();
        try {
            \IonChat\DebugMode::display_option($option_name);
        } catch (\Exception $e) {
            // Catch the exception thrown after die() or exit() is called
        }
        $output = ob_get_clean();

        // Then: Assert that the output contains the pretty-printed option value and script is terminated
        $this->assertStringContainsString($option_value, $output, 'Output should contain the pretty-printed option value.');
        $this->assertInstanceOf(\Exception::class, $e, 'An exception should be caught indicating script termination.');
    }
}