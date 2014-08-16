<?php
class Slick_Tags_WriterForm
{
	public $params = array();
	
	public function display()
	{

		if(posted()){
			try{
				$output =  $this->submitForm();
			}
			catch(Exception $e){
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
		echo recaptcha_get_html(CAPTCHA_PUB, null)
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

		$form = new Slick_UI_Form;

		$name = new Slick_UI_Textbox('name');
		$name->setLabel('Name *');
		$name->addAttribute('required');
		$form->add($name);

		$email = new Slick_UI_Textbox('email');
		$email->setLabel('Email *');
		$email->addAttribute('required');
		$form->add($email);

		$website = new Slick_UI_Textbox('website');
		$website->setLabel('Website');
		$form->add($website);
		
		$btc = new Slick_UI_Textbox('bitcoin-address');
		$btc->setLabel('Bitcoin Address (donated to LTB if left blank)');
		$form->add($btc);

		$message = new Slick_UI_Textarea('message');
		$message->setLabel('Your Blog Post/Article * (you may use <em><a href="markdown-formatting">markdown</a></em> for formatting)');
		$message->addAttribute('required');
		$form->add($message);
		
		$include = new Slick_UI_Radio('include');
		$include->setLabel('Do you want to perform a reading the piece for inclusion as Audio content?');
		$include->addOption('yes', 'Yes');
		$include->addOption('no', 'No');
		$include->setSelected('no');
		$form->add($include);
		
		
		return $form;
		
	}
	
	private function submitForm()
	{
		require_once(SITE_PATH.'/resources/recaptchalib.php');
		$resp = recaptcha_check_answer(CAPTCHA_PRIV,
										$_SERVER["REMOTE_ADDR"],
										$_POST["recaptcha_challenge_field"],
										$_POST["recaptcha_response_field"]);

		if(!$resp->is_valid) {
			throw new Exception('Captcha invalid!');
		}
		
		$form = $this->getForm();
		require_once(SITE_PATH.'/resources/recaptchalib.php');
		
		$data = $form->grabData();
		
		$req = array('email', 'name', 'message');
		foreach($req as $required){
			if(!isset($data[$required]) OR trim($data[$required]) == ''){
				throw new Exception(ucfirst($required).' required');
			}
			$data[$required] = htmlentities(strip_tags($data[$required]));
		}
		
		if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
			throw new Exception('Please enter a valid email address');
		}
		
		if(!isset($this->params['email'])){
			$this->params['email'] = 'nickrathman@gmail.com';
		}
		
		$includeAudio = 'No';
		if(isset($data['include']) AND $data['include'] == 'yes'){
			$includeAudio = 'Yes';
		}
		
		$mail = new Slick_Util_Mail;
		$mail->addTo($this->params['email']);
		$mail->setSubject('Lets Talk Bitcoin! New Writer Submission');
		$mail->setFrom('noreply@letstalkbitcoin.com');
		
		$body = '<p>An article submission from the "Write for LTB!" page has been made. See below:</p>';
		$body .= '<ul>
					<li><strong>Name:</strong> '.$data['name'].'</li>
					<li><strong>Email:</strong> '.$data['email'].'</li>
					<li><strong>Website:</strong> '.$data['website'].'</li>
					<li><strong>Bitcoin Address:</strong> '.$data['bitcoin-address'].'</li>
					<li><strong>Willing to perform a reading as Audio content?</strong><br> '.$includeAudio.'</li>
					<li><strong>IP:</strong> '.$_SERVER['REMOTE_ADDR'].'</li>
					</ul>
				<p>Article below:</p>
				<br>
				'.markdown($data['message']);
		
		$mail->setHTML($body);
		
		$send = $mail->send();
		if(!$send){
			throw new Exception('Error sending contact request, please try again');
		}
		
		$output = '<p><Strong>Thank you for contacting us!</strong></p>';
		
		return $output;
		
	}

}

?>
