<?php
class Slick_App_Account_Home_Model extends Slick_Core_Model
{
	public function getLoginForm()
	{
		$form = new Slick_UI_Form;
		
		$username = new Slick_UI_Textbox('username');
		$username->addAttribute('required');
		$username->setLabel('Username');
		$form->add($username);

		$password = new Slick_UI_Password('password');
		$password->addAttribute('required');
		$password->setLabel('Password');
		$form->add($password);	
		
		$hidden = new Slick_UI_Hidden('submit-type');
		$hidden->setValue('login');
		$form->add($hidden);
		
		$remember = new Slick_UI_Checkbox('rememberMe', 'rememberMe');
		$remember->setLabel('Remember Me?');
		$remember->setBool(1);
		$remember->setValue(1);
		$form->add($remember);
		
		if(isset($_GET['r'])){
			$redirect = new Slick_UI_Hidden('r');
			$redirect->setValue($_GET['r']);
			$form->add($redirect);
		}

		return $form;
	}
	
	
	public function getRegisterForm()
	{
		$form = new Slick_UI_Form;
		
		$username = new Slick_UI_Textbox('username');
		$username->addAttribute('required');
		$username->setLabel('Username *');
		$form->add($username);

		$password = new Slick_UI_Password('password');
		$password->addAttribute('required');
		$password->setLabel('Password *');
		$form->add($password);
		
		$email = new Slick_UI_Textbox('email');
		$email->setLabel('Email *');
		$email->addAttribute('required');
		$form->add($email);	

		$hidden = new Slick_UI_Hidden('submit-type');
		$hidden->setValue('register');
		$form->add($hidden);

		return $form;
		
	}
	
	public function registerAccount($data, $noAuth = false)
	{
		if(!isset($data['isAPI'])){
			require_once(SITE_PATH.'/resources/recaptchalib.php');
				$resp = recaptcha_check_answer (CAPTCHA_PRIV,
												$_SERVER["REMOTE_ADDR"],
												$_POST["recaptcha_challenge_field"],
												$_POST["recaptcha_response_field"]);

				if(!$resp->is_valid) {
					throw new Exception('Captcha invalid!');
				}
		}
	
		$req = array('username' => true, 'password' => true, 'email' => true);
		foreach($req as $key => $required){
			if($required AND !isset($data[$key])){
				throw new Exception(ucfirst($key).' required');
			}
		}
		
		$data['username'] = preg_replace('/\s+/', '', $data['username']);
		
		if(trim($data['username']) == ''){
			http_response_code(400);
			throw new Exception('Username required');
		}
		if(trim($data['password']) == ''){
			http_response_code(400);
			throw new Exception('Password required');
		}
		if(!isset($data['email'])){
			$data['email'] = '';
		}
		else{
			$settingsModel = new Slick_App_Account_Settings_Model;
			$checkEmail = $settingsModel->checkEmailInUse(0, $data['email']);
			if($checkEmail){
				throw new Exception('Email already in use');
			}
		}
		if(isset($data['email']) AND trim($data['email']) != '' AND !filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
			http_response_code(400);
			throw new Exception('Invalid email address');
		}
		
		
		$useData = array('username' => $data['username'], 'password' => $data['password']);
		if(isset($data['email'])){
			$useData['email'] = $data['email'];
		}

		if($this->usernameExists($useData['username'])){
			http_response_code(400);
			throw new Exception('Username already taken');
		}
		
		$genPass = genPassSalt($useData['password']);
		$useData['password'] = $genPass['hash'];
		$useData['spice'] = $genPass['salt'];
		$useData['regDate'] = timestamp();
		$useData['slug'] = genURL($data['username']);
		
		$add = $this->insert('users', $useData);
		if(!$add){
			http_response_code(400);
			throw new Exception('Error creating user account');
		}
		
		if(!$noAuth){
			$this->generateAuthToken($add);
		}
		
		//assign them to any default groups
		$getGroups = $this->getAll('groups', array('isDefault' => 1));
		foreach($getGroups as $group){
			$this->insert('group_users', array('userId' => $add, 'groupId' => $group['groupId']));
		}
		
		$meta = new Slick_App_Meta_Model;
		if(!$noAuth){
			$meta->updateUserMeta($add, 'IP_ADDRESS', $_SERVER['REMOTE_ADDR']);
		}
		if(is_array($data['site'])){
			$data['site'] = $data['site']['domain'];
		}
		$meta->updateUserMeta($add, 'site_registered', $data['site']);
		$meta->updateUserMeta($add, 'pubProf', 1);
		$meta->updateUserMeta($add, 'emailNotify', 1);
		
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
		$get = $this->get('users', $username, array('userId'), 'username');
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
		$token = hash('sha256', $get['username'].$get['spice'].time().mt_rand(0,1000));
		
		$update = $this->edit('users', $userId, array('auth' => $token, 'lastAuth' => timestamp()));
		if(!$update){
			return false;
		}
		
		if(!isset($this->api) OR $this->api != true){
			$_SESSION['accountAuth'] = $token;
		}
		
		$this->updateLastActive($userId);
				
		return $token;
	}

	public static function updateLastActive($playerId)
	{
		$model = new Slick_Core_Model;
		$update = $model->edit('users', $playerId, array('lastActive' => timestamp()));
		if(!$update){
			return false;
		}
		return true;
	}
	
