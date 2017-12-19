<?php

namespace Tests\Queries;

use Tests\Models;
use Tests\Builders;

class BookingQuery extends \Civil\Query
{
    protected $model = Models\Booking::class;

    protected $registrar = [
        'referenceNumber' => Builders\SearchByReferenceNumber::class,
    ];
}
