<?php

namespace App\Reports\Service;

use App\Reports\Model\MessageLog;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Reports\Traits\Context;
use App\Reports\Traits\Utils;
use Symfony\Component\Yaml\Yaml;

class ReportsService {

    use Context;
    use Utils;

    private $context;

    public function __construct() {
        $this->context = self::getContext();
        $this->progress = [];
        $this->uploadProgressArray = [];
        $this->fileToCopy = null;
        $this->fileFullPath = null;
    }

    public function index() {
        return [];
    }

    public function loadDashboard() {
        return [];
    }

    public function loadIndex($request, $app) {
        $this->app = $app;
        $needUpdate = $this->context['config']['need_update'];
        if ($needUpdate === 'yes') {
            return $app->redirect('install');
        }

        return $app->redirect('index');
    }

    public function loadConfig($request, $app) {
        $data['config'] = $this->context['config'];

        return $data;
    }

    public function editUser($request, $app) {
        $id = $request->query->get('id');
        $crRepo = $this->context['getRepository']
            ('Employee');
        $result = $crRepo->findOneBy(['id' => $id]);
        $data['user'] = $result;

        return $data;
    }



    public function saveUser($request, $app) {
        $crRepo = $this->context['getRepository']('Employee');
        $crData = $this->context['employee'];
        $crData->setId($request->request->get('id') ? $request->request->get('id') : NULL);
        $crData->setSalutation($request->request->get('salutation'));
        $crData->setUsername($request->request->get('username'));
        $crData->setPassword(SHA1($request->request->get('password')));
        $crData->setFirstname($request->request->get('firstname'));
        $crData->setLastname($request->request->get('lastname'));
        $crData->setEmailaddress($request->request->get('email'));
        $crData->setDatecreated(date('Y-m-d h:i:s'));

        return $this->response($crRepo->save($crData));
    }

    public function savePermission($request, $app) {
        $crRepo = $this->context['getRepository']('Employeerelationship');
        $crData = $this->context['employeerelation'];
        $crData->setId($request->request->get('id') ? $request->request->get('id') : NULL);
        $data = $request->request->all();
        foreach($data as $column => $value) {
            $crData->setProperty($column, $value);
        }

        return $this->response($crRepo->save($crData));
    }

    public function editPermission($request, $app) {
        $id = $request->query->get('id');
        $crRepo = $this->context['getRepository']
            ('Employeerelationship');
        $data['employees'] = $this->context['getRepository']
            ('Employee')->getAllUsers();
        $result = $crRepo->findOneBy(['id' => $id]);
        $data['user'] = $result;

        return $data;
    }

    public function updateConfig($request, $app) {
        $config = [];
        $oldConfig = $this->context['config'];
        $config['need_update'] = 'no';
        $config['rdbms']['driver'] = 'pdo_mysql';
        $config['rdbms']['host'] = $request->request->get('db_host');
        $config['rdbms']['user'] = $request->request->get('db_user');
        $config['rdbms']['password'] = $request->request->get('db_password');
        $config['rdbms']['dbname'] = $request->request->get('db_dbname');
        $config['S3']['bucket'] = $request->request->get('s3_bucket');
        $config['S3']['region'] = $oldConfig['S3']['region'];
        $config['S3']['url'] = $oldConfig['S3']['url'];
        $config['S3']['version'] = $oldConfig['S3']['version'];
        $config['S3']['credentials']['key'] = $request->request->get('s3_key');
        $config['S3']['credentials']['secret'] = $request->request->get('s3_secret');
        $config['athena']['directory'] = $request->request->get('athena_dir');
        $config['athena']['input'] = sprintf('s3://%s/', $request->request->get('s3_bucket'));
        $config['athena']['output'] = sprintf('s3://%s/athena-output', $request->request->get('s3_bucket'));
        $config['athena']['database'] = $request->request->get('athena_db');
        $config['sqs']['notificationQueue'] = $request->request->get('sqs_queue');
        $config['shell']['emrCreateFile'] = $oldConfig['shell']['emrCreateFile'];
        $config['shell']['emrStartFile'] = $oldConfig['shell']['emrStartFile'];
        $config['shell']['emrStopFile'] = $oldConfig['shell']['emrStopFile'];
        $config['shell']['orcConversion'] = $oldConfig['shell']['orcConversion'];
        $config['shell']['parquetConversion'] = $oldConfig['shell']['parquetConversion'];
        $config['shell']['runQueryShell'] = $oldConfig['shell']['runQueryShell'];
        $newConfig = Yaml::dump($config);
        $configFileDir = __DIR__.'/../Config/';
        $configFile = 'config.yml';
        $configBak = 'config.yml.bak';
        copy($configFileDir.$configFile, $configFileDir.$configBak);
        file_put_contents($configFileDir.$configFile, $newConfig);
        $this->createBucket();
        //$this->createAthenaDir();
        //$this->createAthenaDB();

        return $app->redirect('index');
    }

