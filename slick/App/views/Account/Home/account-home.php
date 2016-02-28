<h2>Welcome Back <?= ucfirst($user['username']) ?></h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/auth/logout">Log Out</a>
</p>
<ul>
	<li><a href="<?= SITE_URL ?>/dashboard">Go to Dashboard</a></li>
</ul>
