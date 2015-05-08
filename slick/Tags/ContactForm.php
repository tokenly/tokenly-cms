<?php
namespace Tags;
use UI, Util;
class ContactForm
{
	public $params = array();
	
	public function display()
	{
		if(posted()){
			try{
				$output =  $this->submitForm();
			}
			catch(\Exception $e){
				$output = $this->showFormError($e->getMessage());
			}
			
			return $output;
		}
		else{
			return $this->showForm();
		}
	}
	
	private function showFormError($err = '')
	{		
		$output = '<p><strong>Error: '.$err.'</strong></p>';
		$output .= $this->showForm();
		
		return $output;
		
	}
	
	private function showForm()
	{
		$form = $this->getForm();
		require_once(SITE_PATH.'/resources/recaptchalib.php');
		ob_start();
		?>
		
		<?= $form->open() ?>
		<?= $form->displayFields() ?>
		<?php
		echo recaptcha_get_html(CAPTCHA_PUB, null, true);
		?>
		<?= $form->displaySubmit() ?>
		<?= $form->close() ?>
		
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
		
	}
	
	private function getForm()
	{
		$form = new UI\Form;
		
		$email = new UI\Textbox('email');
		$email->setLabel('Email Address:');
		$email->addAttribute('required');
		$form->add($email);

		$name = new UI\Textbox('name');
		$name->setLabel('Name:');
		$name->addAttribute('required');
		$form->add($name);
		
		$message = new UI\Textarea('message');
		$message->setLabel('Message:');
		$message->addAttribute('required');
		$form->add($message);
		
		return $form;
		
	}
	
	private function submitForm()
	{
		require_once(SITE_PATH.'/resources/recaptchalib.php');
		$resp = recaptcha_check_answer (CAPTCHA_PRIV,
										$_SERVER["REMOTE_ADDR"],
										$_POST["recaptcha_challenge_field"],
										$_POST["recaptcha_response_field"]);

		if(!$resp->is_valid) {
			throw new \Exception('Captcha invalid!');
		}
		
		$form = $this->getForm();
		require_once(SITE_PATH.'/resources/recaptchalib.php');
		
		$data = $form->grabData();
		
		$req = array('email', 'name', 'message');
		foreach($req as $required){
			if(!isset($data[$required]) OR trim($data[$required]) == ''){
				throw new \Exception(ucfirst($required).' required');
			}
			$data[$required] = htmlentities(strip_tags($data[$required]));
		}
		
		if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
			throw new \Exception('Please enter a valid email address');
		}
		if(!isset($this->params['email'])){
			$this->params['email'] = 'nickrathman@gmail.com';
		}
		
		$mail = new Util\Mail;
		$mail->addTo($this->params['email']);
		$mail->setSubject('Lets Talk Bitcoin! Contact Request');
		$mail->setFrom('noreply@letstalkbitcoin.com');
		
		$body = '<p>A contact request has come in from letstalkbitcoin.com. See below:</p>';
		$body .= markdown($data['message']);
		$body .= '<ul>
					<li><strong>Email:</strong> '.$data['email'].'</li>
					<li><strong>Name:</strong> '.$data['name'].'</li>
					<li><strong>IP:</strong> '.$_SERVER['REMOTE_ADDR'].'</li>
					</ul>';
		
		$mail->setHTML($body);
		
		$send = $mail->send();
		if(!$send){
			throw new \Exception('Error sending contact request, please try again');
		}
		
		$output = '<p><Strong>Thank you for contacting us!</strong></p>';
		
		return $output;
		
	}

}