    public function createBucket() {
        $s3 = $this->context['aws']->createS3();
        $bucket = $this->context['config']['S3']['bucket'];
        $isBucketExists = $s3->doesBucketExist($bucket);
        $result = [];
        if (!$isBucketExists) {
            $result = $s3->createBucket([
                'Bucket' => $bucket, // REQUIRED
            ]);
        }

        return $result;
    }

    public function createAthenaDir() {
        $s3 = $this->context['aws']->createS3();
        $bucket = $this->context['config']['S3']['bucket'];
        $athenaDir = $this->context['config']['athena']['directory'];
        $bills = sprintf('%sbills/', $athenaDir);
        $invoices = sprintf('%sinvoices/', $athenaDir);
        $result = $s3->putObject([
            'Bucket' => $bucket, // REQUIRED
            'Key'    => $athenaDir
        ]);
        $result = $s3->putObject([
            'Bucket' => $bucket, // REQUIRED
            'Key'    => $bills
        ]);
        $result = $s3->putObject([
            'Bucket' => $bucket, // REQUIRED
            'Key'    => $invoices
        ]);

        return $result;
    }

    public function createAthenaDB() {
        $config = $this->context['config']['athena'];
        $query = "CREATE DATABASE IF NOT EXISTS %s;";
        $query = sprintf($query, $config['database']);
        $result = $this->processQuery($query);
        $data = $result->get('ResultSet');

        return $data;
    }

    public function validateLogin($request, $app) {
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        $data = $this->context['getRepository']
            ('Employee')->getLogin($username, SHA1($password));
        if (count($data) > 0){
            $this->context['session']->set('user', array('username' => $username));
            return $app->redirect('startemr');
        }

        return $app->redirect('index');
    }

    public function logOut($request, $app) {
        $this->context['session']->clear();

        return $app->redirect('index');
    }

    public function showReports() {
        $data['customReports'] = $this->context['getRepository']
            ('Emrstatus')->getAllReports();

        return $data;
    }

    public function showUploadForm() {
        return [];
    }

    public function loadViewSQS() {
        return [];
    }
    
    public function processS3Upload($request) {
        $files = $request->files->get('file');
        $uploadDir = __DIR__.'/../data/';
        try {
            $files->move($uploadDir, $files->getClientOriginalName());
        } catch (\FileException $e) {
            return $this->errorResponse(['message' => 'Upload failed']);
        }
        $bucket = $this->context['config']['S3']['bucket'];
        $encryption = $this->context['config']['S3']['sse'];
        $s3 = $this->context['aws']->createS3();
        $queue = $this->context['config']['sqs']['notificationQueue'];
        $sqs = $this->context['aws']->createSqs();
        $progress = [];
        $user = $this->context['session']->get('user');
        $user = $user['username'];
        $promise = $s3->putObjectAsync([
            'Bucket' => $bucket,
            'Key'    => $files->getClientOriginalName(),
            'SourceFile'   => $uploadDir.'/'.$files->getClientOriginalName(),
            'ACL'    => 'public-read',
            'ServerSideEncryption' => $encryption,
            '@http' => [
            'progress' => function ($downloadTotalSize, $downloadSizeSoFar, $uploadTotalSize, $uploadSizeSoFar) use ($queue, $sqs, $progress) {
                    if($uploadTotalSize !=0){
                        array_push($progress,
                                ['percent' => $this->getPercentage(
                                    $uploadSizeSoFar, $uploadTotalSize)
                                ]);
                            //$this->pushProgressQueue(json_encode($this->progress));
                    }
                }
            ]
        ]);

        $result = $promise->wait();

        $data = ["s3Url" => $result['ObjectURL'],
                "progress" => $this->progress,
                "file" => $files->getClientOriginalName()
            ];
        $action = sprintf('%s File uploaded to s3', $files->getClientOriginalName());
        $message = ['message' => 'File uploaded to S3',
                    'fileName' => $files->getClientOriginalName()
                ];
        $actionArray['s3'] = $action;
        $queueData = $this->buildQueueData($actionArray, json_encode($message));
        $this->pushToSQS($queueData);

        return $this->response($data);
    }

