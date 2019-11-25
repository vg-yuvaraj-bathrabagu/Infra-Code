<?php
namespace App\Reports\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;


class Cli extends Command
{
    private $app;

    public function __construct($app){
        parent::__construct();
        $this->app = $app;
    }

    protected function configure()
    {
        $this
            ->setName('Broadcast')
            ->setDescription('Broadcast message using RabbitMQ')
            ->addArgument(
                'controller',
                InputArgument::REQUIRED,
                'Controller?'
            )
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                'Action?'
            )
            ->addArgument(
                'message',
                InputArgument::OPTIONAL,
                'message?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $controller = $input->getArgument('controller');
        $action = $input->getArgument('action');
        $message = $input->getArgument('message');
        if ($controller && $action) {
            $this->app[$controller . '.controller'] = $this->app->share(function($app) use ($controller) {
                $app['controller_obj'] = 'App\\Reports\\Service\\'.$controller;
                return new $app['controller_obj']($app);
        });
            $response = $this->app[$controller . '.controller']->{$action}($message);
        } else {
            $response = 'Not Found';
        }
        $output->writeln($response);
    }

}
