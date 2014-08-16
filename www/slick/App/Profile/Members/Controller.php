<?php
class Slick_App_Profile_Members_Controller extends Slick_App_ModControl
{
	public $args;
	public $data;
	
    function __construct()
    {
        parent::__construct();
        $this->model = new Slick_App_Profile_Members_Model;
        
        
    }
    
    public function init()
    {
		$output = parent::init();
		$output['view'] = 'list';
		
		$profModel = new Slick_App_Profile_User_Model;
		$max = 20;
		$page = 1;
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
			if($page <= 0){
				$page = 1;
			}
		}
		
		$start = ($page * $max) - $max;
		
		$totalUsers = $this->model->count('users');
		
		$users = $this->model->fetchAll('SELECT userId, username, email, regDate, lastActive, slug
										FROM users
										ORDER BY userId DESC
										LIMIT '.$start.', '.$max);
		foreach($users as $key => $user){
			$profile = $profModel->getUserProfile($user['userId'], $this->data['site']['siteId']);
			if($profile['pubProf'] == 0){
				unset($users[$key]);
				$totalUsers--;
				continue;
			}
			$users[$key]['profile'] = $profile;
			
		}
		$output['numPages'] = ceil($totalUsers / $max);
		
		$output['members'] = $users;
		$output['title'] = 'Members';
		
		return $output;
	}
	
}

?>
