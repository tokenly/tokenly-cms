<h2>Verify Address: <?= $address['address'] ?></h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<p>
	There are two ways that you can verify the ownership of your address with us.
	The first is by making a small token donation to us. Once we see any amount of bitcoin
	come in from your address (even as low as 1 satoshi), we can confirm ownership. 
	Please do no re-use deposit addresses. Donations are non-refundable.
</p>
<p>
	The second method is by signing a unique message with your bitcoin address. This will allow
	us to confirm ownership without needing any payment or transaction. Note that not all wallets support
	message signing.
</p>
<p>
	<strong>Choose your method of verification:</strong>
</p>
<div class="address-verify-opts">
	<input type="button" id="tokenPayment" value="Make a Small Donation" />
	<input type="button" id="signMessage" value="Sign a Message" />
	<span class="loader"></span>
</div>

<div id="token-payment-cont" style="display: none;">
	<hr>
	<h3>Make a Donation</h3>
	<p>
		Send any amount of <strong>BTC</strong> from <em><?= $address['address'] ?></em> to:
	</p>
	<p>
		<strong class="verify-deposit-address">
			<img src="<?= SITE_URL ?>/qr.php?q=<?= $depositAddress ?>" alt="" /><br>
			<?= $depositAddress ?></strong>
	</p>
	<div class="payment-status">Waiting for payment...</div>
</div>
<div id="message-sign-cont" style="display: none;">
	<hr>
	<h3>Sign a Message</h3>
	<p>Sign the text below with your address using Base 64 encoding (Bitcoin-QT compatible) and enter in the results to verify your address</p>
	<p><em>Note: there is currently an issue with counterwallet which is causing it to produce invalid signatures</em></p>
	<div class="secret-message">
		<input type="text" readonly onclick="this.select()" value="<?= $secretMessage ?>" />
	</div>
	<textarea id="messageSig" style="height: 50px;" placeholder="Enter signature" ></textarea>
	<input type="button" id="submitSig" value="Verify" />
	<div class="sig-status"></div>

</div>

<script type="text/javascript">
	$(document).ready(function(){

		$('#tokenPayment').click(function(){
			$(this).attr('disabled','disabled');
			$('#token-payment-cont').slideDown();
			window.paymentInt = setInterval(function(){
				var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/checkPayment/<?= $address['address'] ?>';
				$.get(url, function(data){
					if(typeof data.error != 'undefined'){
						console.log(data.error);
						return false;
					}
					if(data.result == 'verified'){
						$('.payment-status').addClass('paid').html('Verified!');
						clearInterval(window.paymentInt);
					}
					
				});
				
			},10000);			
		});
		$('#signMessage').click(function(){
			$(this).attr('disabled','disabled');
			$('#message-sign-cont').slideDown();
		});

		$('#submitSig').click(function(){
			var getSig = $('#messageSig').val();
			if(getSig.trim() == ''){
				return false;
			}
			
			var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/checkMessage/<?= $address['address'] ?>';
			$.post(url, {message: getSig}, function(data){
				if(typeof data.error != 'undefined'){
					$('.sig-status').html(data.error);
					return false;
				}
				if(data.result == 'verified'){
					$('#submitSig').attr('disabled', 'disabled');
					$('.sig-status').addClass('verified').html('Verified!');
				}
				else{
					$('.sig-status').html('Invalid');
				}
			});
			
		});

	});
</script>
