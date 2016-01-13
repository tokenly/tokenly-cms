<?php
namespace App\Profile;
use App\Account;
class Members_Controller extends \App\ModControl
{

    function __construct()
    {
        parent::__construct();
        $this->model = new Members_Model;
    }
    
    protected function init()
    {
		$output = parent::init();
		$output['view'] = 'list';
		
		$profModel = new User_Model;
		$max = 20;
		$page = 1;
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
			if($page <= 0){
				$page = 1;
			}
		}
		
		$start = ($page * $max) - $max;
		
		
		
		$output['query'] = '';
		$output['sort_query'] = '';
		if(isset($_GET['q']) AND trim($_GET['q']) != ''){
			$_GET['q'] = htmlentities($_GET['q']);
			$users = $this->model->fetchAll('SELECT userId, username, email, regDate, lastActive, slug
											FROM users
											WHERE username LIKE :query OR slug LIKE :query2
											ORDER BY lastActive DESC
											LIMIT '.$start.', '.$max,
											array(':query' => '%'.$_GET['q'].'%', ':query2' => '%'.$_GET['q'].'%'));
			$output['query'] = $_GET['q'];
			$totalUsers = $this->model->fetchSingle('SELECT count(*) as total FROM users
													 WHERE username LIKE :query OR slug LIKE :query2',
													 array(':query' => '%'.$_GET['q'].'%', ':query2' => '%'.$_GET['q'].'%'));
			if($totalUsers){
				$totalUsers = $totalUsers['total'];
			}
		}
		else{
			$orderBy = 'lastActive DESC';
			if(isset($_GET['sort'])){
				switch($_GET['sort']){
					case 'active':
						$orderBy = 'lastActive DESC';
						break;
					case 'alph':
						$orderBy = 'username ASC';
						break;
					case 'new':
						$orderBy = 'userId DESC';
						break;
					case 'old':
						$orderBy = 'userId ASC';
						break;
				}
				$output['sort_query'] = '&sort='.$_GET['sort'];
			}
			
			$users = $this->model->fetchAll('SELECT userId, username, email, regDate, lastActive, slug
											FROM users
											ORDER BY '.$orderBy.'
											LIMIT '.$start.', '.$max);
			$totalUsers = $this->model->count('users');											
		}
		
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
		$output['usersFound'] = $totalUsers;
		$output['members'] = $users;
		$output['title'] = 'Community Directory';
		$output['numUsers'] = $this->model->count('users');
		$output['numOnline'] = Account\Home_Model::getUsersOnline();
		$output['mostOnline'] = Account\Home_Model::getMostOnline();		
		$output['template'] = 'profile';
		
		return $output;
	}
}
