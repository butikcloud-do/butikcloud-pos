<?php

namespace App\Notify;

use App\Notify\NotifyProcess;
use App\Notify\SmsGateway;
use App\Notify\Notifiable;
use Illuminate\Support\Facades\Log;


class Sms extends NotifyProcess implements Notifiable{

    /**
    * Mobile number of receiver
    *
    * @var string
    */
	public $mobile;

    /**
    * Assign value to properties
    *
    * @return void
    */
	public function __construct(){
        
		$this->statusField = 'sms_status';
		$this->body = 'sms_body';
		$this->globalTemplate = 'sms_template';
		$this->notifyConfig = 'sms_config';
	}


    /**
    * Send notification
    *
    * @return void|bool
    */
	public function send(){
		Log::info('Sms:send starting...');
        if (!gs('sn')) {
			Log::info('Sms:send skipped (Sms disabled)');
			return false;
		}
        //get message from parent
		$message = $this->getMessage();
		if ($message) {
			try {
				$gateway = gs('sms_config')->name;
                if($this->mobile){
                    Log::info('Sms:send using gateway', ['gateway' => $gateway, 'to' => $this->mobile]);
                    $sendSms = new SmsGateway();
                    $sendSms->to = $this->mobile;
                    $sendSms->from = $this->getSmsFrom();
                    $sendSms->message = strip_tags($message);
                    $sendSms->config = gs('sms_config');
                    
                    Log::info('Sms:send calling gateway method...');
                    $sendSms->$gateway();
                    Log::info('Sms:send gateway method returned');
                    
                    $this->createLog('sms');
                } else {
                	Log::warning('Sms:send mobile number not available');
                }
			} catch (\Exception $e) {
				Log::error('Sms:send failed', ['error' => $e->getMessage()]);
				$this->createErrorLog('SMS Error: '.$e->getMessage());
				session()->flash('sms_error','API Error: '.$e->getMessage());
			}
		} else {
			Log::warning('Sms:send message could not be retrieved');
		}
		Log::info('Sms:send completed');
	}

    /**
    * Configure some properties
    *
    * @return void
    */
	public function prevConfiguration(){
		//Check If User
		if ($this->user) {
			$this->mobile = $this->user->mobileNumber;
			$this->receiverName = $this->user->fullname;
		}
		$this->toAddress = $this->mobile;
	}

    private function getSmsFrom(){
        $this->sentFrom = $this->replaceTemplateShortCode($this->template->sms_sent_from ?? gs('sms_from'));
        return $this->sentFrom;
    }
}
