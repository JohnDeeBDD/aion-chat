Feature: Checking server setup

  Scenario: Verifying the setup of two servers
    Given there are two servers
    When I go to the other servers
    Then I see the servers are setup correctly
