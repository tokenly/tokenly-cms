<?php
namespace App\Account;
use Util\Session;
use API;
/*
 * @module-type = dashboard
 * @menu-label = System Credits
 * 
 * */
class Credits_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Credits_Model;
        $this->tokenslot = new API\TokenSlotClient();
		
	}
	
	protected function init()
	{
		$output = parent::init();
		$this->model->appData = $this->data;

		$output['template'] = 'admin';
		$output['title'] = 'System Credits';
        
		
		if(isset($this->args[2]) AND trim($this->args[2]) != ''){
			switch($this->args[2]){
                case 'transfer':
                    $output = $this->transferCredit($output);
                    break;
                case 'purchase':
                    $output = $this->purchaseCredit($output);
                    break;
                case 'pay':
                    $output = $this->paymentPage($output);
                    break;
                case 'check':
                    $output = $this->checkPayment();
                    break;
                case '_receive':
                    $output = $this->receiveWebhook($output);
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
        if(!$this->data['user']){
            redirect(route('account.auth'));
        }                
        $output['view'] = 'index';
        $output['balance'] = $this->model->getCreditBalance();
        $output['credit_entries'] = $this->model->getCreditEntries($this->data['user']['userId']);
        $output['payment_tokens'] = $this->model->getCreditPaymentTokens();
        
        return $output;
    }
	
    protected function transferCredit($output)
    {
        if(!$this->data['user']){
            redirect(route('account.auth'));
        }                
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
        
        $balance = $this->model->getCreditBalance();
        
        if(!$to OR $to == '' OR !$amount){
            Session::flash('message', 'Please enter all required fields', 'text-danger');
            redirect($this->moduleUrl);
        }
        
        if($amount < 0 OR $amount > $balance){
            Session::flash('message', 'Please enter a valid amount', 'text-danger');
            redirect($this->moduleUrl);
        }
        
        $getUser = $this->model->get('users', $to, array('userId'), 'username');
        if(!$getUser){
            Session::flash('message', 'Invalid user', 'text-danger');
            redirect($this->moduleUrl);       
        }
        
        if($getUser['userId'] == $this->data['user']['userId']){
            Session::flash('message', 'Cannot transfer to yourself', 'text-danger');
            redirect($this->moduleUrl);    
        }
        
        $debit = $this->model->insertCreditEntry($this->data['user']['userId'], 'transfer', $amount, 'debit', 'user:'.$getUser['userId'], $note);
        $credit = $this->model->insertCreditEntry($getUser['userId'], 'transfer', $amount, 'credit', 'user:'.$this->data['user']['userId'], $note);
        
        Session::flash('message', 'Credit transferred!', 'text-success');
        redirect($this->moduleUrl);
    }
    
    protected function purchaseCredit($output)
    {
        if(!$this->data['user']){
            redirect(route('account.auth'));
        }                
        if(!posted()){
            redirect($this->moduleUrl);
        }

        $amount = false;
        if(isset($_POST['purchase-amount'])){
            $amount = floatval($_POST['purchase-amount']);
        }        
        
        $payment_method = false;
        if(isset($_POST['payment-method'])){
            $payment_method = $_POST['payment-method'];
        }
        
        if(!$amount OR !$payment_method){
            Session::flash('message', 'Please include all required fields', 'text-danger');
            redirect($this->moduleUrl); 
        }
        
        if($amount <= 0){
            Session::flash('message', 'Invalid credit amount', 'text-danger');
            redirect($this->moduleUrl); 
        }
        
        $payment_tokens = $this->model->getCreditPaymentTokens();
        if(!isset($payment_tokens[$payment_method])){
            Session::flash('message', 'Invalid payment method', 'text-danger');
            redirect($this->moduleUrl); 
        }
        
        $price = $payment_tokens[$payment_method];
        $order_total = round($price * $amount, 8);
        $order_satoshis = intval($order_total * SATOSHI_MOD);
        
        $app = get_app('account');
        $forward_address = $app['meta']['system_credit_forward_address'];
        if(trim($forward_address) == ''){
            $forward_address = false;
        }
        $tokens = array();
        foreach($payment_tokens as $token => $price){
            $tokens[] = $token;
        }        
        $slot_name = $app['meta']['system_credit_tokenslot_slot'].'_'.$forward_address.'_'.join('_',$tokens);
        
        //generate a tokenslot invoice for them
        $getSlot = $this->tokenslot->getSlot($slot_name);
        $site = currentSite();
        if(!$getSlot){
            $site = currentSite();
            $createSlot = $this->tokenslot->createSlot($tokens, $site['url'].$this->moduleUrl.'/_receive', $forward_address, 1, $slot_name, $slot_name);
            if(!$createSlot){
                Session::flash('message', 'Error setting up payment', 'text-danger');
                redirect($this->moduleUrl); 
            }
        }
        
        $new_payment = $this->tokenslot->newPayment($slot_name, $payment_method, $order_satoshis);  

        if(!$new_payment){
            Session::flash('message', 'Error generating invoice', 'text-danger');
            redirect($this->moduleUrl); 
        }

        //save this to payment_order table
        $order = array();
        $order_data = array();
        $order_data['userId'] = $this->data['user']['userId'];
        $order_data['username'] = $this->data['user']['username'];
        $order_data['total_credits'] = $amount;
        $order_data['credit_value'] = $price;
        $order_data['purchase_token'] = $payment_method;
        $order_data['total_satoshis'] = $order_satoshis;
        $order_data['invoice_id'] = $new_payment['payment_id'];
        $order_data['invoice_address'] = $new_payment['address'];
        
        $order['orderData'] = json_encode($order_data);
        $order['address'] = $new_payment['address'];
        $order['account'] = 'tokenslot';
        $order['amount'] = $order_total;
        $order['asset'] = $payment_method;
        $order['orderTime'] = timestamp();
        $order['orderType'] = 'system-credits';
        
        $add_order = $this->model->insert('payment_order', $order);
        if(!$add_order){
            Session::flash('message', 'Failed saving order', 'text-danger');
            redirect($this->moduleUrl); 
        }
        
        //redirect to payment page
        redirect($this->moduleUrl.'/pay/'.$new_payment['address']); 
    }
    
    protected function paymentPage($output)
    {
        if(!$this->data['user']){
            redirect(route('account.auth'));
        }        
        if(!isset($this->args[3]) OR trim($this->args[3]) == ''){
            redirect($this->moduleUrl); 
        }
        
        $address = $this->args[3];
        $getOrder = $this->model
        ->fetchSingle('SELECT * FROM payment_order WHERE orderType = "system-credits" AND address = :address',
                      array(':address' => $address));
                      
        if($getOrder){
            $getOrder['orderData'] = json_decode($getOrder['orderData'], true);
            if($getOrder['complete'] == 1 OR $getOrder['orderData']['userId'] != $this->data['user']['userId']){
                $getOrder = false; //kick them out if this is already complete or if doesnt belong to them
            }
        }
        if(!$getOrder){
            Session::flash('message', 'Invalid invoice ID', 'text-danger');
            redirect($this->moduleUrl); 
        }
        
        if(isset($this->args[4]) AND $this->args[4] == 'cancel'){
            if($getOrder['complete'] == 1 OR $getOrder['received'] > 0){
                Session::flash('message', 'Cannot cancel order being received', 'text-danger');
            }
            else{
                $this->model->delete('payment_order', $getOrder['orderId']);
                Session::flash('message', 'System credits order cancelled', 'text-success');
            }
            redirect($this->moduleUrl); 
        }
        
        $output['order_data'] = $getOrder['orderData'];
        unset($getOrder['orderData']);
        $output['order'] = $getOrder;
        
        $output['view'] = 'payment';
        
        return $output;
    }
    
    
    protected function checkPayment()
    {
        if(!$this->data['user']){
            die();
        }
        if(!isset($this->args[3]) OR trim($this->args[3]) == ''){
            die(); 
        }
        
        $address = $this->args[3];
        $getOrder = $this->model
        ->fetchSingle('SELECT * FROM payment_order WHERE orderType = "system-credits" AND address = :address',
                      array(':address' => $address));
        if($getOrder){
            $getOrder['orderData'] = json_decode($getOrder['orderData'], true);
            if($getOrder['orderData']['userId'] != $this->data['user']['userId']){
                $getOrder = false;
            }
        }
        if(!$getOrder){
            die();
        }
        
        $output = array();
        $output['complete'] = false;
        $output['receiving'] = false;
        
        if($getOrder['complete'] == 1){
            $output['complete'] = true;
        }
        
        if($getOrder['received'] > 0){
            $output['receiving'] = true;
        }
        
        ob_end_clean();
        header('Content-Type: text/json');
        echo json_encode($output);
        die();
    }
    
    protected function receiveWebhook($output)
    {
        //check webhook
        $json = $this->tokenslot->receivePaymentsWebhook();
        if($json){
            if(isset($json['payment_id']) AND isset($json['payment_address'])){
                //lookup order
                $getOrder = $this->model
                ->fetchSingle('SELECT * FROM payment_order WHERE orderType = "system-credits" AND address = :address',
                              array(':address' => $json['payment_address']));
                
                //check if exists and not already complete
                if($getOrder AND $getOrder['complete'] == 0){
                    $order_data = json_decode($getOrder['orderData'], true);
                    //check matches invoice data
                    if(isset($order_data['invoice_id']) AND $order_data['invoice_id'] == $json['payment_id']){
                        //check if received and/or complete
                        $update_data = array();
                        if($json['received'] > 0){
                            $update_data['received'] = $json['received'];
                        }
                        if($json['complete']){
                            $update_data['complete'] = 1;
                            $update_data['completeTime'] = timestamp();
                            $update_data['collected'] = 1; //tokenslot collects for us
                        }
                        if(count($update_data) > 0){
                            //update order
                            $update = $this->model->edit('payment_order', $getOrder['orderId'], $update_data);
                            if($update){
                                if($json['complete'] AND isset($order_data['total_credits']) AND isset($order_data['userId'])){
                                    //credit their account
                                    $this->model->credit($order_data['total_credits'],
                                                        'order:'.$getOrder['orderId'],
                                                        'Purchased with '.$getOrder['amount'].' '.$getOrder['asset'],
                                                         $order_data['userId']);
                                }
                            }
                        }
                        
                    }
                    
                }
            }
        }
        die();
    }
}
