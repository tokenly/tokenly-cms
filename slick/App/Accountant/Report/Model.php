<?php
class Slick_App_Accountant_Report_Model extends Slick_Core_Model
{
	function __construct()
	{
		parent::__construct();
		$this->inventory = new Slick_App_Tokenly_Inventory_Model;
	}
	
	public function getAddressReportForm()
	{
		$form = new Slick_UI_Form;
		
		$addresses = new SLick_UI_Textarea('addresses');
		$addresses->setLabel('Address List');
		$addresses->addAttribute('placeholder', '(one address per line)');
		$addresses->addAttribute('required');
		$form->add($addresses);
		
		$filters = new Slick_UI_Textarea('filters');
		$filters->setLabel('Asset Filters');
		$filters->addAttribute('placeholder', '(one asset per line)');
		$form->add($filters);
		
		return $form;
		
	}
	
	public function generateAddressReport($data)
	{
		$output = array();
		
		if(!has_key($data, 'addresses')){
			throw new Exception('Address list required');
		}
		
		$expList = explode("\n", $data['addresses']);
		$validList = array();
		$validate = new Slick_API_BTCValidate;
		foreach($expList as $addr){
			$addr = trim($addr);
			if($validate->checkAddress($addr)){
				$validList[] = $addr;
			}
		}
		
		if(count($validList) == 0){
			throw new Exception('Please enter at least 1 valid BTC address');
		}
		
		$filterList = array();
		if(has_key($data, 'filters')){
			$expFilters = explode("\n", $data['filters']);
			foreach($expFilters as $filter){
				$filter = trim($filter);
				if($filter != ''){
					$filterList[] = $filter;
				}
			}
		}
		
		$xcp = new Slick_API_Bitcoin(XCP_CONNECT);
		$btc = new Slick_API_Bitcoin(BTC_CONNECT);
		$txList = array();
		foreach($validList as $addr){
			
			//get BTC tx list
			$btc_txs = $xcp->getaddresstxlist($addr);
	
			//get xcp tx list
			try{
				$xcpCredits = $xcp->get_credits(array('filters' => array('field' => 'address', 'op' => '=', 'value' => $addr), 'limit' => 1000));
				$xcpDebits = $xcp->get_debits(array('filters' => array('field' => 'address', 'op' => '=', 'value' => $addr), 'limit' => 1000));
			}
			catch(Exception $e){
				throw new Exception('Error getting XCP transaction lists for '.$addr);
			}
			$usedTxs = array();
			//xcp credits
			foreach($xcpCredits as $credit){
				if(count($filterList) > 0){
					$matchFound = false;
					foreach($filterList as $filter){
						if(is_match($filter, $credit['asset'])){
							$matchFound = true;
						}
					}
					if(!$matchFound){
						continue;
					}
				}
				$asset = $this->inventory->getAssetData($credit['asset']);
				$item = array();
				$item['type'] = 'credit';
				$item['address'] = $addr;
				$item['asset'] = $credit['asset'];
				$item['block'] = $credit['block_index'];
				$item['action'] = $credit['calling_function'];
				$item['amount'] = $credit['quantity'];
				$item['txId'] = $credit['event'];
				$item['divisible'] = $asset['divisible'];
				$item['btc_amount'] = 0;
				foreach($btc_txs as $tx){
					if($tx['txId'] == $item['txId']){
						$item['btc_amount'] = convertFloat(round($tx['amount'] / SATOSHI_MOD, 8));
						break;
					}
				}
				$txList[] = $item;
				$usedTxs[] = $item['txId'];
			}
			//xcp debits
			foreach($xcpDebits as $debit){
				if(count($filterList) > 0){
					$matchFound = false;
					foreach($filterList as $filter){
						if(is_match($filter, $debit['asset'])){
							$matchFound = true;
						}
					}
					if(!$matchFound){
						continue;
					}
				}				
				$asset = $this->inventory->getAssetData($debit['asset']);
				$item = array();
				$item['type'] = 'debit';
				$item['address'] = $addr;
				$item['asset'] = $debit['asset'];
				$item['block'] = $debit['block_index'];
				$item['action'] = $debit['action'];
				$item['amount'] = $debit['quantity'];
				$item['txId'] = $debit['event'];
				$item['divisible'] = $asset['divisible'];
				$item['btc_amount'] = 0;
				foreach($btc_txs as $tx){
					if($tx['txId'] == $item['txId']){
						$item['btc_amount'] = convertFloat(round($tx['amount'] / SATOSHI_MOD, 8));
						break;
					}
				}		
				$txList[] = $item;
				$usedTxs[] = $item['txId'];
			}			
			
			//btc transactions
			if(count($filterList) > 0){
				$matchFound = false;
				foreach($filterList as $filter){
					if(is_match($filter, 'BTC')){
						$matchFound = true;
					}
				}
				if(!$matchFound){
					continue; //move on to the next address..
				}
			}
			foreach($btc_txs as $tx){
				if(!in_array($tx['txId'], $usedTxs)){
					$item = array();
					if($tx['amount'] < 0){
						$item['type'] = 'debit';
						$item['action'] = 'send';
					}
					else{
						$item['type'] = 'credit';
						$item['action'] = 'receive';
					}
					$item['address'] = $addr;
					$item['asset'] = 'BTC';
					$item['block'] = $tx['block'];
					$item['amount'] = $tx['amount'];
					if($item['amount'] < 0){
						$item['amount'] = 0 - $item['amount'];
					}
					$item['txId'] = $tx['txId'];
					$item['divisible'] = true;
					$item['btc_amount'] = convertFloat(round($tx['amount'] / SATOSHI_MOD, 8));				
					$txList[] = $item;
				}
			}
			
		}
		aasort($txList, 'block');
		$txList = array_values($txList);
		
		$output[] = array('Date', 'Address', 'Asset', 'Debit', 'Credit', 'BTC Amount', 'TX Type', 'Block Height', 'TX ID');
		$meta = new Slick_App_Meta_Model;
		$tokenlyApp = get_app('tokenly');
		$blockCache = $meta->getAppMeta($tokenlyApp['appId'], 'block-cache');
		$blockInfos = array();
		if($blockCache){
			$blockInfos = json_decode($blockCache, true);
		}
		foreach($txList as $tx){
			
			if(!isset($blockInfos[$tx['block']])){
				try{
					$blockHash = $btc->getblockhash($tx['block']);
					$blockInfo = $btc->getblock($blockHash);
				}
				catch(Exception $e){
					throw new Exception('Error getting block info for index '.$tx['block']);
				}
				$blockInfos[$tx['block']] = $blockInfo;
			}
			else{
				$blockInfo = $blockInfos[$tx['block']];
			}

			
			$item = array();
			$item[] = date('Y/m/d H:i:s', $blockInfo['time']);
			$item[] = $tx['address'];
			$item[] = $tx['asset'];
			if($tx['divisible']){
				$tx['amount'] = round($tx['amount'] / SATOSHI_MOD, 8);
			}
			if($tx['type'] == 'debit'){
				$item[] = $tx['amount'];
			}
			else{
				$item[] = 0;
			}
			if($tx['type'] == 'credit'){
				$item[] = $tx['amount'];
			}
			else{
				$item[] = 0;
			}
			$item[] = $tx['btc_amount'];
			if($tx['action'] == 'send' AND $tx['type'] == 'credit'){
				$tx['action'] = 'receive';
			}
			$item[] = $tx['action'];
			$item[] = $tx['block'];
			$item[] = $tx['txId'];
			
			$output[] = $item;
		}
		$meta->updateAppMeta($tokenlyApp['appId'], 'block-cache', json_encode($blockInfos)); //update the block time cache

		return $output;
	}
	
	
}
