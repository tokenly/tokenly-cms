<h2>User: <?= $thisUser['username'] ?></h2>
<hr>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
	<h3>Account Details</h3>
<ul>
	<li><strong>User ID:</strong> <?= $thisUser['userId'] ?></li>
	<li><strong>Username:</strong> <?= $thisUser['username'] ?></li>
	<li><strong>Email:</strong> <?= $thisUser['email'] ?></li>
	<li><strong>Registration Date:</strong> <?= $thisUser['regDate'] ?></li>
	<li><strong>Last Logged In:</strong> <?= $thisUser['lastAuth'] ?></li>
	<li><strong>Last Active:</strong> <?= $thisUser['lastActive'] ?></li>
</ul>
<?php
if(isset($message) AND trim($message) != ''){
	echo '<p class="error">'.$message.'</p>';
}
?>
<?= $form->display() ?>

<br>
<h3>Profile</h3>
<p>
	<a href="<?= SITE_URL ?>/profile/user/<?= $thisUser['slug'] ?>" target="_blank">Go to Profile</a><br>
	<a href="<?= SITE_URL ?>/dashboard/account/profile/<?= $thisUser['userId'] ?>">Edit Profile</a><br>
	<a href="<?= SITE_URL ?>/dashboard/account/settings/<?= $thisUser['userId'] ?>">Edit Account Settings</a>
</p>
<ul class="profile-info">
	<?php
	foreach($thisUser['profile'] as $prof){
		echo '<li><Strong>'.$prof['label'].':</strong> '.$prof['value'].'</li>';
	}
	?>
	
</ul>
<h3>Meta Info</h3>
<ul>
<?php
foreach($thisUser['meta'] as $key => $val){
	echo '<li><strong>'.$key.':</strong> '.$val.'</li>';
}

?>
</ul>
