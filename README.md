CdliTwoStageSignup
==================
Version 0.0.1 Created by the Centre for Distance Learning and Innovation (www.cdli.ca)

Introduction
------------

CdliTwoStageSignup is an extension to [ZfcUser](http://github.com/ZF-Commons/ZfcUser) which converts the new account registration process into a two-stage process where the prospective new user's email address is verified before they are permitted to create a new account:

* Step 1: Email Address Verification
* Step 2: Account Creation

Email Address Verification is performed by sending an email with a registration token to the email address provided by the user in Step 1.  When the user receives the email and clicks on the registration link, they will be directed to the ZfcUser account registration form to complete the registration process.  This ensures that the email address provided by the user is valid and under their control.

Installation
------------

### Main Setup

1. Install the [ZfcUser](https://github.com/ZF-Commons/ZfcUser) ZF2 module
   by cloning it into `./vendor/` and enabling it in your
   `application.config.php` file.
2. Clone this project into your `./vendor/` directory and enable it in your
   `application.config.php` file.
3. Import the SQL schema located in `./vendor/CdliTwoStageSignup/data/schema_up.mysql.sql`.
4. Copy `./vendor/CdliTwoStageSignup/config/module.cdlitwostagesignup.config.php.dist` to
   `./config/autoload/module.cdlitwostagesignup.config.php`.
5. Fill in the required configuration variable values in  `./config/autoload/module.cdlitwostagesignup.config.php` 


DISCLAIMER
----------

This code is considered proof-of-concept, and has not been vetted or tested for
inclusion in a production environment.  Use of this code in such environments is
at your own risk. 

Released under the New BSD license.  See file LICENSE included with the source 
code for this project for a copy of the licensing terms. 
