Feature: Aion notices a comment on its homepage
  As an Aion
  I want to notice a comment
  So taht I can respond to a user

  Scenario:
    Given there is an Aion on the site
    And the ion protocol is "mothership"
    When a comment is posted to an Aion Conversation page
    And the Aion notices the comment on the page
    Then a Prompt should be created
    And the Prompt should be sent to the LLM
    And the response should be published as a comment on the Aion Conversation
    And the response should be sent down

  Scenario: the Reflektor is not the author of the comment
    Given there is a Reflektor on the site
    When a comment is posted to an Aion Conversation page
    And the Reflektor notices the comment on the page
    And the Reflektor is not the author of the comment
    Then a Prompt should be created
    And the Prompt should be sent up
    And the dispatch is recorded

  Scenario: the Reflektor Parent Class is the author of the comment
    Given there is a Reflektor on the site
    When a comment is posted to an Aion Conversation page
    And the Reflektor notices the comment on the page
    And the Reflektor's Parent Class is the author of the comment
    Then the comment is being sent from upstream and should be left alone