Feature: Provider API

  @createSchema
  Scenario: User login
    Given I am on "/login"
    Then I fill in "username" with "user"
    And I fill in "password" with "user"
    And I press "Sign in"
    And I should be on the homepage


  Scenario: User invalid login
    Given I am on "/login"
    Then I fill in "username" with "bad user"
    And I fill in "password" with "bad password"
    And I press "Sign in"
    And I should be on "/login"
