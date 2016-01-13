<?php
namespace App\Blog;
/*
 * @module-type = dashboard
 * @menu-label = Magic Word Submissions
 * 
 * */
class MagicWordSubmits_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new MagicWordSubmits_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		$output['view'] = 'index';
		
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'delete':
					$output = $this->container->deleteWord($output);
					break;
				default:
					$output['view'] = '404';
					break;
			}
		}
		else{
			$output = $this->container->showAllWords($output);
		}
		return $output;
	}
	
	protected function showAllWords($output)
	{
		$output['words'] = $this->model->getWordSubmissions();
		return $output;
	}
	
	protected function deleteWord($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $output;
		}
		
		$getWord = $this->model->get('pop_words', $this->args[3]);
		if($getWord){
			$this->model->delete('pop_words', $this->args[3]);
		}
		
		redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);
		return $output;
	}
}
