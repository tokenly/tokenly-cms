<h2>My Referrals</h2>
<p>
	The internet is a big place, and the LTBn is a new, growing community!  Just as we reward you for
	contributing to the community, so can you now be rewarded for helping us grow!
</p>
<p>
	To get credit for helping a new community member find us, either have them enter your username 
	where it is requested in their profile.  You can also find your specific referral URL below,
	and when you have a friend sign up through that link they'll automatically have you set as
	their referring member.
</p>
<p>
	Each week you'll recieve credit for each member who you referred that has maintained a minimum level of activity on the site during that week.
</p>
<p>
	<strong>My Affiliate Link: <a href="<?= SITE_URL ?>?ref=<?= $refLink ?>"><?= SITE_URL ?>?ref=<?= $refLink ?></a></strong>
</p>

<?php
if(count($refs) == 0){
	echo '<p>No referrals yet!</p>';
}
else{
	foreach($refs as &$row){
		$row['userlink'] = '<a href="'.SITE_URL.'/profile/user/'.$row['slug'].'" target="_blank">'.$row['username'].'</a>';
	}
	
	echo '<p><strong>Total Referrals:</strong> '.count($refs).'</p>';
	$table = $this->generateTable($refs, array('class' => 'admin-table mobile-table',
											   'fields' => array('userlink' => 'Username',
																 'refTime' => 'Referral Date'),
												'options' => array(array('field' => 'refTime', 'params' => array('functionWrap' => 'formatDate')),
																	
												)));
	
	echo $table->display();
	
}

?>
