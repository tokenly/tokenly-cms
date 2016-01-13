<?php

function parse_tca_token($input)
{
	$ands = array();
	$assets = array();
	$exp = explode(',', $input);
	$inventory = new \App\Tokenly\Inventory_Model;
	foreach($exp as $row){
		$or = array();
		$exp2 = explode('_', $row);
		foreach($exp2 as $row2){
			$token = trim($row2);
			$asset = false;
			$getAsset = $inventory->getAssetData($token);
			if($getAsset){
				$asset = $getAsset['asset'];
			}
			if(!$asset){
				throw new \Exception('Invalid access token '.$token);
			}
			$or[] = $asset;
			$assets[] = $asset;
		}
		$ands[] = $or;
	}
	return array('stack' => $ands, 'assets' => $assets);
}

function parse_tca_amount($input)
{
	$exp = explode(',', $input);
	$output = array();
	foreach($exp as $row){
		$output[] = floatval($row);
	}
	return $output;
}

function remove_tca_locks($moduleId, $itemId, $itemType)
{
	$model = new \Core\Model;
	$remove = $model->sendQuery('DELETE FROM token_access
									  WHERE moduleId = :moduleId
									  AND itemId = :itemId
									  AND itemType = :itemType',
									  array(':moduleId' => $moduleId,
											':itemId' => $itemId, ':itemType' => $itemType));
	return $remove;
}

function add_tca_locks($user, $moduleId, $itemId, $itemType, $tokens = array(), $amounts = array())
{
	
	$model = new \Core\Model;
	$stack = array();
	foreach($tokens['stack'] as $andGroup){
		$num = 0;
		foreach($andGroup as $asset){
			$stackOp = 'OR';
			if($num == (count($andGroup) - 1)){
				$stackOp = 'AND';
			}
			$num++;
			$token_lock = array();
			$token_lock['userId'] = $user['userId'];
			$token_lock['moduleId'] = $moduleId;
			$token_lock['itemId'] = $itemId;
			$token_lock['itemType'] = $itemType;
			$token_lock['permId'] = 0;
			$token_lock['asset'] = $asset;
			$token_lock['amount'] = 0;
			$token_lock['op'] = '>';
			$token_lock['stackOp'] =  $stackOp;
			$stack[] = $token_lock;
		}
	}
	foreach($amounts as $k => $amount){
		if(isset($stack[$k])){
			$stack[$k]['amount'] = $amount;
			if($amount > 0){
				$stack[$k]['op'] = '>=';
			}
		}
	}
	foreach($stack as $item){
		$insert = $model->insert('token_access', $item);
		if(!$insert){
			throw new \Exception('Error applying token access');
		}
	}
	return true;
}
