<?php
namespace App\Store;
use API, App\Tokenly, UI, Util, Core;
class Collector_Model extends Core\Model
{
	function __construct()
	{
		parent::__construct();
		$this->btc = new API\Bitcoin(BTC_CONNECT);
		$this->xcp = new API\Bitcoin(XCP_CONNECT);
		$this->inventory = new Tokenly\Inventory_Model;
	}
	
	protected function getSelectionForm()
	{
		$form = new UI\Form;
		$form->setSubmitText('Go');
		$form->setMethod('get');
		$options = new UI\Select('option');
		$options->setLabel('Choose a Collection Option');
		$options->addOption('sponsor-form', 'Sponsorship Form');
		$options->addOption('submission-credits', 'Blog Submisson Credits');
		$options->addOption('tca-forums', 'Token Societies');
		//$options->addOption('distributor-change', 'Distributor Leftover Multisig/Change');
		//$options->addOption('donate-verify', 'Verify-by-donation funds');
		$form->add($options);
		
		return $form;
	}
	
	protected function getValidOptions()
	{
		return array('sponsor-form', 'submission-credits', 'tca-forums', 'distributor-change', 'donate-verify');
	}
	
	protected function getCollectionForm()
	{
		$form = new UI\Form;
		
		$type = new UI\Hidden('type');
		$type->setValue(0);
		$form->add($type);
		
		$address = new UI\Textbox('address');
		$address->setLabel('BTC Address');
		$address->addAttribute('required');
		$address->addAttribute('autocomplete', 'false');
		$form->add($address);
		
		$pass = new UI\Password('pass');
		$pass->setLabel('Your Password');
		$pass->addAttribute('required');
		$pass->addAttribute('autocomplete', 'false');
		$form->add($pass);
		
		$form->setSubmitText('Collect Payments');
		
		return $form;
	}
	
	protected function getServerBalance()
	{
		try{
			$balance = $this->btc->getbalance();
		}
		catch(\Exception $e){
			return false;
		}
		return $balance;
	}
	
	protected function getFuelInfo()
	{
		$output = array('balance' => false, 'address' => false);
		try{
			$balance = $this->btc->getbalance(XCP_FUEL_ACCOUNT, 0);
			$address = $this->btc->getaccountaddress(XCP_FUEL_ACCOUNT);
		}
		catch(\Exception $e){
			return $output;
		}
		$output['balance'] = $balance;
		$output['address'] = $address;
		return $output;
	}
	
	protected function getPaymentsList($option)
	{
		$output = array();
		switch($option){
			case 'sponsor-form':
				$output = $this->container->getSponsorPayments();
				break;
			case 'submission-credits':
				$output = $this->container->getSubmissionCreditPayments();
				break;
			case 'tca-forums':
				$output = $this->container->getTokenSocietyPayments();
				break;
			case 'distributor-change':
				$output = $this->container->getDistributorChange();
				break;
			case 'donate-verify':
				$output = $this->container->getVerifyFunds();
				break;
		}
		
		$newOutput = array();
		foreach($output as $k => $row){
			if($row['amount'] > 0){
				$newOutput[] = $row;
			}
		}
		
		return $newOutput;
	}
	
	protected function getSponsorPayments()
	{
		$output = $this->container->getPaymentOrders('ad-purchase');
		foreach($output as  &$row){
			$data = $row['info']['orderData'];
			switch($data['ad_type']){
				case 'sponsor':
					$row['title'] .= $data['show'].' - '.$data['package'];
					break;
				case 'display';
					$row['title'] .= $data['adspace'].' - '.$data['package'];
					break;
				case 'product':
					$row['title'] .= $data['product'].' - '.$data['package'];
					break;
				case 'consult':
					$row['title'] .= $data['consultant'].' - '.$data['package'];
					break;
			}
		}
		return $output;
	}
	
