<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Broadcast\Config\Database;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

return ConsoleRunner::createHelperSet(Database::setupConnection());
