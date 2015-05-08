<?php
namespace UI;
class Captcha extends FormObject
{
	protected $type = '';
	
	function __construct($type = 'recaptcha')
	{
		parent::__construct();
		$this->type = $type;
		
	}
	
	public function display($elemWrap = '')
	{
		$output = '';
		switch($this->type){
			case 'recaptcha':
			default:
				ob_start();
				?>
				  <div class="g-recaptcha" data-sitekey="<?= CAPTCHA_PUB ?>"></div>
				  <script type="text/javascript"
					  src="https://www.google.com/recaptcha/api.js?hl=en">
				  </script>
				<?php
				$output = ob_get_contents();
				ob_end_clean();
				break;
		}
		if($elemWrap != ''){
			$misc = new Misc;
			$output = $misc->wrap($elemWrap, $output);
		}
		return $output;
	}	
}
