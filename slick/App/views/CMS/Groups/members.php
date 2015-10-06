<h2>View Members for <?= $group['name'] ?></h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<?php
if(!$members OR count($members) == 0){
	echo '<p><strong>No members in this group!</strong></p>';
}
else{
	echo '<p><strong>Total Members:</strong> '.number_format(count($members)).'</p>';
	echo '<ul class="group-member-list">';
	foreach($members as $member){
		echo '<li><strong><a href="'.SITE_URL.'/profile/user/'.$member['slug'].'" target="_blank">'.$member['username'].'</a></strong></li>';
	}
	echo '</ul>';
}
?>
