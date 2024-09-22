<?php

class ActionButtonTest extends \Codeception\TestCase\WPTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        require_once('/var/www/html/wp-content/plugins/aion-chat/src/AionChat/autoloader.php');
    }

    /**
     * @test
     * it should exist
     */
    public function testClassAndMethodsExistence()
    {
        $className = '\AionChat\ActionButtonManager';
        // Check if the class exists
        $this->assertTrue(class_exists($className), "Class {$className} does not exist");

        // If the class exists, check if the methods exists in the class
        $methodNames = ['enable_custom_comment_buttons', 'add_custom_comment_buttons'];
        foreach ($methodNames as $methodName){
            if (class_exists($className)) {
                $this->assertTrue(method_exists($className, $methodName), "Method {$methodName} does not exist in class {$className}");
            }
        }
    }
}