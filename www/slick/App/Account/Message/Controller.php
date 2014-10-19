<?php
class Slick_App_Account_Message_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Account_Message_Model;
		
		
	}
	
	public function init()
	{
		$output = parent::init();
		$this->model->appData = $this->data;

		if(!$this->data['user']){
			$this->redirect($this->data['site']['url']);
			return false;
		}
		$output['template'] = 'admin';
		$output['title'] = 'Private Messages';		
		
		if(isset($this->args[2]) AND trim($this->args[2]) != ''){
			switch($this->args[2]){
				case 'send':
					$output = $this->sendMessage($output);
					break;
				case 'sent':
					$output = $this->outbox($output);
					break;
				case 'view':
					$output = $this->viewMessage($output);
					break;
				case 'delete':
					$output = $this->deleteMessage($output);
					break;
				default:
					$output['view'] = '404';
					break;
			}
		}
		else{
			$output = $this->inbox($output);
		}


		return $output;	
	}
	
	private function inbox($output)
	{
		$page = 0;
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
		}
		
		$perPage = 50;
		
		$output['messages'] = $this->model->getUserInbox($this->data['user']['userId'], $perPage, $page);
		$output['numMessages'] = count($this->model->getUserInbox($this->data['user']['userId'], false));
		$output['perPage'] = $perPage;
		$output['numPages'] = ceil($output['numMessages'] / $output['perPage']);
		$output['view'] = 'index';
		$output['pmbox'] = 'inbox';
		$output['title'] = 'Inbox | '.$output['title'];
		
		return $output;
	}
	
	private function outbox($output)
	{
		$page = 0;
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
		}
		
		$perPage = 50;
		
		$output['messages'] = $this->model->getUserOutbox($this->data['user']['userId'], $perPage, $page);
		$output['numMessages'] = count($this->model->getUserOutbox($this->data['user']['userId'], false));
		$output['perPage'] = $perPage;
		$output['numPages'] = ceil($output['numMessages'] / $output['perPage']);
		$output['view'] = 'index';
		$output['pmbox'] = 'outbox';
		$output['title'] = 'Outbox | '.$output['title'];
	
		return $output;		
	}
	
	private function sendMessage($output)
	{
		$output['view'] = 'send';
		$output['message'] = '';
		$output['form'] = $this->model->getMessageForm();
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['userId'] = $this->data['user']['userId'];
			try{
				$send = $this->model->sendMessage($data);
			}
			catch(Exception $e){
				$send = false;
				$output['message'] = $e->getMessage();
			}
			
			if($send){
				$this->redirect($this->site.$this->moduleUrl.'/view/'.$send.'#message');
			}
		}
		
		if(isset($_GET['user'])){
			$output['form']->setValues(array('username' => $_GET['user']));
		}
		
		return $output;		
	}
	
	private function viewMessage($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $output;
		}
		
		$getMessage = $this->model->get('private_messages', $this->args[3]);
		if(!$getMessage OR ($getMessage['userId'] != $this->data['user']['userId'] AND $getMessage['toUser'] != $this->data['user']['userId'])){
			$output['view'] = '404';
			return $output;
		}
		
		$chainEnd = $this->model->getEndOfChain($getMessage['messageId']);
		if($chainEnd != $getMessage['messageId'] AND !posted()){
			$this->redirect($this->site.$this->moduleUrl.'/view/'.$chainEnd.'#message-'.$getMessage['messageId']);
			return $output;
		}
		
		$getMessage = $this->model->parseMessage($getMessage);
	
		
		if($getMessage['isRead'] == 0 AND $this->data['user']['userId'] == $getMessage['toUser']){
			$this->model->edit('private_messages', $getMessage['messageId'], array('isRead' => 1));
		}
		
		$output['title'] = $getMessage['subject'].' | '.$output['title'];
		$output['form'] = $this->model->getMessageForm();
		$output['form']->remove('username');
		$output['view'] = 'view';
		$output['error'] = '';
		$output['mainMessage'] = $getMessage;
		
		$replies = array_reverse($this->model->getReplyChain($getMessage['replyId']));
		$replies[] = $getMessage;
		$output['messages'] = $replies;
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['userId'] = $this->data['user']['userId'];
			$data['toUser'] = $getMessage['userId'];
			$data['replyId'] = $chainEnd; //make sure reply is always from latest in PM chain
			
			if($getMessage['userId'] == $this->data['user']['userId']){
				$data['toUser'] = $getMessage['toUser'];
			}
			
			try{
				$reply = $this->model->sendReply($data);
			}
			catch(Exception $e){
				$reply = false;
				$output['error'] = $e->getMessage();
			}
			
			if($reply){
				$this->redirect($this->site.$this->moduleUrl.'/view/'.$reply.'#message-'.$reply.'');
			}
		}
		
		if(substr(strtolower($getMessage['subject']), 0, 3) != 're:'){
			$output['form']->setValues(array('subject' => 'Re: '.$getMessage['subject']));
		}
		else{
			$output['form']->setValues(array('subject' => $getMessage['subject']));
		}
		
		return $output;		
	}
	
	private function deleteMessage($output)
	{
		//delete but dont delete for both people....
		return $output;		
	}
}
