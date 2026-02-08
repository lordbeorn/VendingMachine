# REQUIREMENTS AND EXECUTING THE PROTOTYPE

In order to execute the prototype you need to have docker and docker-compose installed.

In the folder script you will able to find several scripts both for windows and linux. Rememebr that for linux you mist set all the .sh files as executable.

- **build-and-run**: Build or rebuild the docker image file and runs it.
- **run**: Run the docker image. You need to have built it previously.
- **shell**: Allows you to enter inside the shell of the docker image. You need to have it running.
- **stop**: Shuts down the docume image.

---

# VENDING MACHINE PROGRAMMING DECISION AND ANALYSYS

I want to create this protoype based on DDD and Hexagonal architecture so the first step is extracting all the information to create the ubiquitous language for the application.  
Based on this I have extracted the following terms and requirements.

- **Vending Machine**: It's the main element that we want to create and the main entity that will be used for the interaction.
- **Coin**: Element that adds a value that can be used to buy vend items. It's possible values are 0.05, 0.10, 0.25 and 1.00
- **Vend Item**: Element that can be bought from the Vending Machine. There will be always three different vend items that can't change: Water, Juice and Soda
- **Vend Item Price**: Amount that is related to a partircular Vend Item and stablishes it's inmutable price: Water = 0.65, Juice = 1.00, Soda = 1.50
- **Currently Inserted Money**: Collection of Coins that allow a client to buy a Vend Item.
- **Change**: Collection of coins that is returned to the Client if the Inserted Money total value is greater than the selected Vend Item Pricce.
- **Available Change**: Collection of coins avaiable in the Vending Machine that is used as a source for the Change
- **Available Items**: Information realted to the stock of Vend Items available in the machine that can be sold.
- **Vend Item Selector**: Element used by a client to chose the Vend Item that wants to buy.
- **Service**: An actor that can change the Available Change and Available Items collection.
- **Client**: An actor that can buy try to Insert Coins, Buy a Vend Item or Get His Inserted Coins returned.

From the business request I have understand that the vending of a vend item can be done only if:

- The vend item exists. It's always good to check these kind of things to prevent hacking or possible errors.
- There's stock for the vend item.
- The coins inserted value is equal or greater than the vend item price.
- There's an available combination of coins to be able to return the change if needed. For some reason the test specifies that only 0.05, 0.10, 0.25 coins can be used to return change. 1.00 coins can't be used.

Other things that I had in account:

- Based on the business request the Vendigg Machine has always the same vend items and the same prices. They can't be changed. For a normal machine they should but it has not been requested.
- Not requested things must not be programmed but it's good to program things in the best way possible so they can be changed in the future if they are requested without overprogramming.

One feature not requested in the problem but I think that it's important to represent a real vending machine is the fact that Client and Service actors can't use the VendingMachine at the same time.  
That means I need to control the VendingMachine status to prevent possible errors.

Another important thing to have in account is the problem presented does not have in account that coins can change and that vend item prices could also change.  
It's something that in a real environment could happen but it's also a mistake to do overprogramming when not necessary. So we can take those values as static but having in mind that they could change in the future.

For checking the prototype functionality I will use symfony to create a small basic website with all the options available and some debug info.

---

# USE CASES

I can divide two different groups of use cases depending on the actor.

## Client / User actor

- Insert coin
- Return coins
- Select items

## Service actor

- Refill items
- Refill change

---

# SLICING AND AGGREGATION

I will use hexagonal architecture even it's a small project. I like to use the slicing arquitecture when developing in Hexagonal Arquitecture so I will build everything around the slice "VendingMachine".  
This slice will have a single Aggregation which Aggregte Root will be VendingMachine.
