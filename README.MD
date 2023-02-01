# Profile-based multistring CSV objects handling library

## Purpose
There a lot of scenarions for CSV format used to export/import structured documents with more complex structure: some of fields are document-level and repeated each row and other fields are row-level. The most common example is an invoice: Customer ID and name, billing address and other header-level fields are repeating each row while other columns contains invoice rows data like sell item, quantity and price.

The idea behing the library is to provide easy and configurable gear to handle this kind of "structured CSV", based on "profile", describig CSV file structures and handling rules.

So, with this lib you need at least:
- Create CSV "profile" data structure to define processing rules
- Create collecton from file and got an array of class instances, representing the CSV encoded entities

In a simple case you need no extra code. For more complex scenarios you may extent Entity class with your features or add field processing functions
- Create a subclass of `Entity` to do extra handling on your 'documents'
- Create custome field classes for sophisticated validation and processing on export and import


[See details and docs in Wiki](https://github.com/pavlyuts/csv-objects/wiki)

## Installation
In the Composer storage. Just add proper require section:

    "require": {
        "pavlyuts/csv-objects": "^0.1"
    }
It is a good idea to fix at least major version you use. Don't use next version without review, I can't promose backward compatibility even will try to keep it. Please, review the [changelog](https://github.com/pavlyuts/csv-objects/blob/master/CHANGELOG.MD) before to change used version.

## Dependencies
- psr/log: ^1.1
- rmccue/requests: ^1.7