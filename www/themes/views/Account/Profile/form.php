<?php
if(isset($avatar) AND trim($avatar) != ''){
	?>
	<div class="profile-pic">
		<img src="<?= SITE_URL ?>/files/avatars/<?= $avatar ?>" alt="<?= $user['username'] ?>" />
	</div>
	<?php
}

if($adminView){
	echo '<h2>Edit Profile - '.$thisUser['username'].'</h2>';
	echo '<p><a href="'.SITE_URL.'/dashboard/accounts/view/'.$thisUser['userId'].'">Go Back</a></p>';
}
else{
	echo '<h2>Edit Profile</h2>';
	echo '<p><a href="'.SITE_URL.'/profile/user/'.$thisUser['slug'].'" target="_blank">Click here</a> to go to your public profile page.</a></p>';
}
?>

<?php
if(isset($message) AND $message != null){
	echo '<p class="error">'.$message.'</p>';
}
?>
<?= $form->display() ?>

<p>
	<strong>Looking for your <em>LTBcoin Compatible Address</em>? Go to your <a href="<?= SITE_URL ?>/dashboard/address-manager">address manager</a> or <a href="<?= SITE_URL ?>/account/settings">account settings</a>.</strong>
</p>
