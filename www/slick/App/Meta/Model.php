<?php
class Slick_App_Meta_Model extends Slick_Core_Model
{
	public static $appMeta = array();
	public static $userMeta = array();
	
	public function updateUserMeta($userId, $key, $value)
	{
		$get = $this->getUserMeta($userId, $key, 1);
		if(!$get){
			//create new row
			$update = $this->insert('user_meta', array('userId' => $userId, 'metaKey' => $key, 'metaValue' => $value));
		}
		else{
			$update = $this->edit('user_meta', $get['metaId'], array('metaValue' => $value));
		}
		
		if(!$update){
			return false;
		}
		return true;
	}
	

	
	public function updateStat($key, $value, $label = '')
	{
		$get = $this->getStat($key, 1);
		if(!$get){
			//create new row
			$values = array('statKey' => $key, 'statValue' => $value);
			if($label != ''){
				$values['label'] = $label;
			}
			$update = $this->insert('stats', $values);
		}
		else{
			$values = array('statValue' => $value);
			if($label != ''){
				$values['label'] = $label;
			}
			$update = $this->edit('stats', $get['statId'], $values);
		}
		
		if(!$update){
			return false;
		}
		return true;
	}
	
	public function getStat($key, $fullData = 0)
	{
		$get = $this->fetchSingle('SELECT * FROM stats WHERE statKey = :key', array(':key' => $key));
		if(!$get){
			return false;
		}
		if($fullData != 0){
			return $get;
		}
		return $get['statValue'];
	}
	
	public function getUserMeta($userId, $key, $fullData = 0)
	{
		if($fullData == 0 AND isset(self::$userMeta[$userId][$key])){
			return self::$userMeta[$userId][$key];
		}
		elseif($fullData == 0){
			if(!isset(self::$userMeta[$userId])){
				$this->userMeta($userId);
				if(!isset(self::$userMeta[$userId][$key])){
					return false;
				}
				return self::$userMeta[$userId][$key];
			}
		}
		
		$get = $this->fetchSingle('SELECT * FROM user_meta WHERE userId = :id AND metaKey = :key', array(':id' => $userId, ':key' => $key));
		if(!$get){
			return false;
		}
		if($fullData != 0){
			return $get;
		}


		self::$userMeta[$userId][$key] = $get['metaValue'];		
		
		return $get['metaValue'];
	}
	

	public function userMeta($userId)
	{
		if(isset(self::$userMeta[$userId])){
			return self::$userMeta[$userId];
		}
		
		$getAll = $this->getAll('user_meta', array('userId' => $userId));
		$output = array();
		foreach($getAll as $row){
			$output[$row['metaKey']] = $row['metaValue'];
		}
		
		self::$userMeta[$userId] = $output;
		
		return $output;
	}
	
	public function appMeta($appId)
	{
		$getApp = $this->get('apps', $appId);
		if(!$getApp){
			$getApp = $this->get('apps', $appId, array(), 'slug');
			if(!$getApp){
				return false;
			}
		}
		$appId = $getApp['appId'];
		if(isset(self::$appMeta[$appId])){
			return self::$appMeta[$appId];
		}
		
		$getAll = $this->getAll('app_meta', array('appId' => $appId));
		$output = array();
		foreach($getAll as $key => $row){
			$output[$row['metaKey']] = $row['metaValue'];
		}
		
		self::$appMeta[$appId] = $output;
		return $output;
	}
	
	public function getAppMeta($appId, $key, $fullData = 0)
	{
		if($fullData == 0 AND isset(self::$appMeta[$appId][$key])){
			return self::$appMeta[$appId][$key];
		}
		elseif($fullData == 0){
			if(!isset(self::$appMeta[$appId])){
				$this->appMeta($appId);
				if(!isset(self::$appMeta[$appId][$key])){
					return false;
				}				
				return self::$appMeta[$appId][$key];
			}			
		}
		
		$get = $this->fetchSingle('SELECT * FROM app_meta WHERE appId = :id AND metaKey = :key',
									array(':id' => $appId, ':key' => $key));
		if(!$get){
			return false;
		}
		
		if($fullData != 0){
			return $get;
		}
		
		self::$appMeta[$appId][$key] = $get['metaValue'];
		
		return $get['metaValue'];
		
	}
	
