<h1><?= $profile['username'] ?></h1>
<div class="profile-cont">
	<?php
	$avImage = $profile['avatar'];
	if(!isExternalLink($profile['avatar'])){
		$avImage = SITE_URL.'/files/avatars/'.$profile['avatar'];
	}


	$avatar = '<img src="'.$avImage.'" alt="'.$profile['username'].'" />';	
	//if(isset($profile['avatar']) AND trim($profile['avatar']) != ''){
		?>
		<div class="profile-pic">
			<?= $avatar ?>
			<?php
			if($user AND $user['userId'] != $profile['userId']){
				echo '<br><a href="'.SITE_URL.'/account/messages/send?user='.$profile['slug'].'" target="_blank" class="send-msg-btn" title="Send private message">Message</a>';
			}							
			?>
		</div>
		<?php
	//}
	?>
	<div class="profile-content">
		<ul class="profile-info">
			<li><strong>Date Registered:</strong> <?= formatDate($profile['regDate']) ?></li>
			<?php
			if(trim($profile['email']) != '' AND intval($profile['showEmail']) === 1 AND intval($profile['pubProf']) === 1){
			?>
				<li><strong>Email:</strong> <a href="mailto:<?= $profile['email'] ?>"><?= $profile['email'] ?></a></li>
			<?php
			}//endif
			if(intval($profile['pubProf']) === 1){
				$model = new Slick_Core_Model;
				foreach($profile['profile'] as $field){
					if($field['fieldId'] == PRIMARY_TOKEN_FIELD){
						$getAddress = $model->getAll('coin_addresses', array('userId' => $profile['userId'], 'address' => $field['value']));
						if($getAddress AND count($getAddress) > 0){
							$getAddress = $getAddress[0];
							if($getAddress['public'] == 0){
								continue;
							}
						}
					}
					if($field['type'] == 'textarea'){
						echo '<li class="profile-area"><strong>'.$field['label'].':</strong><br/>
														'.markdown($field['value']).'</li>';
						
					}
					else{
						echo '<li><strong>'.$field['label'].':</strong> '.autolink($field['value']).'</li>';
					}
					
				}
			}
			?>
		</ul>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>
