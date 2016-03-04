<?php

\Util\Filter::addFilter('Drivers\Auth\Native_Model', 'getRegisterForm', 
	function($form, $args){
		
		$captcha = new UI\Captcha('recaptcha');
		$form->add($captcha);
		
		return $form;
	});


\Util\Filter::addFilter('Drivers\Auth\Native_Model', 'registerAccount', 
	function($data){
		if(!isset($data['isAPI'])){
			require_once(SITE_PATH.'/resources/recaptchalib2.php');
			$recaptcha = new \ReCaptcha(CAPTCHA_PRIV);
			if(!isset($_POST['g-recaptcha-response'])){
				throw new \Exception('Captcha required!');
			}
			$resp = $recaptcha->verifyResponse($_SERVER['REMOTE_ADDR'], $_POST['g-recaptcha-response']);
			if($resp == null OR !$resp->success){
				throw new \Exception('Captcha invalid!');
			}
		}
		return array($data);
	}, true);

