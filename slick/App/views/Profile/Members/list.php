<h2>Active Members</h2>
<p>
	Below you can find a directory of all community members, ranked by most recently active.<br>
	Try using the search tool to find a specific user.
</p>
<div class="pull-right member-search">
	<form action="" method="get">
		<input type="textbox" id="search" name="q" placeholder="Enter a username" <?php if(isset($_GET['q'])){ echo 'value="'.$_GET['q'].'"'; } ?> />
		<input type="submit" value="Search">
	</form>
</div>
<ul>
	<li><strong>Total Members:</strong> <?= number_format($numUsers) ?></li>
	<li><strong>Users Online:</strong> <?= number_format($numOnline) ?></li>
	<li><strong>Most Ever Online:</strong> <?= number_format($mostOnline) ?></li>
</ul>
<div class="clear"></div>
<?php
if($query != ''){
	echo '<p><strong>Search results for keywords:</strong> '.$query.' ('.number_format($usersFound).' results)</p>';
}
if(count($members) == 0){
	echo '<p><strong>No members found</strong></p>';
}
?>
<ul class="member-list">
	<?php
	$time = time();
	foreach($members as $user){
		
		$avImage = $user['profile']['avatar'];
		if(!isExternalLink($user['profile']['avatar'])){
			$avImage = SITE_URL.'/files/avatars/'.$user['profile']['avatar'];
		}
	
	
		$avatar = '<a href="'.SITE_URL.'/profile/user/'.$user['slug'].'"><img src="'.$avImage.'" alt="" /></a>';
		

		$user['sub-name'] = '';
		if(isset($user['profile']['profile']['real-name']) AND trim($user['profile']['profile']['real-name']['value']) != ''){
			$user['sub-name'] = '<p><strong>'.$user['profile']['profile']['real-name']['value'].'</strong></p>';
		}
		$bio = '';
		if(isset($user['profile']['profile']['bio']) AND trim($user['profile']['profile']['bio']['value']) != ''){
			$bio = markdown(shorten($user['profile']['profile']['bio']['value'], 300, SITE_URL.'/profile/user/'.$user['slug'], 'Read More'));
		}
		
			
		$online_icon = 'fa-circle-o text-error';
		$online_title = 'Offline';
		$activeTime = strtotime($user['lastActive']);
		$diff = $time - $activeTime;
		if($diff < 7200){
			$online_icon = 'fa-circle text-success';
			$online_title = 'Recently Online';
		}
		
		$lastActive = '';
		if($user['lastActive'] != '0000-00-00 00:00:00' AND $user['lastActive'] != null){
			$lastActive = '<small><em>Last Active: '.formatDate($user['lastActive']).'</em> <i class="fa '.$online_icon.'" title="'.$online_title.'"></i></small>';
		}
					
		echo '<li>
				<div class="member-avatar">
					'.$avatar.'
				</div>
				<div class="member-info">
					<h4><a href="'.SITE_URL.'/profile/user/'.$user['slug'].'">'.$user['username'].'</a></h4>
					<div class="member-bio">
					'.$user['sub-name'].'
					'.$bio.'
					<p>
						<strong><a href="'.SITE_URL.'/profile/user/'.$user['slug'].'" class="btn btn-small btn-blue">View Profile</a></strong>
					</p>
					'.$lastActive.'
					</div>
				</div>
				<div class="clear"></div>';
		
		echo '</li>';
	}
	
	if($numPages > 1){
		echo '<div class="member-paging blog-paging">
				Pages: ';
		$curPage = 1;
		if(isset($_GET['page'])){
			$curPage = intval($_GET['page']);
		}
		$max = 25;
		$elips = false;
		$elips2 = false;
		for($i = 1; $i <= $numPages; $i++){
			$active = '';
			if(($i <= ($curPage - $max + 5) AND $i > 5)){
				if(!$elips){
					$elips = true;
					echo '.... ';
				}
				continue;
			}
			if(($i > ($max + $curPage) AND $i < ($numPages - 5))){
				if(!$elips2){
					$elips2 = true;
					echo '.... ';
				}
				continue;
			}
			if((isset($_GET['page']) AND $_GET['page'] == $i) OR (!isset($_GET['page']) AND $i == 1)){
				$active = 'active';
			}
			echo '<a href="?page='.$i.'&q='.$query.'" class="'.$active.'">'.$i.'</a> ';
		}
		echo '</div>';
	}
	
	?>
	
</ul>
