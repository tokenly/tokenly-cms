<?php
namespace Drivers\Auth;
use Core, UI, Util, App\Profile, App\Account\Settings_Model;

class Native_Model extends Core\Model
{
	public static $activity_updated = false;
	
	protected function clearSession($auth)
	{
		$getSesh = $this->container->checkSession($auth);
		if(!$getSesh){
			return false;
		}
		$this->edit('users', $getSesh['userId'], array('lastActive' => null));
		return $this->delete('user_sessions', $getSesh['sessionId']);
	}
	
	protected function checkSession($auth, $useCache = false)
	{
		$get = $this->fetchSingle('SELECT * FROM user_sessions WHERE auth = :auth ORDER BY sessionId DESC LIMIT 1',
									array(':auth' => $auth), 0, $useCache);
		if($get){
			return $get;
		}
		return false;
	}
	
	protected function getLoginForm()
	{
		$form = new UI\Form;
		
		$username = new UI\Textbox('username');
		$username->addAttribute('required');
		$username->setLabel('Username');
		$form->add($username);

		$password = new UI\Password('password');
		$password->addAttribute('required');
		$password->setLabel('Password');
		$form->add($password);	
		
		$hidden = new UI\Hidden('submit-type');
		$hidden->setValue('login');
		$form->add($hidden);
		
		$remember = new UI\Checkbox('rememberMe', 'rememberMe');
		$remember->setLabel('Remember Me?');
		$remember->setBool(1);
		$remember->setValue(1);
		$form->add($remember);
		
		if(isset($_GET['r'])){
			$redirect = new UI\Hidden('r');
			$redirect->setValue($_GET['r']);
			$form->add($redirect);
		}

		return $form;
	}	
	
