
<h2>Dashboard</h2>
<hr>
<div class="profile-pic">
	<?php
	if(isset($user['meta']['avatar']) AND trim($user['meta']['avatar']) != ''){
		echo '<img src="'.SITE_URL.'/files/avatars/'.$user['meta']['avatar'].'" alt="" />';
	}
	
	?>
</div>
<p>Welcome back, <strong><?= $user['username'] ?></strong></p>
<p>
	Have a Magic Word? <a href="<?= SITE_URL ?>/dashboard/blog/magic-words">Click Here</a>
</p>
<p>
	<strong>Last Logged In:</strong> <?= formatDate($user['lastAuth']) ?><br>
	<strong>Date Registered:</strong> <?= formatDate($user['regDate']) ?><br>
	<strong>Email Address:</strong> <?php if($user['email'] == ''){ echo 'N/A'; } else{ echo $user['email']; } ?>
	<?php
	if($user['affiliate']){
		echo '<br><strong>Referred By:</strong> <a href="'.SITE_URL.'/profile/user/'.$user['affiliate']['slug'].'" target="_blank">'.$user['affiliate']['username'].'</a>';
	}
	?>
</p>
<p>
	Just added a new Access Token?
		<form action="<?= SITE_URL ?>/dashboard/tokenly/inventory" method="post" style=" margin: 0px;">
		<input type="submit" name="forceRefresh" style="font-size: 10px; margin: 0px;" id="forceRefresh" value="Update My Inventory" />
		</form>
</p>
<h3>Account Information</h3>
<?php
$profModel = new Slick_App_Profile_User_model;
$getProfile = $profModel->getUserProfile($user['userId'], $site['siteId']);
?>
<ul class="dash-home-stats">
	<?php
	if(isset($getProfile['profile']['ltbcoin-address']) AND trim($getProfile['profile']['ltbcoin-address']['value']) != ''){
		echo '<li><strong>LTBcoin Compatible Address:</strong> '.$getProfile['profile']['ltbcoin-address']['value'].'<br> (<a href="#xcp-qr" class="fancy">Click to view QR Code</a>)
			<div id="xcp-qr" style="display: none;"><img src="'.SITE_URL.'/qr.php?q='.$getProfile['profile']['ltbcoin-address']['value'].'" alt="" /></div></li>';
		echo '<li><strong>Token Balances:</strong> <a href="'.SITE_URL.'/dashboard/tokenly/inventory">Click here to go to your <em>inventory</em></a>';
		
		 
					
		echo '</li>';
		
	}
	else{
		echo '<li><strong>It looks like you have not yet registered for the LTBcoin Rewards Program! <br>
				<a href="http://ltbcoin.com/#how-to-receive-ltbcoin" target="_blank">Click here to learn more.</a></strong></li>';
	}
	?>
</ul>
<?php
if(isset($_GET['closeThis'])){
	
	?>
	<script type="text/javascript">
		$(document).ready(function(){
			window.close();
		});
	</script>
	<?php
}
?>
