<?php
class Slick_App_Profile_User_Model extends Slick_Core_Model
{
	public static $profiles = array();
	
	public function getUserProfile($id, $siteId)
	{
		if(isset(self::$profiles[$id])){
			return self::$profiles[$id];
		}
		
		$uFields = array('userId', 'username', 'slug', 'email', 'regDate', 'lastActive', 'lastAuth');
		$get = $this->get('users', $id, $uFields);
		if(!$get){
			$get = $this->get('users', $id, $uFields, 'slug');
			if(!$get){
				return false;
			}
		}
		
		$output = $get;
		$output['profile'] = $this->fetchAll('SELECT f.fieldId, v.value, f.label, f.type,f.slug
												FROM user_profileVals v
												LEFT JOIN profile_fields f ON f.fieldId = v.fieldId
												WHERE v.userId = :userId AND f.public = 1 AND f.active = 1
												AND v.value != "" AND f.siteId = :siteId
												GROUP BY v.fieldId
												ORDER BY f.rank ASC', array(':userId' => $get['userId'], ':siteId' => $siteId));

		$meta = new Slick_App_Meta_Model;
		$output['pubProf'] = $meta->getUserMeta($get['userId'], 'pubProf');
		$output['showEmail'] = $meta->getUserMeta($get['userId'], 'showEmail');
		$output['avatar'] = $meta->getUserMeta($get['userId'], 'avatar');
		
		$getSite = $this->get('sites', $siteId);
		if(trim($output['avatar']) == ''){
			$output['avatar'] = 'http://www.gravatar.com/avatar/'.md5(strtolower($get['email'])).'?d='.urlencode($getSite['url'].'/files/avatars/default.jpg');
			//$output['avatar'] = 'default.jpg';
		}
		
		$prof = array();
		foreach($output['profile'] as $row){
			if(trim($row['slug']) == ''){
				$prof[genURL($row['label'])] = $row;
			}
			else{
				$prof[$row['slug']] = $row;
			}
		}
		$output['profile'] = $prof;

		if(isset($_SERVER['is_api'])){
			$getSite = $this->get('sites', $siteId);
			$output['avatar'] = $getSite['url'].'/files/avatars/'.$output['avatar'];
			if(count($output['profile']) == 0){
				$output['profile'] = null;
			}
		}
		
		return $output;
		
	}
	
	public function getUsersWithProfile($fieldId)
	{
		$get = $this->getAll('user_profileVals', array('fieldId' => $fieldId));
		$users = array();
		$used = array();
		foreach($get as $row){
			if(trim($row['value']) != ''){
				$getUser = $this->get('users', $row['userId'], array('userId', 'username', 'email'));
				if($getUser){
					if(in_array($row['userId'], $used)){
						continue;
					}
					
					$getUser['lastUpdate'] = $row['lastUpdate'];
					$getUser['value'] = $row['value'];
					$users[] = $getUser;
					array_push($used, $row['userId']);
				}
				
			}
		}
		
		return $users;
		
	}
	
	
}