	public function checkAuth($data)
	{	
		if(isset($data['authKey'])){
			http_response_code(400);
			throw new Exception('Already logged in!');
		}
		elseif(isset($_SESSION['accountAuth']) AND !isset($data['isAPI'])){
			http_response_code(400);
			throw new Exception('Already logged in!');
		}
		
		if(!isset($data['username'])){
			http_response_code(400);
			throw new Exception('Username required');
		}
		if(!isset($data['password'])){
			http_response_code(400);
			throw new Exception('Password required');
		}
		
		$get = $this->get('users', $data['username'], array(), 'username');
		if(!$get){
			http_response_code(400);
			throw new Exception('Invalid credentials');
		}

		$meta = new Slick_App_Meta_Model;
		
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
					throw new Exception('Trying too many times, please try again later (attempts: '.($getAttempts).')');
				}
			}
		}
		

		$pass = hash('sha256', $get['spice'].$data['password']);
		if($pass != $get['password']){
			http_response_code(400);
			$getAttempts++;
			$meta->updateUserMeta($get['userId'], 'login_attempts', $getAttempts);
			throw new Exception('Invalid credentials');
		}
		
		$token = $this->generateAuthToken($get['userId']);
		if(!$token){
			http_response_code(400);
			$getAttempts++;
			$meta->updateUserMeta($get['userId'], 'login_attempts', $getAttempts);
			throw new Exception('Error authenticating');
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
		$profModel = new Slick_App_Profile_User_Model;
		$getProf = $profModel->getUserProfile($get['userId'], $data['site']['siteId']);

		$getProf['auth'] = $token;
		$getProf['lastActive'] = timestamp();
		
		
		return $getProf;
		
		
	}
	
	public static function userInfo()
	{
		$model = new Slick_Core_Model;
		if(!isset($_SESSION['accountAuth'])){
			if(isset($_COOKIE['rememberAuth'])){
				Slick_App_Account_Home_Controller::logRemembered();
			}
			return false;
		}
		$get = $model->get('users', $_SESSION['accountAuth'], array('userId', 'username', 'email', 'slug',
																		  'regDate', 'auth', 'lastAuth', 'lastActive'), 'auth');
																
		if(!$get){
			return false;
		}
		
		$activeTime = strtotime($get['lastActive']);
		$timeDiff = time() - $activeTime;
		$getSite = $model->get('sites', $_SERVER['HTTP_HOST'], array(), 'domain');
		/*if($timeDiff > 7200){
			$controller = new Slick_Core_Controller;
			$model->edit('users', $get['userId'], array('auth' => ''));
			unset($_SESSION['accountAuth']);
			if(isset($_COOKIE['rememberAuth'])){
				setcookie('rememberAuth', '', time()-3600,'/');
			}
			$controller->redirect($getSite['url'].'/account');
			
			die();
		}*/
		
		$meta = $model->getAll('user_meta', array('userId' => $get['userId']));
		$get['meta'] = array();
		foreach($meta as $row){
			$get['meta'][$row['metaKey']] = $row['metaValue'];
		}
		
		$getRef = $model->get('user_referrals', $get['userId'], array('affiliateId'), 'userId');
		$get['affiliate'] = false;
		if($getRef){
			$getAffiliate = $model->get('users', $getRef['affiliateId'], array('userId', 'username', 'slug'), 'userId');
			if($getAffiliate){
				$get['affiliate'] = $getAffiliate;
			}
		} 
		
		$get['groups'] = $model->fetchAll('SELECT g.name, g.groupId	
										   FROM group_users u
										   LEFT JOIN groups g ON g.groupId = u.groupId
										   LEFT JOIN group_sites s ON s.groupId = g.groupId
										   WHERE u.userId = :id AND s.siteId = :siteId', array(':id' => $get['userId'], ':siteId' => $getSite['siteId']));
		

		$model->edit('users', $get['userId'], array('lastActive' => timestamp()), 'userId');
		
		return $get;
		
	}
	
	public static function getUsersOnline()
	{
		$model = new Slick_Core_Model;
		$get = $model->fetchSingle('SELECT COUNT(*) as total FROM users
									WHERE  ('.time().' - UNIX_TIMESTAMP(lastActive)) < 7200');
		
		if(!$get){
			return false;
		}
		
		$meta = new Slick_App_Meta_Model;
		$mostOnline = $meta->getStat('mostOnline');
		if($get['total'] > $mostOnline){
			$meta->updateStat('mostOnline', $get['total']);
		}
		return $get['total'];
		
	}
	
	public static function getMostOnline()
	{
		$meta = new Slick_App_Meta_Model;
		$mostOnline = $meta->getStat('mostOnline');
		return $mostOnline;
	}
	
	public static function getOnlineUsers()
	{
		$model = new Slick_App_Profile_User_Model;

		$getUsers = $model->fetchAll('SELECT userId FROM users
									WHERE auth != "" AND ('.time().' - UNIX_TIMESTAMP(lastActive)) < 7200');
		
		$site = $model->get('sites', $_SERVER['HTTP_HOST'], array(), 'domain');
		
		foreach($getUsers as $key => $user){
			$user = $model->getUserProfile($user['userId'], $site['siteId']);
			$user['link'] = '<a href="'.$site['url'].'/profile/user/'.$user['slug'].'">'.$user['username'].'</a>';

			$getUsers[$key] = $user;
		}
		
		return $getUsers;
		
	}
	
	public static function getUserPostCount($userId)
	{
		$model = new Slick_Core_Model;
		$totalPosts = 0;
		$forumApp = $model->get('apps', 'forum', array('appId'), 'slug');
		if($forumApp){
			$numTopics = $model->count('forum_topics', 'userId', $userId);
			$numReplies = $model->count('forum_posts', 'userId', $userId);
			$totalPosts += $numTopics + $numReplies;
		}
		
		$blogApp = $model->get('apps', 'blog', array('appId'), 'slug');
		if($blogApp){
			$numComments = $model->count('blog_comments', 'userId', $userId);
			$numPosts = $model->count('blog_posts', 'userId', $userId);
			$totalPosts += $numComments + $numPosts;
		}
		
		
		return $totalPosts;
	}
	

}
