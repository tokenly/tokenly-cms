<?php
class Slick_App_Dashboard_Blog_Multiblog_Controller extends Slick_App_ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        $this->model = new Slick_App_Dashboard_Blog_Multiblog_Model;

    }
    
    public function init()
    {
		$output = parent::init();
		$this->data['perms'] = Slick_App_Meta_Model::getUserAppPerms($this->data['user']['userId'], 'blog');
			
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->showBlogs();
					break;
				case 'add':
					$output = $this->addBlog();
					break;
				case 'edit':
					$output = $this->editBlog();
					break;
				case 'delete':
					$output = $this->deleteBlog();
					break;
				case 'remove-role':
					$output = $this->removeBlogRole();
					break;
				default:
					$output = $this->showBlogs();
					break;
			}
		}
		else{
			$output = $this->showBlogs();
		}
		$output['template'] = 'admin';
        $output['perms'] = $this->data['perms'];	
        return $output;
    }
    
    private function showBlogs()
    {
		$output = array('view' => 'list');
		
		$wheres = array('siteId' => $this->data['site']['siteId']);		
		$output['blogList'] = $this->model->getAll('blogs', $wheres);
		foreach($output['blogList'] as &$blog){
			$roles = $this->model->getBlogUserRoles($blog['blogId']);
			$roleList = array();
			foreach($roles as $role){
				if(!isset($roleList[$role['type']])){
					$roleList[$role['type']] = array();
				}
				$roleList[$role['type']][] = $role['userId'];
			}
			$blog['roles'] = $roleList;
			
		}
		return $output;
		
	}
	
	
	private function addBlog()
	{
		$output = array('view' => 'form');
		if(!$this->data['perms']['canCreateBlogs']){
			$output['view'] = '403';
			return $output;
		}
		$output['form'] = $this->model->getBlogForm($this->data['site']['siteId']);

		if(!$this->data['perms']['canChangeBlogOwner']){
			$output['form']->remove('userId');
		}

		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			if(!$this->data['perms']['canChangeBlogOwner']){
				$data['userId'] = $this->data['user']['userId'];
			}
			try{
				$add = $this->model->addBlog($data);
			}
			catch(Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				Slick_Util_Session::flash('blog-message', 'Blog created!', 'success');	
				$this->redirect($this->site.$this->moduleUrl);
				return true;
			}
		}
		return $output;
	}
	

	
	private function editBlog()
	{
		if(!isset($this->args[3])){
			$this->redirect('/');
			return false;
		}
		
		$getBlog = $this->model->get('blogs', $this->args[3]);
		if(!$getBlog){
			$this->redirect($this->site.$this->moduleUrl);
			return false;
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
			$output['form']->remove('userId');
		}

		$output['formType'] = 'Edit';
		$output['roleForm'] = $this->model->getBlogRoleForm();
		$output['getBlog'] = $getBlog;
		
		if(posted()){
			if(isset($_POST['roleUserId']) AND isset($_POST['roleType'])){
				try{
					$add = $this->model->addBlogRole($getBlog['blogId'], $_POST['roleUserId'], $_POST['roleType']);
				}
				catch(Exception $e){
					$add = false;
					$output['error'] = $e->getMessage();
				}
				if($add){
					Slick_Util_Session::flash('blog-message', 'Blog role added!', 'success');	
					$this->redirect($this->site.$this->moduleUrl.'/edit/'.$getBlog['blogId']);
					return true;
				}							
			}
			else{			
				$data = $output['form']->grabData();
				$data['siteId'] = $this->data['site']['siteId'];
				try{
					$edit = $this->model->editBlog($this->args[3], $data);
				}
				catch(Exception $e){
					$output['error'] = $e->getMessage();
					$edit = false;
				}
				if($edit){
					Slick_Util_Session::flash('blog-message', 'Blog edited!', 'success');	
					$this->redirect($this->site.$this->moduleUrl);
					return true;
				}				
			}			
		}
		$output['form']->setValues($getBlog);
		
		return $output;
		
	}
	

	
	
	private function deleteBlog()
	{
		if(!isset($this->args[3])){
			$this->redirect($this->site.$this->moduleUrl);
			return false;
		}
		
		$getBlog = $this->model->get('blogs', $this->args[3]);
		if(!$getBlog){
			$this->redirect($this->site.$this->moduleUrl);
			return false;
		}
		
		if(!$this->data['perms']['canCreateBlogs'] AND $getBlog['userId'] != $this->data['user']['userId']){
			$this->redirect($this->site.$this->moduleUrl);
			return false;
		}		
		
		$delete = $this->model->delete('blogs', $this->args[3]);
		Slick_Util_Session::flash('blog-message', 'Blog deleted.', 'success');	
		$this->redirect($this->site.$this->moduleUrl);
		return true;
	}
	
	private function removeBlogRole()
	{
		if(!isset($this->args[3]) OR !isset($this->args[4])){
			$this->redirect($this->site.$this->moduleUrl);
			return false;
		}
		
		$getBlog = $this->model->get('blogs', $this->args[3]);
		$getUser = $this->model->get('users', $this->args[4]);
		if(!$getBlog OR !$getUser){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$output['blogRoles'] = $this->model->getBlogUserRoles($getBlog['blogId']);
		$is_admin = false;
		foreach($output['blogRoles'] as $role){
			if($role['userId'] == $this->data['user']['userId'] AND $role['type'] == 'admin'){
				$is_admin = true;
			}
		}		
		
		if(!$is_admin AND !$this->data['perms']['canManageAllBlogs'] AND $getBlog['userId'] != $this->data['user']['userId']){
			$this->redirect($this->site.$this->moduleUrl.'/edit/'.$getBlog['blogId']);
			return false;
		}				
		
		$blogRole = $this->model->getAll('blog_roles', array('userId' => $getUser['userId'], 'blogId' => $getBlog['blogId']));
		if(!$blogRole OR count($blogRole) == 0){
			$this->redirect($this->site.$this->moduleUrl.'/edit/'.$getBlog['blogId']);
			return false;
		}
		
		$delete = $this->model->delete('blog_roles', $blogRole[0]['userRoleId']);
		
		if($delete){
			$other_roles = $this->model->getAll('blog_roles', array('userId' => $getUser['userId']));
			$foundEditor = false;
			$foundOwner = false;
			foreach($other_roles as $role){
				if($role['roleId'] != $blogRole['roleId']){
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
		
		$this->redirect($this->site.$this->moduleUrl.'/edit/'.$getBlog['blogId']);
	}
	


}

?>
