<?php
namespace Traits;
use Util\Filter;
trait Filterable
{
	static function applyFilters($obj, $method, $arguments, $static = false)
	{
		if(is_object($obj)){
			$class = get_class($obj);
		}
		else{
			$class = $obj;
		}
		
		$filters = Filter::getMethodFilters($class, $method);
		
		$prepends = array();
		if($filters){
			foreach($filters as $k => $filter){
				if($filter['prepend'] === true){
					$prepends[] = $filter;
					unset($filters[$k]);
					continue;
				}
				if($filter['replace'] === true){
					if(is_callable($filter['function'])){
						return call_user_func_array($filter['function'], $arguments);
					}
				}
			}
		}
		else{
			$filters = array();
		}
		foreach($prepends as $prefilter){
			if(is_callable($prefilter['function'])){
				$arguments = call_user_func_array($prefilter['function'], $arguments);
			}
		}
		if(!is_array($arguments)){
			$arguments = array($arguments);
		}
		if($static){
			$output = call_user_func_array('static::'.$method, $arguments);
		}
		else{
			$output = call_user_func_array(array($obj, $method), $arguments);
		}
		foreach($filters as $filter){
			if(is_callable($filter['function'])){
				$output = $filter['function']($output, $arguments);
			}
		}
		return $output;
	}
	
}
