<?php

namespace Tests\Builders;

class SearchByReferenceNumber implements \Civil\ElevationInterface
{
    private $referenceNumber;

    public function __construct($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($builder)
    {
        $builder->where('reference_number', $this->referenceNumber);
    }
}
