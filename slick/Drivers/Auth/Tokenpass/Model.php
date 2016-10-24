<?php
namespace Drivers\Auth;
use Core, UI, Util, App\Profile, App\Account\Settings_Model, App\Meta_Model;
use Tokenly\TokenpassClient\TokenpassAPI;

class Tokenpass_Model extends Core\Model implements \Interfaces\AuthModel
{
	public static $activity_updated = array();
	
	function __construct()
	{
		parent::__construct();
		$this->oauth_url = TOKENPASS_URL.'/oauth';
		$this->scopes = array('user', 'tca', 'private-address');
		$this->tokenpass = new TokenpassAPI;
	}
	
	public static function userInfo($userId = false)
	{
		$model = new Tokenpass_Model;
		$sesh_auth = Util\Session::get('accountAuth');
		if(!$userId AND !$sesh_auth){
			return false;
		}
		
		$self_info = false;
		if(!$userId){
			$self_info = true;
			$get = $model->checkSession($sesh_auth);
		}
		else{
			$get = $model->get('users', $userId);
		}
												
		if(!$get){
			return false;
		}
		
		$user = $model->get('users', $get['userId'], array('userId', 'username', 'email', 'slug', 'regDate', 'lastAuth', 'lastActive'));
		$user['auth'] = $get['auth'];
		
		$getSite = currentSite();

		$meta = new \App\Meta_Model;
		$user['meta'] = $meta->userMeta($get['userId']);

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
		
		if($self_info){
			Tokenpass_Model::updateLastActive($get['userId']);
		}
		
		return $user;
	}
	
