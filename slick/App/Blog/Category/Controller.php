<?php
namespace App\Blog;
use App\Tokenly;
class Category_Controller extends \App\ModControl
{
	public $args;
	public $data;
	
    function __construct()
    {
        parent::__construct();
        $this->model = new Category_Model;
    }
    
    public function init()
    {
		$output = parent::init();
		
		if(!isset($this->args[2])){
			$output['view'] = '404';
			return $output;
		}
		
		$output['category'] = $this->model->get('blog_categories', $this->args[2], array(), 'slug');
		if(!$output['category']){
			$output['view'] = '404';
			return $output;
		}
		
		$tca = new Tokenly\TCA_Model;
		$checkTCA = $tca->checkItemAccess($this->data['user'], $this->data['module']['moduleId'], $output['category']['categoryId'], 'blog-category');
		if(!$checkTCA){
			$output['view'] = '403';
			return $output;
		}
		
		$output['view'] = '../list';
		$output['title'] = $output['category']['name'];
		$postLimit = $this->data['app']['meta']['postsPerPage'];
		$output['commentsEnabled'] = $this->data['app']['meta']['enableComments'];
		if(!$postLimit){
			$postLimit = 10;
		}
		
		$output['posts'] = $this->model->getCategoryPosts($output['category']['categoryId'], $this->data['site']['siteId'], $postLimit);
		$output['numPages'] = $this->model->getCategoryPages($output['category']['categoryId'], $this->data['site']['siteId'], $postLimit);


		if($this->data['user']){
			Tokenly\POP_Model::recordFirstView($this->data['user']['userId'], $this->data['module']['moduleId'], $output['category']['categoryId']);
		}
		
		$getBlog = $this->model->get('blogs', $output['category']['blogId']);
		if($getBlog){
			if($getBlog['themeId'] != 0){
				$getTheme = $this->model->get('themes', $getBlog['themeId']);
				if($getTheme){
					$output['theme'] = $getTheme['location'];
				}
			}
		}
		
		$output['metaDescription'] = $output['category']['description'];
		
		return $output;
	}
}
