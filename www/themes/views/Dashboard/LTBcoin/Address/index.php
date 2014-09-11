<h2>Verified Address Management</h2>
<p>
	Here you can manage your <strong>verified</strong> bitcoin addresess. Addresses can be verified either by
	signing a unique message with your private key, or by sending a small token donation to us. 
</p>
<p>
	<strong>Why should I verify my bitcoin addresses?</strong>
</p>
<ul>
	<li>
		So that we know where to safely send your hard earned LTBcoin!
	</li>
	<li>
		Whenever you send us a tip or interact with us in another way via your bitcoin address, we will
		be able to link it directly to your account, allowing us to award you for your efforts!
	</li>
	<li>
		Enable additional features on the site and provide you additional account data (quick access to token balances etc.)
	</li>
</ul>
<p>
	<strong>Note:</strong> You do not need a verified address to receive weekly LTBcoin rewards.
</p>
<h3>Add New Address</h3>
<?php
if(isset($message) AND trim($message) != ''){
	echo '<p class="error">'.$message.'</p>';
}
?>
<?= $form->display() ?>

<br>
<h3>My Addresses</h3>
<?php
if(count($addresses) == 0){
	echo '<p>No addresses found.</p>';
}
else{
	
	echo '<table class="admin-table mobile-table address-list">
			<thead>
				<tr>
					<th>Label</th>
					<th>Address</th>
					<th>XCP Compatible?</th>
					<th>Public?</th>
					<th></th>
				</tr>
			</thead>
			<tbody>';
	
	foreach($addresses as $address){
		echo '<tr>';
		
		echo '<td>'.$address['label'].'</td>';
		
		$addressLabel = '<a href="https://blockchain.info/address/'.$address['address'].'" target="_blank">'.$address['address'].'</a>';
		if($address['isPrimary'] == 1){
			$addressLabel = '<strong>'.$addressLabel.' [primary]</strong>';
		}
		if($address['verified'] == 0){
			$addressLabel .= '<br><em>(unverified)</em>';
		}
		echo '<td>'.$addressLabel.'</td>';
		echo '<td>'.boolToText($address['isXCP']).'</td>';
		echo '<td>'.boolToText($address['public']).'</td>';
		$verify = '';
		if($address['verified'] == 0){
			$verify = '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/verify/'.$address['address'].'"><strong>VERIFY</strong></a><br>';
		}
		echo '<td>'.$verify.'';
		echo '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'.$address['address'].'">EDIT</a><br>';
		echo '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/'.$address['address'].'" class="delete">DELETE</a></td>';
		
		echo '</tr>';
	}

		
	echo '</tbody></table>';								
}
?>
