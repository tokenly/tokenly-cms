<?php
class Slick_App_Dashboard_LTBcoin_MagicWordSubmits_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Dashboard_LTBcoin_MagicWordSubmits_Model;
	}
	
	public function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		$output['view'] = 'index';
		
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'delete':
					$output = $this->deleteWord($output);
					break;
				default:
					$output['view'] = '404';
					break;
				
			}
		}
		else{
			$output = $this->showAllWords($output);
		}
		
		return $output;
		
	}
	
	private function showAllWords($output)
	{
		$output['words'] = $this->model->getWordSubmissions();
		return $output;
	}
	
	private function deleteWord($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $output;
		}
		
		$getWord = $this->model->get('pop_words', $this->args[3]);
		if($getWord){
			$this->model->delete('pop_words', $this->args[3]);
		}
		
		$this->redirect($this->site.'/'.$this->data['app']['url'].'/'.$this->data['module']['url']);
		
		return $output;
	}
	

}
