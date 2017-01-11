<?php
namespace Drivers\Inventory;
use Tokenly\TokenpassClient\TokenpassAPI;
use Exception;
class Tokenpass_Model extends Native_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->tokenpass = new TokenpassAPI;
		
	}
	
	protected function checkAddressBalances($addressId)
	{
		$getAddress = $this->get('coin_addresses', $addressId);
		if(!$getAddress OR $getAddress['isXCP'] == 0 OR $getAddress['verified'] == 0){
			return false;
		}
		$getUser = $this->get('users', $getAddress['userId']);
		$getCurrent = $this->getAll('xcp_balances', array('addressId' => $addressId));
		
		$cache_key = 'user_tokenpass_addresses_'.$getUser['userId'];
		$get_cached = static_cache($cache_key);
		if(!is_array($get_cached)){
            try{
                $get_cached = $this->tokenpass->getAddresses($getUser['username'], $getUser['auth']);
            }
            catch(Exception $e){
                return false;
            }
			if(!is_array($get_cached)){
				return false;
			}
			static_cache($cache_key, $get_cached);
		}
		$address_list = $get_cached;
		$found_address = false;
		$balances = false;
		foreach($address_list as $row){
			if($row['address'] == $getAddress['address']){
				$found_address = true;
				$balances = $row['balances'];
				break;
			}
		}
		if(!$found_address){
			return false;
		}
		$fullBalances = array();
		$time = timestamp();
		foreach($balances as $asset => $balance){
			$isCurrent = false;
			foreach($getCurrent as $current){
				if($current['asset'] == $asset){
					$isCurrent = $current['balanceId'];
				}
			}
			$balance = round($balance / SATOSHI_MOD, 8);
			if($balance <= 0){
				if($isCurrent){
					$this->delete('xcp_balances', $isCurrent);
				}
				continue;
			}
			$saveData = array('balance' => $balance, 'lastChecked' => $time);
			if($isCurrent){
				$save = $this->edit('xcp_balances', $isCurrent, $saveData);
			}
			else{
				$saveData['addressId'] = $addressId;
				$saveData['asset'] = $asset;
				$save = $this->insert('xcp_balances', $saveData);
			}
			$fullBalances[$asset] = $balance;
		}
		$to_delete = array();
		foreach($getCurrent as $current){
			$found = false;
			foreach($fullBalances as $asset => $balance){
				if($asset == $current['asset']){
					$found = true;
					break;
				}
			}
			if(!$found){
				$to_delete[] = $current['balanceId'];
			}
		}
		if(count($to_delete) > 0){
			$this->sendQuery('DELETE FROM xcp_balances WHERE balanceId IN('.join(',', $to_delete).')');
		}
		return $fullBalances;
	}
	
	
}
