<?php
namespace App\Reports\Service;

use App\Reports\Model\MessageLog;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Reports\Traits\Context;
use App\Reports\Traits\Utils;
use Symfony\Component\Yaml\Yaml;
use Aws\S3\S3Client;

class S3Service {
    use Context;
    use Utils;

    private $context;

    public function __construct() {
        $this->context = self::getContext();
        $this->awsSdk = $this->context['awsSdk'];
        $this->cognito = $this->context['cognito'];
    }

    public function downloadManager($request, $app) {
        $session = $this->context['session'];
        $stsResp = $session->get('stsCredentials');

        $s3Client = new S3Client([
            'credentials' => [
                'key'    => $stsResp['Credentials']['AccessKeyId'],
                'secret' => $stsResp['Credentials']['SecretAccessKey'],
                'token'  => $stsResp['Credentials']['SessionToken']
            ],
            'region' => 'us-east-2',
            'version' => 'latest'
        ]);

        $files = $s3Client->listObjects(['Bucket' => "cognito-s3-test-bucket",
             'Prefix' => 'softwares',
             'Delimiter' => '/'    
            ])->get('Contents');
        
        $tree = [];
        $tree = ["text" => str_replace('/', "", "softwares"),
                 "id" => "softwares"
                ];
        $treeChildren = [];
                
        $tree['children'] = $treeChildren;
        return [];
        
    }


}