	protected function getSubmissionCreditPayments()
	{
		$output = $this->container->getPaymentOrders('blog-submission-credits');
		$newOutput = array();
		foreach($output as  &$row){
			$row['title'] = 'Submission Credits';
			if(!isset($newOutput[$row['address'].'-'.$row['asset']])){
				$newOutput[$row['address'].'-'.$row['asset']] = $row;
				$newOutput[$row['address'].'-'.$row['asset']]['info'] = array($row['info']);
			}
			else{
				$newOutput[$row['address'].'-'.$row['asset']]['info'][] = $row['info'];
			}
		}
		return $newOutput;
	}
	
	protected function getTokenSocietyPayments()
	{
		$output = $this->container->getPaymentOrders('tca-forum');
		foreach($output as  &$row){
			$row['title'] .= $row['info']['orderData']['board'];
		}
		return $output;
	}
	
	protected function getPaymentOrders($type)
	{
		$output = array();
		$orders = $this->getAll('payment_order', array('orderType' => $type, 'complete' => 1, 'collected' => 0), array(), 'orderId', 'desc');
		foreach($orders as &$order){
			$order['orderData'] = json_decode($order['orderData'], true);
			$item = array();
			$item['address'] = $order['address'];
			$item['date'] = $order['completeTime'];
			$item['asset'] = 'BTC';		
			$item['title'] = '#'.$order['orderId'].' ';
			$item['info'] = $order;
			try{
				$item['amount'] = $this->btc->getaddressbalance($order['address']);
			}
			catch(\Exception $e){
				throw new \Exception('Error getting balance for order #'.$order['orderId']);
			}
			$output[] = $item;	
			//check XCP balances
			try{
				$balances = $this->xcp->get_balances(array('filters' => array('field' => 'address', 'op' => '==', 'value' => $item['address'])));
			}
			catch(\Exception $e){
				throw new \Exception('Error getting XCP balances for order #'.$order['orderId']);
			}
			foreach($balances as $balance){
				if($balance['quantity'] <= 0){
					continue;
				}
				$newItem = $item;
				$newItem['asset'] = $balance['asset'];
				$assetData = $this->inventory->getAssetData($balance['asset']);
				if($assetData['divisible']){
					$balance['quantity'] = $balance['quantity'] / SATOSHI_MOD;
				}				
				$newItem['amount'] = $balance['quantity'];
				
				$output[] = $newItem;
			}
		}
		return $output;
	}
	
	protected function getDistributorChange()
	{
		$output = array();
		
		return $output;
	}
	
	protected function getVerifyFunds()
	{
		$output = array();
		
		return $output;
	}
	
	protected function collectPayments($data, $appData)
	{
		$getUser = $this->get('users', $appData['user']['userId']);
		$pass = hash('sha256', $getUser['spice'].$data['pass']);
		if($pass != $getUser['password']){
			throw new \Exception('Invalid password');
		}
		
		$btc_validate = new API\BTCValidate;
		if(!$btc_validate->checkAddress($data['address'])){
			throw new \Exception('Invalid bitcoin address');
		}
		
		if(count($data['payments']) == 0){
			throw new \Exception('No payments selected');
		}
		
		$getPayments = $this->container->getPaymentsList($data['type']);
		$selectAmounts = array();
		foreach($getPayments as $k => $payment){
			if(in_array($k, $data['payments'])){
				$selectAmounts[] = $payment;
			}
		}
		
		$addressList = array();
		foreach($selectAmounts as $select){
			if(!isset($addressList[$select['address']])){
				$addressList[$select['address']] = array();
			}
			if(!isset($addressList[$select['address']][$select['asset']])){
				$addressList[$select['address']][$select['asset']] = 0;
			}
			$addressList[$select['address']][$select['asset']] += $select['amount'];
		}
		
		$fuel_cost = XCP_DEFAULT_FUEL;
		
		$this->btc->walletpassphrase(XCP_WALLET, 300);
		$this->container->primeOutputs($addressList, $fuel_cost);
		$success = array();
		foreach($addressList as $address => $amounts){
			$total_cost = 0;
			foreach($amounts as $asset => $amnt){
				if($asset != 'BTC'){
					$total_cost += $fuel_cost;
				}
			}
			foreach($amounts as $asset => $amnt){
				$collect = null;
				switch($asset){
					case 'BTC':
						$amnt = $amnt - $total_cost - $fuel_cost;
						if($amnt > 0.00001){
							$collect = $this->container->collectBTC($address, $data['address'], $amnt, $fuel_cost);
						}
						break;
					default:
						$collect = $this->container->collectXCP($address, $data['address'], $amnt, $asset, $fuel_cost);
						break;
				}
				
				if($collect == null){
					continue;
				}
				
				if($collect AND $amnt > 0){

					//record this transaction, mark order/entry or whatever as collected
					$collectData = array('userId' => $appData['user']['userId'], 'type' => 'collect-'.$data['type'],
										 'source' => $address, 'destination' => $data['address'], 'amount' => $amnt,
										 'asset' => $asset, 'txId' => $collect, 'collectionDate' => timestamp());
										 

					foreach($selectAmounts as $select){
						if($select['address'] == $address){
							if(isset($select['info']['orderId'])){
								$collectData['itemId'] = $select['info']['orderId'];
								$this->edit('payment_order', $select['info']['orderId'], array('collected' => 1));
							}
							elseif(is_array($select['info'])){
								foreach($select['info'] as $info){
									if(isset($info['orderId'])){
										$this->edit('payment_order', $info['orderId'], array('collected' => 1));
									}
								}
							}
						}
					}
					
					$insert = $this->insert('payment_collections', $collectData);
					$collectData['collectionId'] = $insert;
					$success[] = $collectData;					
				}
				else{
					throw new \Exception('Error collecting '.$asset.' from address '.$address);
				}
			}
		}
		$this->btc->walletlock();
		
		return true;
	}
	