    private function buildQueueData($action, $messageBody, $queue = false) {
        if ($queue === false) {
            $queue = $this->context['config']['sqs']['notificationQueue'];
        }
        $user = $this->context['session']->get('user');
        $actionKey = key($action);
        $user['module'] = $actionKey;
        
        return [
                    'MessageAttributes' => [
                        'User' => [
                            'DataType' => 'String', // REQUIRED
                            'StringValue' => json_encode($user)
                        ],
                        'Module' => [
                            'DataType' => 'String', // REQUIRED
                            'StringValue' => $actionKey
                        ],
                        // ...
                    ],
                    'MessageBody' => $messageBody, // REQUIRED
                    'QueueUrl' => $queue, // REQUIRED
        ];
    }

    private function pushProgressQueue($message) {
        $sqs = $this->context['aws']->createSqs();
        $queueUrl = $this->context['config']['sqs']['progressQueue'];
        $sqs->sendMessage([
            'QueueUrl'    => $queueUrl,
            'MessageBody' => $message
        ]);
    }

    private function pushToSQS($queueData) {
        $sqs = $this->context['aws']->createSqs();
        $result = $sqs->sendMessage($queueData);
    }


    public function viewSQS($request, $app) {
        $sqs = $this->context['aws']->createSqs();
        $queue = $this->context['config']['sqs']['notificationQueue'];
        $result = $sqs->setQueueAttributes(array(
        'Attributes' => [
            'ReceiveMessageWaitTimeSeconds' => 20
        ],
        'QueueUrl' => $queue, // REQUIRED
        ));
        $result = $sqs->receiveMessage([
                'MaxNumberOfMessages' => 10,
                'QueueUrl' => $queue, // REQUIRED
                'MessageAttributeNames' => ['All'],
                'WaitTimeSeconds' => 20,
            ]);
        $data['customReports'] = $result->getPath('Messages');

        return $data;
    }

    public function loadEMR() {
         $data['customReports'] = $this->context['getRepository']
            ('Emrstatus')->getAllReports();

        return $data;
    }

    public function getProgress() {
        return $this->response($this->progress);
    }

    public function createEMR($request, $app) {
        $category = str_replace(' ', '_', $request->request->get('category'));
        $module = $request->request->get('module');
        $description = $request->request->get('description');
        $status = $request->request->get('status');
        $comments = $request->request->get('comments');
        $ts_begin = $request->request->get('ts_begin');
        $dbuser = $this->context['config']['rdbms']['user'];
        $dbpwd = $this->context['config']['rdbms']['password'];
        $dbname = $this->context['config']['rdbms']['dbname'];
        $dbhost = $this->context['config']['rdbms']['host'];
        $shell = $this->context['config']['shell']['emrCreateFile'];
        $nodes = $request->request->get('nodes');
        $status = shell_exec("sh $shell $dbhost $dbuser $dbpwd $dbname $category $nodes");
        $data['customReports'] = $this->context['getRepository']
            ('Emrstatus')->getAllReports();
        $action = sprintf('%s Emr creation failed', $category);
            $message = ['message' => 'EMR Creation failed',
                        'EMR' => $category
                    ];
            $actionArray['emr'] = $action;
            $queueData = $this->buildQueueData($actionArray,
                json_encode($message));
        if ($status == 0 || $status == '0') {
            $action = sprintf('%s Emr Created', $category);
            $message = ['message' => 'EMR Created',
                        'EMR' => $category
                    ];
            $actionArray['emr'] = $action;
            $queueData = $this->buildQueueData($actionArray,
                json_encode($message));
            $this->pushToSQS($queueData);
            return $data;
        }
        $this->pushToSQS($queueData);

        throw new \Excepetion('emr.creation.failed');
    }



