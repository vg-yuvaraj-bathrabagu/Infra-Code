<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Reports\Config\Database;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

return ConsoleRunner::createHelperSet(Database::setupConnection());
