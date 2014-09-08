<?php
class Slick_App_Dashboard_ForumBoard_Model extends Slick_Core_Model
{

	public function getBoardForm($siteId)
	{
		$form = new Slick_UI_Form;
		
		$getCats = $this->getAll('forum_categories', array('siteId' => $siteId), array(), 'rank', 'asc');
		$categoryId = new Slick_UI_Select('categoryId');
		$categoryId->setLabel('Board Category');
		$categoryId->addOption(0, '[none]');
		foreach($getCats as $cat){
			$categoryId->addOption($cat['categoryId'], $cat['name']);
		}
		$form->add($categoryId);
		
		$name = new Slick_UI_Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Board Name');
		$form->add($name);
		
		$slug = new Slick_UI_Textbox('slug');
		$slug->setLabel('Slug / URL (blank to auto generate)');
		$form->add($slug);	
		
		$rank = new Slick_UI_Textbox('rank');
		$rank->setLabel('Order Rank');
		$form->add($rank);
		
		$active = new Slick_UI_Checkbox('active');
		$active->setLabel('Board Active?');
		$active->setBool(1);
		$active->setValue(1);
		$form->add($active);
		
		$description = new Slick_UI_Textarea('description', 'html-editor');
		$description->setLabel('Description');
		$form->add($description);

		return $form;
	}
	


	public function addBoard($data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'rank' => false, 'description' => false, 'active' => false, 'categoryId' => false);
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
		
		if(!isset($useData['slug']) OR trim($useData['slug']) == ''){
			$useData['slug'] = genURL($useData['name']);
		}
		
		$add = $this->insert('forum_boards', $useData);
		if(!$add){
			throw new Exception('Error adding board');
		}
		
		return $add;
		
		
	}
		
	public function editBoard($id, $data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'rank' => false, 'description' => false, 'active' => false, 'categoryId' => false);
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
		
		if(!isset($useData['slug']) OR trim($useData['slug']) == ''){
			$useData['slug'] = genURL($useData['name']);
		}
		
		
		$edit = $this->edit('forum_boards', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing board');
		}
		
		
		return true;
		
	}


	public function getBoardMods($boardId)
	{
		$sql = 'SELECT u.userId, u.username, u.email, u.slug
				FROM forum_mods m
				LEFT JOIN users u ON m.userId = u.userId
				WHERE m.boardId = :boardId
				ORDER BY u.username ASC';
		$get = $this->fetchAll($sql, array(':boardId' => $boardId));
		return $get;
	}

	public function getModForm()
	{
		$form = new Slick_UI_Form;
		
		$id = new Slick_UI_Textbox('userId');
		$id->setLabel('Add New Moderator');
		$id->addAttribute('placeholder', 'Username or User ID');
		$form->add($id);
		
		$form->setSubmitText('Add Mod');
		
		return $form;
	}
	
	public function addMod($boardId, $userId)
	{
		
		$userId = trim($userId);
		$get = $this->get('users', $userId, array(), 'username');
		if(!$get){
			$get = $this->get('users', intval($userId));
			if(!$get){
				throw new Exception('User not found');
			}
		}
		
		$getMod = $this->getAll('forum_mods', array('userId' => $userId, 'boardId' => $boardId));
		if(count($getMod) > 0){
			throw new Exception('User already a moderator');
		}
		
		return $this->insert('forum_mods', array('userId' => $get['userId'], 'boardId' => $boardId));
	}

}

?>
