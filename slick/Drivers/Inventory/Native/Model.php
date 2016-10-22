<?php
namespace Drivers\Inventory;
use Core, API, App\Tokenly\Address_Model;
class Native_Model extends Core\Model
{
	public static $addresses = array();
	public static $assets = false;
	public $addressCacheRate = 1800; //half an hour
	
	function __construct()
	{
		parent::__construct();
		if(!self::$assets){
			$get_assets = $this->getAll('xcp_assetCache');
			self::$assets = array();
			foreach($get_assets as $asset){
				self::$assets[$asset['asset']] = $asset;
			}
		}
	}
	
	protected function getAddressBalances($addressId)
	{
		if(isset(self::$addresses[$addressId])){
			return self::$addresses[$addressId];
		}
		$get = $this->getAll('xcp_balances', array('addressId' => $addressId));
		$balances = array();
		foreach($get as $row){
			if($row['balance'] > 0){
				$balances[$row['asset']] = $row['balance'];
			}
		}
		self::$addresses[$addressId] = $balances;
		return $balances;
	}
	
	protected function getUserBalances($userId, $groupAmounts = false, $type = 'btc', $forceRefresh = false, $keepAddress = false)
	{
		$meta = new \App\Meta_Model;
		$time = time();
		$lastChecked = $meta->getUserMeta($userId, 'lastBalanceCheck');
		if($lastChecked){
			$lastChecked = strtotime($lastChecked);
		}
		else{
			$lastChecked = 0;
		}
		$getAddresses = $this->getAll('coin_addresses', array('userId' => $userId, 'type' => $type, 'verified' => 1, 'isXCP' => 1));
		if(!$getAddresses OR count($getAddresses) == 0){
			return array();
		}
		$timeDiff = $time - $lastChecked;
		$newChecked = false;
		$balances = array();
		$skip_check = false;
		if(defined('ENABLE_TOKEN_BALANCE_CHECK') AND !ENABLE_TOKEN_BALANCE_CHECK){
			$skip_check = true;
		}
		foreach($getAddresses as $address){
			if(($timeDiff >= $this->addressCacheRate OR $forceRefresh) AND !$skip_check){
				$balances[$address['address']] = $this->container->checkAddressBalances($address['addressId']);
				$newChecked = true;
			}
			else{
				$balances[$address['address']] = $this->container->getAddressBalances($address['addressId']);
			}
			if(!$keepAddress AND count($balances[$address['address']]) == 0){
				unset($balances[$address['address']]);
			}
		}
		if($newChecked){
			$meta->updateUserMeta($userId, 'lastBalanceCheck', timestamp());
		}
		if(!$groupAmounts){
			return $balances;
		}
		if(!$balances){
			return array();
		}
		$group = array();
		foreach($balances as $address => $bal){
            if(!is_array($bal)){
                continue;
            }
			foreach($bal as $asset => $amnt){
				if(isset($group[$asset])){
					$group[$asset] += $amnt;
				}
				else{
					$group[$asset] = $amnt;
				}
			}
		}
		return $group;
	}
	
	protected function checkAddressBalances($addressId)
	{
		$getAddress = $this->get('coin_addresses', $addressId);
		if(!$getAddress OR $getAddress['isXCP'] == 0 OR $getAddress['verified'] == 0){
			return false;
		}
		$getCurrent = $this->getAll('xcp_balances', array('addressId' => $addressId));
		$xcp = new API\Bitcoin(XCP_CONNECT);
		try{
			$getBalances = $xcp->get_balances(array('filters' => array('field' => 'address', 'op' => '=', 'value' => $getAddress['address'])));
		}
		catch(\Exception $e){
			return $this->container->getAddressBalances($addressId);
			//return false;
		}
		$fullBalances = array();
		foreach($getBalances as $balance){
			$isCurrent = false;
			foreach($getCurrent as $current){
				if($current['asset'] == $balance['asset']){
					$isCurrent = $current['balanceId'];
				}
			}
			$getAsset = $this->container->getAssetData($balance['asset']);
			if(!$getAsset){
				continue;
			}
			if($getAsset['divisible'] == 1 AND $balance['quantity'] > 0){
				$balance['quantity'] = $balance['quantity'] / SATOSHI_MOD;
			}
			if($balance['quantity'] == 0){
				if($isCurrent){
					$this->delete('xcp_balances', $isCurrent);
				}
				continue;
			}
			$saveData = array('balance' => $balance['quantity'], 'lastChecked' => timestamp());
			if($isCurrent){
				$save = $this->edit('xcp_balances', $isCurrent, $saveData);
			}
			else{
				$saveData['addressId'] = $addressId;
				$saveData['asset'] = $balance['asset'];
				$save = $this->insert('xcp_balances', $saveData);
			}
			$fullBalances[$balance['asset']] = $balance['quantity'];
		}
		return $fullBalances;
	}
	
