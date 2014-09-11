<?php
class Slick_Tags_AdvertiseForm
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
		
		$form->setFileEnc();

		$email = new Slick_UI_Textbox('email');
		$email->setLabel('Your Email *');
		$email->addAttribute('required');
		$form->add($email);

		$website = new Slick_UI_Textbox('url');
		$website->setLabel('URL *');
		$website->addAttribute('required');
		$form->add($website);
		
		$image = new Slick_UI_File('image');
		$image->setLabel('Advertisement Image * (155x155)');
		$form->add($image);

		$notes = new Slick_UI_Textarea('notes');
		$notes->setLabel('Notes');
		$form->add($notes);
		
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
		
		$req = array('email', 'url');
		foreach($req as $required){
			if(!isset($data[$required]) OR trim($data[$required]) == ''){
				throw new Exception(ucfirst($required).' required');
			}
			$data[$required] = htmlentities(strip_tags($data[$required]));
		}
		
		if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
			throw new Exception('Please enter a valid email address');
		}
		
		if(!filter_var($data['url'], FILTER_VALIDATE_URL)){
			throw new Exception('Please enter a valid URL');
		}
		
		if(!isset($this->params['email'])){
			$this->params['email'] = 'nickrathman@gmail.com';
		}
		
		$imageLink = '<li><strong>No image included</strong></li>';
		if(isset($_FILES['image']['tmp_name']) AND trim($_FILES['image']['tmp_name']) != ''){
			$checkImage = getimagesize($_FILES['image']['tmp_name']);
			if($checkImage){
				$fileName = md5($_SERVER['REMOTE_ADDR'].$_FILES['image']['name']).'-'.timestamp().'.jpg';
				$move = move_uploaded_file($_FILES['image']['tmp_name'],
										   SITE_PATH.'/files/images/ad-submissions/'.$fileName);
				if($move){
					$model = new Slick_Core_Model;
					$getSite = $model->get('sites', 1);
					$fileURL = $getSite['url'].'/files/images/ad-submissions/'.$fileName;
					$imageLink = '<li><strong>Image:</strong> <a href="'.$fileURL.'">'.$fileURL.'</a></li>';
				}
			}
		}
		
		$mail = new Slick_Util_Mail;
		$mail->addTo($this->params['email']);
		$mail->setSubject('Lets Talk Bitcoin! Display Ad Submission');
		$mail->setFrom('noreply@letstalkbitcoin.com');
		
		$body = '<p>A new display ad has been submitted. See info below:</p>';
		$body .= '<ul>
					<li><strong>Email:</strong> '.$data['email'].'</li>
					<li><strong>URL:</strong> '.$data['url'].'</li>
					'.$imageLink.'
					<li><strong>IP:</strong> '.$_SERVER['REMOTE_ADDR'].'</li>
					</ul>
				<p>Other Notes:</p>
				<br>
				'.markdown($data['notes']);
		
		$mail->setHTML($body);
		
		$send = $mail->send();
		if(!$send){
			throw new Exception('Error sending submission, please try again');
		}
		
		$output = '<p><strong>Thank you for submitting your ad and contacting us! We will get back to you shortly.</strong></p>';
		
		return $output;
		
	}

}

?>
