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
		php composer.phar install	
### Step 3: Edit the following lines in app/config/app.php
	'url' => "domain.com"
### Step 4
	Setup your MySQL database, edit the details of said database in app/config/database.php
##You must use MySQL
### Step 5: Prep your new database for production.
	cd into your naughtyusers folder and run the following
	php artisan migrate
	php artisan db:seed
	php artisan addservers
### Step 6: Set your root password. 
	Create a new file in controllers called PasswordController.php
	Your file should look something like this
	<?php
		class PasswordController extends Controller {

			public function RootPassword(){
				return "MYPASSWORD";
			}
		}
	
### That's it, you're ready to start rocking and rolling.

### PHP Artisan commands to get stuff done.
> Shared server naughty user report

> normal = a2s

> ssd = a2ss

> iceland = ths

> icelandssd = thss

	php artisan shared normal
	php artisan shared ssd
	php artisan shared iceland
	php artisan shared icelandssd
> Reseller server naughty user report

> normal = sr servers

> ssd = ssr servers

> iceland = thsr

	php artisan reseller normal
	php artisan reseller ssd
	php artisan reseller iceland
	php artisan reseller statistics

### Useful functions inside of controllers/CpanelController.php
	ServerList() - Specify start and finish servers.
	DoLogin() - Process login as root via cPanel XML API
	SetServer() - Start server, end server, type is set via an artisan command (hint: look in commands/reseller or shared.)
	getListAccounts() - Gets a list of all accounts on a group of servers as specified via a php artisan command

> Alright, we get it. This is freaking awesome, is this for me?
	Most likely not, this was designed for A2Hosting Inc. With a little work and a couple beers, there's no reason why it wouldn't work. "It's just a bunch of cPanel API Calls"


