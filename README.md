## Evaluation test

The evaluation test consume data from an API, send the results to an exchange on a RabbitMQ instance where they are filtered, consume the filtered results from a queue and store these in a database.
    
## Prerequisites

* PHP 7.4.10
* Symfony 4.4.14 

## Setting Up

* You must set up the parameters regarding the results API (hostname), the message queue details of RabbitMQ as well as the database URL (DATABASE_URL) under .env 
* Run ```$ composer install``` to install the libraries 

## Run
* Open command line in the main directory of the project and write the following command to consume data from the API and send to the queue:
```
    $   php bin/console consume-data
```
* You can also run the same command giving as an argument a specific number for the messages you want to consume e.g.:
```
    $   php bin/console consume-data 5
```
* Run the following command to consume the messages from the rabbitMQ queue and store them to the database. The command is also waiting for new messages keeping the channel open:
```
    $   php bin/console store-to-db
```

## Database

In the database, a table with the name 'Messages' created. The columns of the table represent the values of each consumed message. An Entity was created with the name 'Message', the code can be found in src/Entity/Message.php. The table created using the following commands of Symfony and Doctrine.
```
    $   php bin/console make:entity
```
```
    $   php bin/console make:migration
```
```
    $   php bin/console doctrine:migrations:migrate
```