    public function stopEmr($request, $app) {
        $shell = $this->context['config']['shell']['emrStopFile'];
        $id = $request->request->get("id");
        $dbuser = $this->context['config']['rdbms']['user'];
        $dbpwd = $this->context['config']['rdbms']['password'];
        $dbname = $this->context['config']['rdbms']['dbname'];
        $dbhost = $this->context['config']['rdbms']['host'];
        $status = shell_exec("sh $shell $dbhost $dbuser $dbpwd $dbname $id");
        $action = sprintf('%s EMR stop falied', $id);
        $message = ['message' => 'EMR stop falied',
                'EMR_id' => $id
            ];
        $actionArray['emr'] = $action;
        $queueData = $this->buildQueueData($actionArray, json_encode($message));
        if (!empty($status) && ($status == 0 || $status == '0')) {
            $action = sprintf('%s EMR stopped', $id);
            $message = ['message' => 'EMR stopped',
                    'EMR_id' => $id
                ];
            $actionArray['emr'] = $action;
            $queueData = $this->buildQueueData($actionArray, json_encode($message));
            $data['customReports'] = $this->context['getRepository']
            ('Emrstatus')->getAllReports();
            $this->pushToSQS($queueData);
            return $data;
        }

        $this->pushToSQS($queueData);

        throw new \Exception('error.stopping.emr');
    }

    public function startEMR($request, $app) {
        $shell = $this->context['config']['shell']['emrStartFile'];
        $id = $request->request->get("id");
        $dbuser = $this->context['config']['rdbms']['user'];
        $dbpwd = $this->context['config']['rdbms']['password'];
        $dbname = $this->context['config']['rdbms']['dbname'];
        $dbhost = $this->context['config']['rdbms']['host'];
        $status = shell_exec("sh $shell $dbhost $dbuser $dbpwd $dbname $id");
        $action = sprintf('%s EMR start falied', $id);
        $message = ['message' => 'EMR start falied',
                'EMR_id' => $id
            ];
        $actionArray['emr'] = $action;
        $queueData = $this->buildQueueData($actionArray, json_encode($message));
        if ($status == 0 || $status == '0') {
            $action = sprintf('%s EMR started', $id);
            $message = ['message' => 'EMR started',
                    'EMR_id' => $id
                ];
            $actionArray['emr'] = $action;
            $queueData = $this->buildQueueData($actionArray, json_encode($message));
            $data['customReports'] = $this->context['getRepository']
            ('Emrstatus')->getAllReports();
            $this->pushToSQS($queueData);
            return $data;
        }
        $this->pushToSQS($queueData);

        throw new \Exception('error.starting.emr');
    }

    public function manageUploads() {
        $s3 = $this->context['aws']->createS3();
        $bucket = $this->context['config']['S3']['bucket'];
        $user = $this->context['session']->get('user');
        $user = $user['username'];
        $prefix = sprintf('data/%s', $user);
        $files = $s3->listObjects(array('Bucket' => $bucket, "Prefix" => $prefix));;

        return ['customReports' => $files->getPath('Contents')];
    }

    public function athenaFolder($request, $app, $isTree = true) {
        $prefix = $request->request->get('folder');
        $s3 = $this->context['aws']->createS3();
        $bucket = $this->context['config']['S3']['bucket'];
        if(!isset($prefix)){
            $athenaDirectory = $this->context['config']['athena']['directory'];
        }
        //echo $prefix;exit;
        $files = $s3->listObjects(array('Bucket' => $bucket, "Prefix" => "$athenaDirectory", 'Delimiter' => '/'));
        $tree = [];
        $tree = ["text" => str_replace('/', "", $athenaDirectory),
                 "id" => $athenaDirectory
                ];
        $children = $files->getPath('CommonPrefixes');
        if ($isTree === false) {
            return $children;
        }
        $treeChildren = [];
        foreach ($children as $key => $value) {
           $prefix = str_replace($athenaDirectory, '', $value['Prefix']);
           $treeChildren[$key]['text'] = str_replace('/', '', $prefix);
           $treeChildren[$key]['id'] = $value['Prefix'];
        }
        $tree['children'] = $treeChildren;

        return $this->response($tree);
    }

    public function loadAthenaTree($request, $app) {
        $file = $request->query->get('file');
        $this->fileFullPath = $file;
        $this->fileToCopy = str_replace("data/", "", $file);

        return [];
    }

