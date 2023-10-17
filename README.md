# INNODESK TICKET'S MANAGER
A helpdesk API to manage issues tickets reporting by our users

## Features
* Create tickets
* List tickets (filter by all, opened or closed)
* Update a ticket
* Close a ticket
* Reopen a ticket

## Requirements
* Node.js
* Composer
* PHP 8.1
* MySQL Server 8.0
* Laravel 10+

## Installation and configuration
* Clone this repository:  
`$ git clone https://github.com/pcfmello/innodesk.git`
* Install dependencies:  
`$ composer install && npm install`
* Request the environment file from the project manager and place it in the root folder of the project.
* Create the database according to the data in the environment file.
* Run the migrations:  
`$ npm run db:migrate`

## Usage
* Running the server locally:  
`$ php artisan serve`
* Creating a changelog:
`$ npm run release`
* API prefix is:
`http://localhost:8000/api`
* API documentation is available at:  
`http://localhost:8000/api/documentation`  
* Generating API documentation:  
`$ npm run generate:api-doc`

## Issues
Issues used to develop this API are available [here](https://github.com/pcfmello/innodesk/issues).  
[Create new issues](https://github.com/pcfmello/innodesk/issues/new) to report bugs and suggest new features.
