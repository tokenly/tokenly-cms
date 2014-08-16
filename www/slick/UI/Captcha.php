<?php
class Slick_UI_Captcha extends Slick_UI_FormObject
{
	protected $type = '';
	
	function __construct($type = 'recaptcha')
	{
		parent::__construct();
		$this->type = $type;
	}
	
	public function display($elemWrap)
	{
		$output = '';
		switch($this->type){
			case 'recaptcha':
				$captcha = new Slick_API_Recaptcha;
				$output = $captcha->recaptcha_get_html(RECAPTCHA_PUBLIC);
				break;
			default:
				$captcha = new Slick_API_Recaptcha;
				$output = $captcha->recaptcha_get_html(RECAPTCHA_PUBLIC);
				break;
		}
		
		if($elemWrap != ''){
			$misc = new Slick_UI_Misc;
			$output = $misc->wrap($elemWrap, $output);
		}
		
		return $output;

	}
	
	
}

?> 
