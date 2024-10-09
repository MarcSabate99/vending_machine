Feature: Return money
  In order to return money from the vending machine
  As a developer
  I want to simulate user input and verify return money is working

  Scenario: Return money without money in vending machine
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "return"
    Then I should see "Money returned" and ends
    Then the vending machine should have "0" as inserted money

  Scenario: Return money with money in the vending machine
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "insert"
    And I wait to "Insert quantity: " and I input "1,0.25,0.05"
    Then I should see "Inserted" and continue
    Then the vending machine should have "1.3" as inserted money
    Then I input "return"
    Then I should see "Money returned" and ends
    Then the vending machine should have "0" as inserted money
