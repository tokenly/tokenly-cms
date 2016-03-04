<div class="register">
	<h2>Register</h2>
	<p>Register a free account using the form below!</p>
	<?= $this->displayFlash('message') ?>
	<?= $form->display() ?>
	<h3>Already have an account? <a href="<?= route('account.auth') ?>">Click here to login.</a></h3>
</div>
