<h2><i class="fa fa-envelope"></i> Private Messages</h2>
<p class="pull-right">
	<a href="#bottom" class="btn">Go to Bottom</a>
	<a href="#reply-form" class="btn">Reply</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<?php
$tca = new \App\Tokenly\TCA_Model;
$profileModule = $tca->get('modules', 'user-profile', array(), 'slug');
if(isset($error) AND trim($error) != ''){
	echo '<p class="error">'.$error.'</p>';
}
?>
<a name="top"></a>
<div class="pm-view">
	<?php
	$message_count = count($messages);
	foreach($messages as $k => $pm){
		
		$checkTCA = true;
		if($pm['from']['userId'] != $user['userId']){
			$checkTCA = $tca->checkItemAccess($user, $profileModule['moduleId'], $pm['from']['userId'], 'user-profile');
		}
		
		$displayName = $pm['from']['username'];
		if($checkTCA){
			$displayName = '<a href="'.SITE_URL.'/profile/user/'.$pm['from']['slug'].'">'.$displayName.'</a>';
		}
		
		echo '<a name="message-'.$pm['messageId'].'" class="anchor"></a>';
		if(($k + 1) == $message_count){
			echo '<a name="bottom"></a>';
		}
	?>
	<h2 class="topic-heading"><?= $pm['subject'] ?></h2>
	<div class="thread-op">
		<div class="op-author">
			<span class="post-username"><?= $displayName ?></span>
			<div class="profile-pic">
				<?php
				$avImage = $pm['from']['avatar'];
				if(trim($pm['from']['real_avatar']) == ''){
					$avImage = SITE_URL.'/files/avatars/default.jpg';
				}
				else{				
					if(!isExternalLink($pm['from']['avatar'])){
						$avImage = SITE_URL.'/files/avatars/'.$pm['from']['avatar'];
					}
				}
				$avImage = '<img src="'.$avImage.'" alt="" />';
				if($checkTCA){
					$avImage = '<a href="'.SITE_URL.'/profile/user/'.$pm['from']['slug'].'">'.$avImage.'</a>';
				}
				echo $avImage;
			

				?>
			</div>
		</div>
		<div class="op-content">
			<div class="post-content" data-message="<?= base64_encode($pm['message']) ?>">
				<?= markdown($pm['message']) ?>
			</div>		
			<div class="clear"></div>
			<span class="post-date">Sent on <?= formatDate($pm['sendDate']) ?></span>
			<div class="clear"></div>
			<div class="post-controls">
				<span class="post-action" style="float: right;">
					<a href="#reply-form" class="quote-message">Quote</a>
				</span>
				<div class="clear"></div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<?php
	}//endforeach
	?>
	<a name="reply-form"></a>
	<p class="pull-right">
		<a href="#top" class="btn">Go to Top</a>
	</p>
	<h3>Reply</h3>
	<?= $form->display() ?>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('.quote-message').click(function(e){
			var message = $(this).parent().parent().parent().parent().find('.post-content').data('message');
			message = Base64.decode(message);
			var newMessage = '';
			var splitMessage = message.split("\n");
			$.each(splitMessage, function(k, v){
				newMessage = newMessage + '> ' + v + "\n";
			});
			
			var curVal = $('#markdown').val();
			if(curVal.trim() != ''){
				newMessage = curVal + "\n\n" +  newMessage + "\n\n";
			}
			else{
				newMessage = newMessage + "\n\n";
			}
			
			$('#markdown').val(newMessage);
		});		
	});
</script>

