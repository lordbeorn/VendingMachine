# REQUIREMENTS AND EXECUTING THE PROTOTYPE

In order to execute the prototype you need to have docker and docker-compose installed.

Inside the script folder you will able to find several scripts both for windows and linux. For Linux, remember to mark all .sh files as executable.

- **build-and-run**: Build or rebuild the docker image file and runs it.
- **run**: Run the docker image. You need to have built it previously.
- **shell**: Allows you to enter inside the shell of the docker image. You need to have it running.
- **stop**: Shuts down the docume image.

Once the machine is running you can access to the small exampple app through http://localhost:3333

---

# VENDING MACHINE PROGRAMMING DECISION AND ANALYSYS

I want to create this protoype based on DDD and Hexagonal architecture. The first step is extracting all the information to create the ubiquitous language for the application.  
Based on this I have extracted the following terms and requirements.

- **Vending Machine**: It's the main element that we want to create and the main entity that will be used for the interaction.
- **Coin**: Element that adds a value that can be used to buy vend items. Its possible values are 0.05, 0.10, 0.25 and 1.00
- **Vend Item**: Element that can be bought from the Vending Machine. There will be always three different vend items that can't change: Water, Juice and Soda
- **Vend Item Price**: Amount that is related to a particular Vend Item and stablishes its inmutable price: Water = 0.65, Juice = 1.00, Soda = 1.50
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

Another decision I've made, based on some errors related to PHP management of float numbers, is I will use integers to manage the coin values. To do that I will work always with cents instead of floats with two decimal values.

One last nore when reviwing the code. IN DDD commented code should not be used except in very complex parts of the code. The usage of the ubiquitous language and the way of programming should be the basis for understanding the code.

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


# AGGREGATOR ROOT

**VendingMachine**:

    - It centralized all the logic of the different actions.
    - It also ensures de requested logical for selling vend items is followed.
    - It's the only entry point to the aggregate.

# ENTITIES

**VendItem**:
    - Represents one of the products sold by the machine.
    - It only needs to know its identity based on the product selector items. 
    - A VendItem it's not related to stock because it's not related to its own identity.
    - A VendItem could be related to price but I think it's not a real property of the VendItem entity and it's more related to the Vending Machine and its selector.


# VALUE OBJECTS

**Coin**: 
    - It represents the differente coin values: 5, 10, 25, 100. These values are immutable. I have decided to store them as cents because sometimes PHP has problems with float functions and improves the st ability if I use ints.

**CoinCollection**: 
    - It represents in the business language a group of coins. It's not an entity because it does not have a real identity by itself. 
    - I've created it as an array although it could make the performance lower (n2) when chacking the change. It could be improved storing each coin in a different array but that would add complexity.
    - I don't think a machine will have thousands of coins inside so the performance should not change so much in exchange of adding more complexity to the code.

**VendItemSelector**:
    - I have created this Value Object to represent the business language of selecting an item. 
    - I have decided to store in this value object the price of a Vend Item because I think this value object it's that related the vending machine selling action with the vend item product.
    - Probably it's one of the VO that could be easily removed or changed in the future if new functionalities are requested.

**AvailableVendItems**:
    - I had doubts about converting this V.O. in an entity "Stock" because it's mutable and have some kind of identity.
    - Finally I opted for a Value Object because a stock does not have a real meaning by itself. All the stocks are the same and it does not matter the products or the quantities they have.

**Mode**:
    - It represents the current vending machine mode in a "state" machine.
    - Although this one does not appear directly in the business request I think this Value Object would appear when the dialog between the Tech and the Business is developed. 
    - Tech could explain these kind of restrictions should need to be checked to prevent problems. The dialog in DDD must be bidirectional.


# SERVICES

**ChangeCalculator**
    - It calculates that there are enough coins in the machine to return the change and returns the collection of coins that should be returned.
    - Impotant! It does not remove the coins. Only does the calculation.



# POSSIBLE IMPROVEMENTS

    - Having in account a real vending machine probably I could also think in other use cases like "Replace vend items" or "Change vend item prices" that could manage other events not requested in the test but are important in a real life environment.
    - It could be important to manage some controls in the VendingMachine for future implementations that controls the machine status:
        * It could be good to control the Service actor does not let the machine "open" by mistake hanging the machine in "Service" mode.
        * It would be good also to check the "Client Mode" does not last for too long because a Client inserted a coin and did not do anythig with it. Could be good to eject the coins as an example.
    - I had to add read functions in the vending machine entity for displaying all the info in the twig. They should have to be removed and implement another way to get the info if needed.
    - Probably thge VendingMachine Entity could be divided using behaviours to separate client and service functions and make it smaller and more mantainable.
    - Could be good to do a coverage map to see if there are missing tests.
