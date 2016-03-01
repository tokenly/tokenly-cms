<h2>Reset Password</h2>
<p>
	<a href="<?= route('account.auth') ?>">Back to Login/Register</a>
</p>
<p>
	Use the form below to reset your password. Please note that your password
	can only be reset if you have an email address attached to your account. If you
	otherwise cannot gain access to your account, please contact the administration.
</p>
<?= $this->displayFlash('message') ?>
<?= $form->display() ?>
