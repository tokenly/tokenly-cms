<?php
namespace App\CMS;
use App\ModControl;
use App\Account\Credits_Model;
use Util, UI, Util\Session;
/*
 * @module-type = dashboard
 * @menu-label = System Credits
 * 
 * */

class Credits_Controller extends ModControl
{
    function __construct()
    {
        $this->model = new Credits_Model;
    }
    
	protected function init()
	{
		$output = parent::init();
		$this->model->appData = $this->data;

		$output['template'] = 'admin';
		$output['title'] = 'Account Credits';
        
		
		if(isset($this->args[2]) AND trim($this->args[2]) != ''){
			switch($this->args[2]){
                case 'delete':
                    $output = $this->deleteCreditEntry();
                    break;
				default:
					$output['view'] = '404';
					break;
			}
		}
        else{
            $output = $this->showIndex($output);
        }

		return $output;	
	}
    
    protected function showIndex($output)
    {
        if(posted()){
            return $this->submitCreditEntry($output);
        }
        
        $output['view'] = 'index';
        $user_search = false;
        if(isset($_GET['search']) AND trim($_GET['search']) != ''){
            $user_search = $_GET['search'];
            $getUser = $this->model->get('users', $_GET['search'], array(), 'username');
            if(!$getUser){
                Session::flash('message', 'User not found', 'text-danger');
                redirect($this->moduleUrl);
            }
            $output['credit_entries'] = $this->model->getCreditEntries($getUser['userId']);
        }
        else{
            $output['credit_entries'] = $this->model->getAllCreditEntries();
        }
        $output['num_entries'] = 0;
        $output['num_debits'] = 0;
        $output['num_credits'] = 0;
        $output['total_supply'] = 0;
        $output['total_debit'] = 0;
        $output['total_credit'] = 0;
        $output['user_search'] = $user_search;
        
        //process some stats
        if($output['credit_entries']){
            foreach($output['credit_entries'] as $row){
                $output['num_entries']++;
                switch($row['type']){
                    case 'debit':
                        $output['num_debits']++;
                        $output['total_supply'] -= $row['amount'];
                        $output['total_debit'] += $row['amount'];
                        break;
                    case 'credit':
                    default:
                        $output['num_credits']++;
                        $output['total_supply'] += $row['amount'];
                        $output['total_credit'] += $row['amount'];
                        break;
                }
            }
        }
        
        if(!$user_search){
            $per_page = 25;
            $pager = new Util\Paging;
            $page_data = $pager->pageArray($output['credit_entries'], $per_page);
            $page = 1;
            if(isset($_GET['page']) AND isset($page_data[$_GET['page']])){
                $page = intval($_GET['page']);
            }
            $output['credit_entries'] = $page_data[$page];
            $output['num_pages'] = count($page_data);
            $page_ui = new UI\Pager;
            $page_ui->addClass('paginator');
            $output['pager'] = $page_ui->display($output['num_pages'], '?page=', $page);
        }
        else{
            $output['num_pages'] = 1;
            $output['pager'] = false;
            foreach($output['credit_entries'] as $k => $row){
                $output['credit_entries'][$k]['username'] = $getUser['username'];
                $output['credit_entries'][$k]['email'] = $getUser['email'];
                $output['credit_entries'][$k]['slug'] = $getUser['slug'];
            }
        }
        
        return $output;
    }
    
    protected function submitCreditEntry($output)
    {              
        if(!posted()){
            redirect($this->moduleUrl);
        }
        $to = false;
        if(isset($_POST['transfer-username'])){
            $to = trim($_POST['transfer-username']);
        }
        
        $amount = false;
        if(isset($_POST['transfer-amount'])){
            $amount = floatval($_POST['transfer-amount']);
        }
        
        $note = false;
        if(isset($_POST['transfer-note'])){
            $note = trim($_POST['transfer-note']);
        }
        

        if(!$to OR $to == '' OR !$amount){
            Session::flash('message', 'Please enter all required fields', 'text-danger');
            redirect($this->moduleUrl);
        }
        
        if($amount < 0){
            Session::flash('message', 'Please enter a valid amount', 'text-danger');
            redirect($this->moduleUrl);
        }
        
        $getUser = $this->model->get('users', $to, array('userId'), 'username');
        if(!$getUser){
            Session::flash('message', 'Invalid user', 'text-danger');
            redirect($this->moduleUrl);       
        }
        
        $type = 'credit';
        $valid_types = array('credit', 'debit');
        if(isset($_POST['transfer-type']) AND in_array($_POST['transfer-type'], $valid_types)){
            $type = $_POST['transfer-type'];
        }

        $credit = $this->model->insertCreditEntry($getUser['userId'], 'admin', $amount, $type, 'admin:'.$this->data['user']['userId'], $note);
        
        Session::flash('message', 'Credit entry submitted!', 'text-success');
        redirect($this->moduleUrl);
    }
    
    
    protected function deleteCreditEntry()
    {
        if(isset($this->args[3]) AND trim($this->args[3]) != ''){
            $getEntry = $this->model->get('system_credits', $this->args[3]);
            if(!$getEntry){
                Session::flash('message', 'Entry not found', 'text-danger');
            }
            else{
                $delete = $this->model->delete('system_credits', $getEntry['id']);
                if(!$delete){
                    Session::flash('message', 'Error deleting entry', 'text-danger');
                }
                else{
                    Session::flash('message', 'Entry deleted!', 'text-success');
                }
            }            
        }
        redirect($this->moduleUrl);
    }
    
}
