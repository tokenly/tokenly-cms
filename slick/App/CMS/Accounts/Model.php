<?php
namespace App\CMS;
use Core, UI, Util;
class Accounts_Model extends Core\Model
{
	protected function accountForm()
	{
		$form = new UI\Form;
		
		$form->add(new UI\FormHeading('Groups', 3));
		$groups = new UI\CheckboxList('groups');
		$groups->setLabel('Group Memberships');
		$groups->setLabelDir('R');
		$getGroups = $this->getAll('groups');
		foreach($getGroups as $group){
			$groups->addOption($group['groupId'], $group['name']);
		}
		
		$form->add($groups);
		
		$form->add(new UI\FormHeading('Change Password', 3));
		$pass = new UI\Password('password');
		$pass->setLabel('New Password');
		$form->add($pass);

		$pass2 = new UI\Password('password2');
		$pass2->setLabel('Repeat Password');
		$form->add($pass2);

		return $form;
	}
	
	protected function updateAccount($id, $data)
	{
		$this->delete('group_users', $id, 'userId');
		foreach($data['groups'] as $groupId){
			$this->insert('group_users', array('groupId' => $groupId, 'userId' => $id));
		}
		
		if(trim($data['password']) != ''){
			if($data['password'] != $data['password2']){
				throw new \Exception('Passwords do not match!');
			}
			$hashPass = genPassSalt($data['password']);
			$update = $this->edit('users', $id, array('password' => $hashPass['hash'], 'spice' => $hashPass['salt']));
			if(!$update){
				throw new \Exception('Error updating password');
			}
		}
		return true;
	}
	
	protected function getSearchForm()
	{
		$form = new UI\Form;
		
		$username = new UI\Textbox('username');
		$username->setLabel('Username');
		$username->addAttribute('required');
		$form->add($username);
		
		return $form;
	}
}
