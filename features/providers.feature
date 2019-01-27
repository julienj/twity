Feature: Provider API

  @createSchema
  Scenario: Create a mirror provider
    Given I am an administrator
    When I send a POST request to "/api/providers" with body:
        """
        {
          "name": "vendor/package",
          "type": "composer"
        }
        """
    Then the response status code should be 201


  Scenario: find a provider
    Given I am an administrator
    When I send a GET request to "/api/providers/vendor/package"
    Then the response status code should be 200
    And the JSON node 'name' should be equal to "vendor/package"
    And the JSON node 'type' should be equal to "composer"
    And the JSON node 'updateInProgress' should be equal to true


  Scenario: list all providers
    Given I am an administrator
    When I send a GET request to "/api/providers"
    Then the response status code should be 200
    And the JSON node 'total' should be equal to 1
    And the JSON node 'facets.composer' should be equal to 1
    And the JSON node 'items.vendor/package.name' should be equal to "vendor/package"
    And the JSON node 'items.vendor/package.type' should be equal to "composer"
    And the JSON node 'items.vendor/package.updateInProgress' should be equal to true



  Scenario: create a vcs provider
    Given I am an administrator
    When I send a POST request to "/api/providers" with body:
        """
        {
          "name": "vendor2/package2",
          "type": "vcs",
          "vcsUri": "https://depot.com/project.git"
        }
        """
    Then the response status code should be 201



  Scenario: list all providers
    Given I am an administrator
    When I send a GET request to "/api/providers"
    Then the response status code should be 200
    And the JSON node 'total' should be equal to 2
    And the JSON node 'facets.composer' should be equal to 1
    And the JSON node 'facets.vcs' should be equal to 1
    And the JSON node 'items.vendor/package.name' should be equal to "vendor/package"
    And the JSON node 'items.vendor2/package2.name' should be equal to "vendor2/package2"
    And the JSON node 'items.vendor2/package2.type' should be equal to "vcs"
    And the JSON node 'items.vendor2/package2.updateInProgress' should be equal to true



  Scenario: create a invalid provider
    Given I am an administrator
    When I send a POST request to "/api/providers" with body:
        """
        {
          "name": "vendor3/package3",
          "type": "other",
          "vcsUri": "https://depot.com/project.git"
        }
        """
    Then the response status code should be 400



  Scenario: refresh a provider
    Given I am an administrator
    When I send a PUT request to "/api/providers/vendor/package"
    Then the response status code should be 204


  Scenario: refresh a invalid provider
    Given I am an administrator
    When I send a PUT request to "/api/providers/vendor3/package3"
    Then the response status code should be 404

  Scenario: delete a provider
    Given I am an administrator
    When I send a DELETE request to "/api/providers/vendor2/package2"
    Then the response status code should be 204


  Scenario: check delete
    Given I am an administrator
    When I send a GET request to "/api/providers/vendor2/package2"
    Then the response status code should be 404
