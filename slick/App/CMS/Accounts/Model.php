<?php
class Slick_App_CMS_Accounts_Model extends Slick_Core_Model
{

	public function accountForm()
	{
		$form = new Slick_UI_Form;
		
		$form->add(new Slick_UI_FormHeading('Groups', 3));
		$groups = new Slick_UI_CheckboxList('groups');
		$groups->setLabel('Group Memberships');
		$groups->setLabelDir('R');
		$getGroups = $this->getAll('groups');
		foreach($getGroups as $group){
			$groups->addOption($group['groupId'], $group['name']);
		}
		
		$form->add($groups);
		
		$form->add(new Slick_UI_FormHeading('Change Password', 3));
		$pass = new Slick_UI_Password('password');
		$pass->setLabel('New Password');
		$form->add($pass);

		$pass2 = new Slick_UI_Password('password2');
		$pass2->setLabel('Repeat Password');
		$form->add($pass2);

		return $form;
	}
	
	public function updateAccount($id, $data)
	{
		$this->delete('group_users', $id, 'userId');
		foreach($data['groups'] as $groupId){
			$this->insert('group_users', array('groupId' => $groupId, 'userId' => $id));
		}
		
		if(trim($data['password']) != ''){
			if($data['password'] != $data['password2']){
				throw new Exception('Passwords do not match!');
			}
			$hashPass = genPassSalt($data['password']);
			$update = $this->edit('users', $id, array('password' => $hashPass['hash'], 'spice' => $hashPass['salt']));
			if(!$update){
				throw new Exception('Error updating password');
			}
		}
		
		
		
		return true;
	}
	
	public function getSearchForm()
	{
		$form = new Slick_UI_Form;
		
		$username = new Slick_UI_Textbox('username');
		$username->setLabel('Username');
		$username->addAttribute('required');
		$form->add($username);
		
		return $form;
	}

}

?>
