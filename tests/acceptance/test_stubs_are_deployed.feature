# /var/www/html/wp-content/plugins/ion-chat/tests/acceptance/test_stubs_are_deployed_context.php
Feature: Test stubs are deployed
  As an acceptance tester
  I want to deploy the test stubs
  So that I can test the plugin

  Scenario: Test stubs are deployed
    Given the plugin is setup
    When test stubs are deployed
    #Then there should be a post on the remote node