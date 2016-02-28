<?php
namespace Drivers\Auth;
use Core;

class Native_Model extends Core\Model
{
	protected function clearSession($auth)
	{
		$getSesh = $this->container->checkSession($auth);
		if(!$getSesh){
			return false;
		}
		$this->edit('users', $getSesh['userId'], array('lastActive' => null));
		return $this->delete('user_sessions', $getSesh['sessionId']);
	}
	
	protected function checkSession($auth, $useCache = false)
	{
		$get = $this->fetchSingle('SELECT * FROM user_sessions WHERE auth = :auth ORDER BY sessionId DESC LIMIT 1',
									array(':auth' => $auth), 0, $useCache);
		if($get){
			return $get;
		}
		return false;
	}
	
}
