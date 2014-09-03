<h2>Private Messages</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<?php
$tca = new Slick_App_LTBcoin_TCA_Model;
$profileModule = $tca->get('modules', 'user-profile', array(), 'slug');
if(isset($error) AND trim($error) != ''){
	echo '<p class="error">'.$error.'</p>';
}
?>
<div class="pm-view">
	<?php
	foreach($messages as $pm){
		
		$checkTCA = true;
		if($pm['from']['userId'] != $user['userId']){
			$checkTCA = $tca->checkItemAccess($user, $profileModule['moduleId'], $pm['from']['userId'], 'user-profile');
		}
		
		$displayName = $pm['from']['username'];
		if($checkTCA){
			$displayName = '<a href="'.SITE_URL.'/profile/user/'.$pm['from']['slug'].'">'.$displayName.'</a>';
		}
		
		echo '<a name="message-'.$pm['messageId'].'" class="anchor"></a>';
	?>
	<h2 class="topic-heading"><?= $pm['subject'] ?></h2>
	<div class="thread-op">
		<div class="op-author">
			<span class="post-username"><?= $displayName ?></span>
			<div class="profile-pic">
				<?php
				$avImage = $pm['from']['avatar'];
				if(!isExternalLink($pm['from']['avatar'])){
					$avImage = SITE_URL.'/files/avatars/'.$pm['from']['avatar'];
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
			<div class="post-controls">
				<span class="post-action" style="float: right;">
					<a href="#reply-form" class="quote-message">Quote</a>
				</span>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	<div class="clear"></div>
	<?php
	}//endforeach
	?>
	<a name="reply-form"></a>
	<h3>Reply</h3>
	<?= $form->display() ?>
	<div class="markdown-preview">
		<h4>Live Preview</h4>
		<div class="markdown-preview-cont">
		</div>
	</div>
</div>
<script type="text/javascript" src="<?= THEME_URL ?>/js/Markdown.Converter.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#markdown').on('input', function(e){
			var thisVal = $(this).val();
			var converter = new Markdown.Converter();
			
			getMarkdown = converter.makeHtml(thisVal);
			$('.markdown-preview-cont').html(getMarkdown);
		});
		
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

