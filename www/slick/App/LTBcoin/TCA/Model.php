<?php
/**
* Token Controlled Access
* 
* Provides functions for checking module/item access and permission overrides for different items across the site.
*
* @package [App][LTBcoin][TCA]
* @author Nick Rathman <nrathman@ironcladtech.ca>
*/
class Slick_App_LTBcoin_TCA_Model extends Slick_Core_Model
{
	public static $balances = array();
	public static $locks = array();
	
	function __construct()
	{
		parent::__construct();
		$this->inventory = new Slick_App_Dashboard_LTBcoin_Inventory_Model;
	}
	
	/**
	* Checks if the currently logged in user can access a specific item or module based on their cached counterparty token balances
	* 
	* @param $userId integer|bool|Array 
	* If an array, assumes its from $data['user'] and tries to grab user ID from that.
	* If 0 or false, assumes no user is logged in.
	* 
	* @param $moduleId integer
	* @param $itemId integer
	* @param $itemType string
	* @return bool
	*/
	public function checkItemAccess($userId, $moduleId, $itemId = 0, $itemType = '')
	{
		$lockHash = md5($moduleId.':'.$itemId.':'.$itemType);
		if(!isset(self::$locks[$lockHash])){
			self::$locks[$lockHash] = $this->getAll('token_access', array('moduleId' => $moduleId, 'itemId' => $itemId, 'itemType' => $itemType, 'permId' => 0), array(), 'stackOrder', 'asc');
		}
		$getLocks = self::$locks[$lockHash];
		if(count($getLocks) == 0){
			return true;
		}
		if(is_array($userId)){
			$userId = $userId['userId'];
		}
		if(!$userId OR $userId == 0){
			return false;
		}
		if(!isset(self::$balances[$userId])){
			self::$balances[$userId] = $this->inventory->getUserBalances($userId, true);
		}
		$getBalances = self::$balances[$userId];
		$stack = array();
		foreach($getLocks as $lock){
			$hasReq = $this->parseLock($getBalances, $lock);
			$stack[] = array('hasReq' => $hasReq, 'stackOp' => $lock['stackOp']);
		}
		$doCheck = $this->parseStack($stack);
		return $doCheck;
	}
	
	/**
	* Checks specific token_access entry against list of user balances based on defined conditional operator.
	*
	* @param $balances Array
	* Must be an array of (grouped) user token balances. $asset => $amount
	* 
	* @lock Array
	* Row from token_access table
	* 
	* @return bool
	*/
	protected function parseLock($balances, $lock)
	{
		$hasReq = false;
		if(!isset($balances[$lock['asset']])){
			$assetAmnt = 0;
		}
		else{
			$assetAmnt = $balances[$lock['asset']];
		}
		switch($lock['op']){
			case '==':
			case '=':
				if($assetAmnt == $lock['amount']){
					$hasReq = true;
				}
				break;
			case '!=':
			case '!':
				if($assetAmnt != $lock['amount']){
					$hasReq = true;
				}
				break;
			case '>':
				if($assetAmnt > $lock['amount']){
					$hasReq = true;
				}
				break;
			case '>=':
				if($assetAmnt >= $lock['amount']){
					$hasReq = true;
				}
				break;
			case '<':
				if($assetAmnt < $lock['amount'] AND $assetAmnt > 0){
					$hasReq = true;
				}
				break;
			case '<=':
				if($assetAmnt <= $lock['amount'] AND $assetAmnt > 0){
					$hasReq = true;
				}
				break;
		}
		return $hasReq;
	}
	
	/**
	* Parses a stack order array (e.g from checkItemAccess) to determine if all conditions are properly met.
	* 
	* The stack order is split up into "OR" groups. Each time stackOp == "OR" is encountered, a new stack group is created.
	* At least one "OR" group must be fully true to return true, otherwise returns false. 
	* 
	* @example 
	* e.g stack order: AND, OR, AND, AND, OR,AND
	* would result in three groups, (AND), (OR,AND,AND), (OR,AND).
	* If all conditions in at least one of those three groups are met, returns true.
	* 
	* @param $stack Array Stack order array looks like Array(hasReq => (bool), stackOp => string("AND"|"OR"))
	* @return bool
	*/ 
	protected function parseStack($stack)
	{
		$groups = array();
		$gnum = -1;
		foreach($stack as $k => $item){
			if($k == 0 OR $item['stackOp'] == 'OR'){
				$gnum++;
				$groups[$gnum] = array($item);
			}
			else{
				$groups[$gnum][] = $item;
			}
		}
		foreach($groups as $group){
			$groupMatch = true;
			foreach($group as $item){
				if(!$item['hasReq']){
					$groupMatch = false;
				}
			}
			if($groupMatch){
				return true;
			}
		}
		return false;
	}
	
}