	public function checkAuth($data)
	{
		if(!isset($data['username']) OR trim($data['username']) == ''){
			throw new \Exception('Username required');
		}
		
		if(!isset($data['password']) OR trim($data['password']) == ''){
			throw new \Exception('Password required');
		}		
		
		$request_url = $this->getAuthUrl(true);

		$params = array(
			'username' => $data['username'],
			'password' => $data['password'],
			'grant_access' => 1,
		);
		

		$ch = curl_init($request_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

		$response = json_decode(curl_exec($ch), true);
		curl_close($ch);		

		if(!is_array($response)){
			throw new \Exception('Error authenticating');
		}
		
		if(isset($response['error'])){
			throw new \Exception($response['error']);
		}
        
        if(isset($data['no_token']) AND $data['no_token']){
            return true;
        }
		
		$token = $this->container->getAuthToken($response['code']);
		if(!$token){
			throw new \Exception('Error retrieving access token');
		}
		
		$oauth_user = $this->container->getOAuthUser($token);
		if(!$oauth_user){
			throw new \Exception('Error getting user data');
		}
		
		$get_user = $this->container->findTokenPassUser($oauth_user['id']);
		if($get_user){
			//in system already
			$userId = $get_user['userId'];
		}
		else{
			//check if username and email already in system... merge or create new account
			$mergable = $this->container->findMergableUser($oauth_user);
			if($mergable){
				//merge existing account
				$meta = new Meta_Model;
				$meta->updateUserMeta($mergable['userId'], 'tokenly_uuid', $oauth_user['id']);
				$userId = $mergable['userId'];
			}
			else{
				//create new account
				$gen_user = $this->container->generateUser($oauth_user);
				$userId = $gen_user;
			}
		}
		$this->container->makeSession($userId, $token);
		
		$profModel = new Profile\User_Model;
		$site = currentSite();
		$getProf = $profModel->getUserProfile($userId, $site['siteId']);

		$getProf['auth'] = $token;
		$getProf['lastActive'] = timestamp();
		
		return $getProf;
	}
	
	public function checkSession($auth, $useCache = true)
	{
		if($useCache){
			$cached = static_cache('sesh_'.$auth);
			if($cached){
				return $cached;
			}
		}
		$get = $this->fetchSingle('SELECT * FROM user_sessions WHERE auth = :auth ORDER BY sessionId DESC LIMIT 1',
									array(':auth' => $auth));
		if($get){
			static_cache('sesh_'.$auth, $get);
			return $get;
		}
		return false;
	}
	
	public function clearSession($auth)
	{
		$getSesh = $this->container->checkSession($auth);
		if(!$getSesh){
			return false;
		}
		$this->edit('users', $getSesh['userId'], array('lastActive' => null));
		Util\Session::clear('accountAuth');
		if(isset($_COOKIE['rememberAuth'])){
			setcookie('rememberAuth', '', time()-3600,'/');
		}		
		$delete = $this->delete('user_sessions', $getSesh['sessionId']);
		if(!$delete){
			return false;
		}
		$logout_url = TOKENPASS_URL.'/api/v1/oauth/logout?client_id='.TOKENPASS_CLIENT.'&token='.$auth;
		$tokenpass_logout = json_decode(@file_get_contents($logout_url), true);
		if(!is_array($tokenpass_logout) OR !$tokenpass_logout['result']){
			return false;
		}
		return true;
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
		$this->edit('users', $userId, array('auth' => $token, 'lastAuth' => $time, 'lastActive' => $time));
		Util\Session::set('accountAuth', $token);
		return true;
	}
	
	protected function setState()
	{
		$state = hash('sha256', microtime().':'.mt_rand(0, 1000).':'.$_SERVER['REMOTE_ADDR']);
		Util\Session::set('auth_state', $state);
		return $state;
	}
	
	protected function getState()
	{
		return Util\Session::get('auth_state');
	}
	
	protected function getAuthUrl($api = false)
	{
		$state = $this->container->setState();
		$client_id = TOKENPASS_CLIENT;
		$scope = join(',', $this->scopes);
		$site = currentSite();
		$account_app = get_app('account');
		$auth_module = get_app('account.auth');
		$redirect = $site['url'].'/'.$account_app['url'].'/'.$auth_module['url'].'/callback';
		$query = array('state' => $state, 'client_id' => $client_id, 'scope' => $scope, 'redirect_uri' => $redirect,
						'response_type' => 'code');
		if($api){
			$auth_url = TOKENPASS_URL.'/api/v1/oauth/request?'.http_build_query($query);
		}
		else{
			$auth_url = $this->oauth_url.'/authorize?'.http_build_query($query);
		}
		return $auth_url;
	}
	
	protected function getAuthToken($code)
	{
		$url = $this->oauth_url.'/access-token';
		
		$site = currentSite();
		$account_app = get_app('account');
		$auth_module = get_app('account.auth');
		$redirect = $site['url'].'/'.$account_app['url'].'/'.$auth_module['url'].'/callback';		
	
		$params = array(
			'grant_type' => 'authorization_code',
			'code' => $code,
			'client_id' => TOKENPASS_CLIENT,
			'client_secret' => TOKENPASS_SECRET,
			'redirect_uri' => $redirect,
		);
		

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

		$response = json_decode(curl_exec($ch), true);
		curl_close($ch);		
		
		if(!$response OR isset($response['error']) OR !isset($response['access_token'])){
			return false;
		}
		
		return $response['access_token'];
	}
	
	protected function getOAuthUser($token)
	{
		$url = $this->oauth_url.'/user';
		$params = array('client_id' => TOKENPASS_CLIENT, 'access_token' => $token);
		$url .= '?'.http_build_query($params);
		$get = json_decode(@file_get_contents($url), true);
		if(!isset($get['id'])){
			return false;
		}
		return $get;		
	}
	
	protected function findTokenPassUser($uuid)
	{
		$get = $this->fetchSingle('SELECT userId FROM user_meta WHERE metaKey = "tokenly_uuid" AND metaValue = :value',
								   array(':value' => $uuid));
		if(!$get){
			return false;
		}
		$getUser = $this->get('users', $get['userId'], array('userId', 'username', 'email', 'activated'));
		return $getUser;
	}
	
	protected function findMergableUser($oauth_data)
	{
		$get = $this->fetchSingle('SELECT userId, username, email, activated FROM users
									WHERE username = :username AND email = :email',
								array(':username' => $oauth_data['username'], ':email' => $oauth_data['email']));	
		return $get;
	}
	
	protected static function updateLastActive($userId)
	{
		if(!isset(self::$activity_updated[$userId])){
			$model = new Tokenpass_Model;
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
						self::$activity_updated[$userId] = true;
						return true;
					}
				}
			}
			return false;
		}
		return true;
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
	
	protected function generateUser($data)
	{
		//check username is taken
		$get = $this->get('users', $data['username'], array(), 'username');
		$meta = new \App\Meta_Model;
		if($get){
			//throw new \Exception('Username already taken by other user in system');
			//merge account and just let them through - tempory during migration
			$meta->updateUserMeta($get['userId'], 'tokenly_uuid', $data['id']);
			return $get['userId'];
		}
		
		///set up user data
		$time = timestamp();
		$useData = array();
		$useData['username'] = $data['username'];
		$useData['password'] = 'tokenpass';
		$useData['spice'] = 'tokenpass';
		$useData['email'] = $data['email'];
		$useData['regDate'] = $time;
		$useData['lastAuth'] = $time;
		$useData['lastActive'] = $time;
		$useData['slug'] = genURL($data['username']);
		$useData['slug'] = $this->container->checkSlugExists($useData['slug']);
		$useData['activated'] = 1;
		
		$add = $this->insert('users', $useData);
		if(!$add){
			throw new \Exception('Error saving user');
		}
		
		//assign them to any default groups
		$getGroups = $this->getAll('groups', array('isDefault' => 1));
		foreach($getGroups as $group){
			$this->insert('group_users', array('userId' => $add, 'groupId' => $group['groupId']));
		}
		
		//assign meta data
		
		$site = currentSite();

		$meta->updateUserMeta($add, 'IP_ADDRESS', $_SERVER['REMOTE_ADDR']);
		$meta->updateUserMeta($add, 'site_registered', $site['domain']);
		$meta->updateUserMeta($add, 'pubProf', 1);
		$meta->updateUserMeta($add, 'emailNotify', 1);		
		$meta->updateUserMeta($add, 'tokenly_uuid', $data['id']);
		
		return $add;
	}
	
	
	public function registerAccount($data)
	{	
		$data = $this->apply_pre_mods('registerAccount', array($data));
		$data = $data[0];	
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
		
		if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
			http_response_code(400);
			throw new \Exception('Invalid email');
		}
		
		$settingsModel = new Settings_Model;
		$checkEmail = $settingsModel->checkEmailInUse(0, $data['email']);
		if($checkEmail){
			throw new \Exception('Email already in use');
		}		
		
		if($this->container->usernameExists($data['username'])){
			http_response_code(400);
			throw new \Exception('Username already taken');
		}		
		
		$tokenpass_register = $this->tokenpass->registerAccount($data['username'], $data['password'], $data['email']);
		if(!$tokenpass_register OR !isset($tokenpass_register['id'])){
			http_response_code(400);
			throw new \Exception('Error registering with TokenPass');
		}
		
		$userData = array();
		$userData['username'] = $data['username'];
		$userData['email'] = $data['email'];
		$userData['password'] = 'tokenpass';
		$userData['spice'] = 'tokenpass';
		$userData['regDate'] = timestamp();
		$userData['slug'] = genURL($data['username']);
		$userData['slug'] = $this->container->checkSlugExists($userData['slug']);
		$userData['activated'] = 1;
		
		$add = $this->insert('users', $userData);
		if(!$add){
			http_response_code(400);
			throw new \Exception('Error saving account');
		}
		
		//assign them to any default groups
		$getGroups = $this->getAll('groups', array('isDefault' => 1));
		foreach($getGroups as $group){
			$this->insert('group_users', array('userId' => $add, 'groupId' => $group['groupId']));
		}
		
		$meta = new \App\Meta_Model;
		$site = currentSite();
		$meta->updateUserMeta($add, 'site_registered', $site['domain']);
		$meta->updateUserMeta($add, 'pubProf', 1);
		$meta->updateUserMeta($add, 'emailNotify', 1);		
		$meta->updateUserMeta($add, 'tokenly_uuid', $tokenpass_register['id']);		
		$add = $this->apply_post_mods('registerAccount', $add, array($data));
		return $add;

	}	
	
	protected function usernameExists($username)
	{
		$get = $this->fetchSingle('SELECT userId FROM users WHERE LOWER(username) = :username', array(':username' => strtolower(trim($username))));
		if($get){
			return true;
		}
		return false;
	}		
	
	public function updateAccount($id, $data)
	{
		$getUser = $this->get('users', $id);
		$meta = new Meta_Model;
		$tokenly_uuid = $meta->getUserMeta($id, 'tokenly_uuid');
		if(!$tokenly_uuid){
			return false;
		}
		if(!isset($data['curPassword'])){
			throw new \Exception('Current password required to make changes');
		}
		$token = $getUser['auth'];
		
		$useData = array();
		if(isset($data['email']) AND trim($data['email']) != ''){
			$useData['email'] = $data['email'];
		}
		if(isset($data['password']) AND trim($data['password']) != ''){
			$useData['password'] = $data['password'];
		}		
		
		if(count($useData) > 0){
			try{
				$update = $this->tokenpass->updateAccount($tokenly_uuid, $token, $data['curPassword'], $useData);
			}
			catch(\Exception $e){
				$update = false;
			}
		}
		
		return true;
	}	
	
	protected function syncAddresses($user)
	{
		try{
			$get = $this->tokenpass->getAddressesForAuthenticatedUser($user['auth']);
		}
		catch(\Exception $e){
			return false;
		}
		if(!is_array($get)){
			return false;
		}
		$current = $this->getAll('coin_addresses', array('userId' => $user['userId']));
		
		//remove any that don't exist on their account first
		$to_delete = array();
		if($current){
			foreach($current as $c_row){
				$found = false;
				foreach($get as $row){
					if($row['address'] == $c_row['address']){
						$found = true;
						break;
					}
				}
				if(!$found){
					$to_delete[] = $c_row['addressId'];
				}
			}
		}
		foreach($to_delete as $addressId){
			$this->delete('coin_addresses', $addressId);
		}
		
		//add new addresses
		$time = timestamp();
		foreach($get as $row){
			$found = false;
			foreach($current as $c_row){
				if($c_row['address'] == $row['address']){
					$found = true;
					break;
				}
			}
			if(!$found){
				$address = array();
				$address['userId'] = $user['userId'];
				$address['address'] = $row['address'];
				$address['type'] = 'btc';
				$address['submitDate'] = $time;
				$address['verified'] = 1;
				$address['public'] = intval($row['public']);
				$address['isXCP'] = 1;
				$add = $this->insert('coin_addresses', $address);
				if($add){
					if(is_array($row['balances'])){
						foreach($row['balances'] as $asset => $amnt){
							$balance = array();
							$balance['addressId'] = $add;
							$balance['asset'] = $asset;
							$balance['balance'] = round($amnt / SATOSHI_MOD, 8);
							$balance['lastChecked'] = $time;
							$this->insert('xcp_balances', $balance);
						}
					}
				}
			}
		}
		$inv = new \App\Tokenly\Inventory_Model;
		$inv->getUserBalances($user['userId'], false, 'btc', true);
		return true;
	}
	
}
