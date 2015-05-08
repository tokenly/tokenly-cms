<?php
namespace Util;
class Paging
{
	
	public function pageArray($data = array(), $perPage = 0)
	{
		if(count($data) == 0){
			return false;
		}
		if($perPage == 0){
			return false;
		}
		$i = 1;
		$newArray = array();
		$curCount = 0;
		foreach($data as $item){
			if(!isset($newArray[$i])){
				$newArray[$i] = array();
			}
			$curCount++;
			$newArray[$i][] = $item;
			if($curCount == $perPage){
				$i++;
				$curCount = 0;
			}
		}
		return $newArray;
	}

}
