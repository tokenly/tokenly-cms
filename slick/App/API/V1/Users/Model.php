<?php
class Slick_App_API_V1_Users_Model extends Slick_Core_Model
{
	
	public function getProfileFields($user, $siteId)
	{
		$getGroups = $this->getAll('group_users', array('userId' => $user['userId']));
		$groupIds = array();
		foreach($getGroups as $group){
			$groupIds[] = $group['groupId'];
		}
		
		$getFields = $this->fetchAll('SELECT f.fieldId, f.label, f.type, f.options, f.public, f.rank, f.slug
									  FROM profile_fieldGroups g 
									  LEFT JOIN profile_fields f ON f.fieldId = g.fieldId
									  WHERE g.groupId IN('.join(',', $groupIds).')
									  AND f.active = 1 AND f.siteId = :siteId
									  AND f.public = 1
									  GROUP BY g.fieldId
									  ORDER BY f.rank ASC', array(':siteId' => $siteId));
		foreach($getFields as $k => $row){
			$getVal = $this->fetchSingle('SELECT * FROM user_profileVals WHERE userId = :userId AND fieldId = :fieldId',
										array(':userId' => $user['userId'], ':fieldId' => $row['fieldId']));
			$getFields[$k]['value'] = $getVal['value'];
		}
									  
		
		return $getFields;
		
	}
	
	public function updateProfile($data)
	{
		if(!isset($data['fields']) OR count($data['fields']) == 0){
			throw new Exception('No fields set');
		}
		$getFields = $this->getProfileFields($data['user'], $data['site']['siteId']);
		
		$useData = array();
		foreach($getFields as $field){
			foreach($data['fields'] as $pKey => $pVal){
				if($field['fieldId'] != $pKey){
					continue;
				}
				$useData['field-'.$pKey] = $pVal;
				continue 2;
				
			}
		}
		
		$model = new Slick_App_Account_Profile_Model;
		$update = $model->updateProfile($data['user'], $useData);
		if(!$update){
			throw new Exception('Error updating profile');
		}
		
		return true;
		
	}
	
	
}

?>
