# C\Eloquent

[![Commitizen friendly](https://img.shields.io/badge/commitizen-friendly-brightgreen.svg)](http://commitizen.github.io/cz-cli/)

C\Eloquent module provides `Eloquent` database framework integration for `C` modules.

C is a lightweight framework dedicated to frontend development for php applications.

Based on top of silex, symfony, laravel, composer.

Please see more at
http://maboiteaspam.github.io/Foundation/index.html

## About Eloquent

Please get more information about `Eloquent` framework here

- http://laravel.com/docs/5.1/eloquent
- https://github.com/illuminate/database

## Install

Until the module is published,
add this repository to the `composer` file
then run `composer update`.
```
# composer.json
,
    {
      "type": "git",
      "url": "git@github.com:maboiteaspam/c-eloquent.git
    }

shell
# composer update
```

or run `c2-bin require-gh -m=...`

```
c2-bin require-gh -m=maboiteaspam/c-eloquent
```


## Registration

To register this module please proceed such,

```php

require 'vendor/autoload.php';

$app = new Application();

$config = [
    'env'=>'dev',
    'capsule.connections' => [
        "default"=>[
            'driver'    => 'sqlite',
            'database'  => __DIR__.'/../run/database.sqlite',
            'prefix'    => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ],
        "prod"=>[
            'driver'      => 'sqlite',
            'database'    => ':memory:',
            'prefix'      => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
        ],
        "mariadb"=>[
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'database'    => 'blog',
            'username'    => 'root',
            'password'    => '',
            'prefix'      => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
        ],
    ],
];
foreach ($config as $k=>$v) {
    $app[$k] = $v;
}


$eloquent = new Eloquent();
$app->register($eloquent);


$app->boot();

```

## Configuration

This module exposes those configuration values,

##### capsule.connections

`capsule.connections` to define multiple eloquent database connections.

##### capsule.use_connection

`capsule.connection` to define name of the connection to use.

Defaults to `env` if its value exists on `$app`,

Otherwise it defaults to `default`.

## Requirements

php-pdo and consorts.

## Contributing

For now on please follow `angular` contributing guide as it s very nice effort.

https://github.com/angular/angular.js/blob/master/CONTRIBUTING.md#-git-commit-guidelines

Read also about `symfony` recommendations
- http://symfony.com/doc/current/contributing/documentation/format.html
- http://symfony.com/doc/current/contributing/documentation/standards.html

Check also this wonderful software to realize the `git commit` command

- https://github.com/commitizen/cz-cli
- https://github.com/kentcdodds/validate-commit-msg

## Credits, notes, more

Have fun!
