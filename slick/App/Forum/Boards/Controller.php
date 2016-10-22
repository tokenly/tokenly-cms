<?php
namespace App\Forum;
/*
 * @module-type = dashboard
 * @menu-label = Manage Boards
 * 
 * */
class Boards_Controller extends \App\ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        $this->model = new Boards_Model;
        $this->board_model = new Board_Model;
    }
    
    protected function init()
    {
		$output = parent::init();
		$this->data['perms'] = \App\Meta_Model::getUserAppPerms($this->data['user']['userId'], 'forum');
			
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->container->showBoards();
					break;
				case 'add':
					$output = $this->container->addBoard();
					break;
				case 'edit':
					$output = $this->container->editBoard();
					break;
				case 'delete':
					$output = $this->container->deleteBoard();
					break;
				case 'remove-mod':
					$output = $this->container->removeModerator();
					break;
				default:
					$output = $this->container->showBoards();
					break;
			}
		}
		else{
			$output = $this->container->showBoards();
		}
		$output['template'] = 'admin';
        $output['perms'] = $this->data['perms'];	
        return $output;
    }
    
    protected function showBoards()
    {
		$output = array('view' => 'list');
		$output['boardList'] = $this->model->getBoardFormParentList();

		if(!$this->data['perms']['canManageAllBoards']){
			foreach($output['boardList'] as $key => $board){
				if($board['ownerId'] != $this->data['user']['userId']){
					unset($output['boardList'][$key]);
				}
			}
		}
		return $output;
	}
	
	
	protected function addBoard()
	{
		$output = array('view' => 'form');
		if(!$this->data['perms']['canManageAllBoards']){
			$output['view'] = '403';
			return $output;
		}
		$output['form'] = $this->model->getBoardForm();
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
            if(!$this->data['perms']['canChangeParentBoard']){
                $output['form']->remove('parentId');
            }                 
			try{
				$add = $this->model->addBoard($data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->moduleUrl);
			}
			
		}
		return $output;
	}
	
	protected function editBoard()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getBoard = $this->model->get('forum_boards', $this->args[3]);
		if(!$getBoard){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output = array('view' => 'form');
		if(!$this->data['perms']['canManageAllBoards'] AND $getBoard['ownerId'] != $this->data['user']['userId']){
			$output['view'] = '403';
			return $output;
		}
		$output['form'] = $this->model->getBoardForm($getBoard['boardId']);
		if(!$this->data['perms']['canChangeBoardOwner']){
			$output['form']->remove('ownerId');
		}
		if(!$this->data['perms']['canChangeBoardCategory']){
			$output['form']->remove('categoryId');
		}
		if(!$this->data['perms']['canChangeBoardRank']){
			$output['form']->remove('rank');
		}		
        if(!$this->data['perms']['canChangeParentBoard']){
            $output['form']->remove('parentId');
        }        
		$output['formType'] = 'Edit';
		$output['boardMods'] = $this->model->getBoardMods($getBoard['boardId']);
		$output['modForm'] = $this->model->getModForm();
		$output['getBoard'] = $getBoard;
        $output['boardMeta'] = $this->board_model->boardMeta($getBoard['boardId']);
		
		if(posted()){
			if(isset($_POST['userId'])){
				try{
					$add = $this->model->addMod($getBoard['boardId'], $_POST['userId']);
				}
				catch(\Exception $e){
					$output['error'] = $e->getMessage();
				}
				if($add){
					redirect($this->site.$this->moduleUrl.'/edit/'.$getBoard['boardId']);
				}							
			}
			else{			
				$data = $output['form']->grabData();
				$data['siteId'] = $this->data['site']['siteId'];
				try{
					$add = $this->model->editBoard($this->args[3], $data);
				}
				catch(\Exception $e){
					$output['error'] = $e->getMessage();
					$add = false;
				}
				if($add){
					redirect($this->site.$this->moduleUrl);
				}				
			}			
		}
		$output['form']->setValues($getBoard);
		
		return $output;
	}
	
	protected function deleteBoard()
	{
		if(isset($this->args[3])){
			$getBoard = $this->model->get('forum_boards', $this->args[3]);
			if($getBoard){
				if($this->data['perms']['canManageAllBoards'] OR $getBoard['ownerId'] == $this->data['user']['userId']){
					$delete = $this->model->delete('forum_boards', $this->args[3]);
                    $this->model->sendQuery('UPDATE forum_boards SET parentId = 0 WHERE parentId = :id', array(':id' => $this->args[3]));
				}						
			}			
		}
		redirect($this->site.$this->moduleUrl);
	}
	
	protected function removeModerator()
	{
		if(isset($this->args[3]) AND isset($this->args[4])){
			$getBoard = $this->model->get('forum_boards', $this->args[3]);
			$getUser = $this->model->get('users', $this->args[4]);
			if($getBoard AND $getUser){
				if($this->data['perms']['canManageAllBoards'] OR $getBoard['ownerId'] == $this->data['user']['userId']){
					$boardMod = $this->model->getAll('forum_mods', array('userId' => $getUser['userId'], 'boardId' => $getBoard['boardId']));
					if($boardMod AND count($boardMod) > 0){
						$this->model->delete('forum_mods', $boardMod[0]['modId']);
					}
				}		
			}
		}
		redirect($this->site.$this->moduleUrl.'/edit/'.$getBoard['boardId']);
	}
}
