<?php

namespace Tests;

use Illuminate\Pagination\LengthAwarePaginator;

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
        $this->assertTrue($query->first() ? true : false);

        # user id must be equal the one from the record
        $this->assertEquals($query->first()->reported_by_id, $userId);

        # calling the builder or directing calling non-existing method from the civil
        # should resolve the method from builder
        $this->assertEquals($query->builder()->first(), $query->first());

        # assert paginate works in the query.
        $this->assertInstanceOf(LengthAwarePaginator::class, $query->paginate(10));

        # should contain a select
        $this->assertRegExp('/select \* from \((.*)\) as (.*)/i', $query->rebuild()->toSql());
    }

    public function testMakeBuilderInstance()
    {
        $query = Queries\BookingQuery::initialize();

        # or direct makeBuilderInstance of the query class
        $query->makeBuilderInstance(new Builders\Creator($userId = 2, 'bookings', 'booked_by_id'));

        $this->assertTrue($query->first() ? true : false);
        $this->assertEquals($query->first()->booked_by_id, $userId);
        $this->assertEquals($query->builder()->first(), $query->first());
        $this->assertInstanceOf(LengthAwarePaginator::class, $query->paginate(10));
        $this->assertRegExp('/select \* from \((.*)\) as (.*)/i', $query->rebuild()->toSql());
    }
}
