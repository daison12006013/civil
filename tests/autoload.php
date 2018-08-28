<?php

use Illuminate\Database\Capsule\Manager as Capsule;

require_once __DIR__ . '/../vendor/autoload.php';

$sqlLiteFile = __DIR__ . '/database.sqlite';

# refresh the sqlite file
@unlink($sqlLiteFile);
touch($sqlLiteFile);

$capsule = new Capsule;

$capsule->addConnection([
    'driver'   => 'sqlite',
    'database' => $sqlLiteFile,
    'prefix'   => '',
]);

# make this Capsule instance available globally via static methods
$capsule->setAsGlobal();

# setup the Eloquent ORM
$capsule->bootEloquent();

Capsule::connection()->unprepared(file_get_contents(__DIR__ . '/preload-statements.sql'));
