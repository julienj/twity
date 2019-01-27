Feature: Profile API

  @createSchema
  Scenario: Retrieve information from user profile
    Given I am a user
    When I send a "GET" request to "/api/me"
    Then the response status code should be 200
    And the JSON node "username" should be equal to "user"
    And the JSON node "createdAt" should not be null
    And the JSON node "token" should not be null
    And the JSON node "roles" should have 1 element
    And the JSON node "role" should be equal to "ROLE_USER"

  Scenario: Retrieve information from admin profile
    Given I am an administrator
    When I send a "GET" request to "/api/me"
    Then the response status code should be 200
    And the JSON node "username" should be equal to "admin"
    And the JSON node "createdAt" should not be null
    And the JSON node "token" should not be null
    And the JSON node "roles" should have 3 elements
    And the JSON node "role" should be equal to "ROLE_ADMIN"

  Scenario: Regenerate token
    Given I am a user
    When I send a "POST" request to "/api/me/regenerate-token"
    Then the response status code should be 201