	public function updateAppMeta($appId, $key, $value, $label = '', $isSetting = 0, $type = '')
	{
		$get = $this->getAppMeta($appId, $key, 1);
		if(!$get){
			//create new row
			if(trim($type) == ''){
				$type = 'textbox';
			}
			$update = $this->insert('app_meta', array('appId' => $appId, 'metaKey' => $key, 'metaValue' => $value, 'type' => $type,
														'label' => $label, 'isSetting' => $isSetting));
		}
		else{
			if(trim($label) == ''){
				$label = $get['label'];
			}
			if(trim($type) == ''){
				$type = $get['type'];
			}
			$update = $this->edit('app_meta', $get['appMetaId'], array('metaValue' => $value, 'type' => $type,
								  'label' => $label, 'isSetting' => $isSetting));
		}
		
		if(!$update){
			return false;
		}
		return true;
		
	}
	
	public static function getUserAppPerms($userId, $appId)
	{
		$model = new Slick_App_Meta_Model;
		$app = $model->get('apps',$appId, array('appId'));
		if(!$app){
			$app = $model->get('apps', $appId, array('appId'), 'slug');
			if(!$app){
				return false;
			}
		}
		
		$groups = $model->getAll('group_users', array('userId' => $userId));
		$perms = array();
		$getPerms = $model->getAll('app_perms', array('appId' => $app['appId']));
		foreach($getPerms as $perm){
			$perms[$perm['permKey']] = false;
			foreach($groups as $group){
				$getGroupPerms = $model->getAll('group_perms', array('groupId' => $group['groupId']));
				foreach($getGroupPerms as $gPerm){
					if($gPerm['permId'] == $perm['permId']){
						$perms[$perm['permKey']] = true;
						break 2;
					}
				}
			}
		}
		
		return $perms;
	}
	
	public function getAppPerm($appId, $key)
	{
		$getPerms = $this->getAll('app_perms', array('appId' => $appId, 'permKey' => $key));
		if(count($getPerms) == 0){
			return false;
		}
		return $getPerms[0];
	}
	
	public function addAppPerm($appId, $key)
	{
		$getPerm = $this->getAppPerm($appId, $key);
		if(!$getPerm){
			$add = $this->insert('app_perms', array('appId' => $appId, 'permKey' => $key));
			if(!$add){
				return false;
			}
			return true;
		}
		return false;
	}
	
	public static function notifyUser($userId, $message, $itemId = 0, $type = '', $allowDupe = false, $data = array())
	{
		$model = new Slick_Core_Model;
		if($itemId != 0 AND $type != '' AND !$allowDupe){
			$checkItem = Slick_App_Meta_Model::checkItemNotified($userId, $itemId, $type);
			if($checkItem){
				return false;
			}
		}
		
		$getSite = $model->get('sites', $_SERVER['HTTP_HOST'], array(), 'domain');
		
		//attempt to find a message template, if not, assume custom message
		$messageFile = str_replace('.', '/', trim($message));
		$getFile = is_file(SITE_PATH.'/themes/views/'.$messageFile.'.php');
		$messageOutput = $message;
		$notificationMessageOutput = $messageOutput;
		if($getFile){
			ob_start();
			include(SITE_PATH.'/themes/views/'.$messageFile.'.php');
			$messageOutput = ob_get_contents();
			ob_end_clean();

			// try a separate view for the notification
			$notificationMessageOutput = $messageOutput;
			$message_paths = explode('.', trim($message));
			if ($message_paths[0] == 'emails') {
				$message_paths[0] = 'notifications';
				$notificationMessageFile = implode('/', $message_paths);
				if (is_file(SITE_PATH.'/themes/views/'.$notificationMessageFile.'.php')) {
					ob_start();
					include(SITE_PATH.'/themes/views/'.$notificationMessageFile.'.php');
					$notificationMessageOutput = ob_get_contents();
					ob_end_clean();
				}
			}
		}
	
		$meta = new Slick_App_Meta_Model;
		$notifyEmail = $meta->getUserMeta($userId, 'emailNotify');
		if($notifyEmail AND $notifyEmail == 1){
			$getUser = $model->get('users', $userId, array('username', 'email'));

			if(filter_var($getUser['email'], FILTER_VALIDATE_EMAIL)){
				$template = '[MESSAGE]';
				$getTemplate = is_file(SITE_PATH.'/themes/views/emails/template.php');
				if($getTemplate){
					ob_start();
					include(SITE_PATH.'/themes/views/emails/template.php');
					$template = ob_get_contents();
					ob_end_clean();
				}
				$body = str_replace('[MESSAGE]', $messageOutput, $template);
				$mail = new Slick_Util_Mail;
				$mail->addTo($getUser['email']);
				$mail->setFrom('noreply@'.$getSite['domain']);
				$mail->setSubject('['.$getSite['name'].'] New notification received');
				$mail->setHTML($body);
				$mail->send();
			}
		}
		
		$add = $model->insert('user_notifications', array('userId' => $userId, 'message' => $notificationMessageOutput,
													'noteDate' => timestamp(), 'itemId' => $itemId, 'type' => $type));
		if(!$add){
			return false;
		}
		return $add;
	}
	
