Feature: Service
  In order to service the vending machine
  As a developer
  I want to simulate user input and verify service is working

  Scenario: Add change
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "service"
    And I wait to "Choose an action (change, products, exit): " and I input "change"
    And I wait to "Insert the change: " and I input "10"
    Then I should see "Change added" and ends
    Then the vending machine should have "10" as change

  Scenario: Add change and add again
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "service"
    And I wait to "Choose an action (change, products, exit): " and I input "change"
    And I wait to "Insert the change: " and I input "10"
    Then I should see "Change added" and continue
    Then I input "service"
    Then I input "change"
    And I wait to "Insert the change: " and I input "15"
    Then I should see "Change added" and ends
    Then the vending machine should have "25" as change

  Scenario: Add product
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "service"
    And I wait to "Choose an action (change, products, exit): " and I input "products"
    And I wait to "Choose an action (add, modify, cancel): " and I input "add"
    And I wait to "Insert the total elements, price and name following this format -> example: (10,0.45,Bread): " and I input "10,0.35,Tomato"
    Then I should see "Product added" and ends
    Then the vending machine should have "Tomato" product with quantity "10" and price "0.35"

  Scenario: Add product with invalid data and retry to add
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "service"
    And I wait to "Choose an action (change, products, exit): " and I input "products"
    And I wait to "Choose an action (add, modify, cancel): " and I input "add"
    And I wait to "Insert the total elements, price and name following this format -> example: (10,0.45,Bread): " and I input "10,0.35"
    Then I should see "Provide a valid input" and continue
    Then I input "10,0.35,Tomato"
    Then I should see "Product added" and ends
    Then the vending machine should have "Tomato" product with quantity "10" and price "0.35"

  Scenario: Modify the quantity of a product
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "service"
    And I wait to "Choose an action (change, products, exit): " and I input "products"
    And I wait to "Choose an action (add, modify, cancel): " and I input "add"
    And I wait to "Insert the total elements, price and name following this format -> example: (10,0.45,Bread): " and I input "10,0.35,Tomato"
    Then I should see "Product added" and continue
    Then the vending machine should have "Tomato" product with quantity "10" and price "0.35"
    Then I input "service"
    And I wait to "Choose an action (change, products, exit): " and I input "products"
    And I wait to "Choose an action (add, modify, cancel): " and I input "modify"
    Then I input "1"
    And I wait to "What do you want to modify? (quantity, price, cancel): " and I input "quantity"
    And I wait to "Enter the quantity: " and I input "5"
    Then I should see "Quantity modified" and ends
    Then the vending machine should have "Tomato" product with quantity "5" and price "0.35"

  Scenario: Modify the price of a product
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "service"
    And I wait to "Choose an action (change, products, exit): " and I input "products"
    And I wait to "Choose an action (add, modify, cancel): " and I input "add"
    And I wait to "Insert the total elements, price and name following this format -> example: (10,0.45,Bread): " and I input "10,0.35,Tomato"
    Then I should see "Product added" and continue
    Then the vending machine should have "Tomato" product with quantity "10" and price "0.35"
    Then I input "service"
    And I wait to "Choose an action (change, products, exit): " and I input "products"
    And I wait to "Choose an action (add, modify, cancel): " and I input "modify"
    Then I input "1"
    And I wait to "What do you want to modify? (quantity, price, cancel): " and I input "price"
    And I wait to "Enter the price: " and I input "0.25"
    Then I should see "Price modified" and ends
    Then the vending machine should have "Tomato" product with quantity "10" and price "0.25"

  Scenario: Modify the price of a product that no exists and then I input one existent
    When I wait to "Choose an action (insert, return, get, service, exit): " and I input "service"
    And I wait to "Choose an action (change, products, exit): " and I input "products"
    And I wait to "Choose an action (add, modify, cancel): " and I input "add"
    And I wait to "Insert the total elements, price and name following this format -> example: (10,0.45,Bread): " and I input "10,0.35,Tomato"
    Then I should see "Product added" and continue
    Then the vending machine should have "Tomato" product with quantity "10" and price "0.35"
    Then I input "service"
    And I wait to "Choose an action (change, products, exit): " and I input "products"
    And I wait to "Choose an action (add, modify, cancel): " and I input "modify"
    Then I input "1234"
    And I wait to "Enter the id: " and I input "1"
    And I wait to "What do you want to modify? (quantity, price, cancel):" and I input "price"
    And I wait to "Enter the price: " and I input "0.65"
    Then I should see "Price modified" and ends
    Then the vending machine should have "Tomato" product with quantity "10" and price "0.65"

