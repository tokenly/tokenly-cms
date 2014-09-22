<?php
class Slick_App_Dashboard_ForumBoard_Controller extends Slick_App_ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->model = new Slick_App_Dashboard_ForumBoard_Model;
        
        
    }
    
    public function init()
    {
		$output = parent::init();
		$this->data['perms'] = Slick_App_Meta_Model::getUserAppPerms($this->data['user']['userId'], 'forum');
			
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->showBoards();
					break;
				case 'add':
					$output = $this->addBoard();
					break;
				case 'edit':
					$output = $this->editBoard();
					break;
				case 'delete':
					$output = $this->deleteBoard();
					break;
				case 'remove-mod':
					$output = $this->removeModerator();
					break;
				default:
					$output = $this->showBoards();
					break;
			}
		}
		else{
			$output = $this->showBoards();
		}
		$output['template'] = 'admin';
        $output['perms'] = $this->data['perms'];	
        return $output;
    }
    
    private function showBoards()
    {
		$output = array('view' => 'list');
		$output['boardList'] = $this->model->fetchAll('SELECT b.*, c.name as category
													 FROM forum_boards b
													 LEFT JOIN forum_categories c ON c.categoryId = b.categoryId
													 WHERE b.siteId = :siteId
													 ORDER BY c.rank ASC, b.categoryId ASC, b.rank ASC',
													array(':siteId' => $this->data['site']['siteId']));

		if(!$this->data['perms']['canManageAllBoards']){
			foreach($output['boardList'] as $key => $board){
				if($board['ownerId'] != $this->data['user']['userId']){
					unset($output['boardList'][$key]);
				}
			}
		}
		
		return $output;
		
	}
	
	
	private function addBoard()
	{
		$output = array('view' => 'form');
		if(!$this->data['perms']['canManageAllBoards']){
			$output['view'] = '403';
			return $output;
		}
		$output['form'] = $this->model->getBoardForm($this->data['site']['siteId']);
		if(!$this->data['perms']['canChangeBoardCategory']){
			$output['form']->remove('categoryId');
		}
		if(!$this->data['perms']['canChangeBoardOwner']){
			$output['form']->remove('ownerId');
		}
		if(!$this->data['perms']['canChangeBoardRank']){
			$output['form']->remove('rank');
		}
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			if(!$this->data['perms']['canChangeBoardCategory']){
				$data['categoryId'] = 0;
			}
			try{
				$add = $this->model->addBoard($data);
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
	

	
	private function editBoard()
	{
		if(!isset($this->args[3])){
			$this->redirect('/');
			return false;
		}
		
		$getBoard = $this->model->get('forum_boards', $this->args[3]);
		if(!$getBoard){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$output = array('view' => 'form');
		if(!$this->data['perms']['canManageAllBoards'] AND $getBoard['ownerId'] != $this->data['user']['userId']){
			$output['view'] = '403';
			return $output;
		}
		$output['form'] = $this->model->getBoardForm($this->data['site']['siteId']);
		if(!$this->data['perms']['canChangeBoardOwner']){
			$output['form']->remove('ownerId');
		}
		if(!$this->data['perms']['canChangeBoardCategory']){
			$output['form']->remove('categoryId');
		}
		if(!$this->data['perms']['canChangeBoardRank']){
			$output['form']->remove('rank');
		}		
		$output['formType'] = 'Edit';
		$output['boardMods'] = $this->model->getBoardMods($getBoard['boardId']);
		$output['modForm'] = $this->model->getModForm();
		$output['getBoard'] = $getBoard;
		
		if(posted()){
			if(isset($_POST['userId'])){
				try{
					$add = $this->model->addMod($getBoard['boardId'], $_POST['userId']);
				}
				catch(Exception $e){
					$output['error'] = $e->getMessage();
				}
				if($add){
					$this->redirect($this->site.'/'.$this->moduleUrl.'/edit/'.$getBoard['boardId']);
					return true;
				}							
			}
			else{			
				$data = $output['form']->grabData();
				$data['siteId'] = $this->data['site']['siteId'];
				try{
					$add = $this->model->editBoard($this->args[3], $data);
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
		}
		$output['form']->setValues($getBoard);
		
		return $output;
		
	}
	

	
	
	private function deleteBoard()
	{
		if(!isset($this->args[3])){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$getBoard = $this->model->get('forum_boards', $this->args[3]);
		if(!$getBoard){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		if(!$this->data['perms']['canManageAllBoards'] AND $getBoard['ownerId'] != $this->data['user']['userId']){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}		
		
		$delete = $this->model->delete('forum_boards', $this->args[3]);
		$this->redirect($this->site.'/'.$this->moduleUrl);
		return true;
	}
	
	private function removeModerator()
	{
		if(!isset($this->args[3]) OR !isset($this->args[4])){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$getBoard = $this->model->get('forum_boards', $this->args[3]);
		$getUser = $this->model->get('users', $this->args[4]);
		if(!$getBoard OR !$getUser){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		if(!$this->data['perms']['canManageAllBoards'] AND $getBoard['ownerId'] != $this->data['user']['userId']){
			$this->redirect($this->site.'/'.$this->moduleUrl.'/edit/'.$getBoard['boardId']);
			return false;
		}				
		
		$boardMod = $this->model->getAll('forum_mods', array('userId' => $getUser['userId'], 'boardId' => $getBoard['boardId']));
		if(!$boardMod OR count($boardMod) == 0){
			$this->redirect($this->site.'/'.$this->moduleUrl.'/edit/'.$getBoard['boardId']);
			return false;
		}
		
		$this->model->delete('forum_mods', $boardMod[0]['modId']);
		
		$this->redirect($this->site.'/'.$this->moduleUrl.'/edit/'.$getBoard['boardId']);
	}
	


}

?>