	protected function collectBTC($from, $to, $amount, $cost)
	{
		try{
			$send = $this->btc->sendfromaddress($from, $amount, $to, $cost);
		}
		catch(\Exception $e){	
			throw new \Exception('Error collecting BTC from '.$from);
		}
		
		return $send;
	}
	
	protected function collectXCP($from, $to, $amount, $asset, $cost)
	{
		try{
			$assetData = $this->inventory->getAssetData($asset);
			$getAddress = $this->btc->validateaddress($from);
			if(!$getAddress OR !$getAddress['ismine']){
				throw new \Exception('Error getting pubkey for '.$from);
			}
			
			if($assetData['divisible']){
				$amount = floor($amount * SATOSHI_MOD);
			}
			else{
				$amount = floor($amount);
			}
			if($amount <= 0){
				return false;
			}
			
			$sendData = array('source' => $from, 'destination' => $to,
							  'asset' => $asset, 'quantity' => $amount, 'allow_unconfirmed_inputs' => true,
							  'pubkey' => $getAddress['pubkey'], 'regular_dust_size' => round(0.000025 * SATOSHI_MOD), 'multisig_dust_size' => round(0.000025 * SATOSHI_MOD),
							  'fee' => round(0.00005 * SATOSHI_MOD));
			
			$getRaw = $this->xcp->create_send($sendData);
			$sign = $this->btc->signrawtransaction($getRaw);
			$send = $this->btc->sendrawtransaction($sign['hex']);

			
		}
		catch(\Exception $e){
			throw new \Exception('Error collecting '.$asset.' from '.$from);
		}
		
		return $send;
	}
	
	protected function primeOutputs(&$list, $cost)
	{
		//check the BTC balance of each address in the list, make sure it has enough to cover costs
		foreach($list as $address => &$amounts){
			$total_cost = 0;
			foreach($amounts as $asset => $amnt){
				if($asset != 'BTC'){
					$total_cost += $cost;
				}
			}
			try{
				$balance = $this->btc->getaddressbalance($address);
				$diff = $total_cost - $balance;
		
				//top off address
				if($diff > 0){
					if(isset($amounts['BTC'])){
						//cancel the BTC tx
						unset($amounts['BTC']);
					}
					
					if($diff < $cost){
						$diff = $cost;
					}							
				
					$sendDiff = $this->btc->sendfrom(XCP_FUEL_ACCOUNT, $address, $diff);
					sleep(2);
					
				}			
			}
			catch(\Exception $e){
				throw new \Exception('Error priming outputs for '.$address);
			}
		}
	}
}
