<h1>Token Inventory</h1>
<p class="pull-right">
	<a href="<?= route('account.auth') ?>/sync?r=<?= urlencode(route('tokenly.token-inventory') ) ?>" class="btn">Sync Account</a>
</p>
<p>
	Manage your <strong>verified bitcoin addresses</strong> and your <strong>token inventory</strong>
	using the TokenPass dashboard below.<br>
	Once complete, you may use the "sync account" button to ensure all changes are applied.
</p>
<iframe src="<?= TOKENPASS_URL ?>/inventory" width="100%" height="1000"></iframe>
