<h2>Reset Password</h2>
<p>
	<a href="<?= route('account.auth') ?>">Back to Login/Register</a>
</p>
<p>
	Hello <?= $user['username'] ?>, enter in your new password below to complete your password reset.
</p>
<?= $this->displayFlash('message') ?>
<?= $form->display() ?>