    public function showAthenaDirectories($request, $app) {
        $athenaFolders = $this->athenaFolder($request, $app, false);
        return ['customReports' => $athenaFolders];
    }

    public function copyORC($request, $app) {
        $shell = $this->context['config']['shell']['orcConversion'];
        $file = $request->request->get('file');
        $fileToCopy = sprintf("%s.orc", $file);
        $status = 1;
        $bucket = $this->context['config']['S3']['bucket'];
        $key = $this->context['config']['S3']['credentials']['key'];
        $secret = $this->context['config']['S3']['credentials']['secret'];
        $source = sprintf("s3://%s/%s", $bucket, $file);
        $destination = sprintf("s3://%s/%s.orc", $bucket, $file);
        if(file_exists($shell)) {
            $status = shell_exec("sh $shell $source $destination $key $secret");
        } else {
            $s3 = $this->context['aws']->createS3();
            $bucket = $this->context['config']['S3']['bucket'];
            $batch = $s3->copyObject(array(
                'Bucket'     => $bucket,
                'Key'        => $fileToCopy,
                'CopySource' => "{$bucket}/{$file}",
            ));

            $newFile = $s3->headObject([
                'Bucket'     => $bucket,
                'Key'        => $fileToCopy
            ]);
            if ($newFile) {
                $status = 0;
            }
        }

        $action = sprintf('%s ORC Conversion falied', $file);
        $message = ['message' => 'ORC Conversion falied',
                'File' => $file
            ];
        $actionArray['s3'] = $action;
        $queueData = $this->buildQueueData($actionArray, json_encode($message));
        if ($status == 0 || $status == '0') {
            $action = sprintf('%s ORC Conversion success', $file);
            $message = ['message' => 'ORC Conversion success',
                'File' => $file
            ];
            $actionArray['s3'] = $action;
            $queueData = $this->buildQueueData($actionArray, json_encode($message));
            $this->pushToSQS($queueData);

            return $this->response(['response' => 'success']);
        }

        $this->pushToSQS($queueData);


        throw new \Exception('file.orc.conversion.error');
    }

    public function deleteObject($request, $app) {
        $file = $request->request->get('file');
        $s3 = $this->context['aws']->createS3();
        $bucket = $this->context['config']['S3']['bucket'];
        $batch = $s3->deleteObject(array(
            'Bucket'     => $bucket,
            'Key'        => $file,
        ));


        if ($batch) {
            $action = sprintf('%s Successfully deletd', $file);
            $message = ['message' => 'Successfully deletd',
                'File' => $file
            ];
            $actionArray['s3'] = $action;
            $queueData = $this->buildQueueData($actionArray, json_encode($message));
            $this->pushToSQS($queueData);

            return $this->response(['response' => 'success']);
        } else {
            $action = sprintf('%s Deleting s3 file failure', $file);
        $message = ['message' => 'Deleting s3 file failure',
                'File' => $file
            ];
        $actionArray['s3'] = $action;
        $queueData = $this->buildQueueData($actionArray, json_encode($message));
        $this->pushToSQS($queueData);

        }

        throw new \Exception('file.orc.conversion.error');
    }

    public function copyParquet($request, $app) {
        $shell = $this->context['config']['shell']['parquetConversion'];
        $file = $request->request->get('file');
        $fileToCopy = sprintf("%s.parquet", $file);
        $s3 = $this->context['aws']->createS3();
        $bucket = $this->context['config']['S3']['bucket'];
        $key = $this->context['config']['S3']['credentials']['key'];
        $secret = $this->context['config']['S3']['credentials']['secret'];
        $source = sprintf("s3://%s/%s", $bucket, $file);
        $destination = sprintf("s3://%s/%s.parquet", $bucket, $file);
        if(file_exists($shell)) {
            $status = shell_exec("sh $shell $source $destination $key $secret");
        } else {
            $s3 = $this->context['aws']->createS3();
            $bucket = $this->context['config']['S3']['bucket'];
            $batch = $s3->copyObject(array(
                'Bucket'     => $bucket,
                'Key'        => $fileToCopy,
                'CopySource' => "{$bucket}/{$file}",
            ));

            $newFile = $s3->headObject([
                'Bucket'     => $bucket,
                'Key'        => $fileToCopy
            ]);
            if ($newFile) {
                $status = 0;
            }
        }
        $action = sprintf('%s parquet Conversion falied', $file);
        $message = ['message' => 'parquet Conversion falied',
                'File' => $file
            ];
        $actionArray['s3'] = $action;
        $queueData = $this->buildQueueData($actionArray, json_encode($message));
        if ($status == 0 || $status == '0') {
            $action = sprintf('%s parquet Conversion success', $file);
            $message = ['message' => 'parquet Conversion success',
                'File' => $file
            ];
            $actionArray['s3'] = $action;
            $queueData = $this->buildQueueData($actionArray, json_encode($message));
            $this->pushToSQS($queueData);

            return $this->response(['response' => 'success']);
        }

        $this->pushToSQS($queueData);


        throw new \Exception('file.par.conversion.error');
    }

