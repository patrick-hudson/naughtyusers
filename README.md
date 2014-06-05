naughtyusers
============

###cPanel - Laravel integration for spotting users with a very high disk usage as well as breaking down reseller statistics.

[Laravel Bootstrapped starter site Forked from andrewelkins] (https://github.com/andrewelkins/Laravel-4-Bootstrap-Starter-Site)

[Custom cPanel Laravel 4.x Integration comes from me](https://github.com/patrick-hudson/cpanel-laravel-api) [originally forked from adelynx] (https://github.com/adelynx/cpanel)

## Features

* Bootstrap 3.x
* Custom Error Pages
	* 403 for forbidden page accesses
	* 404 for not found pages
	* 500 for internal server errors
* Confide for Authentication and Authorization
* Back-end
	* User and Role management
	* Manage blog posts and comments
    * Colorbox Lightbox jQuery modal popup.
* Front-end
	* User login, registration, forgot password
	* User account area
* Packages included:
	* [Confide](https://github.com/zizaco/confide)
	* [Entrust](https://github.com/zizaco/entrust)
	* [Ardent](https://github.com/laravelbook/ardent)
	* [Generators](https://github.com/JeffreyWay/Laravel-4-Generators/blob/master/readme.md)



How the hell do I use this thing?

Well that's a good question.
##Requirements

	PHP >= 5.4.0
	MCrypt PHP Extension
	MySQL is REQUIRED for 
	PHP Timeout must be quite lone. I have mine set to 12 hours.
	Must be able to connect outbound to port 2097
### Step 1: Get the code
	git clone https://github.com/patrick-hudson/naughtyusers.git
### Step 2: Use Composer to install dependencies
		cd naughtyusers
			curl -s http://getcomposer.org/installer | php
			php composer.phar install --dev		
