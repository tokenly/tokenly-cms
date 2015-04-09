<h2>Reset Password</h2>
<p>
	<a href="<?= SITE_URL ?>/account">Back to Login/Register</a>
</p>
<p>
	Hello <?= $user['username'] ?>, enter in your new password below to complete your password reset.
</p>
<?php
if(isset($message) and trim($message) != ''){
	echo '<p class="error">'.$message.'</p>';
}
?>
<?= $form->display() ?>
