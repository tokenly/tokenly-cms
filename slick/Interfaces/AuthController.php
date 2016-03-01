<?php
namespace Interfaces;

interface AuthController
{
	function init();
	
	function login();
	
	function logout();
	
	function register();
	
	function sync();
	
	function verify();
	
	function reset();
	
}
