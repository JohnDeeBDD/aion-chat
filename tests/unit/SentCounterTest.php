<?php

require_once('/var/www/html/wp-content/plugins/email-tunnel/src/EmailTunnel/autoloader.php');
class SentCounterTest extends \Codeception\TestCase\WPTestCase
{

    /**
     * @skip
     * getTest()
     * it should return the current count
     */
    public function getTest(){
        //Given there is an existent count
        $this->createMockData();
        
        //When get() is called
        $count = \EmailTunnel\SentCounter::get("https://somesite.com", "exit");
        
        //Then the correct count should be returned
        $this->assertEquals(123, $count);

        $count = \EmailTunnel\SentCounter::get("https://some-other-site.com", "entrance");

        //Then the correct count should be returned
        $this->assertEquals(456, $count);

        $count = \EmailTunnel\SentCounter::get("https://some-other-site.com", "exit");

        //Then the correct count should be returned
        $this->assertEquals(0, $count);
    }
    
    /**
     * @test
     * edge case: the count is zero and just starting
     * getTest()
     */
    public function getEdgeCaseNoCountTest(){
        //Given there is no count yet
        
        //When get() is called
        $count = \EmailTunnel\SentCounter::get();
        
        //Then the correct count should be returned
        $this->assertEquals(0, $count);
    }

    /**
     * @test
     * incrementTest()
     */
    public function incrementTest(){
        $count = \EmailTunnel\SentCounter::get();
        $this->assertEquals(0, $count);

        \EmailTunnel\SentCounter::increment();

        $count = \EmailTunnel\SentCounter::get();
        $this->assertEquals(1, $count);

        \EmailTunnel\SentCounter::increment();
        $count = \EmailTunnel\SentCounter::get();
        $this->assertEquals(2, $count);

        \EmailTunnel\SentCounter::increment();
        $count = \EmailTunnel\SentCounter::get();
        $this->assertEquals(3, $count);

        }

}