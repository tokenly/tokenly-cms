<?php
namespace App\Blog;
/*
 * @module-type = dashboard
 * @menu-label = Manage Blogs
 * 
 * */
use Util, App\Tokenly, App\CMS;
class Multiblog_Controller extends \App\ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        $this->model = new Multiblog_Model;
        $this->settingModel = new CMS\AppSettings_Model;
    }
    
    protected function init()
    {
		$output = parent::init();
		$this->data['perms'] = \App\Meta_Model::getUserAppPerms($this->data['user']['userId'], 'blog');
			
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->container->showBlogs();
					break;
				case 'add':
					$output = $this->container->addBlog();
					break;
				case 'edit':
					$output = $this->container->editBlog();
					break;
				case 'delete':
					$output = $this->container->deleteBlog();
					break;
				case 'remove-role':
					$output = $this->container->removeBlogRole();
					break;
				default:
					$output = $this->container->showBlogs();
					break;
			}
		}
		else{
			$output = $this->container->showBlogs();
		}
		$output['template'] = 'admin';
        $output['perms'] = $this->data['perms'];	
        return $output;
    }
    
    protected function showBlogs()
    {
		$output = array('view' => 'list');
		$wheres = array('siteId' => $this->data['site']['siteId']);		
		$output['blogList'] = $this->model->getAll('blogs', $wheres);
		foreach($output['blogList'] as &$blog){
			$roles = $this->model->getBlogUserRoles($blog['blogId']);
			$roleList = array();
			foreach($roles as $role){
				if($role['userId'] == 0){
					continue;
				}		
				if(!isset($roleList[$role['type']])){
					$roleList[$role['type']] = array();
				}
				$roleList[$role['type']][] = $role['userId'];
			}
			$blog['roles'] = $roleList;
		}
		return $output;
	}
	
	protected function addBlog()
	{
		$output = array('view' => 'form');
		if(!$this->data['perms']['canCreateBlogs']){
			$output['view'] = '403';
			return $output;
		}
		$output['form'] = $this->model->getBlogForm($this->data['site']['siteId']);

		if(!$this->data['perms']['canChangeBlogOwner']){
			$output['form']->remove('user');
		}

		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			if(!$this->data['perms']['canChangeBlogOwner']){
				$data['user'] = $this->data['user']['username'];
			}
			try{
				$add = $this->model->addBlog($data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				Util\Session::flash('blog-message', 'Blog created!', 'success');	
				redirect($this->site.$this->moduleUrl);
			}
		}
		return $output;
	}
	
	protected function editBlog()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getBlog = $this->model->get('blogs', $this->args[3]);
		if(!$getBlog){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output = array('view' => 'form');
		$output['blogRoles'] = $this->model->getBlogUserRoles($getBlog['blogId']);
		$is_admin = false;
		foreach($output['blogRoles'] as $role){
			if($role['userId'] == $this->data['user']['userId'] AND $role['type'] == 'admin'){
				$is_admin = true;
			}
		}
		if(!$is_admin AND !$this->data['perms']['canManageAllBlogs'] AND $getBlog['userId'] != $this->data['user']['userId']){
			$output['view'] = '403';
			return $output;
		}
		
		$output['form'] = $this->model->getBlogForm($this->data['site']['siteId']);
		if(!$this->data['perms']['canChangeBlogOwner']){
			$output['form']->remove('user');
		}
		
		$getBlog['user'] = '';
		$getBlogUser = $this->model->get('users', $getBlog['userId']);
		if($getBlogUser){
			$getBlog['user'] = $getBlogUser['username'];
		}

		$output['formType'] = 'Edit';
		$output['roleForm'] = $this->model->getBlogRoleForm();
		
		$getBlog['settings'] = $this->model->getSingleBlogSettings($getBlog);
		$settingFormData = $this->model->getBlogSettingFormDataFromKeys($getBlog['settings']);
		$output['settingForm'] = $this->settingModel->getSettingsForm($settingFormData);
    
        $domain = new \UI\Textbox('domain');
        $domain->setLabel('Domain name');
        if(isset($getBlog['settings']['domain'])){
            $domain->setValue($getBlog['settings']['domain']);
        }
        $output['settingForm']->add($domain);    
    
		$settingTrigger = new \UI\Hidden('settingUpdate');
		$settingTrigger->setValue(1);
		$output['settingForm']->add($settingTrigger);
		
		$output['getBlog'] = $getBlog;
		
		if(posted()){
			if(isset($_POST['roleUserId']) AND isset($_POST['roleType'])){
				try{
					$add = $this->model->addBlogRole($getBlog['blogId'], $_POST['roleUserId'], $_POST['roleType'], $this->data['user']);
				}
				catch(\Exception $e){
					$add = false;
					$output['error'] = $e->getMessage();
				}
				if($add){
					Util\Session::flash('blog-message', 'Blog role added!', 'success');	
					redirect($this->data['site']['url'].$this->moduleUrl.'/edit/'.$getBlog['blogId']);
				}							
			}
			elseif(isset($_POST['settingUpdate'])){
				$data = $output['settingForm']->grabData();
                if(isset($_POST['domain'])){
                    $data['domain'] = $_POST['domain'];
                }
				try{
					$update = $this->model->updateBlogSettings($getBlog['blogId'], $data);
				}
				catch(\Exception $e){
					$output['error'] = $e->getMessage();
					$update = false;
				}
                
				if($update){
					Util\Session::flash('blog-message', 'Blog settings updated!', 'success');	
					redirect($this->data['site']['url'].$this->moduleUrl.'/edit/'.$getBlog['blogId']);
				}
                
			}
			else{			
				$data = $output['form']->grabData();
				$data['siteId'] = $this->data['site']['siteId'];
				if(!$this->data['perms']['canChangeBlogOwner']){
					if(isset($data['user'])){
						unset($data['user']);
					}
				}
				try{
					$edit = $this->model->editBlog($this->args[3], $data);
				}
				catch(\Exception $e){
					$output['error'] = $e->getMessage();
					$edit = false;
				}
				if($edit){
					Util\Session::flash('blog-message', 'Blog edited!', 'success');	
					redirect($this->data['site']['url'].$this->moduleUrl.'/edit/'.$getBlog['blogId']);
				}				
			}			
		}
		$output['form']->setValues($getBlog);
		return $output;
	}
	
	protected function deleteBlog()
	{
		if(isset($this->args[3])){
			$getBlog = $this->model->get('blogs', $this->args[3]);
			if($getBlog){
				if($this->data['perms']['canCreateBlogs'] OR $getBlog['userId'] == $this->data['user']['userId']){
					$delete = $this->model->delete('blogs', $this->args[3]);
					Util\Session::flash('blog-message', 'Blog deleted.', 'success');
				}	
			}
		}
		redirect($this->site.$this->moduleUrl);
	}
	
	protected function removeBlogRole()
	{		
		if(!isset($this->args[3]) OR !isset($this->args[4])){
			redirect($this->site.$this->moduleUrl);
		}
		
		$getBlog = $this->model->get('blogs', $this->args[3]);
		$getBlogRole = $this->model->get('blog_roles', $this->args[4]);
		if(!$getBlog OR !$getBlogRole){
			redirect($this->site.$this->moduleUrl);
		}

		$getUser = false;
		if($getBlogRole['userId'] != 0){
			$getUser = $this->model->get('users', $getBlogRole['userId']);
		}
		
		$inventory = new Tokenly\Inventory_Model;		
		$getToken = false;
		if($getBlogRole['token'] != ''){
			$getToken = $inventory->getAssetData($getBlogRole['token']);
		}
		
		$output['blogRoles'] = $this->model->getBlogUserRoles($getBlog['blogId']);
		$is_admin = false;
		foreach($output['blogRoles'] as $role){
			if($role['userId'] == $this->data['user']['userId'] AND $role['type'] == 'admin'){
				$is_admin = true;
			}
		}		
		
		if(!$is_admin AND !$this->data['perms']['canManageAllBlogs'] AND $getBlog['userId'] != $this->data['user']['userId']){
			redirect($this->site.$this->moduleUrl.'/edit/'.$getBlog['blogId']);
		}				
			
		$delete = $this->model->delete('blog_roles', $getBlogRole['userRoleId']);
		
		if($delete){
			if($getBlogRole['userId'] == 0 AND $getBlogRole['token'] != ''){
				//token entry, remove TCA rules
				$this->model->delete('token_access', 'blog-role:'.$getBlogRole['userRoleId'], 'reference');
			}
			elseif($getUser){
				$other_roles = $this->model->getAll('blog_roles', array('userId' => $getUser['userId']));
				$foundEditor = false;
				$foundOwner = false;
				foreach($other_roles as $role){
					if($role['userRoleId'] != $getBlogRole['userRoleId']){
						if($role['type'] == 'editor'){
							$foundEditor = true;
						}
						elseif($role['type'] == 'admin'){
							$foundOwner = true;
						}
					}
				}
				
				if(!$foundEditor){
					$editorGroup = $this->model->get('groups', 'blog-editor', array(), 'slug');
					if($editorGroup){
						$findGroups = $this->model->getAll('group_users', array('groupId' => $editorGroup['groupId'], 'userId' => $getUser['userId']));
						foreach($findGroups as $group){
							$this->model->delete('group_users', $group['groupUserId']);
						}
					}
				}
				
				if(!$foundOwner){
					$ownerGroup = $this->model->get('groups', 'blog-owner', array(), 'slug');
					if($ownerGroup){
						$findGroups = $this->model->getAll('group_users', array('groupId' => $ownerGroup['groupId'], 'userId' => $getUser['userId']));
						foreach($findGroups as $group){
							$this->model->delete('group_users', $group['groupUserId']);
						}
					}
				}
			}
			
		}
		redirect($this->site.$this->moduleUrl.'/edit/'.$getBlog['blogId']);
	}
}

