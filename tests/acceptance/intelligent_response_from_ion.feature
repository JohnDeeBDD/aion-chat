# /var/www/html/wp-content/plugins/ion-chat/tests/acceptance/test_stubs_are_deployed_context.php
Feature: Intelligent Response from Ion

  As an acceptance tester
  I want to receive intelligent replies from Ion
  So that I can improve my understanding and efficiency

  Scenario: Ion replies to a comment
    Given the plugin is setup
    And appropriate test stubs have been deployed
    When I make a comment
    Then I should see an intelligent response from Ion