<?php
namespace Tags;
use App\Meta_Model, Util\Mail, API;
class TokenSlotDemo
{
	protected $slot_name = 'tokenslot_demo';	
	protected $token = 'TOKENLY';
	protected $page_name = 'tokenslot-demo';

	function __construct()
	{
		$this->meta = new Meta_Model;
		$this->slots = new API\TokenSlotClient();
		
	}
	
	public function display()
	{
		$tokenlyApp = get_app('tokenly');
		$getOrders = $this->meta->getAppMeta($tokenlyApp['appId'], $this->page_name.'-orders');
		if(!$getOrders){
			$getOrders = array();
		}
		else{
			$getOrders = json_decode($getOrders, true);
		}
		if(isset($_GET['hook'])){
			$json = $this->slots->receivePaymentsWebhook();
			if($json){
				if(isset($json['payment_id'])){
					foreach($getOrders as &$order){
						if($order['payment_id'] == $json['payment_id']){
							if($json['complete']){
								//do logic for completing order
								$site = currentSite();
								$mail = new Mail;
								$mail->addTo($order['email']);
								$mail->setSubject('Greetings from Tokenly');
								$mail->setHTML('
Thank you for redeeming your '.$this->token.' tokens with us! *high five*

- Team
								');
								
								$mail->setFrom('noreply@'.$site['domain']);
								$send = $mail->send();
								$order['status'] = 'complete';
							}
							else{
								$order['status'] = 'receiving';
							}
							break;
						}
					}
					$this->meta->updateAppMeta($tokenlyApp['appId'], 'tokenslot-demo-orders', json_encode($getOrders));
					die();
				}
			}
		}
		elseif(isset($_GET['check'])){
			header('Content-Type: text/json');
			$output['error'] = null;
			$output['result'] = false;
			foreach($getOrders as $order){
				if($order['payment_id'] == $_GET['check']){
					$output['result'] = $order;
				}
			}
			if(!$output['result']){
				$output['error'] = 'Could not find order';
			}
			
			
			echo json_encode($output);
			die();
		}
		if(posted()){
			if(isset($_GET['request']) AND isset($_POST['email'])){
				header('Content-Type: text/json');
				$output = array('error' => null);
				if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
					$output['error'] = 'Invalid email';
					echo json_encode($output);
					die();					
				}
				$getSlot = $this->slots->getSlot($this->slot_name);
				if(!$getSlot){
					$site = currentSite();
					$createSlot = $this->slots->createSlot($this->token, $site['url'].'/'.$this->page_name.'?hook=1', false, 0, $this->slot_name, $this->slot_name);
					if(!$createSlot){
						$output['error'] = 'Error generating slot';
						echo json_encode($output);
						die();
					}
				}
				$getAddress = $this->slots->newPayment($this->slot_name, $this->token, 1*SATOSHI_MOD);
				if(isset($getAddress['address'])){
					$output = $getAddress;
					$payment_info = $getAddress;
					$payment_info['email'] = $_POST['email'];
					$payment_info['date'] = timestamp();
					$payment_info['IP'] = $_SERVER['REMOTE_ADDR'];
					$payment_info['complete'] = false;
					$payment_info['status'] = 'pending';
					$getOrders[] = $payment_info;
					$this->meta->updateAppMeta($tokenlyApp['appId'], 'tokenslot-demo-orders', json_encode($getOrders));
					
				}
				
				echo json_encode($output);
				die();
			}
		}	
		ob_start();
		
		?>
		<h2>Token Slot Demo</h2>
		<p>
			Redeem <strong>1 <?= $this->token ?></strong> to get a special thank you e-mail and digital high-five
			from the Tokenly development team!
		</p>
		<div class="tokenslot-demo">
			<div>
				<input id="email" type="text" placeholder="Your email address">
			</div>
			<button id="redeem-token" style="font-size: 22px;">Pay Now</button>
			<div id="payment-status" style="display: none;">
				<p>Please send <strong>1 <?= $this->token ?></strong> to the following address</p>
				<div class="deposit-address" style="font-size: 18px; font-weight: 700; margin-bottom: 15px; color: #2ba6cb;">
					<span class="companion-tip-button-test" data-address="" data-tokens="<?= $this->token ?>" data-label="Token Slot Demo"></span>
				</div>
				<p style="font-weight: 700;">
					<span class="status text-default">Waiting for payment...</span>
				</p>
			</div>
		</div>
		<script type="text/javascript">
			$('#redeem-token').click(function(e){
				
				var thisEmail = $('input#email').val();
				var url = '?request=1';
				var thisBtn = $(this);
				$.post(url, {email: thisEmail}, function(data){
					console.log(data);
					if(data.error != null){
						alert(data.error);
						$('input#email').val('');
						return false;
					}
					$('#payment-status').find('.deposit-address').find('span').html(data.address).data('address', data.address);
					thisBtn.attr('disabled', 'disabled');
					$('#payment-status').slideDown();
					window.payment_id = data.payment_id;
					window.redeemInt = setInterval(function(){
						var url = '?check=' + window.payment_id;
						$.get(url, function(data2){
							if(data2.error != null){
								console.log(data2.error);
								return false;
							}
							var status_text = 'Waiting for payment...';
							var status_class = 'text-default';
							if(data2.result.status == 'receiving'){
								status_text = 'Receiving funds...';
								status_class = 'text-pending';
							}
							if(data2.result.status == 'complete'){
								status_text = 'Payment complete! Email has been sent to your inbox.';
								status_class = 'text-success';
							}
							$('#payment-status').find('.status').html(status_text).attr('class', 'status ' + status_class);
						});
						
					}, 5000);
				});
				
			});
		
		</script>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	
}
