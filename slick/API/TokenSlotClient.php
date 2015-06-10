<?php
namespace API;
class TokenSlotClient
{
	function __construct($url = false, $key = false)
	{
		//constructor
		if(!$url){
			$url = TOKENSLOT_URL;
		}
		if(!$key){
			$key = TOKENSLOT_KEY;
		}
		$this->apiURL = $url;
		$this->apiKey = $key;
	}
	
	/*
	 * Makes a general API call to the Token Slot API
	 * @param string $endpoint desired API endpoint
	 * @param array $args list of parameters to include with request
	 * @param string $method request method
	 * @return Array API response
	 * */	
	public function call($endpoint, $args = array(), $method = 'GET')
	{
		$url = $this->apiURL;
		$params = array();
		foreach($args as $key => $val){
			$params[] = $key.'='.urlencode($val);
			$args[$key] = urlencode($val);
		}
		$url .= $endpoint;		
		$params[] = 'key='.$this->apiKey;
		switch($method){
			case 'GET':
				$url .= '?'.join('&', $params);
				$get = @file_get_contents($url);
				if(!$get){
					return false;
				}
				break;
			case 'POST':
				ob_start();
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_POST, count($params));
				curl_setopt($curl, CURLOPT_POSTFIELDS, join('&', $params));		
				$get = curl_exec($curl);
				curl_close($curl);
				$get = ob_get_contents();
				ob_end_clean();
				break;
			case 'PATCH':
				ob_start();
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
				curl_setopt($curl, CURLOPT_POSTFIELDS, join('&', $params));					
				$get = curl_exec($curl);
				curl_close($curl);
				$get = ob_get_contents();
				ob_end_clean();			
				break;
			case 'DELETE':
				ob_start();
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
				curl_setopt($curl, CURLOPT_POSTFIELDS, join('&', $params));				
				$get = curl_exec($curl);
				curl_close($curl);
				$get = ob_get_contents();
				ob_end_clean();			
				break;
		}
		$decode =  json_decode(trim($get), true);
		return $decode;
	}
	
	/**
	 * Generates a new payment request
	 * @param string $slot can be slot public ID or nickname
	 * @param string $token name of the asset which you want to accept for this payment
	 * @param integer $total the total amount of the order, in satoshis. leave 0 for "pay what you want"
	 * @param string $ref extra reference string you can include
	 * @return Array Payment Request object
	 * */
	public function newPayment($slot, $token, $total = 0, $ref = null)
	{
		$get = $this->call('payments/request/'.$slot, array('token' => strtoupper($token), 'total' => (integer)$total, 'ref' => $ref));		
		return $get;
	}
	
	/*
	 * Checks if a payment request has been completed
	 * @param mixed $id can be payment ID, payment address or the reference field for a payment
	 * @return boolean
	 * */
	public function checkPaymentComplete($id)
	{
		$get = $this->call('payments/'.$id);
		if($get){
			if($get['complete']){
				return true;
			}
		}
		return false;
	}
	
	/*
	 * Parses webhook data and either returns the payload, or calls a custom function
	 * Webhooks should be located at secret URLs which cannot be easily guessed.
	 * @param callable $function custom function to call when payload is received
	 * @return mixed response from function or array of payload field
	 * */
	public function receivePaymentsWebhook($function = false)
	{
		$input = file_get_contents('php://input');
		$json = json_decode(trim($input), true);
		if(!is_array($json) OR !isset($json['payload'])){
			return false;
		}
		if(!$function OR !is_callable($function)){
			if(is_array($json['payload'])){
				return $json['payload'];
			}
			return json_decode($json['payload'], true);	
		}
		return $function($json['payload']);
	}
	
	/*
	 * Cancels a payment request, no more webhook notifications will be received from it
	 * @param mixed $id payment ID, payment address or payment request reference data
	 * @return boolean
	 * */	
	public function cancelPayment($id)
	{
		$cancel = $this->call('payments/'.$id.'/cancel', array(), 'POST');
		if(!$cancel OR !isset($cancel['result']) OR !$cancel['result']){
			return false;
		}
		return true;
		
	}
	
	/*
	 * Gets data for a payment
	 * @param mixed $id payment ID, payment address or payment request reference data
	 * @return Array Payment object
	 * */	
	public function getPayment($id)
	{
		return $this->call('payments/'.$id);
	}
	
	/*
	 * Creates a new "slot" to receive payments at for a specific token
	 * @param mixed $asset the counterparty asset/token to accept. can be an array if accepting multiple tokens
	 * @param string $webhook URL of custom webhook to receive payments to
	 * @param string $forward_address custom bitcoin address to forward payments to
	 * @param integer $min_conf minimum number of confirmations before considering a payment to this slot as "complete"
	 * @param string $label custom internal reference label
	 * @param string $nickname alias you can use instead of slot public_id to make payment requests or get info
	 * @return Array Slot object
	 * 
	 * */	
	public function createSlot($asset, $webhook = false, $forward_address = false, $min_conf = 0, $label = '', $nickname = '')
	{
		$data = array('tokens' => $asset, 'webhook' => $webhook, 'forward_address' => $forward_address,
					  'min_conf' => $min_conf, 'label' => $label, 'nickname' => $nickname);
		return $this->call('slots', $data, 'POST');
	}
	
	/*
	 * Deletes a "slot" from the system - *warning* this will remove any payments associated with the slot
	 * @param string $slot can be slot public ID or nickname
	 * @return boolean
	 * */	
	public function deleteSlot($id)
	{
		$cancel = $this->call('slots/'.$id, array(), 'DELETE');
		if(!$cancel OR !isset($cancel['result']) OR !$cancel['result']){
			return false;
		}
		return true;
	}
	
	/*
	 * Gets data for a specific slot
	 * @param string $slot can be slot public ID or nickname
	 * @return Array Slot object
	 * */	
	public function getSlot($id)
	{
		return $this->call('slots/'.$id);
	}
	
	/*
	 * Fetches data for a slot, or if it does not exist, generates a new one.
	 * @param string $nickname alias you can use instead of slot public_id to make payment requests or get info	 
	 * @param mixed $asset the counterparty asset/token to accept. can be an array if multiple tokens to accept
	 * @param integer $min_conf minimum number of confirmations before considering a payment to this slot as "complete"
	 * @param string $forward_address custom bitcoin address to forward payments to
	 * @param string $webhook URL of custom webhook to receive payments to* 
	 * @param string $label custom internal reference label
	 * @return Array Slot object
	 * */
	public function getOrCreateSlot($nickname, $asset, $min_conf = 0, $forward_address = false, $webhook = false, $label = '')
	{
		$getSlot = $this->getSlot($nickname);
		if($getSlot){
			return $getSlot;
		}
		return $this->createSlot($asset, $webhook, $forward_address, $min_conf, $label, $nickname);
	}
	
	/*
	 * Updates information on a specific slot
	 * @param string $slot can be slot public ID or nickname
	 * @param Array $data slot parameters to update, refer to main API documentation for updateable fields.
	 * @return Array Slot object
	 * */	
	public function updateSlot($id, $data)
	{
		return $this->call('slots/'.$id, $data, 'PATCH');
	}
	
	/*
	 * Gets a list of all payment requests associated with a slot
	 * @param string $slot can be slot public ID or nickname
	 * @return Array of Payment objects
	 * */	
	public function getSlotPayments($id)
	{
		return $this->call('slots/'.$id.'/payments');
	}
	
	
	
}