    public function copyBetweenFolders($request, $app) {
        $path = $request->request->get('path');
        $file = $request->request->get('file');
        $fileToCopy = str_replace("data/", "", $file);
        $fileToCopy = sprintf("%s%s", $path, $fileToCopy);
        //return $this->response($fileToCopy);
        $s3 = $this->context['aws']->createS3();
        $bucket = $this->context['config']['S3']['bucket'];
        $batch = $s3->copyObject(array(
            'Bucket'     => $bucket,
            'Key'        => $fileToCopy,
            'CopySource' => "{$bucket}/{$file}",
        ));

        $newFile = $s3->headObject([
            'Bucket'     => $bucket,
            'Key'        => $fileToCopy
        ]);
        $oldFile = $s3->headObject([
            'Bucket'     => $bucket,
            'Key'        => $file
        ]);

        $newMd5 = $newFile->getPath('ETag');
        $oldMd5 = $oldFile->getPath('ETag');
        $action = sprintf('%s Copying to athena falied', $file);
        $message = ['message' => 'Copying to athena falied',
                'File' => $file
            ];
        $actionArray['athena'] = $action;
        $queueData = $this->buildQueueData($actionArray, json_encode($message));

        if ($newMd5 === $oldMd5) {
            $action = sprintf('%s Copying to athena success', $file);
            $message = ['message' => 'Copying to athena success',
                'File' => $file
            ];
            $actionArray['athena'] = $action;
            $queueData = $this->buildQueueData($actionArray, json_encode($message));
            $this->pushToSQS($queueData);


            return $this->response(['response' => 'success']);
        }

        $this->pushToSQS($queueData);


        return $this->response(['response' => 'faliure']);
    }

    public function createTable($request, $app) {
        $config = $this->context['config']['athena'];
        $athena = $this->context['aws']->createAthena();
        $columns = $request->request->get('columns');
        $file = $request->request->get('file');
        $table = $request->request->get('tablename');
        $oldTable = $request->request->get('oldtable');
        $table = str_replace($config['directory'], '', $table);
        $table = str_replace('/', '', $table);
        $query = "CREATE EXTERNAL TABLE IF NOT EXISTS %s.%s (%s) ROW FORMAT DELIMITED FIELDS TERMINATED BY ',' LOCATION '%s%s';";
        if (!empty($oldTable)) {
            $deleteQuery = sprintf("DROP TABLE %s.%s", $config['database'], $oldTable);
            $result = $athena->startQueryExecution([
                'QueryString' => $deleteQuery, // REQUIRED
                'ResultConfiguration' => [ // REQUIRED
                    'EncryptionConfiguration' => [
                        'EncryptionOption' => 'SSE_S3', // REQUIRED
                        ],
                    'OutputLocation' => $config['output'], // REQUIRED
                    ],
            ]);
        }
        $query = sprintf($query,  $config['database'], $table, trim($columns),
            $config['input'], $file);


        $result = $athena->startQueryExecution([
            'QueryString' => $query, // REQUIRED
            'ResultConfiguration' => [ // REQUIRED
                'EncryptionConfiguration' => [
                    'EncryptionOption' => 'SSE_S3', // REQUIRED
                    ],
                'OutputLocation' => $config['output'], // REQUIRED
                ],
        ]);

        $action = sprintf('%s Table Created', $table);
        $message = ['message' => 'Table Created',
                'table_name' => $table
            ];
        $actionArray['athena'] = $action;
        $queueData = $this->buildQueueData($actionArray, json_encode($message));
        $this->pushToSQS($queueData);


        return $result->getPath('QueryExecutionId');
    }

