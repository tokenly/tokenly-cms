<?php
class Slick_App_Dashboard_LTBcoin_Inventory_Model extends Slick_Core_Model
{
	public static $addresses = array();
	public static $assets = array();
	public $addressCacheRate = 1800; //half an hour
	
	public function getAddressBalances($addressId)
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
	
	public function getUserBalances($userId, $groupAmounts = false, $type = 'btc', $forceRefresh = false, $keepAddress = false)
	{
		$meta = new Slick_App_Meta_Model;
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
		foreach($getAddresses as $address){
			if($timeDiff >= $this->addressCacheRate OR $forceRefresh){
				$balances[$address['address']] = $this->checkAddressBalances($address['addressId']);
				$newChecked = true;
			}
			else{
				$balances[$address['address']] = $this->getAddressBalances($address['addressId']);
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
		$group = array();
		foreach($balances as $address => $bal){
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
	
	public function checkAddressBalances($addressId)
	{
		$getAddress = $this->get('coin_addresses', $addressId);
		if(!$getAddress OR $getAddress['isXCP'] == 0 OR $getAddress['verified'] == 0){
			return false;
		}
		$getCurrent = $this->getAll('xcp_balances', array('addressId' => $addressId));
		$xcp = new Slick_API_Bitcoin(XCP_CONNECT);
		try{
			$getBalances = $xcp->get_balances(array('filters' => array('field' => 'address', 'op' => '=', 'value' => $getAddress['address'])));
		}
		catch(Exception $e){
			return false;
		}
		$fullBalances = array();
		foreach($getBalances as $balance){
			$isCurrent = false;
			foreach($getCurrent as $current){
				if($current['asset'] == $balance['asset']){
					$isCurrent = $current['balanceId'];
				}
			}
			$getAsset = $this->getAssetData($balance['asset']);
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
	
	public function getAssetData($asset)
	{
		$xcp = new Slick_API_Bitcoin(XCP_CONNECT);
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
			catch(Exception $e){
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
	
	public function hasBalance($userId, $asset, $minAmount = 0, $addressId = 0, $type = 'btc')
	{
		if($minAmount < 0){
			return false;
		}
		$getBalances = $this->getUserBalances($userId, false, $type);
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
}
