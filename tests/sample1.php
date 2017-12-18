<?php

include_once __DIR__.'/../vendor/autoload.php';

class BookingQuery extends \Civil\Query
{
    protected $model = App\Booking::class;

    protected $registrar = [
        'filterByReferenceNumber' => SearchByReferenceNumber::class,
        'filterByCreator'         => 'FilterByCreator',
    ];
}

class SearchByReferenceNumber implements \Civil\ElevationInterface
{
    private $referenceNumber;

    public function __construct($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;
    }

    /**
     * {@inheritdoc}s
     */
    public function handle($builder)
    {
        $builder->where('reference_number', $this->referenceNumber);
    }
}

$query = BookingQuery::initialize();

# to manually register
$query->register('myMethod', 'Namespace\\To\\My\\Class');

# To call builders
    # based from the registrar
    $query->referenceNumber('1234567890');
    # or direct call of the query class
    $query->call(new SearchByReferenceNumber('1234567890'));

# extract the builder's data
dd($query->builder()->get()->toArray());
