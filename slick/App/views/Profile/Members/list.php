<h1 class="large">Community Directory</h1>
<hr>
<div class="pull-right member-search">
	<form action="" method="get">
		<label for="sort">Sort members by:</label>
		<?php
		$sortSelect = array('active' => '', 'alph' => '', 'new' => '', 'old' => '');
		if(isset($_GET['sort'])){
			$sortSelect[$_GET['sort']] = 'selected';
		}
		?>
		<select name="sort" id="sort">
			<option value="active" <?= $sortSelect['active'] ?>>Recently Active</option>
			<option value="alph" <?= $sortSelect['alph'] ?>>Alphabetical</option>
			<option value="new" <?= $sortSelect['new'] ?>>Newest</option>
			<option value="old" <?= $sortSelect['old'] ?>>Oldest</option>
		</select>
		<input type="submit" value="Go" />
	</form>
	<form action="" method="get">
		<input type="textbox" id="search" name="q" placeholder="Enter a username" <?php if(isset($_GET['q'])){ echo 'value="'.$_GET['q'].'"'; } ?> />
		<input type="submit" value="Search">
	</form>	
</div>
<p>
	Below you can find a directory of all community members.<br>
	Try using the search tool to find a specific user.
</p>
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
	$real_user = $user;
	$tca = new \App\Tokenly\TCA_Model;	
	$profileModule = $tca->get('modules', 'user-profile', array(), 'slug');	
	foreach($members as $user){
		$checkTCA = true;
		if($real_user AND $user['userId'] != $real_user['userId']){
			$checkTCA = $tca->checkItemAccess($user, $profileModule['moduleId'], $user['userId'], 'user-profile');
		}
		
		$avImage = $user['profile']['avatar'];
		if(isset($user['profile']['real_avatar']) AND trim($user['profile']['real_avatar']) == ''){
			$avImage = SITE_URL.'/files/avatars/default.jpg';
		}
		else{				
			if(!isExternalLink($user['profile']['avatar'])){
				$avImage = SITE_URL.'/files/avatars/'.$user['profile']['avatar'];
			}
		}
		
		if($checkTCA){
			$avatar = '<a href="'.SITE_URL.'/profile/user/'.$user['slug'].'"><img src="'.$avImage.'" alt="" /></a>';
		}	


		$user['sub-name'] = '';
		if(isset($user['profile']['profile']['real-name']) AND trim($user['profile']['profile']['real-name']['value']) != ''){
			$user['sub-name'] = '<p><strong>Name:</strong> '.$user['profile']['profile']['real-name']['value'].'</p>';
		}
		$bio = '';
		if(isset($user['profile']['profile']['bio']) AND trim($user['profile']['profile']['bio']['value']) != ''){
			$bio = markdown(shorten($user['profile']['profile']['bio']['value'], 300, SITE_URL.'/profile/user/'.$user['slug'], 'Read More'));
		}
		
			
		$online_icon = 'fa-circle';
		$online_title = 'Offline';
		$activeTime = strtotime($user['lastActive']);
		$diff = $time - $activeTime;
		if($diff < 7200){
			if(isset($user['profile']['custom_status'])){
				switch($user['profile']['custom_status']){
					case 'away':
						$online_icon .= ' text-pending';
						$online_title = 'Away';							
						break;	
					case 'busy':
						$online_icon .= ' text-progress';
						$online_title = 'Busy';							
						break;												
					case 'offline':
						$online_icon .= ' text-error';
						$online_title = 'Offline';							
						break;					
					default:
						$online_icon .= ' text-success';
						$online_title = 'Online';							
						break;
				}
			}
			else{
				$online_icon .= ' text-success';
				$online_title = 'Online';				
			}
		}
		else{
			$online_icon .= ' text-error';
		}
		
		$lastActive = '';
		if($user['lastActive'] != '0000-00-00 00:00:00' AND $user['lastActive'] != null){
			$lastActive = ' - Last active: '.formatDate($user['lastActive']);
		}
		
		$status = '<strong>Status:</strong> '.$online_title.' <i class="fa '.$online_icon.'" title="'.$online_title.$lastActive.'"></i>';
					
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
					<small>
						'.$this->includeView('inc/group-title', array('profile' => $user['profile'], 'primary_only' => true), false).'
						'.$status.'
					</small>
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
			echo '<a href="?page='.$i.'&q='.$query.$sort_query.'" class="'.$active.'">'.$i.'</a> ';
		}
		echo '</div>';
	}
	
	?>
	
</ul>
