<?php
namespace App\API\V1;
use Core;
class Users_Model extends Core\Model
{
	protected function getProfileFields($user, $siteId)
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
	
	protected function updateProfile($data)
	{
		if(!isset($data['fields']) OR count($data['fields']) == 0){
			throw new \Exception('No fields set');
		}
		$getFields = $this->container->getProfileFields($data['user'], $data['site']['siteId']);
		
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
		$model = new \App\Account\Profile_Model;
		$update = $model->updateProfile($data['user'], $useData);
		if(!$update){
			throw new \Exception('Error updating profile');
		}
		return true;
	}
}
