<?php

namespace Tests\Queries;

use Tests\Models;
use Tests\Builders;

class TicketQuery extends \Civil\Query
{
    protected $model = Models\Ticket::class;

    protected $registrar = [
        'referenceNumber' => Builders\SearchByReferenceNumber::class,
    ];
}
