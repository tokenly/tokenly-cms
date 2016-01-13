<?php
namespace App\Tokenly;
use Core;
/**
* Token Controlled Access
* 
* Provides functions for checking module/item access and permission overrides for different items across the site.
*
* @package [App][LTBcoin][TCA]
* @author Nick Rathman <nrathman@ironcladtech.ca>
*/
class TCA_Model extends Core\Model
{
	public static $balances = array();
	public static $locks = array();
	public static $rows = false;
	public static $appPerms = false;
	
	function __construct()
	{
		parent::__construct();
		$this->inventory = new Inventory_Model;
		if(!self::$rows){
			//load all token_access entries
			self::$rows = $this->getAll('token_access', array(), array(), 'stackOrder', 'ASC');
		}
		if(!self::$appPerms){
			self::$appPerms = $this->getAll('app_perms', array(), array('permId', 'permKey', 'appId'));
		}
	}
	
	/**
	* Takes an array of permissions and checks against the token_access table for TCA
	*
	* @param $userId integer|bool|Array 
	* If an array, assumes its from $data['user'] and tries to grab user ID from that.
	* If 0 or false, assumes no user is logged in.
	* 
	* @param $perms Array
	* @param $moduleId integer
	* @param $itemId integer
	* @param $itemType string
	* @param $defaultReturn bool
	* @return Array	
	*/
	protected function checkPerms($userId, $perms, $moduleId, $itemId = 0, $itemType = '')
	{
		foreach($perms as $key => $val){
			$getPerm = extract_row(self::$appPerms, array('permKey' => $key));
			if($getPerm){
				$getPerm = $getPerm[0];
				$defaultReturn = true;
				if(!$val){
					$defaultReturn = false;
				}
				$checkAccess = $this->container->checkItemAccess($userId, $moduleId, $itemId, $itemType, $defaultReturn, $getPerm['permId'], $val);
				if(!$checkAccess){
					$perms[$key] = false;
				}
				else{
					$perms[$key] = true;
				}
			}
		}
		return $perms;
	}
	
	/**
	* Checks if the currently logged in user can access a specific item, module or permission based on their cached counterparty token balances
	* 
	* @param $userId integer|bool|Array 
	* If an array, assumes its from $data['user'] and tries to grab user ID from that.
	* If 0 or false, assumes no user is logged in.
	* 
	* @param $moduleId integer
	* @param $itemId integer
	* @param $itemType string
	* @param $defaultReturn bool
	* @param $permId integry
	* @return bool
	*/
	protected function checkItemAccess($userId, $moduleId, $itemId = 0, $itemType = '', $defaultReturn = true, $permId = 0, $override = false)
	{
		$lockHash = md5($moduleId.':'.$itemId.':'.$itemType.':'.$permId);
		if(!isset(self::$locks[$lockHash])){
			self::$locks[$lockHash] = extract_row(self::$rows, array('moduleId' => $moduleId, 'itemId' => $itemId, 'itemType' => $itemType, 'permId' => $permId), true);
		}
		$getLocks = self::$locks[$lockHash];
		if(count($getLocks) == 0){
			return $defaultReturn;
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
			if($lock['overrideable'] == 1 AND $override){
				$hasReq = true;
			}
			else{
				$hasReq = $this->container->parseLock($getBalances, $lock);
			}
			$stack[] = array('hasReq' => $hasReq, 'stackOp' => $lock['stackOp']);
		}
		$doCheck = $this->container->parseStack($stack);
		return $doCheck;
	}
	
	/**
	 * Generic TCA checking
	 * Checks if one or more addresses meet the defined Token-Access conditions. Returns true or false
	 * 
	 * @param $userId integer - ID of the user
	 * @param $conditions array
	 * @example $conditions = array(array('asset' => 'ASSET', 'amount' => 5000, 'op' => '=', 'stackOp' => 'AND'));
	 * 
	 * @return bool
	 */
	protected function checkAccess($userId, $conditions = array())
	{
		$getBalances = $this->inventory->getUserBalances($userId, true);
		$stack = array();
		foreach($conditions as $lock){
			$hasReq = $this->container->parseLock($getBalances, $lock);
			$stack[] = array('hasReq' => $hasReq, 'stackOp' => $lock['stackOp']);
		}
		$doCheck = $this->container->parseStack($stack);
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
