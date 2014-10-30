<?php
class Slick_App_Dashboard_BlogCategory_Controller extends Slick_App_ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->model = new Slick_App_Dashboard_BlogCategory_Model;
        
        
    }
    
    public function init()
    {
		$output = parent::init();
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

		return $output;
		
	}
	
	
	private function addBlogCategory()
	{
		$output = array('view' => 'form');
		$output['form'] = $this->model->getBlogCategoryForm($this->data['site']['siteId']);
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->addBlogCategory($data);
			}
			catch(Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				$this->redirect($this->site.'/'.$this->moduleUrl);
				return true;
			}
			
		}
		
		return $output;
		
	}
	

	
	private function editBlogCategory()
	{
		if(!isset($this->args[3])){
			$this->redirect('/');
			return false;
		}
		
		$getBlogCategory = $this->model->get('blog_categories', $this->args[3]);
		if(!$getBlogCategory){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
		$checkTCA = $tca->checkItemAccess($this->data['user'], $catModule['moduleId'], $getBlogCategory['categoryId'], 'blog-category');
		if(!$checkTCA){
			$output['view'] = '403';
			return $output;
		}
		
		$output = array('view' => 'form');
		$output['form'] = $this->model->getBlogCategoryForm($this->data['site']['siteId'], $this->args[3]);
		$output['formType'] = 'Edit';
		$output['category'] = $getBlogCategory;
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->editBlogCategory($this->args[3], $data);
			}
			catch(Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				$this->redirect($this->site.'/'.$this->moduleUrl);
				return true;
			}
			
		}
		$output['form']->setValues($getBlogCategory);
		
		return $output;
		
	}
	

	
	
	private function deleteBlogCategory()
	{
		if(!isset($this->args[3])){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		
		$getBlogCategory = $this->model->get('blog_categories', $this->args[3]);
		if(!$getBlogCategory){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
		$checkTCA = $tca->checkItemAccess($this->data['user'], $catModule['moduleId'], $getBlogCategory['categoryId'], 'blog-category');
		if(!$checkTCA){
			$output['view'] = '403';
			return $output;
		}
				
		
		$delete = $this->model->delete('blog_categories', $this->args[3]);
		$this->redirect($this->site.'/'.$this->moduleUrl);
		return true;
	}
	


}

?>
