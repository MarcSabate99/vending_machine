Feature: Insert money
  In order to insert money to the vending machine
  As a developer
  I want to simulate user input and verify insert money is working

  Scenario: Insert coins with valid quantity as concatenated string
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "insert"
    And I wait to "Insert quantity: " and I input "1,0.25,0.05"
    Then I should see "Inserted" and ends
    Then the vending machine should have "1.3" as inserted money

  Scenario: Insert coins with valid quantity as individual string
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "insert"
    And I wait to "Insert quantity: " and I input "0.05"
    Then I should see "Inserted" and ends
    Then the vending machine should have "0.05" as inserted money

  Scenario: Insert coins with invalid quantity as concatenated string
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "insert"
    And I wait to "Insert quantity: " and I input "1,0.25,0.5"
    Then I should see "Could not insert provided coin, valid coins: 0.05,0.1,0.25,1" and ends
    Then the vending machine should have "0" as inserted money

  Scenario: Insert coins with invalid quantity as individual string
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "insert"
    And I wait to "Insert quantity: " and I input "0.5"
    Then I should see "Could not insert provided coin, valid coins: 0.05,0.1,0.25,1" and ends
    Then the vending machine should have "0" as inserted money