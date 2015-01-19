<h2>Verified Address Management</h2>
<p>
	Here you can manage your <strong>verified</strong> bitcoin addresess. Addresses can be verified either by
	signing a unique message with your private key, or by sending a small token donation to us. 
</p>
<p>
	<strong>Why should I verify my bitcoin addresses?</strong>
</p>
<p>
	By verfiying your bitcoin address, we are able to track your balances, including any Counterparty tokens you might have (such as LTBCOIN).
	This gives you access to additional features on the platform such as "Token Controlled Access" and the ability to quickly check your balances without having to open up your wallet.
</p>
<p>
	Token Controlled Access (TCA) is a concept in which we can grant your account access to different pages or specific features and different
	levels of permissions based on the amount of a specific token that you hold in your verified bitcoin addresses.
	For example, if you happen to own at least 1 PODCAST token, you can gain permissions to post and publish your own podcasts!
	Different access tokens can be obtained in a variety of ways, most often through fellow community members or from giveaways and crowdsales. 
	For more information, see <a href="http://letstalkbitcoin.com/blog/post/tcv">here</a>.
</p>
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
