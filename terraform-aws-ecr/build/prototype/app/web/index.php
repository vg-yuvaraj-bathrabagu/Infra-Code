<?php

/**
 * Front Controller
 *
 * @category Controller
 * @package  None
 * @author   Yuvaraj Bathrabagu <yuvaraj.bathrabagu@tnqmail.com>
 * @license  TnQ Softwares (http://tnq.co.in)
 * @link     link
 *
 */
if (version_compare(PHP_VERSION, '5.4.0') < 0) {
    die('Require PHP version greater than or equal to 5.4.0');
}

require_once __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

use App\Reports\Traits\Context;
use App\Reports\Traits\Utils;
use Symfony\Component\Yaml\Yaml;

class Reports {

    use Context, Utils;

    private $app = null;
    private $context = null;

    public function __construct() {
        $this->app = new Silex\Application();
        $this->context = self::getContext();
        $this->setupRoutes();
    }

    public function setupRoutes() {
        $routes = Yaml::parse(__DIR__ . "/../OnCloudTime/Config/routes.yml");
        foreach ($routes as $route) {
            $this->app->match($route['pattern'], function(Request $request) use ($route) {
                $method = $route['defaults']['_method'];
                $response = $this->context[$route['defaults']['_controller']]->$method($request, $this->app);
                if (isset($route['defaults']['_view'])){
                    //var_dump($response);exit;
                    //$response['pattern'] = str_replace('/', '', $route['pattern']);
                    //echo 'here';exit;
                    /*if (isset($route['defaults']['_renderView'])) {
                        return $this->renderView($response,
                        $route['defaults']['_view']);
                    }*/
                    return $this->render($response,
                        $route['defaults']['_view']);
                }
                else
                    return $response;
            })->method(isset($route['method']) ? $route['method'] : 'GET');
        }
    }

    public function run() {
        $this->app['debug'] = true;
        $this->app->boot();
        $this->app->run();
    }

}

$reports = new Reports();
$reports->run();
