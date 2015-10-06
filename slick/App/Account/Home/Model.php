<?php
namespace App\Account;
use Core, UI, Util, API, App\Profile;
class Home_Model extends Core\Model
{
	public static $activity_updated = false;
	
	public function getLoginForm()
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
	
	
	public function getRegisterForm()
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
	
	public function registerAccount($data, $noAuth = false)
	{
		if(!isset($data['isAPI'])){
			require_once(SITE_PATH.'/resources/recaptchalib2.php');
			$recaptcha = new \ReCaptcha(CAPTCHA_PRIV);
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
			}
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

		if($this->usernameExists($useData['username'])){
			http_response_code(400);
			throw new \Exception('Username already taken');
		}
		
		$genPass = genPassSalt($useData['password']);
		$useData['password'] = $genPass['hash'];
		$useData['spice'] = $genPass['salt'];
		$useData['regDate'] = timestamp();
		$useData['slug'] = genURL($data['username']);
		$useData['slug'] = $this->checkSlugExists($useData['slug']);
		
		//generate activation code
		$useData['activate_code'] = hash('sha256', time().$genPass['salt'].$useData['slug'].mt_rand(0,1000));
		
		$add = $this->insert('users', $useData);
		if(!$add){
			http_response_code(400);
			throw new \Exception('Error creating user account');
		}
		
		if(!$noAuth){
			//disable auto logging in when registering
			//$this->generateAuthToken($add);
		}
		
		$getSite = $this->getAll('sites', array('isDefault' => 1));
		$getSite = $getSite[0];
		
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
		
		if(isset($_SESSION['affiliate-ref'])){
			$getLink = $this->get('user_meta', $_SESSION['affiliate-ref'], array('userId'), 'metaValue');
			if($getLink){
				$this->insert('user_referrals', array('userId' => $add, 'affiliateId' => $getLink['userId'], 'refTime' => timestamp()));
				unset($_SESSION['affiliate-ref']);
			}
		}
		
		return $add;

	}
	
	public function usernameExists($username)
	{
		$get = $this->fetchSingle('SELECT userId FROM users WHERE LOWER(username) = :username', array(':username' => strtolower(trim($username))));
		if($get){
			return true;
		}
		return false;
	}
	
	public function generateAuthToken($userId)
	{
		$get = $this->get('users', $userId);
		if(!$get){
			return false;
		}
		$token = hash('sha256', $get['username'].$get['spice'].$_SERVER['REMOTE_ADDR'].time().mt_rand(0,1000));
		$makeSession = $this->makeSession($userId, $token);
		if(!$makeSession){
			return false;
		}
		$update = $this->edit('users', $userId, array('lastAuth' => timestamp()));
		if(!$update){
			return false;
		}
		if(!isset($this->api) OR $this->api != true){
			$_SESSION['accountAuth'] = $token;
		}
		Home_Model::updateLastActive($userId);
		return $token;
	}

	public static function updateLastActive($userId)
	{
		if(!self::$activity_updated){
			$model = new Home_Model;
			$auth = false;
			if(isset($_SERVER['HTTP_X_AUTHENTICATION_KEY'])){
				$auth = $_SERVER['HTTP_X_AUTHENTICATION_KEY'];
			}
			elseif(isset($_SESSION['accountAuth'])){
				$auth = $_SESSION['accountAuth'];
			}
			if(!$auth){
				return false;
			}
			$getSesh = $model->checkSession($auth);
			if($getSesh){
				$time = timestamp();
				$update = $model->edit('user_sessions', $getSesh['sessionId'], array('lastActive' => $time));
				if($update){
					$editUser = $model->edit('users', $getSesh['userId'], array('lastActive' => $time));
					self::$activity_updated = true;
					return true;
				}
			}
			return false;
		}
		return true;
	}