    public function editTable($request, $app) {
        $config = $this->context['config']['athena'];
        $table = $request->query->get('table');
        $query = "DESC %s";
        $query = sprintf($query, $table);
        $result = $this->processQuery($query);
        $result = $result->get('ResultSet');
        $result = array_column($result['Rows'], 'Data');
        $ref = [];
        $data = [];
        foreach ($result as $res) {
            array_push($ref , array_column($res, 'VarCharValue'));
        }
        $data['tables']['definition'] = str_replace(' ', '', implode(',', $this->array_flatten($ref)));
        $data['customReports'] = $this->athenaFolder($request, $app, false);
        $data['tables']['table'] = $table;

        return $data;
    }

    private function array_flatten($array, $return = []) {
        for($x = 0; $x <= count($array); $x++) {
            if(is_array($array[$x])) {
                $return = $this->array_flatten($array[$x], $return);
            }
            else {
                if(isset($array[$x])) {
                    $return[] = $array[$x];
                }
            }
        }
        return $return;
    }


    public function showTables($request, $app) {

        $athenaFolders = $this->athenaFolder($request, $app, false);
        $data ['customReports'] = $athenaFolders;

        return $data;

    }

    public function listTables($request, $app) {
        $config = $this->context['config']['athena'];
        $query = "SHOW TABLES IN %s";
        $query = sprintf($query, $config['database']);
        $result = $this->processQuery($query);
        $data = $result->get('ResultSet');

        return $data;
    }

    public function runQuery($request, $app) {
        $id = $request->query->get('id');
        $crRepo = $this->context['getRepository']
            ('Customreport');
        $config = $this->context['config']['athena'];
        $result = $crRepo->findOneBy(['id' => $id]);
        $query = $result->toArray();
        $category = $query['category'];
        $action = sprintf('%s Query Run', $table);
        $message = ['message' => 'Query Run',
            'query' => $query['querystring']
            ];
        $actionArray[$category] = $action;
        $queueData = $this->buildQueueData($actionArray, json_encode($message));
        $this->pushToSQS($queueData);
        if ($query['category'] !== 'athena') {
            $shell = $this->context['config']['shell']['runQueryShell'];
            extract($query);
            $shell = sprintf('sh %s "%s" "%s" "%s" "%s" "%s" "%s" "%s"', $shell,
                $title, $category, $bucket, $description, $querystring,
                $config['database'], $config['output']
                );
            $data = shell_exec($shell);
            return $data = ['error' => ['value' => $data]];
        }

        $query = $query['querystring'];
        try {
            $result = $this->processQuery($query);
        } catch (\Exception $e) {

            return $data = ['error' => ['value' => "Problem in executing the query"]];
        }

        return $result->get('ResultSet');
    }

    private function processQuery($query) {
        $config = $this->context['config']['athena'];
        $athena = $this->context['aws']->createAthena();
        $promise = $athena->startQueryExecutionAsync([
            'QueryExecutionContext' => [
                'Database' => $config['database'],
            ],
            'QueryString' => $query, // REQUIRED
            'ResultConfiguration' => [ // REQUIRED
                'EncryptionConfiguration' => [
                    'EncryptionOption' => 'SSE_S3', // REQUIRED
                    ],
                'OutputLocation' => $config['output'], // REQUIRED
                ],
        ]);

        $result = $promise->wait();
        $executionId = $result->getPath('QueryExecutionId');
        $this->waitForQuerySuccess($executionId);

        return $this->getQueryResult($executionId);
    }

    private function waitForQuerySuccess($executionId) {
        $isQueryStillRunning = true;
        while($isQueryStillRunning) {
            $state = $this->getQueryExecutionSatus($executionId);
            switch ($state) {
                case 'FAILED':
                    throw new \Exception ("Query FAILED");
                    break;
                case 'CANCELLED':
                    throw new \Exception ("Query Cancelled");
                    break;
                case 'SUCCEEDED':
                    $isQueryStillRunning = false;
                    break;
                default:
                    sleep(10);
                    break;
            }
        }

        return $state;
    }

