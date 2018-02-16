# Laravel Firebase Sync
## Synchronize your Eloquent models with the [Firebase Realtime Database](https://firebase.google.com/docs/database/)

![image](http://img.shields.io/packagist/v/mpociot/laravel-firebase-sync.svg?style=flat)
![image](http://img.shields.io/packagist/l/mpociot/laravel-firebase-sync.svg?style=flat)
[![codecov.io](https://codecov.io/github/mpociot/laravel-firebase-sync/coverage.svg?branch=master)](https://codecov.io/github/mpociot/laravel-firebase-sync?branch=master)
[![Build Status](https://travis-ci.org/mpociot/laravel-firebase-sync.svg?branch=master)](https://travis-ci.org/mpociot/laravel-firebase-sync)

## Contents

- [Installation](#installation)
- [Usage](#usage)
- [License](#license)

<a name="installation" />

## Installation

In order to add Laravel Firebase Sync to your project, just add

    "mpociot/laravel-firebase-sync": "~1.0"

to your composer.json. Then run `composer install` or `composer update`.

Or run `composer require mpociot/laravel-firebase-sync ` if you prefer that.


<a name="usage" />

## Usage

### Configuration

This package requires you to add the following section to your `config/services.php` file:

```php
'firebase' => [
        'api_key' => env('FIREBASE_API_KEY',''), // Only used for JS integration
        'auth_domain' => env('FIREBASE_AUTH_DOMAIN',''), // Only used for JS integration
        'database_url' => env('FIREBASE_DB_URL',''),
        'secret' => env('FIREBASE_DATABASE_SECRET',''),
        'storage_bucket' => env('FIREBASE_STORAGE_BUCKET',''), // Only used for JS integration
    ]
```

add the fillowing keys to your `.env` file, and set it with your own configuration.
```
FIREBASE_API_KEY=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx #normally begins with AIza...
FIREBASE_AUTH_DOMAIN=foo-bar-baz.firebaseapp.com
FIREBASE_DB_URL='https://foo-bar-baz.firebaseio.com'
FIREBASE_STORAGE_BUCKET=foo-bar-baz.appspot.com
FIREBASE_DATABASE_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```
**Note**: This package only requires the configuration keys `database_url` and `secret`. The other keys are only necessary if you want to also use the firebase JS API. 


### Synchronizing models

To synchronize your Eloquent models with the Firebase realtime database, simply let the models that you want to synchronize with Firebase use the `Mpociot\Firebase\SyncsWithFirebase` trait.

```php
use Mpociot\Firebase\SyncsWithFirebase;

class User extends Model {

    use SyncsWithFirebase;

}
```

The data that will be synchronized is the array representation of your model. That means that you can modify the data using the existing Eloquent model attributes like `visible`, `hidden` or `appends`.

If you need more control over the data that gets synchronized with Firebase, you can override the `getFirebaseSyncData` of the `SyncsWithFirebase` trait and let it return the array data you want to send to Firebase.

#### Manual synchronization
You can force a model to sync with Firebase via the `syncWithFirebase` method:
```php
$u = \App\User::find(1);
$u->syncWithFirebase();
```

The trait also extends the default Collection allowing bulk synchronizations:
```php
// Please note that a bulk action like this should be avoided on large datasets, it's just an example
\App\User::all()->syncWithFirebase();
```

<a name="license" />

## License

Laravel Firebase Sync is free software distributed under the terms of the MIT license.
