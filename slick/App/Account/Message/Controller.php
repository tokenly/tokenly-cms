<?php
namespace App\Account;
/*
 * @module-type = dashboard
 * @menu-label = Private Messages
 * 
 * */
class Message_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Message_Model;
		
		
	}
	
	protected function init()
	{
		$output = parent::init();
		$this->model->appData = $this->data;

		if(!$this->data['user']){
			redirect($this->data['site']['url']);
		}
		$output['template'] = 'admin';
		$output['title'] = 'Private Messages';		
		
		if(isset($this->args[2]) AND trim($this->args[2]) != ''){
			switch($this->args[2]){
				case 'send':
					$output = $this->container->sendMessage($output);
					break;
				case 'sent':
					$output = $this->container->outbox($output);
					break;
				case 'view':
					$output = $this->container->viewMessage($output);
					break;
				case 'delete':
					$output = $this->container->deleteMessage($output);
					break;
				default:
					$output['view'] = '404';
					break;
			}
		}
		else{
			$output = $this->container->inbox($output);
		}


		return $output;	
	}
	
	protected function inbox($output)
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
	
	protected function outbox($output)
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
	
	protected function sendMessage($output)
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
			catch(\Exception $e){
				$send = false;
				$output['message'] = $e->getMessage();
			}
			
			if($send){
				redirect($this->site.$this->moduleUrl.'/view/'.$send.'#message');
			}
		}
		
		if(isset($_GET['user'])){
			$output['form']->setValues(array('username' => $_GET['user']));
		}
		
		return $output;		
	}
	
	protected function viewMessage($output)
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
			redirect($this->site.$this->moduleUrl.'/view/'.$chainEnd.'#message-'.$getMessage['messageId']);
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
			catch(\Exception $e){
				$reply = false;
				$output['error'] = $e->getMessage();
			}
			
			if($reply){
				redirect($this->site.$this->moduleUrl.'/view/'.$reply.'#message-'.$reply.'');
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
	
	protected function deleteMessage($output)
	{
		//delete but dont delete for both people....
		return $output;		
	}
}
