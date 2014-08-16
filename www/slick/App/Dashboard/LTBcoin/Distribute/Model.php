<?php
class Slick_App_Dashboard_LTBcoin_Distribute_Model extends Slick_Core_Model
{
	
	private $coinFieldId = 12;

	public function getShareForm()
	{
		$form = new Slick_UI_Form;
		
		$name = new Slick_UI_Textbox('name');
		$name->setLabel('Distribution Title (optional)');
		$form->add($name);
		
		$asset = new Slick_UI_Textbox('asset');
		$asset->addAttribute('required');
		$asset->setLabel('Asset Name');
		$form->add($asset);
		
		$type = new Slick_UI_Select('valueType');
		$type->setLabel('Input Value Type');
		$type->addOption('fixed', 'Fixed');
		$type->addOption('percent', 'Percentage');
		$form->add($type);
		
		$amount = new Slick_UI_Textbox('amount', 'amount');
		$amount->setLabel('Total Amount to Send');
		$form->add($amount);		
		
		$upload = new Slick_UI_File('csv');
		$upload->setLabel('Upload CSV file:');
		$form->add($upload);
		
		$addresses = new Slick_UI_Textarea('addresses', 'distribute-addresses');
		$addresses->setLabel('or Pay to Addresses:');
		$addresses->addAttribute('placeholder', '<address>, <amount>');
		$form->add($addresses);
		
		$form->setSubmitText('Generate Payment Address');
		$form->setFileEnc();
		
		return $form;
		
	}
	
