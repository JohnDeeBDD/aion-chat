# /var/www/html/wp-content/plugins/ion-chat/tests/acceptance/test_stubs_are_deployed_context.php
Feature: Intelligent Response from Ion

  As an acceptance tester
  I want to receive intelligent replies from Ion
  So that I can improve my understanding and efficiency

  Scenario: Ion replies to a comment on a remote
    Given the plugin is setup on the servers
    And appropriate test stubs have been deployed on the remote
    When I make a comment on the remote
    Then I should see an intelligent response from Ion on the remote

  Scenario: Ion replies to a comment on the mothership
    Given the plugin is setup on the servers
    And appropriate test stubs have been deployed on the mothership
    When I make a comment on the mothership
    Then I should see an intelligent response from Ion on the mothership