	public function checkAuth($data)
	{	
		if(isset($data['authKey'])){
			http_response_code(400);
			throw new \Exception('Already logged in!');
		}
		elseif(isset($_SESSION['accountAuth']) AND !isset($data['isAPI'])){
			http_response_code(400);
			throw new \Exception('Already logged in!');
		}
		
		$checkPassword = true;
		$meta = new \App\Meta_Model;
		$get = false;
		if(app_enabled('tokenly') AND isset($data['address']) AND isset($data['signed_message'])){
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
		$getAttempts = $this->getLoginAttempts($get);
				
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
		
		$token = $this->generateAuthToken($get['userId']);
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
	
	public function getLoginAttempts($get)
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
	
	public static function userInfo($userId = false)
	{
		$model = new Home_Model;
		if(!$userId AND !isset($_SESSION['accountAuth'])){
			if(isset($_COOKIE['rememberAuth'])){
				Home_Controller::logRemembered();
			}
			return false;
		}
		
		if(!$userId){
			$get = $model->checkSession($_SESSION['accountAuth']);
		}
		else{
			$get = $model->get('users', $userId);
		}
												
		if(!$get){
			return false;
		}
		
		$user = $model->get('users', $get['userId'], array('userId', 'username', 'email', 'slug', 'regDate', 'lastAuth', 'lastActive'));
		$user['auth'] = $get['auth'];
		
		$activeTime = strtotime($get['lastActive']);
		$timeDiff = time() - $activeTime;
		$getSite = currentSite();
		/*if($timeDiff > 7200){
			$model->edit('users', $get['userId'], array('auth' => ''));
			unset($_SESSION['accountAuth']);
			if(isset($_COOKIE['rememberAuth'])){
				setcookie('rememberAuth', '', time()-3600,'/');
			}
			redirect($getSite['url'].'/account');
		}*/
		
		$meta = $model->getAll('user_meta', array('userId' => $get['userId']));
		$user['meta'] = array();
		foreach($meta as $row){
			$user['meta'][$row['metaKey']] = $row['metaValue'];
		}
		
		$getRef = $model->get('user_referrals', $get['userId'], array('affiliateId'), 'userId');
		$user['affiliate'] = false;
		if($getRef){
			$getAffiliate = $model->get('users', $getRef['affiliateId'], array('userId', 'username', 'slug'), 'userId');
			if($getAffiliate){
				$user['affiliate'] = $getAffiliate;
			}
		} 
		
		$user['groups'] = $model->fetchAll('SELECT g.name, g.groupId, g.displayName, g.displayView, g.displayRank, g.isSilent as silent
										   FROM group_users u
										   LEFT JOIN groups g ON g.groupId = u.groupId
										   LEFT JOIN group_sites s ON s.groupId = g.groupId
										   WHERE u.userId = :id AND s.siteId = :siteId
										   ORDER  BY g.displayRank DESC, g.displayName ASC, g.name ASC
										   ', array(':id' => $get['userId'], ':siteId' => $getSite['siteId']));
		$user['primary_group'] = false;
		$primary_found = false;
		foreach($user['groups'] as $gk => $gv){
			if(trim($gv['displayName']) == ''){
				$user['groups'][$gk]['displayName'] = $gv['name'];
			}
			if(!$primary_found AND $gv['silent'] == 0){
				$user['primary_group'] = $gv;
				$primary_found = true;
			}
		}
		
		Home_Model::updateLastActive($get['userId']);
		
		return $user;
		
	}
	
	public static function getUsersOnline()
	{
		$model = new Core\Model;
		$sql= 'SELECT COUNT(*) as total FROM users
									WHERE  ('.time().' - UNIX_TIMESTAMP(lastActive)) < 7200';
		$get = $model->fetchSingle($sql);
		if(!$get){
			return false;
		}
		
		$meta = new \App\Meta_Model;
		$mostOnline = $meta->getStat('mostOnline');
		if($get['total'] > $mostOnline){
			$meta->updateStat('mostOnline', $get['total']);
		}
		return $get['total'];
		
	}
	
	public static function getMostOnline()
	{
		$meta = new \App\Meta_Model;
		$mostOnline = $meta->getStat('mostOnline');
		return $mostOnline;
	}
	
	public static function getOnlineUsers()
	{
		$model = new Profile\User_Model;

		$getUsers = $model->fetchAll('SELECT userId FROM user_sessions
									WHERE auth != "" AND ('.time().' - UNIX_TIMESTAMP(lastActive)) < 7200
									GROUP BY userId');
		
		$site = currentSite();
		
		foreach($getUsers as $key => $user){
			$user = $model->getUserProfile($user['userId'], $site['siteId']);
			$user['link'] = '<a href="'.$site['url'].'/profile/user/'.$user['slug'].'">'.$user['username'].'</a>';

			$getUsers[$key] = $user;
		}
		
		return $getUsers;
		
	}
	
	public static function getUserPostCount($userId)
	{
		$model = new Core\Model;
		$totalPosts = 0;
		$forumApp = $model->get('apps', 'forum', array('appId'), 'slug');
		if($forumApp){
			$numTopics = $model->fetchSingle('SELECT count(*) as total FROM forum_topics WHERE userId = :userId AND buried = 0',
											array(':userId' => $userId));
			$numTopics = $numTopics['total'];
			$numReplies = $model->fetchSingle('SELECT count(*) as total FROM forum_posts WHERE userId = :userId AND buried = 0',
											array(':userId' => $userId));
			$numReplies = $numReplies['total'];
			$totalPosts += $numTopics + $numReplies;
		}
		
		return $totalPosts;
	}
	
	public function checkSlugExists($slug, $ignore = 0, $count = 0)
	{
		$useslug = $slug;
		if($count > 0){
			$useslug = $slug.'-'.$count;
		}
		$get = $this->get('users', $useslug, array('userId', 'slug'), 'slug');
		if($get AND $get['userId'] != $ignore){
			//slug exists already, search for next level of slug
			$count++;
			return $this->checkSlugExists($slug, $ignore, $count);
		}
		
		if($count > 0){
			$slug = $slug.'-'.$count;
		}

		return $slug;
	}
	
	public function findSession($userId, $ip)
	{
		$get = $this->fetchSingle('SELECT * FROM user_sessions WHERE userId = :id AND IP = :IP ORDER BY sessionId DESC LIMIT 1',
								array(':id' => $userId, ':IP' => $ip));
		if(!$get){
			return false;
		}
		return $get;
	}
	
	public function checkSession($auth, $useCache = false)
	{
		$get = $this->fetchSingle('SELECT * FROM user_sessions WHERE auth = :auth ORDER BY sessionId DESC LIMIT 1',
									array(':auth' => $auth), 0, $useCache);
		if($get){
			return $get;
		}
		return false;
	}
	
	public function clearSession($auth)
	{
		$getSesh = $this->checkSession($auth);
		if(!$getSesh){
			return false;
		}
		$this->edit('users', $getSesh['userId'], array('lastActive' => null));
		return $this->delete('user_sessions', $getSesh['sessionId']);
	}
	
	public function countSessions($userId = 0)
	{
		if($userId > 0){
			return $this->count('user_sessions', 'userId', $userId);
		}
		return $this->count('user_sessions');
	}
	
	public function getSessions($userId = 0)
	{
		$wheres = array();
		if($userId > 0){
			$wheres['userId'] = $userId;
		}
		return $this->getAll('user_sessions', $wheres, array(), 'sessionId');
	}
	
	public function makeSession($userId, $token)
	{
		$check = $this->checkSession($token);
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
}
