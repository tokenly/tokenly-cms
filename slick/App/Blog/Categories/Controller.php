<?php
namespace App\Blog;
/*
 * @module-type = dashboard
 * @menu-label = Categories
 * 
 * */
use Util, App\Tokenly;
class Categories_Controller extends \App\ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->model = new Categories_Model;
        $this->multiblog_model = new Multiblog_Model;
    }
    
    public function init()
    {
		$output = parent::init();
		$this->data['perms'] = \App\Meta_Model::getUserAppPerms($this->data['user']['userId'], 'blog');
		$this->data['user']['perms'] = $this->data['perms'];
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->showBlogCategories();
					break;
				case 'add':
					$output = $this->addBlogCategory();
					break;
				case 'edit':
					$output = $this->editBlogCategory();
					break;
				case 'delete':
					$output = $this->deleteBlogCategory();
					break;
				default:
					$output = $this->showBlogCategories();
					break;
			}
		}
		else{
			$output = $this->showBlogCategories();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    private function showBlogCategories()
    {
		$output = array('view' => 'list');
		$output['catList'] = $this->model->getCategories($this->data['site']['siteId']);
		
		foreach($output['catList'] as $catKey => &$cat){
			$cat['blogRoles'] = $this->multiblog_model->getBlogUserRoles($cat['blogId']);
			$is_admin = false;
			foreach($cat['blogRoles'] as $role){
				if($role['userId'] == $this->data['user']['userId'] AND $role['type'] == 'admin'){
					$is_admin = true;
				}
			}
			
			if(!$is_admin AND !$this->data['perms']['canManageAllBlogs'] AND $cat['blog']['userId'] != $this->data['user']['userId']){
				unset($output['catList'][$catKey]);
				continue;
			}	
		}
		return $output;
	}
	
	private function addBlogCategory()
	{
		$output = array('view' => 'form');
		$output['form'] = $this->model->getBlogCategoryForm($this->data);
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->addBlogCategory($data, $this->data['user']);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				Util\Session::flash('blog-message', 'Category created!', 'success');	
				redirect($this->site.$this->moduleUrl);
			}
			
		}
		return $output;
	}

	private function editBlogCategory()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getBlogCategory = $this->model->get('blog_categories', $this->args[3]);
		if(!$getBlogCategory){
			redirect($this->site.$this->moduleUrl);
		}
		
		$getBlogCategory['blog'] = $this->model->get('blogs', $getBlogCategory['blogId']);
		$getBlogCategory['blogRoles'] = $this->multiblog_model->getBlogUserRoles($getBlogCategory['blogId']);
		$is_admin = false;
		foreach($getBlogCategory['blogRoles'] as $role){
			if($role['userId'] == $this->data['user']['userId'] AND $role['type'] == 'admin'){
				$is_admin = true;
			}
		}
		
		if(!$is_admin AND !$this->data['perms']['canManageAllBlogs'] AND $getBlogCategory['blog']['userId'] != $this->data['user']['userId']){
			$output['view'] = '403';
			return $output;
		}			
		
		$tca = new Tokenly\TCA_Model;
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
		$checkTCA = $tca->checkItemAccess($this->data['user'], $catModule['moduleId'], $getBlogCategory['categoryId'], 'blog-category');
		if(!$checkTCA){
			$output['view'] = '403';
			return $output;
		}
		
		$output = array('view' => 'form');
		$output['form'] = $this->model->getBlogCategoryForm($this->data, $this->args[3]);
		$output['form']->remove('blogId');
		$output['formType'] = 'Edit';
		$output['category'] = $getBlogCategory;
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->editBlogCategory($this->args[3], $data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				Util\Session::flash('blog-message', 'Category ['.$getBlogCategory['name'].'] edited!', 'success');				
				redirect($this->site.$this->moduleUrl);
			}
			
		}
		$output['form']->setValues($getBlogCategory);
		return $output;
	}
	
	private function deleteBlogCategory()
	{
		if(!isset($this->args[3])){
			redirect($this->site.$this->moduleUrl);
		}
		
		$getBlogCategory = $this->model->get('blog_categories', $this->args[3]);
		if(!$getBlogCategory){
			redirect($this->site.$this->moduleUrl);
		}
		
		$getBlogCategory['blog'] = $this->model->get('blogs', $getBlogCategory['blogId']);
		$getBlogCategory['blogRoles'] = $this->multiblog_model->getBlogUserRoles($getBlogCategory['blogId']);
		$is_admin = false;
		foreach($getBlogCategory['blogRoles'] as $role){
			if($role['userId'] == $this->data['user']['userId'] AND $role['type'] == 'admin'){
				$is_admin = true;
			}
		}
		
		if(!$is_admin AND !$this->data['perms']['canManageAllBlogs'] AND $getBlogCategory['blog']['userId'] != $this->data['user']['userId']){
			$output['view'] = '403';
			return $output;
		}			
		
		$tca = new Tokenly\TCA_Model;
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
		$checkTCA = $tca->checkItemAccess($this->data['user'], $catModule['moduleId'], $getBlogCategory['categoryId'], 'blog-category');
		if(!$checkTCA){
			$output['view'] = '403';
			return $output;
		}
				
		$delete = $this->model->delete('blog_categories', $this->args[3]);
		Util\Session::flash('blog-message', 'Category ['.$getBlogCategory['name'].'] deleted.', 'success');	
		redirect($this->site.$this->moduleUrl);
	}
}