	public static function checkItemNotified($userId, $itemId, $type)
	{
		$model = new SLick_Core_Model;
		$get = $model->fetchSingle('SELECT * FROM user_notifications WHERE userId = :userId AND type = :type AND itemId = :itemId',
									array(':userId' => $userId, ':itemId' => $itemId, ':type' => $type), 0, true);
		if(!$get){
			return false;
		}
		return $get;
	}
	

	public function getPageMeta($pageId, $key, $fullData = 0)
	{
		
		$get = $this->fetchSingle('SELECT * FROM page_meta WHERE pageId = :id AND metaKey = :key', array(':id' => $pageId, ':key' => $key));
		if(!$get){
			return false;
		}
		if($fullData != 0){
			return $get;
		}
		
		return $get['value'];
	}
	
	public function updatePageMeta($pageId, $key, $value)
	{
		$get = $this->getPageMeta($pageId, $key, 1);
		if(!$get){
			//create new row
			$update = $this->insert('page_meta', array('pageId' => $pageId, 'metaKey' => $key, 'value' => $value));
		}
		else{
			$update = $this->edit('page_meta', $get['pageMetaId'], array('value' => $value));
		}

		if(!$update){
			return false;
		}
		return true;
	}	
	
	public function pageMeta($pageId)
	{
		$getAll = $this->getAll('page_meta', array('pageId' => $pageId));
		$output = array();
		foreach($getAll as $key => $row){
			$output[$row['metaKey']] = $row['value'];
		}
		
		return $output;
	}	
	
	
	public function getBlockMeta($blockId, $key, $fullData = 0)
	{
		
		$get = $this->fetchSingle('SELECT * FROM block_meta WHERE blockId = :id AND metaKey = :key', array(':id' => $blockId, ':key' => $key));
		if(!$get){
			return false;
		}
		if($fullData != 0){
			return $get;
		}
		
		return $get['value'];
	}
	
	public function updateBlockMeta($blockId, $key, $value)
	{
		$get = $this->getBlockMeta($blockId, $key, 1);
		if(!$get){
			//create new row
			$update = $this->insert('block_meta', array('blockId' => $blockId, 'metaKey' => $key, 'value' => $value));
		}
		else{
			$update = $this->edit('block_meta', $get['blockMetaId'], array('value' => $value));
		}

		if(!$update){
			return false;
		}
		return true;
	}	
	
	public function blockMeta($blockId)
	{
		$getAll = $this->getAll('block_meta', array('blockId' => $blockId));
		$output = array();
		foreach($getAll as $key => $row){
			$output[$row['metaKey']] = $row['value'];
		}
		
		return $output;
	}	
}

?>
