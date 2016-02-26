<?php
namespace App\TokenItem;
use Core, API, App\Tokenly\Inventory_Model as Token_Inv;
class Inventory_Model extends Core\Model
{
	
	protected function getUserItems()
	{
		$user = user();
		if(!$user){
			return array();
		}
		
		$inv = new Token_Inv;
		$balances = $inv->getUserBalances($user['userId'], true);
		if(!$balances){
			return array();
		}
		
		$token_list = array();
		foreach($balances as $asset => $balance){
			if($balance <= 0){
				continue;
			}
			$token_list[] = '"'.$asset.'"';
		}
		
		$possible_items = $this->fetchAll('SELECT * FROM token_items WHERE token IN('.join(',', $token_list).')
											AND active = 1
											ORDER BY rank ASC, name ASC');
											
		if(!$possible_items OR count($possible_items) == 0){
			return array();
		}
		
		$output = array();
		$itemModel = new Items_Model;
		foreach($possible_items as $item){
			if(!isset($balances[$item['token']])){
				continue;
			}
			if($balances[$item['token']] >= ($item['min_token'] / SATOSHI_MOD)){
				$user_item = $item;
				$user_item['properties'] = $itemModel->getItemProperties($item['id']);
				$user_item['count'] = floor($balances[$item['token']] / ($item['min_token'] / SATOSHI_MOD));
				if($user_item['count'] == 0){
					continue;
				}
				$output[$item['slug']] = $user_item;
			}
		}
		return $output;
	}
}
