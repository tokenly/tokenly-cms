<?php

if($adminView){
	echo '<h2>Edit Profile - '.$thisUser['username'].'</h2>';
	echo '<p><a href="'.SITE_URL.'/dashboard/cms/accounts/view/'.$thisUser['userId'].'">Go Back</a></p>';
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
	<strong>Looking for your <em>LTBcoin Compatible Address</em>? Go to your <a href="<?= SITE_URL ?>/dashboard/tokenly/address-manager">address manager</a> or <a href="<?= SITE_URL ?>/dashboard/account/settings">account settings</a>.</strong>
</p>
