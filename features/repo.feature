Feature: Composer repository API

  @createSchema
  Scenario: Invalid token
    Given I have a invalid user token
    When I send a "GET" request to "/packages.json"
    Then the response status code should be 401

  Scenario: Repository index
    Given I have a user token
    And  I load "simple_provider.yml" fixture
    When I send a "GET" request to "/packages.json"
    Then the response status code should be 200
    And the JSON node "packages" should have 0 element
    And the JSON node "providers" should have 1 element
    And the JSON node "providers.vendor/package" should exist

  Scenario: Search package
    Given I have a user token
    When I send a "GET" request to "/p/search.json?q=vendor"
    Then the response status code should be 200
    And the JSON node "results" should have 1 element

  Scenario: Search unknown package
    Given I have a user token
    When I send a "GET" request to "/p/search.json?q=unknown"
    Then the response status code should be 200
    And the JSON node "results" should have 0 element
