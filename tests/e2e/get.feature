Feature: Get products (buy products)
  In order to get products from the vending machine
  As a developer
  I want to simulate user input and verify get products is working

  Scenario: Get product with invalid input and leave
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "get"
    When I wait to "Insert the quantity and the product name, example -> (10,Soda): " and I input "10"
    Then I should see "Provide a valid input" and continue
    Then I input "2,Coffee"
    Then I should see "Product Coffee not found" and continue
    Then I input "exit"

  Scenario: Get product
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "service"
    And I wait to "Choose an action (change, products, exit): " and I input "products"
    And I wait to "Choose an action (add, modify, cancel): " and I input "add"
    And I wait to "Insert the total elements, price and name following this format -> example: (10,0.45,Bread): " and I input "10,0.35,Tomato"
    Then I should see "Product added" and continue
    Then the vending machine should have "Tomato" product with quantity "10" and price "0.35"
    When I input "get"
    When I wait to "Insert the quantity and the product name, example -> (10,Soda): " and I input "2,Tomato"
    Then I should see "Insufficient money to buy Tomato" and continue
    Then I input "insert"
    Then I wait to "Insert quantity: " and I input "1,1"
    Then I should see "Inserted" and continue
    Then I input "service"
    When I wait to "Choose an action (change, products, exit): " and I input "change"
    When I wait to "Insert the change: " and I input "2"
    Then I should see "Change added" and continue
    Then I input "get"
    When I wait to "Insert the quantity and the product name, example -> (10,Soda): " and I input "2,Tomato"
    Then I should see "Sold product: Tomato" and ends
    Then the vending machine should have "8" as quantity of "Tomato"

  Scenario: Get product without change
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "service"
    And I wait to "Choose an action (change, products, exit): " and I input "products"
    And I wait to "Choose an action (add, modify, cancel): " and I input "add"
    And I wait to "Insert the total elements, price and name following this format -> example: (10,0.45,Bread): " and I input "10,0.35,Tomato"
    Then I should see "Product added" and continue
    Then the vending machine should have "Tomato" product with quantity "10" and price "0.35"
    When I input "get"
    When I wait to "Insert the quantity and the product name, example -> (10,Soda): " and I input "2,Tomato"
    Then I should see "Insufficient money to buy Tomato" and continue
    Then I input "insert"
    Then I wait to "Insert quantity: " and I input "1,1"
    Then I should see "Inserted" and continue
    Then I input "get"
    When I wait to "Insert the quantity and the product name, example -> (10,Soda): " and I input "1,Tomato"
    Then I should see "Insufficient change in vending machine" and ends

  Scenario: Get product with enough stock
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "service"
    And I wait to "Choose an action (change, products, exit): " and I input "products"
    And I wait to "Choose an action (add, modify, cancel): " and I input "add"
    And I wait to "Insert the total elements, price and name following this format -> example: (10,0.45,Bread): " and I input "10,0.35,Tomato"
    Then I should see "Product added" and continue
    Then the vending machine should have "Tomato" product with quantity "10" and price "0.35"
    When I input "get"
    When I wait to "Insert the quantity and the product name, example -> (10,Soda): " and I input "2,Tomato"
    Then I should see "Insufficient money to buy Tomato" and continue
    Then I input "insert"
    Then I wait to "Insert quantity: " and I input "1,1,1,1,1,1,1,1,1,1,1,1"
    Then I should see "Inserted" and continue
    Then I input "service"
    When I wait to "Choose an action (change, products, exit): " and I input "change"
    When I wait to "Insert the change: " and I input "20"
    Then I should see "Change added" and continue
    Then I input "get"
    When I wait to "Insert the quantity and the product name, example -> (10,Soda): " and I input "11,Tomato"
    Then I should see "Insufficient stock to buy Tomato" and ends

  Scenario: Get product with enough money
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "service"
    And I wait to "Choose an action (change, products, exit): " and I input "products"
    And I wait to "Choose an action (add, modify, cancel): " and I input "add"
    And I wait to "Insert the total elements, price and name following this format -> example: (10,0.45,Bread): " and I input "10,2,Tomato"
    Then I should see "Product added" and continue
    Then the vending machine should have "Tomato" product with quantity "10" and price "2"
    When I input "get"
    When I wait to "Insert the quantity and the product name, example -> (10,Soda): " and I input "2,Tomato"
    Then I should see "Insufficient money to buy Tomato" and continue
    Then I input "insert"
    Then I wait to "Insert quantity: " and I input "1"
    Then I should see "Inserted" and continue
    Then I input "service"
    When I wait to "Choose an action (change, products, exit): " and I input "change"
    When I wait to "Insert the change: " and I input "20"
    Then I should see "Change added" and continue
    Then I input "get"
    When I wait to "Insert the quantity and the product name, example -> (10,Soda): " and I input "1,Tomato"
    Then I should see "Insufficient money to buy Tomato" and ends

  Scenario: Get product no exists
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "service"
    And I wait to "Choose an action (change, products, exit): " and I input "products"
    And I wait to "Choose an action (add, modify, cancel): " and I input "add"
    And I wait to "Insert the total elements, price and name following this format -> example: (10,0.45,Bread): " and I input "10,2,Tomato"
    Then I should see "Product added" and continue
    Then the vending machine should have "Tomato" product with quantity "10" and price "2"
    When I input "get"
    When I wait to "Insert the quantity and the product name, example -> (10,Soda): " and I input "2,Grape"
    Then I should see "Product Grape not found" and ends

  Scenario: Get product with change if 0.05
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "service"
    And I wait to "Choose an action (change, products, exit): " and I input "products"
    And I wait to "Choose an action (add, modify, cancel): " and I input "add"
    And I wait to "Insert the total elements, price and name following this format -> example: (10,0.45,Bread): " and I input "10,0.35,Tomato"
    Then I should see "Product added" and continue
    Then the vending machine should have "Tomato" product with quantity "10" and price "0.35"
    When I input "get"
    When I wait to "Insert the quantity and the product name, example -> (10,Soda): " and I input "2,Tomato"
    Then I should see "Insufficient money to buy Tomato" and continue
    Then I input "insert"
    Then I wait to "Insert quantity: " and I input "0.05,0.25,0.05,0.05"
    Then I should see "Inserted" and continue
    Then I input "service"
    When I wait to "Choose an action (change, products, exit): " and I input "change"
    When I wait to "Insert the change: " and I input "2"
    Then I should see "Change added" and continue
    When I input "get"
    When I wait to "Insert the quantity and the product name, example -> (10,Soda): " and I input "1,Tomato"
    Then I should see "Sold product: Tomato" and ends
    Then the vending machine should have "1.95" as a change

