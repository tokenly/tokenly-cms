<?php
/*
 * @name = Registration Form Challenge Question
 * 
 * 
 * */

\Util\Filter::addFilter('Drivers\Auth\Native_Model', 'getRegisterForm', 
	function($form, $args){
		
		$challenge = new UI\Textbox('challenge');
		$challenge->setLabel('Question: Who created the very first version of Bitcoin?');
		$challenge->addAttribute('required');
		$form->add($challenge);

		return $form;
	});


\Util\Filter::addFilter('Drivers\Auth\Native_Model', 'registerAccount', 
	function($data){
		if(!isset($data['isAPI'])){
			if(!isset($data['challenge']) OR trim($data['challenge']) == ''){
				throw new \Exception('Please answer the challenge question');
			}
			$possible_answers = array('satoshi', 'satoshi nakamoto', 'nakamoto');
			if(!in_array(trim(strtolower($data['challenge'])), $possible_answers)){
				throw new \Exception('Incorrect challenge answer');
			}
		}
		return array($data);
	}, true);


