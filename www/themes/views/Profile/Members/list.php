
<?php
if(count($members) == 0){
	echo '<p><strong>No members found</strong></p>';
}
?>
<ul class="member-list">
	<?php
	foreach($members as $user){
		
		$avImage = $user['profile']['avatar'];
		if(!isExternalLink($user['profile']['avatar'])){
			$avImage = SITE_URL.'/files/avatars/'.$user['profile']['avatar'];
		}
	
	
		$avatar = '<a href="'.SITE_URL.'/profile/user/'.$user['slug'].'"><img src="'.$avImage.'" alt="" /></a>';
		

		if(isset($user['profile']['profile']['real-name']) AND trim($user['profile']['profile']['real-name']['value']) != ''){
			$user['username'] = $user['profile']['profile']['real-name']['value'];
		}
		$bio = '';
		if(isset($user['profile']['profile']['bio']) AND trim($user['profile']['profile']['bio']['value']) != ''){
			$bio = markdown(shorten($user['profile']['profile']['bio']['value'], 300, SITE_URL.'/profile/user/'.$user['slug'], 'Read More'));
		}
		echo '<li>
				<div class="member-avatar">
					'.$avatar.'
				</div>
				<div class="member-info">
					<h4><a href="'.SITE_URL.'/profile/user/'.$user['slug'].'">'.$user['username'].'</a></h4>
					<div class="member-bio">
					'.$bio.'
					</div>
				</div>
				<div class="clear"></div>';
		
		echo '</li>';
	}
	
	if($numPages > 1){
		echo '<div class="member-paging blog-paging">
				Pages: ';
		for($i = 1; $i <= $numPages; $i++){
			$active = '';
			if((isset($_GET['page']) AND $_GET['page'] == $i) OR (!isset($_GET['page']) AND $i == 1)){
				$active = 'active';
			}
			echo '<a href="?page='.$i.'" class="'.$active.'">'.$i.'</a> ';
		}
		echo '</div>';
	}
	
	?>
	
</ul>
