<?php
namespace Tags;
class Redirect
{
	public $params = array();
	public function display()
	{
		if(!isset($this->params['link'])){
			return false;
		}
		if(substr($this->params['link'], 0, 1) == '/'){
			$getSite = currentSite();
			$this->params['link'] = $getSite['url'].$this->params['link'];
		}
		redirect($this->params['link']);
	}
}
