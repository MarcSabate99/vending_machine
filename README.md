# Vending Machine App
This is a vending machine CLI application.
### Features
* Insert money to the vending machine
* Get the inserted money back
* Buy a product
* Manage vending machine
  * Add change money
  * Manage products
    * Add product
    * Modify existing product
### Requirements to install the app
* Download docker (https://docs.docker.com/get-started/get-docker/)

### How to install the application
Clone the project:
```
git clone https://github.com/MarcSabate99/vending_machine.git
```
Execute this command in the project root path:
```
make install
```
### How to run the tests
To run unit test run in the project root path:
```
make test-unit
```
To run integration test run in the project root path:
```
make test-integration
```
To run e2e test run in the project root path:
```
make test-e2e
```
To run all tests run in the project root path:
```
make test
```

### How works
You can run the application using in the project root path:
```
make run
```
Then you will see this menu:
```
Choose an action (insert, return, get, service, exit):
```
You have different options here, enter the desired.
#### Options:
##### insert
This will ask to insert the quantity you want to enter to the vending machine:
```
Insert quantity:
```
Enter the quantity (Valid coins: 0.05,0.1,0.25,1) -> You can enter the quantity like this:
```
1,0.05,0.25
```
or just a number:
```
0.25
```
##### return
This returns the inserted money
##### get
This allows you to buy a product
```
Insert the quantity and the product name, example -> (10,Soda):
```
Enter the product quantity and the product name

##### service
This allows to add change money and manage products
```
Choose an action (change, products, exit):
```
Enter "change" to add change to the vending machine:
```
Insert the change:
```
Enter "products" to manage products:
```
Choose an action (add, modify, cancel):
```
Enter "add" to add a new product
```
Insert the total elements, price and name following this format -> example: (10,0.45,Bread):
```
Enter "modify" to modify a product
```
Select the product: 
[1] Water
[2] Juice
[3] Soda
```
Enter the id
```
Enter the id:
```
Let's input 1, this will ask what do you want to modify:
```
What do you want to modify? (quantity, price, cancel):
```
If you select quantity:
```
Enter the quantity:
```
If you select price:
```
Enter the price:
```

