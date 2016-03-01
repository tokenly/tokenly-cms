<div class="register">
	<h2>Register</h2>
	<p>Register a free account using the form below!</p>
	<?= $this->displayFlash('message') ?>
	<?= $form->open() ?>
	<?= $form->displayFields() ?>
  <div class="g-recaptcha" data-sitekey="<?= CAPTCHA_PUB ?>"></div>
  <script type="text/javascript"
	  src="https://www.google.com/recaptcha/api.js?hl=en">
  </script>
	<input type="submit" value="Register Now" />
	<?= $form->close() ?>
	<h3>Already have an account? <a href="<?= route('account.auth') ?>">Click here to login.</a></h3>
</div>