	protected function checkAuth($data)
	{	
		$sesh_auth = Util\Session::get('accountAuth');
		if(isset($data['authKey'])){
			http_response_code(400);
			throw new \Exception('Already logged in!');
		}
		elseif($sesh_auth AND !isset($data['isAPI'])){
			http_response_code(400);
			throw new \Exception('Already logged in!');
		}
		
		$checkPassword = true;
		$meta = new \App\Meta_Model;
		$get = false;
		if(app_enabled('tokenly') AND isset($data['address']) AND isset($data['signed_message'])){
			//attempt sign in via signed bitcoin address message
			$get = $this->container->checkAuthViaBitcoin($data);
		}
		if(!$get){
			//use traditional username/password combo
			if(!isset($data['username'])){
				http_response_code(400);
				throw new \Exception('Username required');
			}
			if(!isset($data['password'])){
				http_response_code(400);
				throw new \Exception('Password required');
			}
			
			$get = $this->get('users', $data['username'], array(), 'username');
		}
		
		if(!$get){ 
			http_response_code(401);
			throw new \Exception('Invalid credentials');
		}		
		
		if($get['activated'] == 0){
			http_response_code(403);
			throw new \Exception('Account not activated. Please check your email');
		}

		$meta = new \App\Meta_Model;		
		$getAttempts = $this->container->getLoginAttempts($get);
				
		if($checkPassword){
			if(!isset($data['password'])){
				$data['password'] = null;
			}
			$pass = hash('sha256', $get['spice'].$data['password']);
			if($pass != $get['password']){
				http_response_code(401);
				$getAttempts++;
				$meta->updateUserMeta($get['userId'], 'login_attempts', $getAttempts);
				throw new \Exception('Invalid credentials');
			}
		}
		
		$token = $this->container->generateAuthToken($get['userId']);
		if(!$token){
			http_response_code(400);
			$getAttempts++;
			$meta->updateUserMeta($get['userId'], 'login_attempts', $getAttempts);
			throw new \Exception('Error authenticating');
		}
		
		if(isset($data['rememberMe']) AND intval($data['rememberMe']) === 1){
			$hashAgain = hash('sha256', $get['password'].$get['username']);
			$baseId = base64_encode($get['userId']);
			setcookie('rememberAuth', $hashAgain.':'.$baseId.':'.md5($hashAgain.':'.$baseId), time()+(60*60*24*30), '/');
		}
		
		$meta->updateUserMeta($get['userId'], 'login_attempts', 0);
		$getNumLogins = $meta->getUserMeta($get['userId'], 'num_logins');
		if(!$getNumLogins){
			$getNumLogins = 0;
		}
		
		$meta->updateUserMeta($get['userId'], 'num_logins', ($getNumLogins + 1));
		$profModel = new Profile\User_Model;
		$getProf = $profModel->getUserProfile($get['userId'], $data['site']['siteId']);

		$getProf['auth'] = $token;
		$getProf['lastActive'] = timestamp();
		
		return $getProf;
	}	
	
	
	protected function checkAuthViaBitcoin($data)
	{
		$get = false;
		//use BTC address + signed message to sign in
		$findAddress = $this->fetchSingle('SELECT * FROM coin_addresses
										   WHERE address = :address AND verified = 1', array(':address' => trim($data['address'])));
		if($findAddress){
			$site = currentSite();
			$get = $this->get('users', $findAddress['userId']);
			if($get){
				$btc_access = intval($meta->getUserMeta($get['userId'], 'btc_access'));
				if($btc_access == 1){
					$secret_message = $site['domain'].' '.date('Y/m/d');
					$btc = new API\Bitcoin(BTC_CONNECT);
					try{
						$extract_signed = extract_signature($data['signed_message']);
						$verify = $btc->verifymessage($findAddress['address'], $extract_signed, $secret_message);
					}
					catch(\Exception $e){
						http_response_code(400);
						throw new \Exception('Error verifying signed message (bitcoin down?)');
					}
					
					if($verify){
						$checkPassword = false;
					}
					else{
						$get = false;
					}
				}
			}
		}
		return $get;
	}
	
	protected function getLoginAttempts($get)
	{
		$meta = new \App\Meta_Model;
		$lastAttempt = strtotime($meta->getUserMeta($get['userId'], 'last_attempt'));
		$meta->updateUserMeta($get['userId'], 'last_attempt', timestamp());
		$getAttempts = $meta->getUserMeta($get['userId'], 'login_attempts');
		if($getAttempts === false){
			$meta->updateUserMeta($get['userId'], 'login_attempts', 0);
			$getAttempts = 0;
		}
		else{
			$getAttempts = intval($getAttempts);
			if(intval($getAttempts) >= 5){
				if(time() - $lastAttempt > 3600){
					$meta->updateUserMeta($get['userId'], 'login_attempts', 0);
					$getAttempts = 0;	
				}
				else{
					if($getAttempts < 25){
						$getAttempts++;
						$meta->updateUserMeta($get['userId'], 'login_attempts', $getAttempts);
					}
					http_response_code(429);
					throw new \Exception('Trying too many times, please try again later (attempts: '.($getAttempts).')');
				}
			}
		}
		return $getAttempts;	
	}
	
	protected function generateAuthToken($userId)
	{
		$get = $this->get('users', $userId);
		if(!$get){
			return false;
		}
		$token = hash('sha256', $get['username'].$get['spice'].$_SERVER['REMOTE_ADDR'].time().mt_rand(0,1000));
		$makeSession = $this->container->makeSession($userId, $token);
		if(!$makeSession){
			return false;
		}
		$update = $this->edit('users', $userId, array('lastAuth' => timestamp()));
		if(!$update){
			return false;
		}
		if(!isset($this->api) OR $this->api != true){
			Util\Session::set('accountAuth', $token);
		}
		Native_Model::updateLastActive($userId);
		return $token;
	}	
	
	protected function makeSession($userId, $token)
	{
		$check = $this->container->checkSession($token);
		if($check){
			return false;
		}
		$time = timestamp();
		$insert = $this->insert('user_sessions', array('userId' => $userId, 'auth' => $token, 'IP' => $_SERVER['REMOTE_ADDR'],
													   'authTime' => $time, 'lastActive' => $time));
		if(!$insert){
			return false;
		}
		return true;
	}	
	
	protected static function updateLastActive($userId)
	{
		if(!self::$activity_updated){
			$model = new Native_Model;
			$auth = false;
			$sesh_auth = Util\Session::get('accountAuth');
			if(isset($_SERVER['HTTP_X_AUTHENTICATION_KEY'])){
				$auth = $_SERVER['HTTP_X_AUTHENTICATION_KEY'];
			}
			elseif($sesh_auth){
				$auth = $sesh_auth;
			}
			if(!$auth){
				return false;
			}
			$getSesh = $model->checkSession($auth);
			if($getSesh){
				$time = timestamp();
				$diff = strtotime($time) - strtotime($getSesh['lastActive']);
				if($diff >= 300){
					$update = $model->edit('user_sessions', $getSesh['sessionId'], array('lastActive' => $time));
					if($update){
						$editUser = $model->edit('users', $getSesh['userId'], array('lastActive' => $time));
						self::$activity_updated = true;
						return true;
					}
				}
			}
			return false;
		}
		return true;
	}	
	
	protected function getRegisterForm()
	{
		$form = new UI\Form;
		
		$username = new UI\Textbox('username');
		$username->addAttribute('required');
		$username->setLabel('Username *');
		$form->add($username);

		$password = new UI\Password('password');
		$password->addAttribute('required');
		$password->setLabel('Password *');
		$form->add($password);
		
		$email = new UI\Textbox('email');
		$email->setLabel('Email *');
		$email->addAttribute('required');
		$form->add($email);	
		
		$hny = new UI\Textbox('website');
		$hny->addClass('hny');
		$hny->setLabel('Your Website:', 'hny');
		$form->add($hny);
		
		$challenge = new UI\Textbox('challenge');
		$challenge->setLabel('Question: Who created the very first version of Bitcoin?');
		$challenge->addAttribute('required');
		$form->add($challenge);

		$hidden = new UI\Hidden('submit-type');
		$hidden->setValue('register');
		$form->add($hidden);

		return $form;
		
	}
	
	protected function registerAccount($data, $noAuth = false)
	{
		if(!isset($data['isAPI'])){
		/*	require_once(SITE_PATH.'/resources/recaptchalib2.php');
			$recaptcha = new \ReCaptcha(CAPTCHA_PRIV);
			if(!isset($_POST['g-recaptcha-response'])){
				throw new \Exception('Captcha required!');
			}
			$resp = $recaptcha->verifyResponse($_SERVER['REMOTE_ADDR'], $_POST['g-recaptcha-response']);
			if($resp == null OR !$resp->success){
				throw new \Exception('Captcha invalid!');
			}
			if(!isset($data['challenge']) OR trim($data['challenge']) == ''){
				throw new \Exception('Please answer the challenge question');
			}
			$possible_answers = array('satoshi', 'satoshi nakamoto', 'nakamoto');
			if(!in_array(trim(strtolower($data['challenge'])), $possible_answers)){
				throw new \Exception('Incorrect answer');
			}*/
		}
		else{
			if(!isset($data['site_referral'])){
				$data['site_referral'] = 'api';
			}
		}
	
		$req = array('username' => true, 'password' => true, 'email' => true);
		foreach($req as $key => $required){
			if($required AND !isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
			}
		}
		
		$data['username'] = preg_replace('/\s+/', '', $data['username']);
		
		if(trim($data['username']) == ''){
			http_response_code(400);
			throw new \Exception('Username required');
		}
		if(trim($data['password']) == ''){
			http_response_code(400);
			throw new \Exception('Password required');
		}
		if(!isset($data['email'])){
			$data['email'] = '';
		}
		else{
			$settingsModel = new Settings_Model;
			$checkEmail = $settingsModel->checkEmailInUse(0, $data['email']);
			if($checkEmail){
				throw new \Exception('Email already in use');
			}
		}
		if(isset($data['email']) AND trim($data['email']) != '' AND !filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
			http_response_code(400);
			throw new \Exception('Invalid email address');
		}
		
		//check honeypot, mark as spammer if true
		$spammer = false;
		if(isset($data['website']) AND $data['website'] != ''){
			$spammer = true;
		}
		else{
			//check stopforumspam API
			$getSpam = @file_get_contents(STOPFORUMSPAM_API.'?email='.$data['email'].'&f=json');
			if($getSpam){
				$checkSpam = json_decode($getSpam, true);
				$spamLimit = 1;
				if(isset($checkSpam['email'])){
					if($checkSpam['email']['frequency'] >= $spamLimit){
						$spammer = true;
					}
				}
			}
		}
		
		$useData = array('username' => $data['username'], 'password' => $data['password']);
		if(isset($data['email'])){
			$useData['email'] = $data['email'];
		}

		if($this->container->usernameExists($useData['username'])){
			http_response_code(400);
			throw new \Exception('Username already taken');
		}
		
		$genPass = genPassSalt($useData['password']);
		$useData['password'] = $genPass['hash'];
		$useData['spice'] = $genPass['salt'];
		$useData['regDate'] = timestamp();
		$useData['slug'] = genURL($data['username']);
		$useData['slug'] = $this->container->checkSlugExists($useData['slug']);
		
		//generate activation code
		$useData['activate_code'] = hash('sha256', time().$genPass['salt'].$useData['slug'].mt_rand(0,1000));
		
		$add = $this->insert('users', $useData);
		if(!$add){
			http_response_code(400);
			throw new \Exception('Error creating user account');
		}
		
		if(!$noAuth){
			//disable auto logging in when registering
			//$this->container->generateAuthToken($add);
		}
		
		$getSite = currentSite();
		$data['site'] = $getSite;
		
		$accountApp = $this->get('apps', 'account', array(), 'slug');
		
		$activateURL = $getSite['url'].'/'.$accountApp['url'].'/verify/'.$useData['activate_code'];
		
		//generate activation email
		$mail = new Util\Mail;
		$mail->setFrom('noreply@'.SITE_DOMAIN);
		$mail->addTo($useData['email']);
		$mail->setSubject(SITE_NAME.' - Account Activiation');
		$mail->setHTML('<p>Thank you for registering at '.SITE_NAME.'</p>
							<p>To activate your account, click here: <a href="'.$activateURL.'">'.$activateURL.'</a></p>');
							
		$send = $mail->send();
		
		//assign them to any default groups
		$getGroups = $this->getAll('groups', array('isDefault' => 1));
		foreach($getGroups as $group){
			$this->insert('group_users', array('userId' => $add, 'groupId' => $group['groupId']));
		}
		
		if($spammer){
			$getTrollGroup = $this->get('groups', 'forum-troll', array(), 'slug');
			if($getTrollGroup){
				$this->insert('group_users', array('userId' => $add, 'groupId' => $getTrollGroup['groupId']));
			}
		}
		
		$meta = new \App\Meta_Model;
		if(!$noAuth){
			$meta->updateUserMeta($add, 'IP_ADDRESS', $_SERVER['REMOTE_ADDR']);
		}
		if(is_array($data['site'])){
			$data['site'] = $data['site']['domain'];
		}
		$meta->updateUserMeta($add, 'site_registered', $data['site']);
		$meta->updateUserMeta($add, 'pubProf', 1);
		$meta->updateUserMeta($add, 'emailNotify', 1);
		
		if(isset($data['site_referral'])){
			$meta->updateUserMeta($add, 'site_referral', trim(htmlentities(strip_tags($data['site_referral']))));
		}
		elseif($spammer){
			$meta->updateUserMeta($add, 'site_referral', 'spammer');
		}
		
		$aff_ref = Util\Session::get('affiliate-ref');
		if($aff_ref){
			$getRef = $this->get('user_meta', $aff_ref, array('userId'), 'metaValue');
			if($getRef){
				$this->insert('user_referrals', array('userId' => $add, 'affiliateId' => $getRef['userId'], 'refTime' => timestamp()));
				Util\Session::clear('affiliate-ref');
			}
		}
		
		return $add;

	}
	
	protected function checkSlugExists($slug, $ignore = 0, $count = 0)
	{
		$useslug = $slug;
		if($count > 0){
			$useslug = $slug.'-'.$count;
		}
		$get = $this->get('users', $useslug, array('userId', 'slug'), 'slug');
		if($get AND $get['userId'] != $ignore){
			//slug exists already, search for next level of slug
			$count++;
			return $this->container->checkSlugExists($slug, $ignore, $count);
		}
		
		if($count > 0){
			$slug = $slug.'-'.$count;
		}

		return $slug;
	}
	
	protected function usernameExists($username)
	{
		$get = $this->fetchSingle('SELECT userId FROM users WHERE LOWER(username) = :username', array(':username' => strtolower(trim($username))));
		if($get){
			return true;
		}
		return false;
	}	
	
	
	protected function getResetForm()
	{
		$form = new UI\Form;

		$username = new UI\Textbox('username');
		$username->setLabel('Username');
		$form->add($username);

		$email = new UI\Textbox('email');
		$email->setLabel(' Or Email Address');
		$form->add($email);
		
		$form->setSubmitText('Send Password Reset');
		return $form;
		
	}
	
	protected function sendPasswordReset($data)
	{
		$site = currentSite();
		if((!isset($data['email']) OR trim($data['email']) == '') AND (!isset($data['username']) OR trim($data['username']) == '')){
			throw new \Exception('Email address or Username required');
		}
		
		if(isset($data['email']) AND trim($data['email']) != '' AND !filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
			throw new \Exception('Invalid email address');
		}
		
		$get = false;
		if(isset($data['username']) AND trim($data['username']) != ''){
			$get = $this->get('users', $data['username'], array('userId', 'username', 'email', 'lastAuth'), 'username');
		}
		if(!$get AND isset($data['email']) AND trim($data['email']) != ''){
			$get = $this->get('users', $data['email'], array('userId', 'username', 'email', 'lastAuth'), 'email');
		}
		
		if(!$get){
			throw new \Exception('No user found');
		}
		
		$genLink = hash('sha256', $get['userId'].time().':'.mt_rand(0,1000).$get['lastAuth']);
		$addLink = $this->insert('reset_links', array('userId' => $get['userId'], 'url' => $genLink, 'requestTime' => timestamp()));
		if(!$addLink){
			throw new \Exception('Error generating reset link');
		}
		
		$mail = new Util\Mail;
		$mail->addTo($get['email']);
		$mail->setFrom('noreply@'.$site['domain']);
		$mail->setSubject($site['name'].' Password Reset');
		$body = '<p>
		Hello '.$get['username'].',
		</p>
		<p>
			A request has been made on '.$site['name'].' to reset your password.
		</p>
		<p>
			<strong>To complete your password reset please <a href="'.$site['url'].'/account/reset/'.$genLink.'">click here</a></strong>.
			This request will be valid for the next two hours.
		</p>
		<p>
			If this was not you, please ignore this email.
		</p>';
		
		$mail->setHTML($body);
		$send = $mail->send();
		if(!$send){
			//throw new \Exception('Error sending password reset');
		}
		return true;
	}
	
	protected function getPassResetForm()
	{
		$form = new UI\Form;
		$form->setSubmitText('Complete Password Reset');
		
		$pass = new UI\Password('password');
		$pass->setLabel('New Password');
		$pass->addAttribute('required');
		$form->add($pass);
		
		$pass2 = new UI\Password('password2');
		$pass2->setLabel('New Password (repeat)');
		$pass2->addAttribute('required');
		$form->add($pass2);	
		
		return $form;
	}
	
	protected function completePassChange($data)
	{
		if(!isset($data['password']) OR trim($data['password']) == ''){
			throw new \Exception('Password');
		}
		if(!isset($data['password2']) OR trim($data['password2']) == ''){
			throw new \Exception('Password');
		}
		if($data['password'] != $data['password2']){
			throw new \Exception('Passwords do not match');
		}
		if(!isset($data['userId'])){
			throw new \Exception('No user set');
		}
		if(!isset($data['resetId'])){
			throw new \Exception('Invalid reset link');
		}

		$hashPass = genPassSalt($data['password']);
		$update = $this->edit('users', $data['userId'], array('password' => $hashPass['hash'], 'spice' => $hashPass['salt']));
		if(!$update){
			throw new \Exception('Error resetting password');
		}
	
		$this->delete('reset_links', $data['resetId']);
		return true;
		
	}	
}
