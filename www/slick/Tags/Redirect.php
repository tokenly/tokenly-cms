<?php
class Slick_Tags_Redirect
{
	public $params = array();
	
	public function display()
	{
		if(!isset($this->params['link'])){
			return false;
		}
		
		if(substr($this->params['link'], 0, 1) == '/'){
			$model = new Slick_Core_Model;
			$getSite = $model->get('sites', $_SERVER['HTTP_HOST'], array(), 'domain');
			
			$this->params['link'] = $getSite['url'].$this->params['link'];
		}
		
		header('Location: '.$this->params['link']);
		die();
		
	}


}
