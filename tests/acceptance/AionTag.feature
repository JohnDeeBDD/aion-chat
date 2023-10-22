Feature: AionTag
  As a WordPress developer,
  I want to be able to recognize Aion enabled posts
  So that the users and Aions can benefit

  Scenario: Tag is present on a WordPress post
    Given a WordPress post is viewed
    And the post has a tag "Aion"
    Then a Prompt should be created
    And the Prompt is stored in a site option
    And the site option should be visible with the get variable debug option=option_name