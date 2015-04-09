<?php
class Slick_App_Tokenly_Address_Model extends Slick_Core_Model
{
	public function getAddressForm()
	{
		$form = new Slick_UI_Form;
		
		$address = new Slick_UI_Textbox('address');
		$address->setLabel('BTC Address');
		$address->addAttribute('required');
		$form->add($address);
		
		$label = new Slick_UI_Textbox('label');
		$label->setLabel('Label (optional)');
		$form->add($label);
		
		$isPrimary = new Slick_UI_Checkbox('isPrimary');
		$isPrimary->setBool(1);
		$isPrimary->setValue(1);
		$isPrimary->setLabel('Primary Address?');
		$form->add($isPrimary);
		
		$isXCP = new Slick_UI_Checkbox('isXCP');
		$isXCP->setBool(1);
		$isXCP->setValue(1);
		$isXCP->setLabel('Counterparty Compatible Address?');
		$form->add($isXCP);
		
		$public = new Slick_UI_Checkbox('public');
		$public->setBool(1);
		$public->setValue(1);
		$public->setLabel('Public Address?');
		$form->add($public);		
		
		return $form;
		
	}
	
	public function addAddress($data)
	{
		if(!isset($data['userId'])){
			throw new Exception('No User ID set');
		}
		
		$validTypes = array('btc');
		if(!in_array($data['type'], $validTypes)){
			throw new Exception('Invalid coin type!');
		}
			
		$validate = new Slick_API_BTCValidate;
		if(!isset($data['address'])){
			throw new Exception('No address set!');
		}
		$check = $validate->checkAddress($data['address']);
		if(!$check){
			throw new Exception('Invalid Bitcoin address');
		}
		
		//check if they added this address already
		$checkAdded = $this->getAll('coin_addresses', array('userId' => $data['userId'], 'address' => $data['address']));
		if(count($checkAdded) > 0){
			throw new Exception('Address already submitted');
		}
		
		$isXCP = 0;
		if(isset($data['isXCP']) AND intval($data['isXCP']) === 1){
			$isXCP = 1;
		}
		$isPrimary = 0;
		if(isset($data['isPrimary']) AND intval($data['isPrimary']) === 1){
			$isPrimary = 1;
		}	
		$public = 0;
		if(isset($data['public']) AND intval($data['public']) === 1){
			$public = 1;
		}			
		
		$useData = array('type' => $data['type'], 'address' => $data['address'], 'submitDate' => timestamp(),
						'isXCP' => $isXCP, 'userId' => $data['userId'], 'label' => $data['label'], 'public' => $public);
		

		$add = $this->insert('coin_addresses', $useData);
		if(!$add){
			throw new Exception('Error adding address');
		}
		
		if($isPrimary === 1){
			$this->switchPrimary($data['userId'], $add);
		}
		
		return $add;
	}
	
	public function switchPrimary($userId, $addressId)
	{
		$sql = 'UPDATE coin_addresses SET isPrimary = 0 WHERE userId = :userId';
		$exec = $this->sendQuery($sql, array('userId' => $userId));
		if(!$exec){
			return false;
		}
		$edit = $this->edit('coin_addresses', $addressId, array('isPrimary' => 1));
		
		if(!$edit){
			return false;
		}
		
		$getAddress = $this->get('coin_addresses', $addressId);
		
		if($getAddress['isXCP'] != 0){
			//update token profile field
			$getVal = $this->fetchSingle('SELECT * FROM user_profileVals WHERE userId = :userId AND fieldId = :fieldId',
										array(':userId' => $userId, ':fieldId' => PRIMARY_TOKEN_FIELD), 0, true);
										
			$getField = $this->get('profile_fields', PRIMARY_TOKEN_FIELD);

			if($getField){
				$insertData = array('value' => $getAddress['address'], 'lastUpdate' => timestamp());
				if($getVal){
					$update = $this->edit('user_profileVals', $getVal['profileValId'], $insertData);
				}
				else{
					//insert new one
					$insertData['userId'] = $userId;
					$insertData['fieldId'] = PRIMARY_TOKEN_FIELD;
					$update = $this->insert('user_profileVals', $insertData);
				}
			}
		}
		
		return true;
	}
	
