<h2>Stats</h2>
<hr>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<h4>General</h4>
<ul class="system-stats">
	<li><strong># Users Online:</strong> <?= count(\App\Account\Home_Model::getOnlineUsers()) ?>
	<li><strong>Most Ever Online:</strong> <?= \App\Account\Home_Model::getMostOnline() ?>
	<li><strong># Users:</strong> <?= $stats['numUsers'] ?></li>
	<li><strong># Groups:</strong> <?= $stats['numGroups'] ?></li>
	<li><strong># Sites:</strong> <?= $stats['numSites'] ?></li>
</ul>

<br>
<h4>Blog</h4>
<ul class="system-stats">
	<li><strong># Blog Categories:</strong> <?= $stats['numBlogCats'] ?></li>
	<li><strong># Blog Posts:</strong> <?= $stats['numBlogPosts'] ?></li>
	<!--<li><strong># Blog Comments:</strong> <?= $stats['numBlogComments'] ?></li>-->
</ul>

<br>
<h4>Forums</h4>
<ul class="system-stats">
	<li><strong># Forum Categories:</strong> <?= $stats['numForumCats'] ?></li>
	<li><strong># Boards:</strong> <?= $stats['numForumBoards'] ?></li>
	<li><strong># Topics:</strong> <?= $stats['numForumTopics'] ?></li>
	<li><strong># Replies:</strong> <?= $stats['numForumPosts'] ?></li>
	<li><strong>Total Forum Posts:</strong> <?= $stats['numForumPosts'] + $stats['numForumTopics'] ?></li>
</ul>
<h4>Network & LTBcoin</h4>
<ul class="system-stats">
	<li><strong># Users w/ LTBcoin Addresses:</strong> <?= $stats['numLTBcoinUsers'] ?> <em>(<a href="#ltbcoinusers" class="fancylist">Click to view list</a>)</em></li>
	<li><strong># New or changed LTBcoin Addresses today:</strong> <?= $stats['numLTBcoinUsersToday'] ?></li>
</ul>
<div id="ltbcoinusers" style="display: none;">
	<ul>
		<?php
		foreach($stats['LTBcoinUsers'] as $ltbuser){
			echo  '<li>'.$ltbuser['username'].' - '.$ltbuser['value'].'</li>';
		}
		?>
	</ul>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('.fancylist').fancybox();
		
	});
</script>
