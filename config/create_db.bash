#!/bin/bash

mkdir -p db
mkdir -p tests/db

dbReal=$(cat <<EOF
{
    "products": [
        {
            "name": "Water",
            "quantity": 5,
            "price": 0.65,
            "id": 1
        },
        {
            "name": "Juice",
            "quantity": 5,
            "price": 1.00,
            "id": 2
        },
        {
            "name": "Soda",
            "quantity": 5,
            "price": 1.50,
            "id": 3
        }
    ],
    "change": 0,
    "insertedMoney": 0
}
EOF
)

dbTest=$(cat <<EOF
{
    "products": [],
    "change": 0,
    "insertedMoney": 0
}
EOF
)

echo "$dbReal" > db/vending_machine.json
echo "$dbTest" > tests/db/vending_machine.json

echo "Database files created successfully."
