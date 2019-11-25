<?php
/**
* Dependency Injection Container
*
* @category PHP
* @package  Util
* @author   Peroli Sivaprakasam <peroli.sivaprakasam@tnqsoftware.co.in>
* @license  TnQ Softwares (http://tnq.co.in)
* @link     link
*
*/

namespace App\Reports\Util;

use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\Console\Application;
use App\Reports\Service\ReportsService;
use App\Reports\Service\AuthenticationService;
use App\Reports\Service\S3Service;
use App\Reports\Config\Database;
use App\Reports\Repository\BaseRepository;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\FileLocator;
use Silex\Application as SA;
use Pimple\Container as Pimple;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Aws\Silex\AwsServiceProvider;
use Silex\Provider\SessionServiceProvider;
use App\Reports\Model\Customreport;
use App\Reports\Model\Employee;
use App\Reports\Model\Employeerelationship;
use App\Reports\Model\Notification;


/**
* Pimple Dependency Injection Container (Singleton)
*
* @category PHP
* @package  Util
* @author   Peroli Sivaprakasam <peroli.sivaprakasam@tnqsoftware.co.in>
* @license  TnQ Softwares (http://tnq.co.in)
* @link     link
*
*/

class Container
{
    private static $_instance;
    private $_app;
    private $app;

    /**
     * Private Constructor for this Singleton
     *
     * Sets up a single Pimple object and re uses it on subsequent requests
     *
     * @param array $options Any options needed by Pimple DI.
     */
    final private function __construct(array $options = [])
    {
        $this->_app = new Pimple();

        $this->_setup($options);


    }

    /**
     * Returns an already cached instance or creates a new one.
     *
     * @param array $options Any options needed by Pimple DI. Default empty.
     *
     * @return Pimple instance
     */
    public static function getAppContext(array $options=[])
    {
        if (!self::$_instance) {

            self::$_instance = new Container($options);
        }

        return self::$_instance->_app;
    }

    final private function __clone() {}

    /**
     * Create and register all injectable objects.
     *
     * @param Options $options Any options needed by Pimple DI.
     *
     * @return void
     */
    private function _setup(array $options)
    {
        $app = $this->_app;


        $app['debug'] = true;

        $app['console'] = function () {
            return new Application();
        };

        /*$app["entityManager"] = $app->share(function() {
            return Database::setupConnection();
        });*/

        $app['getRepository'] = $app->protect(function($repo) use ($app) {
            return $app['entityManager']->getRepository("App\\Reports\\Model\\$repo");
        });

        $app['getRepositoryName'] = $app->protect(function($repo){
            //echo 'here';exit;
            return 'App\\Reports\\Model\\'.$repo;
        });

        $app['customReport'] = $app->factory(function($repo){
            //echo $repo;exit;
            return new Customreport();
        });

        $app['employee'] = $app->factory(function($repo){
            //echo $repo;exit;
            return new Employee();
        });

        $app['employeerelation'] = $app->factory(function($repo){
            //echo $repo;exit;
            return new Employeerelationship();
        });

        $app['notification'] = $app->factory(function($repo){
            //echo $repo;exit;
            return new Notification();
        });

        $app['ReportService'] = function ($c) use ($app) {
            return new ReportsService($app);
        };

        $app['AuthenticationService'] = function ($c) use ($app) {
            return new AuthenticationService($app);
        };

        $app['S3Service'] = function ($c) use ($app) {
            return new S3Service($app);
        };

        $app['tableDefinitions'] = $app->protect(function($table) use ($app) {
            $tableDefinitions = Yaml::parse(__DIR__ . "/../Config/TableDefinition.yml");
            return $tableDefinitions[$table];
        });

        $app['config'] = $app->factory(function ($c) use ($app) {
            return Yaml::parse(__DIR__ . "/../Config/config.yml");
        });

        // $app['entityManager'] = function($c) use ($app) {
        //     $paths = [];
        //     $config = $app['config'];

        //     array_push($paths, sprintf('%s/%s', $config['app_ns_root'], 'Model'));
        //     $isDevMode = true;

        //     $configuration = Setup::createAnnotationMetadataConfiguration(
        //         $paths, $isDevMode, null, null, false
        //     );
        //     //print_r($config['rdbms']);exit;
        //     return EntityManager::create($config['rdbms'], $configuration);
        // };

        $app['error'] = $app->factory(function (\Exception $e, $code) {
            return new Response($e.'-'.$code);
        });

        $app->register(new TwigServiceProvider(), array(
            'twig.path' => __DIR__.'/../Views',
        ));

        $app['credentials'] = function ($c) use ($app) {
            return $app['config']['awsCredentials'];
        };
        
        
        
        $app['awsSdk'] = function ($c) use ($app) {
            $s3 = $app['config']['awsCredentials'];
            return new \Aws\Sdk($s3);
        };

        $app['cognito'] = function ($c) use ($app) {
            $config = $app['config']['awsCredentials'];
            $cognitoClient = $app['awsSdk']->createCognitoIdentityProvider();
            $client = new \pmill\AwsCognito\CognitoClient($cognitoClient);
            $client->setAppClientId($config['app_client_id']);
            $client->setAppClientSecret($config['app_client_secret']);
            $client->setRegion($config['region']);
            $client->setUserPoolId($config['user_pool_id']);
            
            return $client;
            //return new \Aws\Sdk($s3);
        };

        $s3 = $app['config']['awsCredentials'];
        $app['charset'] = 'UTF-8';
        $app->register(new AwsServiceProvider(), array(
                'aws.config' => $s3
        ));

        

        $app->register(new SessionServiceProvider());


    }
}

