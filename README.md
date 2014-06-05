naughtyusers
============

###cPanel - Laravel integration for spotting users with a very high disk usage as well as breaking down reseller statistics.

[Laravel Bootstrapped starter site Forked from andrewelkins] (https://github.com/andrewelkins/Laravel-4-Bootstrap-Starter-Site)

[Custom cPanel Laravel 4.x Integration comes from me](https://github.com/patrick-hudson/cpanel-laravel-api) [originally forked from adelynx] (https://github.com/adelynx/cpanel)

#Table of Contents
[Features](#features)

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



##How the hell do I use this thing?

Well that's a good question.

##Requirements
	PHP >= 5.4.0
	MCrypt PHP Extension
	MySQL is REQUIRED for 
	PHP Timeout must be quite lone. I have mine set to 12 hours.
	Must be able to connect outbound to port 2087
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

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => 'badusers',
			'username'  => 'badusers',
			'password'  => 'randompassword',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),
		
### Step 5: Prep your new database for production.
	cd into your naughtyusers folder and run the following
	php artisan migrate
	php artisan db:seed
	php artisan addservers
### Step 6: Set your root password. 
> Create a new file in app/controllers called PasswordController.php

> Your file should look something like this
	
	<?php
		class PasswordController extends Controller {

			public function RootPassword(){
				return "MYPASSWORD";
			}
		}
	
### That's it, you're ready to start rocking and rolling.

### PHP Artisan commands to get stuff done.
> Shared server naughty user report. Stores server, username, diskspace used in GB, account owner, owners allowed disk space, and report ran at date.

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

> statistics = get every reseller account, return the total accounts, and the total size of the reseller account

	php artisan reseller normal
	php artisan reseller ssd
	php artisan reseller iceland
	php artisan reseller statistics

### Useful functions inside of controllers/CpanelController.php
	ServerList() - Specify start and finish servers.
	DoLogin() - Process login as root via cPanel XML API
	SetServer() - Start server, end server, type is set via an artisan command (hint: look in commands/reseller or shared.)
	getListAccounts() - Gets a list of all accounts on a group of servers as specified via a php artisan command
	GetResellerStatistics() - Gets the reseller stats
	AddServers() - Add server based on the array defined at the top of the page.

##Web Interface

	Default login
	Username: support
	Password: support
		
> Note: You must actually run a server report before you can use anything in the web interface.

#### Dashboard
	Pretty self explanitory. You can search by username, diskspace, by server, by report date, after certain date, reseller stats, and multi search. (Hint: Multisearch needs some work)
	
#### Functions 
	Shows in real time the servers response time and the total bad users on that server. (jQuery can be a bit wonky)
	
	You can also clear the list at any time should it become too large to handle.
	
#### Admin Panel
	Not a whole lot in here, you can add/remove users if you wish.

> Alright, we get it. This is freaking awesome, is this for me?
	Most likely not, this was designed for A2Hosting Inc. With a little work and a couple beers, there's no reason why it wouldn't work. "It's just a bunch of cPanel API Calls". Refer to the legal mish-mash below for more information.

### Legal Mish-Mash
> Everything published in this repository is open source. Before using this software, you must agree to the license of each component. This includes but is not limited to cPanel, Laravel, Twitter Bootstrap. The usage of this repository as a whole is licensed under GPLv3. Further inquiries about such license or the product itself should be directed towards me phudson2@gmail.com. Inquiries about software used in the development of this product should be directed at the appropriate companies. Naughty Users is developed by Patrick Hudson, while working for A2Hosting Inc, but not FOR A2Hosting. Naughty users is freely available to be used/modified and distributed as long as it meets GPLv3.
	
	A GPLv3 License is provided within this repository. All copyrights from the above mentioned companies are included in the affected files.
