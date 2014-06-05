cPanel API package for Laravel 4
======
originally forked from https://github.com/adelynx/cpanel


## Installation

Begin by installing this package through Composer. Edit your project's `composer.json` file to require `joselara/cpanel-laravel`.

    "require": {
		"laravel/framework": "4.0.*",
		"joselara/cpanel-laravel": "dev-master"
	}

Next, update Composer from the Terminal:

    composer update

Once this operation completes add the service provider, aliases and configuration file.

1 - Provider: Open `app/config/app.php`, and add a new item to the providers array.

    'JoseLara\Cpanel\CpanelServiceProvider'

2 - Provider: Open `app/config/app.php`, and add a new item to the facade array.

    'Cpanel' => 'JoseLara\Cpanel\Facades\Cpanel',

3 - Publish Config

    php artisan config:publish joselara/cpanel-laravel

That's it! You're all set to go.

## Usage

```php
<?php

class CpanelController extends Controller {

    public function getListAccounts()
    {
         try {

                $listaccts = array(json_decode(Cpanel::listaccts(), true));
                return $listaccts;

         } catch (Exception $e) {
                return 'Exception: ' .$e->getMessage();
         }

    }

}
```


