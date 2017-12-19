<?php

namespace Tests\Builders;

class Creator implements \Civil\ElevationInterface
{
    private $table;
    private $column;
    private $userId;

    public function __construct($userId, $table, $column)
    {
        $this->table = $table;
        $this->column = $column;
        $this->userId = $userId;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($builder)
    {
        # you could use whereHas() instead from the model's relation
        # for now we'll use leftJoin for testing

        $tableColumn = $this->table.'.'.$this->column;

        $builder
            ->leftJoin('users', $tableColumn, '=', 'users.id')
            ->where($tableColumn, $this->userId);
    }
}
