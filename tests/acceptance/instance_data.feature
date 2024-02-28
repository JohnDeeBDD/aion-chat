# /var/www/html/wp-content/plugins/ion-chat/tests/acceptance/test_stubs_are_deployed_context.php
Feature: Particular instances store their data
  As an instance of the plugin
  I want to have my own data
  So that I can be all I can be

  Scenario:
  Given there is an instance of the plugin
    When the instance is a dev instance
    And the instance is on a localhost
    Then the data should be found in the yml file

  Scenario: The instance determines where and who it is
    Given there is an instance of the plugin
    When the plugin runs
    Then it should have a process


    on a localhost
    node
    prod node
    mothership
    prod mothership