	public function initDistribution($data)
	{
		
		if(!isset($data['asset']) OR trim($data['asset']) == ''){
			throw new Exception('Asset name required');
		}

		$hasAddresses = false;
		if(isset($_FILES['csv']['tmp_name']) AND trim($_FILES['csv']['tmp_name']) != ''){
			$hasAddresses = true;
		}
		elseif(isset($data['addresses']) AND trim($data['addresses']) != ''){
			$hasAddresses = true;
		}
		if(!$hasAddresses){
			throw new Exception('Please either upload a .csv of addresses/amounts, or enter in the addresses below');
		}
		
		
		$xcp = new Slick_API_Bitcoin(XCP_CONNECT);
		$data['asset'] = trim(strtoupper($data['asset']));
		$getAsset = $xcp->get_asset_info(array('assets' => array($data['asset'])));
		
		if(!$getAsset){
			throw new Exception('Asset ['.$data['asset'].'] does not exist');
		}
		
		$getAsset = $getAsset[0];
		
		$usePercents = false;
		if(isset($data['valueType']) AND $data['valueType'] == 'percent'){
			if(!isset($data['amount']) OR trim($data['amount']) == ''){
				throw new Exception('Must enter total sending amount when using percentage values');
			}
			
			$usePercents = true;
			if($getAsset['divisible']){
				$totalToSend = floatval($data['amount']);
			}
			else{
				$totalToSend = (int)intval($data['amount']);
			}
		}
		
		
		$getApp = $this->get('apps', 'ltbcoin', array(), 'slug');
		$appSettings = $this->getAll('app_meta', array('appId' => $getApp['appId'], 'isSetting' => 1)); 
		$getAll = $this->getAll('xcp_distribute', array('complete' => 0));
		//default values
		$distributeFee = XCP_BASE_FEE;
		$distributeDust = XCP_FEE_MOD * 2; //dust for 2 outputs
		$distributeCut = 0; //make this work later
		$distributeDecimals = 8;
		foreach($appSettings as $setting){
			switch($setting['metaKey']){
				case 'distribute-fee':
					$distributeFee = floatval($setting['metaValue']) * SATOSHI_MOD;
					break;
				case 'distribute-dust':
					$distributeDust = (floatval($setting['metaValue']) * 2) * SATOSHI_MOD;
					break;
				case 'distribute-cut':
					$distributeCut = floatval($setting['metaValue']) * SATOSHI_MOD;
					break;
				case 'distribute-decimals':
					$distributeDecimals = intval($setting['metaValue']);
					break;					
			}
		}		
		
		$addressList = array();

		if(isset($_FILES['csv']['tmp_name']) AND trim($_FILES['csv']['tmp_name']) != ''){
			$getCsv = file_get_contents($_FILES['csv']['tmp_name']);
			$expCsv = explode("\n", $getCsv);
			foreach($expCsv as $csvRow){
				$expRow = explode(',', $csvRow);
				foreach($expRow as &$csvField){
					$csvField = str_replace('"', '', $csvField);
				}
				
				$csvAddr = $expRow[0];
				
				if(trim($csvAddr) == ''){
					continue;
				}
				
				$check = $this->obtainAddress($csvAddr);
				if(!$check){
					continue;
					//throw new Exception('Invalid bitcoin address or system user: '.$csvAddr);
				}
				
				if(!isset($expRow[1])){
					throw new Exception('No amount set for '.$csvAddr);
				}
				
				if(!$usePercents){
					$csvAmount = $expRow[1];
					if($getAsset['divisible']){
						$csvAmount = round(floatval(trim($csvAmount)), $distributeDecimals, PHP_ROUND_HALF_DOWN)  * SATOSHI_MOD; //convert to satoshis
					}
					else{
						$csvAmount = round(floatval(trim($csvAmount)), 0, PHP_ROUND_HALF_DOWN);
					}
				}
				else{
					$percent = floatval(trim($expRow[1])) / 100;
					$csvAmount = $totalToSend * $percent;
					if($getAsset['divisible']){
						$csvAmount = round($csvAmount, $distributeDecimals, PHP_ROUND_HALF_DOWN) * SATOSHI_MOD;
					}
					else{
						$csvAmount = round($csvAmount, 0, PHP_ROUND_HALF_DOWN);
					}
				}
				
				
				if(!isset($addressList[$check])){
					$addressList[$check] = (int)$csvAmount;
				}
				else{
					$addressList[$check] += (int)$csvAmount;
				}
			}
			
		}
		
		if(isset($data['addresses']) AND trim($data['addresses']) != ''){
			$expAddress = explode("\n", $data['addresses']);
			foreach($expAddress as $exp){
				$expAmount = explode(',', $exp);
				
				$address = trim($expAmount[0]);
				if(trim($address) == ''){
					continue;
				}
				$check = $this->obtainAddress($address);
				if(!$check){
					continue;
					//throw new Exception('Invalid bitcoin address or system user: '.$address);
				}
								
				if(!isset($expAmount[1])){
					throw new Exception('No amount set for '.$address);
				}
				
				if(!$usePercents){
					if($getAsset['divisible']){
						$amount = round(floatval(trim($expAmount[1])), $distributeDecimals, PHP_ROUND_HALF_DOWN) * SATOSHI_MOD; //convert to satoshis
					}
					else{
						$amount = round(floatval(trim($expAmount[1])), 0, PHP_ROUND_HALF_DOWN);
					}
				}
				else{
					$percent = floatval(trim($expAmount[1])) / 100;
					$amount = $totalToSend * $percent;
					if($getAsset['divisible']){
						$amount = round($csvAmount, $distributeDecimals, PHP_ROUND_HALF_DOWN) * SATOSHI_MOD;
					}
					else{
						$amount = round($csvAmount, 0, PHP_ROUND_HALF_DOWN);
					}
				}
				
				if(!isset($addressList[$check])){
					$addressList[$check] = (int)$amount;
				}
				else{
					$addressList[$check] += (int)$amount;
				}
			}
		}
		
		if(count($addressList) == 0){
			throw new Exception('No valid addresses found');
		}
		
		$totalSending = 0;
		foreach($addressList as $addr => $amnt){
			$totalSending += $amnt;
		}
		
		if($totalSending > $getAsset['supply']){
			if($getAsset['divisible']){
				$newSupply = (($totalSending - $getAsset['supply']) / SATOSHI_MOD);
			}
			else{
				$newSupply = ($totalSending - $getAsset['supply']);
			}
			
			throw new Exception('Invalid total amount (not enough supply). If needed, issue at least '.$newSupply.' more tokens');
		}
		
		$time = timestamp();
		$xcpAccount = XCP_PREFIX.'Distribute_'.substr(hash('sha256', $time.$data['asset'].json_encode($addressList)), 0, 10);
		
		$btc = new Slick_API_Bitcoin(BTC_CONNECT);
		$getAddress = $btc->getaccountaddress($xcpAccount);
		
		if(!$getAddress){
			throw new Exception('Error retreiving payment address');
		}
		
		$fee = (count($addressList) * $distributeFee);
		$fee += (count($addressList) * $distributeDust);
		$fee = $fee / SATOSHI_MOD;

		
		$useData = array('addressList' => json_encode($addressList), 'address' => $getAddress, 'account' => $xcpAccount,
						'initDate' => $time, 'asset' => $data['asset'], 'status' => 'processing', 'userId' => $data['userId'], 'fee' => $fee,
						'valueType' => $data['valueType']);
		if(isset($data['name'])){
			$useData['name'] = $data['name'];
		}
		
		if($getAsset['divisible']){
			$useData['divisible'] = 1;
		}
		
		$insert = $this->insert('xcp_distribute', $useData);
		if(!$insert){
			throw new Exception('Error initializing distribution');
		}
		
		$useData['distributeId'] = $insert;
		$useData['addressList'] = $addressList; 
	
		return $useData;
	}
	
