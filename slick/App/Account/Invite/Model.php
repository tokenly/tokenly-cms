<?php
namespace App\Account;
use Core, \App\Meta_Model;
class Invite_Model extends Core\Model
{
	
	protected function sendInvite($data)
	{
		$data = $this->container->createInvite($data);
		$data = $this->container->getInviteUsers($data);
		$notify = Meta_Model::notifyUser($data['acceptUser'], 'emails.invites.'.$data['type'], $data['inviteId'], 'user-invite', false, $data);
		if(!$notify){
			throw new \Exception('Error sending invite notification');
		}
		return $data;
	}
	
	protected function createInvite($data)
	{
		$req = array('userId', 'sendUser', 'type', 'itemId', 'class');
		foreach($req as $required){
			if(!isset($data[$required])){
				throw new \Exception($required.' required');
			}
		}
		if(!isset($data['acceptUser'])){
			$data['acceptUser'] = $data['userId'];
		}
		
		$code = hash('sha256', json_encode($data).time());
		
		if(!isset($data['info'])){
			$data['info'] = array();
		}
		
		$insertData = array('userId' => $data['userId'], 'sendUser' => $data['sendUser'],
							'acceptUser' => $data['acceptUser'], 'type' => $data['type'],
							'itemId' => $data['itemId'], 'acceptCode' => $code,
							'inviteDate' => timestamp(), 'class' => $data['class'], 'info' => json_encode($data['info']));
		
		$insert = $this->insert('user_invites', $insertData);
		
		if(!$insert){
			throw new \Exception('Error creating invitation');
		}
		
		$data['inviteId'] = $insert;
		$data['acceptCode'] = $code;
		
		return $data;
	}
	
	protected function getInviteUsers($data)
	{
		$data['main_user'] = $this->get('users', $data['userId'], array('userId', 'username', 'slug', 'email'));
		$data['send_user'] = $this->get('users', $data['sendUser'], array('userId', 'username', 'slug', 'email'));
		$data['accept_user'] = $this->get('users', $data['acceptUser'], array('userId', 'username', 'slug', 'email'));
		
		return $data;
	}
	
	protected function getAcceptForm()
	{
		ob_start();
		?>
		<div class="invite-accept-form">
			<form action="" method="post">
				<input type="submit" name="decision" value="Accept" />
				<input type="submit" name="decision" value="Decline" />
			</form>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}

