**Master Branch Status:**<br>
[![Build Status](https://travis-ci.org/daison12006013/civil.svg?branch=master)](https://travis-ci.org/daison12006013/civil)

# Civil Structural Builder for Laravel's Eloquent

`TL;DR;` This package helps you separate a query from a model in which you could re-use for other models.

## Installation

```
composer require daison/civil
```

## Test

To test this package, you may run `./vendor/bin/phpunit` and it should return something like this

```bash
PHPUnit 7.0-ga57366b by Sebastian Bergmann and contributors.

Runtime:       PHP 7.1.10-1+ubuntu16.04.1+deb.sury.org+1
Configuration: /home/vagrant/repositories/civil/phpunit.xml

......                                                              6 / 6 (100%)

Time: 332 ms, Memory: 6.00MB

OK (6 tests, 12 assertions)
```

## Basic

Assume that we have a class called `BookingQuery`, and below code is the simplest way to make a query class, by just providing the model class path.

```php
class BookingQuery extends \Civil\Query
{
    protected $model = App\Booking::class;
}
```

To use above class, please see below code.

```php
$query = BookingQuery::initialize();

dd($query->builder()->get()->toArray());
```

The above code will just dump all bookings from your database table.

```php
$builder = \App\Booking::where('status', 'paid');
$query = BookingQuery::initialize($builder);

dd($query->builder()->get()->toArray());
```

The above will do the same process as it dumps the data, but we're passing an existing builder inside the `initialize()` method.

## Advanced

Let's put a `referenceNumber()` method inside the query class.

```php
class BookingQuery extends \Civil\Query
{
    protected $model = App\Booking::class;

    public function referenceNumber($str)
    {
        // $this->builder or $this->builder(), both works
        $this->builder()->where('reference_number', $str);
    }
}
```

To use above, you mostly use it this way.

```php
$query = BookingQuery::initialize();
$query->referenceNumber(
    request()->get('reference_number')
);
dd($query->builder()->first());
```

The above code easily filters a reference number, but here comes the catch in our whole system, we will be having a `TicketQuery` which we hold all support tickets in our system, a ticket can have the same field which contains a `reference_number`, the query is the same thing as the `BookingQuery`.

## Expert

The problem in most applications that we have is the **RE-USABILITY** of each builders.

Let's create a builder class called `SearchByReferenceNumber` which implements `Civil\ElevationInterface`

```php
class SearchByReferenceNumber implements \Civil\ElevationInterface
{
    private $referenceNumber;

    public function __construct($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;
    }

    public function handle($builder)
    {
        $builder->where('reference_number', $this->referenceNumber);
    }
}
```

Go back to your `BookingQuery` and initialize the property `$registrar`

```php
class BookingQuery extends \Civil\Query
{
    protected $model = App\Booking::class;

    protected $registrar = [
        'referenceNumber' => SearchByReferenceNumber::class,
    ];
}
```

The above code, we register the class `SearchByReferenceNumber` as a method `referenceNumber` in the `BookingQuery`. You may run the same query as the previous query we had, which you will get the same result, check below.

```php
$query = BookingQuery::initialize();
$query->referenceNumber(
    request()->get('reference_number')
);
dd($query->builder()->first());
```

Then inside the `TicketQuery`, we could re-use the same builder `SearchByReferenceNumber` which they re-use the same functionality.

You may run a builder from the query too by just using the `call()` method instead of manual registrar.

```php
$refNum = request()->get('reference_number');
// some codes here ...
$query->call(
    new SearchByReferenceNumber($refNum)
);
```

To append a new builder, you may use `register($method, $class)`

```php
// some codes here ...
$query->register('referenceNumber', SearchByReferenceNumber::class);
```