	public function obtainAddress($address)
	{
		$btc = new Slick_API_BTCValidate;
		
		if($btc->checkAddress($address)){
			return $address;
		}
		
		//search for username, get ltbcoin address
		$get = $this->fetchSingle('SELECT userId FROM users WHERE LOWER(username) = :name', array(':name' => trim(strtolower($address))));
		$getField = $this->get('profile_fields', $this->coinFieldId);
		if(!$get OR !$getField){
			return false;
		}
		
		$getAddress = $this->fetchSingle('SELECT * FROM user_profileVals WHERE userId = :id AND fieldId = :field',
											array(':id' => $get['userId'], ':field' => $getField['fieldId']));
		
		if(!$getAddress){
			return false;
		}
		
		$check = $btc->checkAddress(trim($getAddress['value']));
		if(!$check){
			return false;
		}
		
		return trim($getAddress['value']);
	}
	
	public function lookupAddress($address)
	{
		$get = $this->getAll('user_profileVals', array('value' => $address, 'fieldId' => $this->coinFieldId));
		if(!$get OR count($get) == 0){
			return false;
		}
		
		$output = array('users' => array());
		$names = array();
		foreach($get as $row){
			$getUser = $this->get('users', $row['userId'], array('userId', 'username', 'email'));
			if($getUser){
				$output['users'][] = $getUser;
				$names[] = $getUser['username'];
			}
		}
		$output['names'] = join(', ', $names);
		
		return $output;
	}
	
	public function getEditShareForm()
	{
		$form = new Slick_UI_Form;
		
		$name = new Slick_UI_Textbox('name');
		$name->setLabel('Distribution Title (optional)');
		$form->add($name);

		$status = new Slick_UI_Select('status');
		$status->setLabel('Status');
		$status->addOption('processing', 'Processing');
		$status->addOption('receiving', 'Receiving');
		$status->addOption('sending', 'Sending');
		$status->addOption('complete', 'Complete');
		$status->addOption('hold', 'On Hold');
		$form->add($status);
		
		$batch = new Slick_UI_Textbox('currentBatch');
		$batch->setLabel('Current Batch #');
		$form->add($batch);

		$form->setSubmitText('Edit Details');
		
		return $form;
	}
	
	public function editDistribution($data)
	{
		if(!isset($data['distributeId'])){
			throw new Exception('No distribution set');
		}
		$useData = checkRequiredFields($data, array('status' => false, 'name' => false, 'batch' => false));
		if(count($useData) == 0){
			throw new Exception('No fields set');
		}
		if(isset($useData['status'])){
			if($useData['status'] == 'complete'){
				$useData['complete'] = 1;
			}
			else{
				$useData['complete'] = 0;
			}
		}
		if(isset($useData['currentBatch'])){
			$useData['currentBatch'] = intval($useData['currentBatch']);
		}
		
		$edit = $this->edit('xcp_distribute', $data['distributeId'], $useData);
		if(!$edit){
			throw new Exception('Error updating distribution');
		}
		return true;
	}
}