	protected function getAssetData($asset)
	{
		$xcp = new API\Bitcoin(XCP_CONNECT);
		$getAsset = false;
		if(isset(self::$assets[$asset])){
			$getAsset = self::$assets[$asset];
		}
		else{
			$getAsset = $this->get('xcp_assetCache', $asset, array(), 'asset');
		}
		if(!$getAsset){
			try{
				$asset = $xcp->get_asset_info(array('assets' => array($asset)));
			}
			catch(\Exception $e){
				return false;
			}
			if(!isset($asset[0])){
				return false;
			}
			$asset = $asset[0];
			$isDivisible = 0;
			if($asset['divisible']){
				$isDivisible = 1;
			}
			$assetData = array('asset' => $asset['asset'], 'divisible' => $isDivisible, 'lastChecked' => timestamp(), 'description' => $asset['description'], 'link' => '');
			$insertAsset = $this->insert('xcp_assetCache', $assetData);
			self::$assets[$asset['asset']] = $assetData;
			$getAsset = $assetData;
		}
		return $getAsset;
	}
	
	protected function hasBalance($userId, $asset, $minAmount = 0, $addressId = 0, $type = 'btc')
	{
		if($minAmount < 0){
			return false;
		}
		$getBalances = $this->container->getUserBalances($userId, false, $type);
		if($addressId != 0){
			$getAddress = $this->get('coin_addresses', $addressId);
			if(!$getAddress OR $getAddress['isXCP'] == 0 OR $getAddress['verified'] == 0){
				return false;
			}
			foreach($getBalances as $address => $balances){
				if($address == $getAddress['address']){
					if(isset($balances[$asset])){
						if($minAmount == 0 AND $balances[$asset] > 0){
							return true;
						}
						elseif($balances[$asset] >= $minAmount){
							return true;
						}
					}
				}
			}
		}
		else{
			$total = 0;
			foreach($getBalances as $address => $balances){
				if(isset($balances[$asset])){
					$total += $balances[$asset];
				}
			}
			if($minAmount == 0 AND $total > 0){
				return true;
			}
			elseif($total >= $minAmount){
				return true;
			}
		}
		return false;
	}
	
	protected function getWeightedUserTokenScore($userId, $opUserId, $token, $minScore = 0, $maxScore = 5, $tokenStep = 1000, $maxTokens = 500000)
	{
		$userBalances = $this->container->getUserBalances($userId, true);
		$opBalances = $this->container->getUserBalances($opUserId, true);
		$userTokens = 0;
		$opTokens = 0;
		$maxSteps = $maxTokens / $tokenStep;
		$perStep = $maxScore / $maxSteps;
		$score = 0;		
		
		if(isset($userBalances[$token])){ $userTokens = round($userBalances[$token]); }
		if(isset($opBalances[$token])){ $opTokens = round($opBalances[$token]); }
		
		$tokenDiff = $userTokens - $opTokens;
		if($tokenDiff > $maxTokens){ $tokenDiff = $maxTokens; }
		
		$numSteps = $tokenDiff / $tokenStep;
		if($numSteps > $maxSteps){ $numSteps = $maxSteps; }
		if($numSteps < 0){ $numSteps = 0; }
		
		for($i = 0; $i < $numSteps; $i++){
			$score += $perStep;
		}
		
		if($score <= $minScore){ $score = $minScore; }
		if($score >= $maxScore){ $score = $maxScore; }
		
		return array('score' => $score, 'user' => $userTokens, 'op' => $opTokens);
	}	
	
	protected function getUserInventoryTransactions($userId, $limit = false, $andUpdate = false)
	{
		 $get_all = $this->getAll('coin_addresses', array('userId' => $userId, 'verified' => 1));
		 $tx_list = array();
		 foreach($get_all as $address){
			 $address_tx = $this->container->getUserAddressTransactions($userId, $address['address'], $andUpdate);
			 if(is_array($address_tx)){
				 foreach($address_tx as $tx){
					 $tx_list[] = $tx;
				 }
			 }
		 }
		 if(count($tx_list) == 0){
			 return false;
		 }
		 aasort($tx_list, 'time');
		 $tx_list = array_reverse($tx_list);
		 if($limit){
			 $new_list = array();
			 $num = 0;
			 foreach($tx_list as $tx){
				 $num++;
				 if($num > $limit){
					 break;
				 }
				 $new_list[] = $tx;
			 }
			 return $new_list;
		 }
		 return $tx_list;
	}
	
	protected function getUserAddressTransactions($userId, $address, $andUpdate = false)
	{
		 $get = $this->getAll('coin_addresses', array('userId' => $userId, 'address' => $address));
		 if(!$get OR count($get) == 0){
			 return false;
		 }
		 $get = $get[0];
		 if($get['verified'] == 0){
			 return false;
		 }
		 
		 $model = new Address_Model;
		 $get_tx = $model->getAddressTransactions($get['addressId'], $andUpdate);
		 return $get_tx;
	}
}
