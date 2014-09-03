<div class="login">
	<h2>Login</h2>
	<?php
	if($loginMessage != ''){
		echo '<p class="error">'.$loginMessage.'</p>';
	}
	?>
	<?= $loginForm->display() ?>
	<p>
		Forgot your password? <a href="<?= SITE_URL ?>/account/reset">Click here to reset your password.</a>
	</p>
</div>
<?php
if($registerForm){
?>
<div class="register">
	<h2>Register</h2>
	<p>Register a free account using the form below!</p>
	<?php
	if($registerMessage != ''){
		echo '<p class="error"><strong>'.$registerMessage.'</p>';
	}
	?>
	<?= $registerForm->open() ?>
	<?= $registerForm->displayFields() ?>
	<?php
	require_once(SITE_PATH.'/resources/recaptchalib.php');
	echo recaptcha_get_html(CAPTCHA_PUB, null)
	?>
	<input type="submit" value="Register Now" />
	<?= $registerForm->close() ?>
</div>
<?php
}//endif
?>
