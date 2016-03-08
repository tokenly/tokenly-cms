<?php
namespace Interfaces;

interface AuthController
{
	public function init();
	
	public function login();
	
	public function logout();
	
	public function register();
	
	public function sync();
	
	public function verify();
	
	public function reset();
	
}
