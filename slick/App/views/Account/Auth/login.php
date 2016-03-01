<div class="login">
	<h2>Login</h2>
	<?= $this->displayFlash('message') ?>
	<?= $form->display() ?>
	<p>
		Forgot your password? <a href="<?= route('account.auth') ?>/reset">Click here to reset your password.</a>
	</p>
	<h3>Don't have an account? <a href="<?= route('account.auth') ?>/register">Click here to register.</a></h3>
</div>

