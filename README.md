# Subsession\Cache

PHP file caching system

## Instalation

Install using `composer`:

```
composer require subsession\cache
```

## Running unit tests

Unit tests are using PHPUnit ^7

> https://phpunit.de/getting-started/phpunit-7.html

### PHP Archive (PHAR)

```
./phpunit --bootstrap src/autoload.php tests/CacheTests
```

The above assumes that you have downloaded phpunit.phar and put it into your \$PATH as phpunit and that src/autoload.php is a script that sets up autoloading for the classes that are to be tested. Such a script is commonly generated using a tool such as phpab.

### Composer

```
./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/CacheTests
```

The example shown above assumes that composer is on your \$PATH.

### TestDox

Below you see an alternative output which is based on the idea that the name of a test can be used to document the behavior that is verified by the test:

```
./vendor/bin/phpunit --bootstrap vendor/autoload.php --testdox tests
```
