<?php
require_once __DIR__.'/../vendor/autoload.php';

use App\Reports\Util\Container;
use App\Reports\Command\Cli;
ini_set('display_errors', 1);
error_reporting(-1);
$app = new Silex\Application();
Container::getAppContext($app);
$console = $app['console'];
$console->add(new Cli($app));
$console->run();
