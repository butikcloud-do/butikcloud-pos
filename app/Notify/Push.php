<?php

namespace App\Notify;

use App\Notify\NotifyProcess;
use App\Notify\Notifiable;
use Illuminate\Support\Facades\Log;

class Push extends NotifyProcess implements Notifiable{

    /**
    * Device Id of receiver
    *
    * @var array
    */
	public $deviceId;

    public $redirectUrl;

    public $pushImage;


    /**
    * Assign value to properties
    *
    * @return void
    */
	public function __construct(){
		$this->statusField = 'push_status';
		$this->body = 'push_body';
		$this->globalTemplate = 'push_template';
		$this->notifyConfig = 'firebase_config';
	}


    public function redirectForApp($getTemplateName){

        $screens = [

        ];

        foreach($screens as $screen => $array){
            if(in_array($getTemplateName ,$array)){
                return $screen;
            }
        }

        return 'HOME';
    }


    /**
    * Send notification
    *
    * @return void|bool
    */
	public function send(){
		Log::info('Push:send starting...');
        if (!gs('pn')) {
        	Log::info('Push:send skipped (Push disabled)');
			return false;
		}

        //get message from parent
        $message = $this->getMessage();
        if ($message) {
            try {
                Log::info('Push:send initializing Firebase credentials...');
                $credentialsFilePath = getFilePath('pushConfig').'/push_config.json';
                $client = new \Google_Client();
                $client->setAuthConfig($credentialsFilePath);
                $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
                
                Log::info('Push:send fetching access token...');
                $client->fetchAccessTokenWithAssertion();
                $token = $client->getAccessToken();
                $access_token = $token['access_token'];
                Log::info('Push:send access token retrieved');

                $headers = [
                    "Authorization: Bearer $access_token",
                    'Content-Type: application/json'
                ];

                $data['notification'] = [
                    'body'=>$message,
                    'title'=>$this->getTitle(),
                    'image'=>asset(getFilePath('push')).'/'.$this->pushImage,
                ];

                $data['data'] = [
                    'icon'=>siteFavicon(),
                    'click_action'=>$this->redirectUrl,
                    'app_click_action'=>$this->redirectForApp($this->templateName)
                ];

                Log::info('Push:send starting token iteration...', ['token_count' => count($this->toAddress)]);
                foreach ($this->toAddress as $toAddress) {
                    $data['token'] = $toAddress;
                    $payloadData['message'] = $data;
                    $payload = json_encode($payloadData);
                    
                    Log::info('Push:send calling FCM API...', ['token' => $toAddress]);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/'.gs('firebase_config')->projectId.'/messages:send');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                    curl_exec($ch);
                    curl_close($ch);
                    Log::info('Push:send FCM API call returned');
                }
                $this->createLog('push');
            } catch(\Exception $e){
            	Log::error('Push:send failed', ['error' => $e->getMessage()]);
                $this->createErrorLog($e->getMessage());
                session()->flash('firebase_error',$e->getMessage());
            }
        } else {
        	Log::warning('Push:send message could not be retrieved');
        }
        Log::info('Push:send completed');
    }



    /**
     * Configure some properties
     *
     * @return void
     */
	public function prevConfiguration(){
        // Check if push notifications are enabled before accessing database
        if (!gs('pn')) {
            $this->deviceId = [];
            $this->toAddress = [];
            return;
        }

		if ($this->user) {
            $this->deviceId = $this->user->deviceTokens()->pluck('token')->toArray();
			$this->receiverName = $this->user->fullname;
		}
		$this->toAddress = $this->deviceId;
	}

    private function getTitle(){
        return $this->replaceTemplateShortCode($this->template->push_title ?? gs('push_title'));
    }
}
