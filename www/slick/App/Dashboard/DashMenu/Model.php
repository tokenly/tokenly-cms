<?php
class Slick_App_Dashboard_DashMenu_Model extends Slick_Core_Model
{

	public function getItemForm($themeId = 0)
	{
		$form = new Slick_UI_Form;

		$getModules = $this->getAll('modules');
		$moduleId = new Slick_UI_Select('moduleId');
		$moduleId->setLabel('Module');
		foreach($getModules as $module){
			$getApp = $this->get('apps', $module['appId']);
			$moduleId->addOption($module['moduleId'], $getApp['name'].'\\'.$module['name']);
		}
		$form->add($moduleId);

		$params = new Slick_UI_Textbox('mod-params');
		$params->setLabel('Module URL Parameters');
		$form->add($params);

		$group = new Slick_UI_Textbox('dashGroup');
		$group->addAttribute('required');
		$group->setLabel('Dashboard Group');
		$form->add($group);
	
		$label = new Slick_UI_Textbox('label');
		$label->addAttribute('required');
		$label->setLabel('Label');
		$form->add($label);
		
		$rank = new Slick_UI_Textbox('rank');
		$rank->setLabel('Order Rank');
		$form->add($rank);	

		$checkAccess = new Slick_UI_Checkbox('checkAccess', 'checkAccess');
		$checkAccess->setBool(1);
		$checkAccess->setValue(1);
		$checkAccess->setLabel('Check Module Access?');
		$form->add($checkAccess);
		


		return $form;
	}
	


	public function addItem($data)
	{
		$req = array('moduleId' => true, 'dashGroup' => true, 'label' => true, 'rank' => false, 'checkAccess' => false, 'mod-params' => false);
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new Exception(ucfirst($key).' required');
				}
				else{
					$useData[$key] = '';
				}
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		if(isset($useData['mod-params'])){
			$useData['params'] = $useData['mod-params'];
			unset($useData['mod-params']);
		}
		
		$add = $this->insert('dash_menu', $useData);
		
		if(!$add){
			throw new Exception('Error adding dashboard item');
		}
		
		return $add;
		
		
	}
		
	public function editItem($id, $data)
	{
		$req = array('moduleId' => true, 'dashGroup' => true, 'label' => true, 'rank' => false, 'checkAccess' => false, 'mod-params' => false);
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new Exception(ucfirst($key).' required');
				}
				else{
					$useData[$key] = '';
				}
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		if(isset($useData['mod-params'])){
			$useData['params'] = $useData['mod-params'];
			unset($useData['mod-params']);
		}


		$edit = $this->edit('dash_menu', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing dashboard item');
		}
		
		
		return true;
		
	}
	
	
	public static function getDashMenu()
	{
		$model = new Slick_Core_Model;
		$getUser = Slick_App_Account_Home_Model::userInfo();
		if(!$getUser){
			return false;
		}
		
		$getSite = $model->get('sites', $_SERVER['HTTP_HOST'], array(), 'domain');

		$groupList = array();
		foreach($getUser['groups'] as $group){
			$groupList[] = $group['groupId'];
		}


		$access = array();
		$getPerms = $model->fetchAll('SELECT * FROM group_access WHERE groupId IN('.join(',', $groupList).')');
										
		foreach($getPerms as $perm){
			$access[] = $perm['moduleId'];
		}
		
		$getMenu = $model->fetchAll('SELECT d.*, a.url as appUrl, m.url as moduleUrl
									FROM dash_menu d
									LEFT JOIN modules m ON m.moduleId = d.moduleId
									LEFT JOIN apps a ON a.appId = m.appId
									ORDER BY dashGroup ASC, rank ASC');
		$menu = array();
		foreach($getMenu as $item){
			if($item['checkAccess'] == 1){
				if(!in_array($item['moduleId'], $access)){
					continue;
				}
			}
			if(!isset($menu[$item['dashGroup']])){
				$menu[$item['dashGroup']] = array();
			}
			
			$moduleUrl = $item['appUrl'];
			if($item['moduleUrl'] != ''){
				$moduleUrl .= '/'.$item['moduleUrl'];
			}
			$menu[$item['dashGroup']][] = array('label' => $item['label'], 'url' => $getSite['url'].'/'.$moduleUrl.$item['params']);
		}

		return $menu;
	}

}

