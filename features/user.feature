Feature: User API

  @createSchema
  Scenario: Create user
    Given I am an administrator
    When I send a POST request to "/api/users" with body:
        """
        {
          "username": "john.doe",
          "fullName": "John Doe",
          "email": "john.doe@exemple.com",
          "role": "ROLE_USER"
        }
        """
    Then the response status code should be 201


  Scenario: list all user
    Given I am an administrator
    When I send a GET request to "/api/users"
    Then the response status code should be 200
    And the JSON node 'total' should be equal to 3


  Scenario: create a invalid user
    Given I am an administrator
    When I send a POST request to "/api/users" with body:
        """
        {
          "firstName": "John",
          "lastName": "Doe"
        }
        """
    Then the response status code should be 400

