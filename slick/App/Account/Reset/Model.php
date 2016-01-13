<?php
namespace App\Account;
use Core, UI, Util;
class Reset_Model extends Core\Model
{
	protected function getResetForm()
	{
		$form = new UI\Form;

		$username = new UI\Textbox('username');
		$username->setLabel('Username');
		$form->add($username);

		$email = new UI\Textbox('email');
		$email->setLabel(' Or Email Address');
		$form->add($email);
		
		$form->setSubmitText('Send Password Reset');
		return $form;
		
	}
	
	protected function sendPasswordReset($data, $site)
	{
		if((!isset($data['email']) OR trim($data['email']) == '') AND (!isset($data['username']) OR trim($data['username']) == '')){
			throw new \Exception('Email address or Username required');
		}
		
		if(isset($data['email']) AND trim($data['email']) != '' AND !filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
			throw new \Exception('Invalid email address');
		}
		
		$get = false;
		if(isset($data['username']) AND trim($data['username']) != ''){
			$get = $this->get('users', $data['username'], array('userId', 'username', 'email', 'lastAuth'), 'username');
		}
		if(!$get AND isset($data['email']) AND trim($data['email']) != ''){
			$get = $this->get('users', $data['email'], array('userId', 'username', 'email', 'lastAuth'), 'email');
		}
		
		if(!$get){
			throw new \Exception('No user found');
		}
		
		$genLink = hash('sha256', $get['userId'].time().':'.mt_rand(0,1000).$get['lastAuth']);
		$addLink = $this->insert('reset_links', array('userId' => $get['userId'], 'url' => $genLink, 'requestTime' => timestamp()));
		if(!$addLink){
			throw new \Exception('Error generating reset link');
		}
		
		$mail = new Util\Mail;
		$mail->addTo($get['email']);
		$mail->setFrom('noreply@'.$site['domain']);
		$mail->setSubject($site['name'].' Password Reset');
		$body = '<p>
		Hello '.$get['username'].',
		</p>
		<p>
			A request has been made on '.$site['name'].' to reset your password.
		</p>
		<p>
			<strong>To complete your password reset please <a href="'.$site['url'].'/account/reset/'.$genLink.'">click here</a></strong>.
			This request will be valid for the next two hours.
		</p>
		<p>
			If this was not you, please ignore this email.
		</p>';
		
		$mail->setHTML($body);
		$send = $mail->send();
		if(!$send){
			throw new \Exception('Error sending password reset');
		}
		return true;
	}
	
	protected function getPassResetForm()
	{
		$form = new UI\Form;
		$form->setSubmitText('Complete Password Reset');
		
		$pass = new UI\Password('password');
		$pass->setLabel('New Password');
		$pass->addAttribute('required');
		$form->add($pass);
		
		$pass2 = new UI\Password('password2');
		$pass2->setLabel('New Password (repeat)');
		$pass2->addAttribute('required');
		$form->add($pass2);	
		
		return $form;
	}
	
	protected function completePassChange($data)
	{
		if(!isset($data['password']) OR trim($data['password']) == ''){
			throw new \Exception('Password');
		}
		if(!isset($data['password2']) OR trim($data['password2']) == ''){
			throw new \Exception('Password');
		}
		if($data['password'] != $data['password2']){
			throw new \Exception('Passwords do not match');
		}
		if(!isset($data['userId'])){
			throw new \Exception('No user set');
		}
		if(!isset($data['resetId'])){
			throw new \Exception('Invalid reset link');
		}

		$hashPass = genPassSalt($data['password']);
		$update = $this->edit('users', $data['userId'], array('password' => $hashPass['hash'], 'spice' => $hashPass['salt']));
		if(!$update){
			throw new \Exception('Error resetting password');
		}
	
		$this->delete('reset_links', $data['resetId']);
		return true;
		
	}
}
