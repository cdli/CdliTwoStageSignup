CdliTwoStageSignup
==================
Version 0.6.1 Created by the Centre for Distance Learning and Innovation (www.cdli.ca)

[![Build Status](https://secure.travis-ci.org/cdli/CdliTwoStageSignup.png?branch=master)](http://travis-ci.org/cdli/CdliTwoStageSignup)

Introduction
------------

CdliTwoStageSignup is an extension to [ZfcUser](http://github.com/ZF-Commons/ZfcUser) which converts the new account registration process into a two-stage process where the prospective new user's email address is verified before they are permitted to create a new account:

* Step 1: Email Address Verification
* Step 2: Account Creation

Email Address Verification is performed by sending an email with a registration token to the email address provided by the user in Step 1.  When the user receives the email and clicks on the registration link, they will be directed to the ZfcUser account registration form to complete the registration process.  This ensures that the email address provided by the user is valid and under their control.

Installation
------------

### (1) Installation

Choose one of the two available installation methods:

#### Composer

1. Add the following line inside the require block of your composer.json file:
```
"cdli/CdliTwoStageSignup": "dev-master"
```

2. Run `php composer.phar update`

#### Git Submodule

1. Follow the [ZfcUser](https://github.com/ZF-Commons/ZfcUser) installation instructions to install that module and it's dependencies.

2. Clone this project into your `./vendor/` directory
```
cd vendor;
git clone git://github.com/cdli/CdliTwoStageSignup.git;
```

###  (2) Configuration

1. Ensure that this module and it's dependencies are enabled in your `application.config.php` file in the following order:
    * ZfcBase
    * ZfcUser
    * CdliTwoStageSignup
3. Import the SQL schema located in `./vendor/CdliTwoStageSignup/data/schema_up.mysql.sql`.
4. Copy `./vendor/CdliTwoStageSignup/config/cdlitwostagesignup.global.php.dist` to
   `./config/autoload/cdlitwostagesignup.global.php`.
5. Fill in the required configuration variable values in  `./config/autoload/cdlitwostagesignup.global.php` 


DISCLAIMER
----------

This code is considered proof-of-concept, and has not been vetted or tested for
inclusion in a production environment.  Use of this code in such environments is
at your own risk. 

Released under the New BSD license.  See file LICENSE included with the source 
code for this project for a copy of the licensing terms. 
