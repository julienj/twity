Feature: Application API

  @createSchema
  Scenario: Create an application
    Given I am an administrator
    When I send a POST request to "/api/applications" with body:
        """
        {
          "name": "my app",
          "description": "my app"
        }
        """
    Then the response status code should be 201


  Scenario: list all applications
    Given I am an administrator
    When I send a GET request to "/api/applications"
    Then the response status code should be 200
    And the JSON node 'total' should be equal to 1



  Scenario: create a invalid application
    Given I am an administrator
    When I send a POST request to "/api/applications" with body:
        """
        {
          "name": "empty description"
        }
        """
    Then the response status code should be 400

