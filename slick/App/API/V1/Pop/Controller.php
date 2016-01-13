<?php
namespace App\API\V1;
class Pop_Controller extends \Core\Controller
{
	public $methods = array('GET', 'POST');
	
	function __construct()
	{
		parent::__construct();
		$this->model = new \App\Blog\MagicWords_Model;
	}
	
	protected function init($args = array())
	{
		$this->args = $args;
		$output = array();

		try{
			$this->user = Auth_Model::getUser($this->args['data']);
		}
		catch(\Exception $e){
			http_response_code(403);
			$output['error'] = $e->getMessage();
			return $output;
		}

		if(isset($this->args[1])){
			switch($this->args[1]){
				case 'magic-words':
					$output = $this->container->magicWords();
					break;
				default:
					http_response_code(400);
					$output['error'] = 'Invalid request';
					return $output;
			}
		}
		else{
			http_response_code(400);
			$output['error'] = 'Invalid request';
			return $output;
		}
		return $output;
	}
	
	
	protected function magicWords()
	{
		if($this->useMethod == 'POST'){
			return $this->container->postMagicWord();
		}
		$output = array();
		
		$output['words'] = $this->model->getAll('pop_words', array('userId' => $this->user['userId']), array('word', 'moduleId', 'itemId', 'submitDate'), 'submitId', 'desc');
		
		$blogModule = $this->model->get('modules', 'blog-post', array(), 'slug');
		
		foreach($output['words'] as &$word){
			$word['slug'] = '';
			$word['type'] = '';
			$word['name'] = '';
			switch($word['moduleId']){
				case $blogModule['moduleId']:
					$getPost = $this->model->get('blog_posts', $word['itemId']);
					if($getPost){
						$word['slug'] = $getPost['url'];
						$word['name'] = $getPost['title'];
					}
					$word['type'] = 'blog-post';
					break;
			}
			unset($word['moduleId']);
		}
		return $output;
	}
	
	protected function postMagicWord()
	{
		$output = array();
		
		if(!isset($this->args['data']['word'])){
			http_response_code(400);
			$output['error'] = 'Invalid request: "word" required';
			return $output;
		}
		
		try{
			$check = $this->model->checkMagicWord($this->args['data']['word'], $this->user['userId'], 'blog');
		}
		catch(\Exception $e){
			http_response_code(400);
			$output['error'] = $e->getMessage();
			return $output;
		}
		
		$output['result'] = 'success';
		return $output;
	}
}
