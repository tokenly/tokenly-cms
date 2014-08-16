<?php
class Slick_App_Account_Notification_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_Core_Model;
		
	}
	
	public function init()
	{
		$output = parent::init();

		if(!$this->data['user']){
			$this->redirect($this->data['site']['url']);
			return false;
		}
		
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'setread':
					header('Content-Type: text/json');
					$output = array('result' => 'success');
					$this->model->sendQuery('UPDATE user_notifications SET isRead = 1 WHERE userId = :userId', array(':userId' => $this->data['user']['userId']));
					echo json_encode($output);
					die();
					break;
				case 'check':
					header('Content-type: text/json');
					$output = array();
					$getNotes = $this->model->getAll('user_notifications', array('isRead' => 0, 'userId' => $this->data['user']['userId']));
					foreach($getNotes as $key => $row){
						$getNotes[$key]['formatDate'] = formatDate($row['noteDate']);
					}
					$output['notes'] = $getNotes;
					echo json_encode($output);
					die();
					break;
				
			}
		}
		
		if(isset($_GET['clear'])){
			$this->model->sendQuery('DELETE FROM user_notifications WHERE userId = :id',
									array(':id' => $this->data['user']['userId']));
			
			$this->redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url']);
			die();
		}

		$output['view'] = 'index';
		$output['template'] = 'admin';
		$output['title'] = 'Notifications';
		
		$totalNotes = $this->model->count('user_notifications', 'userId', $this->data['user']['userId']);
		$start = 0;
		$max = 25;
		$numPages = ceil($totalNotes / $max);
		
		$page = 1;
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
			if($page > 1 AND $page <= $numPages){
				$start = ($page  * $max) - $max;
			}
		}
		$limit = 'LIMIT '.$start.', '.$max;
		
		$getNotes = $this->model->fetchAll('SELECT * FROM user_notifications
											WHERE userId = :userId
											ORDER BY noteId DESC
											'.$limit, array(':userId' => $this->data['user']['userId']));
		
		$output['totalNotes'] = $totalNotes;
		$output['notes'] = $getNotes;
		$output['numPages'] = $numPages;
		$output['page'] = $page;
		
		$this->model->sendQuery('UPDATE user_notifications SET isRead = 1 WHERE userId = :userId', array(':userId' => $this->data['user']['userId']));
		
		return $output;	
	}
	
	
}
