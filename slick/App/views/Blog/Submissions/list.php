<?php
if($perms['canWritePost']){
	$creditDisable = '';
	if($num_credits <= 0){
		$creditDisable = 'disabled';
		$num_credits = 0;
	}
?>
<div class="pull-right blog-submit-actions">
	<?php
	if($perms['canBypassSubmitFee']){
		?>
		<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add" class="btn btn-large new-article-btn">New Submission</a>
		<?php
	}
	else{
	?>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add" class="btn btn-large <?= $creditDisable ?> new-article-btn">New Submission (<?= $num_credits ?>)</a>
	<a href="<?= SITE_URL ?>/dashboard/account/credits" class="btn btn-large">Purchase Credits</a>
	<?php
	}//endif
	?>
</div>
<?php
}//endif
?>
<h2>My Article Submissions</h2>
<?=  $this->displayFlash('blog-message') ?>
<?=  $this->displayFlash('message') ?>
<div class="clear"></div>
<?= $this->displayBlock('dashboard-blog-submissions') ?>
<hr>
<div class="clear"></div>
<?php
if($trashMode == 0){
?>
<div class="newsroom-stats-cont">
<ul class="ltb-pop-stats">
	<li><strong>Posts Submitted:</strong> <?= number_format($totalPosts) ?></li>
	<li><strong>Posts Published:</strong> <?= number_format($totalPublished) ?></li>
	<li><strong>Posts Contributed To:</strong> <?= number_format($totalContributed) ?></li>
	<li><strong>Total Views:</strong> <?= number_format($totalViews) ?></li>
	<li><strong>Total Comments:</strong> <?= number_format($totalComments) ?></li>
</ul>
<div class="clear"></div>
</div>
<br>
<?php
	echo '<p class="blog-trash-link"><a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/trash">View Trash ('.$trashCount.')</a></p>';
}else{
	if($perms['canDeleteSelfPost']){
		echo '<p class="pull-right"><a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/clear-trash" class="delete btn btn-large">Clear Trash</a></p>';
	}
	echo '<h3>Trash Bin</h3>';
	echo  $this->displayFlash('blog-message');
	echo '<p class="blog-trash-link"><a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'">Back to Submissions</a></p>';
}
?>
<?php
if(count($postList) == 0){
	echo '<div class="clear"></div><br><p>No posts found</p>';
}
else{
	foreach($postList as $key => $val){
		if($val['published'] == 1){
			$postList[$key]['status'] = '<span class="text-success">Published</span>';
			$postList[$key]['postDate'] = $val['publishDate'];
		}
		elseif($val['status'] == 'ready'){
			$postList[$key]['status'] = '<span class="text-pending">Ready for Review</span>';
		}
		elseif($val['published'] == 0 AND $val['status'] == 'published'){
			$postList[$key]['status'] = '<span class="text-progress">Pending Approval</span>';
		}
		else{
			$postList[$key]['status'] = '<span class="text-default">Draft</span>';
		}
	}

	echo '<table class="admin-table mobile-table data-table submissions-table">
			<thead>
				<tr>
					<th>Title</th>
					<th>Status</th>
					<th>Views</th>
					<th>Comments</th>
					<th>Publish Date</th>
					<th class="no-sort"></th>
				</tr>
			</thead>
			<tbody>';
	foreach($postList as $post){
		$titleLink = $post['title'];
		$actionLinks = '';
		if($trashMode == 0){
			$editLink = '';
			$deleteLink = '';
			$viewLink = '';

			$editLink = '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'.$post['postId'].'" class="">Edit</a>';
			$titleLink = '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'.$post['postId'].'" class="">'.$post['title'].'</a>';
		
			
			$commentIcon = '';
			if($post['new_comments']){
				$commentIcon = '<i class="fa fa-comment text-success" title="New Editorial Comments"></i> ';
			}
			if($post['userId'] != $user['userId']){
				$commentIcon .= ' <i class="fa fa-user text-pending" title="Contributing"></i> ';
			}
			$titleLink = $commentIcon.$titleLink;
		
			
			$titleLink = $titleLink.'<br><small>Author: <a href="'.SITE_URL.'/profile/user/'.$post['author']['slug'].'" target="_blank">'.$post['author']['username'].'</a></small>';
			
			if($user['userId'] == $post['userId']){
				$deleteLink = '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/trash/'.$post['postId'].'" class="">Move to Trash</a>';
			}
			
			if($post['published'] == 1){
				$viewLink = '<a href="'.SITE_URL.'/'.$blogApp['url'].'/'.$postModule['url'].'/'.$post['url'].'" class="" target="_blank">View Post</a>';
			}
			else{
				$viewLink = '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/preview/'.$post['postId'].'" class="" target="_blank">View Draft</a>';
			}
			$actionLinks = $viewLink.' '.$editLink.' '.$deleteLink;
		}
		else{
			$restoreLink = '';
			$deleteLink = '';
			if(($user['userId'] == $post['userId'] AND $post['perms']['canDeleteSelfPost'])
				OR ($user['userId'] != $post['userId'] AND $post['perms']['canDeleteOtherPost'])){
				if($post['published'] == 0 OR ($post['published'] == 1 AND $post['perms']['canPublishPost'])){
					$restoreLink = '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/restore/'.$post['postId'].'">Restore</a>';
					$deleteLink = '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/'.$post['postId'].'" class="delete ">Delete</a>';
				}
			}			
			
			$actionLinks = $restoreLink.' '.$deleteLink;
		}
		
		echo '<tr>';
		echo '<td class="post-title">'.$titleLink.'</td>
			  <td>'.$post['status'].'</td>
			  <td>'.number_format($post['views']).'</td>
			  <td>'.number_format($post['commentCount']).'</td>
			  <td>'.date('Y/m/d \<\b\r\> H:i', strtotime($post['publishDate'])).'</td>
			  <td class="table-actions">
				'.$actionLinks.'
			  </td>';
		echo '</tr>';
		
	}
	echo '</tbody></table>';

}
?>
<link href="//cdn.datatables.net/1.10.3/css/jquery.dataTables.css" rel="stylesheet" />
<script type="text/javascript" src="//cdn.datatables.net/1.10.3/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('.data-table').DataTable({
			searching: true,
			lengthChange: false,
			paging: true,
			iDisplayLength: 20,
			"order": [[ 4, "desc" ]]
		});
		
		<?php
		if(!$perms['canBypassSubmitFee']){
		?>
		window.credit_watch = false;
		$('.purchase-credits').click(function(e){
			e.preventDefault();
			if($(this).hasClass('collapse')){
				$('.purchase-credits-cont').slideUp();
				$(this).removeClass('collapse');
			}
			else{
				$('.purchase-credits-cont').slideDown();
				$(this).addClass('collapse');
				if(!window.credit_watch){
					window.credit_watch = setInterval(function(){
						var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/check-credits';
						$.get(url, function(data){
							console.log(data);
							if(data.error != null){
								console.log('Error: ' + data.error);
								return false;
							}
							if(data.result != 'none'){
								successMsg = data.received + ' LTBcoin received';
								if(data.old_change > 0){
									successMsg = successMsg + ' (+' + data.old_change + ' previous change)';
								}
								successMsg = successMsg + '! You have purchased ' + data.new_credits + ' submission credits.';
								if(data.new_change > 0){
									successMsg = successMsg + ' You have ' + data.new_change + ' LTBcoin leftover to go towards your next submission credits purchase';
								}
								$('.purchase-credits-cont').find('.payment-status').addClass('text-success').html(successMsg);
								$('.new-article-btn').removeClass('disabled').html('New Submission (' + data.credits + ')');
							}
						});
						
					}, 10000);					
				}
			}
		});
		<?php
		}//endif
		?>
		

	});
</script>
