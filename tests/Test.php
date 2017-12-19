<?php

namespace Tests;

class Test extends \PHPUnit\Framework\TestCase
{
    public function testSimpleQuery()
    {
        $query = Queries\BookingQuery::initialize();

        $this->assertTrue(
            count($query->builder()->get()->toArray()) > 1
        );

        $query = Queries\TicketQuery::initialize();

        $this->assertTrue(
            $query->builder()->count() === 2
        );
    }

    public function testInitialize()
    {
        $query = Queries\BookingQuery::initialize(
            Models\Booking::where('reference_number', $refNum = 'BXYZRGYER10')
        );

        $this->assertTrue($query->builder()->first() ? true : false);
        $this->assertEquals($query->builder()->first()->reference_number, $refNum);
    }

    public function testReferenceNumberInBooking()
    {
        $query = Queries\BookingQuery::initialize();

        # To call builders
        # based from the registrar
        $query->referenceNumber($refNum = 'B1234567890');

        # there must be a record
        $this->assertTrue($query->builder()->first() ? true : false);
        $this->assertEquals($query->builder()->first()->reference_number, $refNum);
    }

    public function testReferenceNumberInTicket()
    {
        $query = Queries\TicketQuery::initialize();

        # To call builders
        # based from the registrar
        $query->referenceNumber($refNum = 'TXYZRGYER10');

        # there must be a record
        $this->assertTrue($query->builder()->first() ? true : false);
        $this->assertEquals($query->builder()->first()->reference_number, $refNum);
    }

    public function testRegister()
    {
        $query = Queries\TicketQuery::initialize();

        # to manually register
        $query->register('createdBy', Builders\Creator::class);

        $query->createdBy($userId = 2, 'tickets', 'reported_by_id');

        # there must be a record
        $this->assertTrue($query->builder()->first() ? true : false);
        $this->assertEquals($query->builder()->first()->reported_by_id, $userId);
    }

    public function testCall()
    {
        $query = Queries\BookingQuery::initialize();

        # or direct call of the query class
        $query->call(new Builders\Creator($userId = 2, 'bookings', 'booked_by_id'));

        # there must be a record
        $this->assertTrue($query->builder()->first() ? true : false);
        $this->assertEquals($query->builder()->first()->booked_by_id, $userId);
    }
}