	public function editAddress($addressId, $data)
	{
		$getAddress = $this->get('coin_addresses', $addressId);
		$isXCP = 0;
		if(isset($data['isXCP']) AND intval($data['isXCP']) === 1){
			$isXCP = 1;
		}
		$isPrimary = 0;
		if(isset($data['isPrimary']) AND intval($data['isPrimary']) === 1){
			$isPrimary = 1;
		}	
		$public = 0;
		if(isset($data['public']) AND intval($data['public']) === 1){
			$public = 1;
		}		
		$useData = array('label' => $data['label'], 'isXCP' => $isXCP, 'isPrimary' => $isPrimary, 'public' => $public);
		$edit = $this->edit('coin_addresses', $addressId, $useData);
		if(!$edit){
			throw new Exception('Error editing address');
		}
		if($useData['isPrimary'] == 1){
			$this->switchPrimary($getAddress['userId'], $addressId);
		}		
		return true;
	}
	
	public function getDepositAddress($address)
	{
		$btc = new Slick_API_Bitcoin(BTC_CONNECT);
		$account = 'VERIFY_'.$address['userId'].':'.$address['addressId'];
		try{
			$getAddress = $btc->getaccountaddress($account);
		}
		catch(Exception $e){
			$getAddress = 'Error retrieving deposit address';
		}
		
		return $getAddress;
		
	}
	
	public function checkAddressPayment($address)
	{
		if($address['verified'] != 0){
			return array('result' => 'verified');
		}
		$btc = new Slick_API_Bitcoin(BTC_CONNECT);
		$account = 'VERIFY_'.$address['userId'].':'.$address['addressId'];
		try{
			$txs = $btc->listtransactions($account);
			$verified = false;
			foreach($txs as $tx){
				if($tx['category'] == 'receive'){
					$getRaw = $btc->getrawtransaction($tx['txid']);
					$decodeRaw = $btc->decoderawtransaction($getRaw);
					$getRaw2 = $btc->getrawtransaction($decodeRaw['vin'][0]['txid']);
					$decodeRaw2 = $btc->decoderawtransaction($getRaw2);
					
					foreach($decodeRaw2['vout'] as $vout){
						$outAddress = $vout['scriptPubKey']['addresses'][0];
						if($outAddress == $address['address'] AND $tx['amount'] > 0){
							$verified = true;
							$btc->move($account, 'VERIFY_FUNDS', $tx['amount']);
						}
					}
				}
			}			
		}
		catch(Exception $e){
			return array('error' => 'Error getting transactions');
		}

		if($verified){
			$update = $this->edit('coin_addresses', $address['addressId'], array('verified' => 1));
			if(!$update){
				return array('error' => 'Error updating address');
			}
			return array('result' => 'verified');
		}
		
		return array('result' => 'none');
	}
	
	public function getSecretMessage($address)
	{
		$chars = 12;
		$hash = hash('sha256', md5($address['submitDate'].$address['address'].'_'.$address['addressId']));
		$hashFrag = substr($hash, 0, $chars);
		$output = '';
		$idx = 0;
		$max = ceil($chars / 3);
		for($i = 0; $i < 3; $i++){
			for($i2 = 0; $i2 < $max; $i2++){
				$output .= $hashFrag[$idx];
				$idx++;
			} 
			$output .= ' ';
		}
		$output = trim($output);
		return $output;
		
	}
	
	public function checkSecretMessage($address)
	{
		if($address['verified'] != 0){
			return array('error' => 'Already verified');
		}
				
		if(!isset($_POST['message'])){
			return array('error' => 'No message entered');
		}
		$getMessage = $this->getSecretMessage($address);
		$btc = new Slick_API_Bitcoin(BTC_CONNECT);
		
		$inputMessage = trim($_POST['message']);
		if(strpos($inputMessage, '-----BEGIN BITCOIN SIGNATURE-----') !== false){
			//pgp style signed message format, extract the actual signature from it
			$expMsg = explode("\n", $inputMessage);
			foreach($expMsg as $k => $line){
				if($line == '-----END BITCOIN SIGNATURE-----'){
					if(isset($expMsg[$k-1])){
						$inputMessage = trim($expMsg[$k-1]);
					}
				}
			}
		}
		
		$result = false;
		try{
			$checkMessage = $btc->verifymessage($address['address'], $inputMessage, $getMessage);
			if($checkMessage){
				$result = true;
			}
		}
		catch(Exception $e){
			return array('error' => 'Invalid signature');
		}
		
		if($result){
			$update = $this->edit('coin_addresses', $address['addressId'], array('verified' => 1));
			if(!$update){
				return array('error' => 'Error updating address');
			}
			return array('result' => 'verified');
		}
		else{
			return array('result' => 'none');
		}
	}
	
}
