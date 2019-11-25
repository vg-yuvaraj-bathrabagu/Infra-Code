<?php
namespace App\Reports\Service;

use App\Reports\Model\MessageLog;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Reports\Traits\Context;
use App\Reports\Traits\Utils;
use Symfony\Component\Yaml\Yaml;

class AuthenticationService {
    use Context;
    use Utils;

    private $context;

    public function __construct() {
        $this->context = self::getContext();
        $this->awsSdk = $this->context['awsSdk'];
        $this->cognito = $this->context['cognito'];
    }
    
    public function loadIndex($request, $app) {
        return $app->redirect('login');
    }
    
    public function login() {
        return [];
    }

    public function authenticate($request, $app) {
        $client = $this->cognito;
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        try {
            $authenticationResponse = $client->authenticate($username, $password);
            $this->setSession($username, $authenticationResponse);
            return $this->createAccess($app);
        } catch (ChallengeException $e) {
            if ($e->getChallengeName() === CognitoClient::CHALLENGE_NEW_PASSWORD_REQUIRED) {
                $authenticationResponse = $client->respondToNewPasswordRequiredChallenge($username, 'password_new', $e->getSession());
            }
        } catch (PasswordResetRequiredException $e) {
            die("PASSWORD RESET REQUIRED");
        }
    }

    private function createAccess($app) {
        $cognito = $this->context['aws']->createCognitoIdentity();
        $credentials = $this->context['credentials'];
        $session = $this->context['session'];
        $currentSession = $session->get('user');
        $result = $cognito->getId([
            'AccountId' => $credentials['accountId'],
            'IdentityPoolId' => $credentials['idPoolId'], // REQUIRED
            'Logins' => [
                $credentials['linkedLogins'] => $currentSession['idToken']
            ]]);
        $identityId = $result['IdentityId'];
        $tokenResp = $cognito->getOpenIdToken([
                'IdentityId' => $identityId,
                'Logins' => [
                    $credentials['linkedLogins'] => $currentSession['idToken']
                ]
            ]);
        $token = $tokenResp['Token'];
        $stsClient = $this->context['aws']->createSts();

        $stsResp = $stsClient->assumeRoleWithWebIdentity(array(
            'RoleArn' => $credentials['roleArn'],
            'RoleSessionName' => 'OnCloudSession', // you need to give the session a name
            'WebIdentityToken' => $token
        ));

        $session->set('stsCredentials', $stsResp);

        return $app->redirect('downloadmanager');
        //var_dump($stsResp);exit;

    }

    public function setSession($userName, $authenticationResponse) {
        $this->context['session']->set('user', 
            ['username' => $userName,
             'idToken' => $authenticationResponse['IdToken']
            ]);
    }
}