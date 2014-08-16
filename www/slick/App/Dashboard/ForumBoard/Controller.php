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
				default:
					$output = $this->showBoards();
					break;
			}
		}
		else{
			$output = $this->showBoards();
		}
		$output['template'] = 'admin';
        
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

		
		return $output;
		
	}
	
	
	private function addBoard()
	{
		$output = array('view' => 'form');
		$output['form'] = $this->model->getBoardForm($this->data['site']['siteId']);
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
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
		$output['form'] = $this->model->getBoardForm($this->data['site']['siteId']);
		$output['formType'] = 'Edit';
		
		if(posted()){
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
		
		$delete = $this->model->delete('forum_boards', $this->args[3]);
		$this->redirect($this->site.'/'.$this->moduleUrl);
		return true;
	}
	


}

?>
