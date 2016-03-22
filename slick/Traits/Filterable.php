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
					return call_user_func_array($filter['function'], $arguments);
				}
			}
		}
		else{
			$filters = array();
		}
		foreach($prepends as $prefilter){
			$arguments = call_user_func_array($prefilter['function'], $arguments);
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
			$output = $filter['function']($output, $arguments);
		}
		return $output;
	}
	
	static function applyPreFilters($class, $method, $arguments)
	{
		if(is_object($class)){
			$class = get_class($class);
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
			}
			foreach($prepends as $prefilter){
				$arguments = call_user_func_array($prefilter['function'], $arguments);
			}				
		}		
		return $arguments;
	}
	
	static function applyPostFilters($class, $method, $output, $arguments)
	{
		if(is_object($class)){
			$class = get_class($class);
		}
		$filters = Filter::getMethodFilters($class, $method);	
		if($filters){
			foreach($filters as $filter){
				if($filter['prepend'] === true OR $filter['replace'] === true){
					unset($filters[$k]);
					continue;
				}
			}
			foreach($filters as $filter){
				$output = $filter['function']($output, $arguments);
			}			
		}
		return $output;		
	}
	
}
