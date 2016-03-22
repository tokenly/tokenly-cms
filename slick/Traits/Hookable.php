<?php
namespace Traits;
use Util\Hook;

trait Hookable
{
	static function triggerHooks($obj, $method, $output)
	{
		if(!is_string($obj)){
			$class = get_class($obj);
		}
		else{
			$class = $obj;
		}
		$hooks = Hook::getMethodHooks($class, $method);
		if($hooks){
			foreach($hooks as $hook){
				if(is_callable($hook['function'])){
					$hook['function']($output);
				}
			}
		}
	}

}