    private function getQueryResult($executionId) {
        $config = $this->context['config']['athena'];
        $athena = $this->context['aws']->createAthena();
        $promise = $athena->getQueryResultsAsync(
            ['QueryExecutionId' => $executionId]);

        $result = $promise->wait();

        return $result;
    }

    public function saveQuery($request, $app) {
        $crRepo = $this->context['getRepository']('Customreport');
        $crData = $this->context['customReport'];
        $crData->setId($request->request->get('id') ? $request->request->get('id') : NULL);
        $crData->setTitle($request->request->get('title'));
        $crData->setCategory($request->request->get('type'));
        $crData->setDescription($request->request->get('description'));
        $crData->setQuerystring($request->request->get('querystring'));
        $crData->setBucket($request->request->get('bucket'));

        return $this->response($crRepo->save($crData));
    }

    public function pushNotification($request, $app) {
        $notifyRepo = $this->context['getRepository']('Notification');
        $notifyData = $this->context['notification'];
        $user = $this->context['session']->get('user');
        $notifyData->setUser($user['username']);
        $action = $request->request->get('message');
        $notifyData->setAction($action);

        return $this->response($notifyRepo->save($notifyData));
    }

    public function getQueryStatus($request, $app) {
        $athena = $this->context['aws']->createAthena();
        $queryID = $request->request->get('queryid');

        $result = $athena->getQueryExecution([
            'QueryExecutionId' => "$queryID", // REQUIRED
        ]);
        $execution = $result->getPath('QueryExecution');
        $state = $execution['Status']['State'];

        return $this->getExecutionPercentage($state);

    }

    private function getQueryExecutionSatus($queryID) {
        $athena = $this->context['aws']->createAthena();
        $result = $athena->getQueryExecution([
            'QueryExecutionId' => "$queryID", // REQUIRED
        ]);
        $execution = $result->getPath('QueryExecution');

        return $state = $execution['Status']['State'];
    }

    public function getAllQueries() {
        $data['customReports'] = $this->context['getRepository']
            ('Customreport')->getAllReports();

        return $data;
    }

    public function editQuery($request, $app) {
        $crRepo = $this->context['getRepository']
            ('Customreport');
        $id = $request->query->get('id');
        $result = $crRepo->findOneBy(['id' => $id]);
        $data['query'] = $result->toArray();

        return $data;
    }

    private function getExecutionPercentage($state) {
        switch ($state) {
            case 'QUEUED':
                return 30;
                break;
            case 'RUNNING':
                return 65;
                break;
            case 'SUCCEEDED':
                return 100;
                break;
            default:
                return 0;
                break;
        }
    }

    public function getNotifications() {
        $notifyRepo = $this->context['getRepository']('Notification');
        $notifyData = $this->context['notification'];
        $user = $this->context['session']->get('user');
        //var_dump($user);

        $result = $notifyRepo->getAllNotification($user['username']);
        return $this->response($result);
    }

    public function deleteQuery($request, $app) {
        $crRepo = $this->context['getRepository']('Customreport');
        $crData = $this->context['customReport'];
        $id = $request->query->get('id');
        $crData = $crRepo->findOneBy(['id' => $id]);
        return $this->response($crRepo->remove($crData));
    }

    public function getUsers() {
        $data['customReports'] = $this->context['getRepository']
        ('Employee')->getAllUsers();
        $crData = $this->context['employee'];
        $ref = new \ReflectionClass($crData);
        $props = $ref->getProperties();


        return $data;
    }

    public function deleteUser($request, $app) {
        $crRepo = $this->context['getRepository']('Employee');
        $crData = $this->context['employee'];
        $id = $request->query->get('id');
        $crData = $crRepo->findOneBy(['id' => $id]);
        return $this->response($crRepo->remove($crData));
    }

    public function deletePermission($request, $app) {
        $crRepo = $this->context['getRepository']('Employeerelationship');
        $crData = $this->context['employeerelation'];
        $id = $request->query->get('id');
        $crData = $crRepo->findOneBy(['id' => $id]);

        return $this->response($crRepo->remove($crData));
    }

    public function getPermissions() {
        $data['customReports'] = $this->context['getRepository']
        ('Employeerelationship')->getPermissions();
        $data['employees'] = $this->context['getRepository']
        ('Employee')->getAllUsers();
        return $data;
    }

}
