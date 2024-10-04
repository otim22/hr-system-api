# HR Management System

> ### This is a Laravel application codebase containing authentication, staff registration, retrieval, and updated following the standard development practices of APIs.

This repo is functionality complete — PRs and issues welcome!

----------

# Getting started

## Installation

Please check the official laravel installation guide for server requirements before you start. [Official documentation](https://laravel.com/docs/11.x)


Clone the repository

    git clone git@github.com:otim22/hr-system-api.git

Switch to the repo folder

    cd hr-system-api

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000

**TL;DR command list**

    git clone clone git@github.com:otim22/hr-system-api.git
    
    cd hr-system-api
    
    composer install
    
    cp .env.example .env
    
    php artisan key:generate
    
**Make sure you set the correct database connection information before running the migrations** [Environment variables](#environment-variables)

    php artisan migrate
    
    php artisan serve

<!-- ***Note*** : It's recommended to have a clean database before seeding. You can refresh your migrations at any point to clean the database by running the following command

    php artisan migrate:refresh -->
    
The api can be accessed at [http://localhost:8000/api](http://localhost:8000).

----------

# Code overview

<!-- ## Dependencies

- [jwt-auth](https://github.com/tymondesigns/jwt-auth) - For authentication using JSON Web Tokens
- [lumen-generator](https://github.com/flipboxstudio/lumen-generator) - To add any Laravel code generator on your Lumen project
- [redis](https://github.com/illuminate/redis) - To handle any application caching 
- [inspector-laravel](https://github.com/inspector-apm/inspector-laravel) - To connect your Lumen application to Inspector. -->

## Folders

- `app/Models` - Contains all the Eloquent models
- `app/Http/Controllers` - Contains all the api controllers
- `app/Http/Middleware` - Contains the JWT auth middleware
- `config` - Contains all the application configuration files
- `database/factories` - Contains the model factory for all the models
- `database/migrations` - Contains all the database migrations
- `database/seeds` - Contains the database seeder
- `routes` - Contains all the api routes defined in api.php file
- `storage` - Contains all the api storage, logging details
- `tests` - Contains all the application tests

## Environment variables

- `.env` - Environment variables can be set in this file

***Note*** : You can quickly set the database information and other variables in this file and have the application fully working.

----------

# Testing API

Run the laravel development server

    php artisan serve

The api can now be accessed at

    http://localhost:8000

Request headers

| **Required** 	| **Key**              	| **Value**            	|
|----------	|------------------	|------------------	|
| Yes      	| Content-Type     	| application/json 	|
| Yes      	| X-Requested-With 	| XMLHttpRequest   	|
| Yes 	    | Authorization    	| Token {JWT}      	|


Find an invite to Postman below and test the endpoints

    https://app.getpostman.com/join-team?invite_code=9f59467bbf22d370bf0b010a4b66fdb3&target_code=bcacafe6133e03ca7339a31e70d18a92

----------
 
# Authentication
 
This applications uses JSON Web Token (JWT) to handle authentication. The token is passed with each request using the `Authorization` header with `Token` scheme.

----------
 
# Deployment
 
Make you install deployer on your local machine. By running the following 
    composer require --dev deployer/deployer
 
To initialize deployer in your project run:
    vendor/bin/dep init

Add next alias to your .bashrc file:
    alias dep='vendor/bin/dep'

Now lets cd into the project and run the following command:
    dep init

Check a sample of deploy script in the root project called "deploy.sample.php"

To deploy the project:
    dep deploy

Ssh to the host, for example, for editing .env file:
    dep ssh

**TL;DR command list**  

    composer require --dev deployer/deployer
    
    vendor/bin/dep init
    
    alias dep='vendor/bin/dep'
    
    dep init
    
    dep deploy

Please find the deployer documentation below here 
- https://deployer.org/docs/7.x/getting-started


----------

# Done!!

Yeah! We are finally done, thank you.