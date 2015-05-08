<span class="rss-link"><a href="<?= SITE_URL ?>/rss"><img src="<?= THEME_URL ?>/images/rss.png" alt="RSS Feed" /></a></span>
<?php
if(isset($category) AND $category['slug'] != 'uncoinventional-living'){
	if($category['image'] != '' AND file_exists(SITE_PATH.'/files/blogs/'.$category['image'])){
		echo '<div class="blog-category-image"><img src="'.SITE_URL.'/files/blogs/'.$category['image'].'" alt="" /></div>';
	}
}
?>

<h1><?= $title ?></h1>
<?php
if(isset($category)){
	if(trim($category['description']) != ''){
		echo '<div class="blog-description">'.$category['description'].'</div>';
	}
	if($category['slug'] == 'uncoinventional-living'){
		?>
		<hr>
		<ul class="ltb-stat-tabs" data-tab-type="uncoin-blog-cont">
				<li><strong><a href="#" class="tab active" data-tab="uncoin-blog">Blog</a></strong></li>
				<li><strong><a href="#" class="tab" data-tab="uncoin-live">Live Updates</a></strong></li>
		</ul>
		<div class="uncoin-blog-cont">
			<div class="ltb-data-tab" id="uncoin-blog">
		<?php
	}
}
$tca = new \App\Tokenly\TCA_Model;
$profileModule = $tca->get('modules', 'user-profile', array(), 'slug');
$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
?>
<div class="clear"></div>
<ul class="blog-list">
<?php
$extraUrl = '';
if($module){
	$extraUrl = '/'.$module['url'];
}

$settings = new \App\CMS\Settings_Model;
$maxChars = $settings->getSetting('blog-excerptChars');
if(!$maxChars){
	$maxChars = 250;
}

if(count($posts) == 0){
	echo '<p>Sorry, no posts found</p>';
}
$imagePath = SITE_PATH.'/files/blogs';
foreach($posts as $post){
	$postTCA = $tca->checkItemAccess($user, $postModule['moduleId'], $post['postId'], 'blog-post');
	foreach($post['categories'] as $cat){
		$catTCA = $tca->checkItemAccess($user, $catModule['moduleId'], $cat['categoryId'], 'blog-category');
		if(!$catTCA){
			continue 2;
		}
	}
	if(!$postTCA){
		continue;
	}

	$getIndex = $settings->getAll('page_index', array('itemId' => $post['postId'], 'moduleId' => 28, 'siteId' => $site['siteId']));

	if($getIndex AND count($getIndex) > 0){
		$post['url'] = SITE_URL.'/'.$getIndex[count($getIndex) - 1]['url'];

	}
	else{
		$post['url'] = SITE_URL.'/'.$app['url'].'/post/'.$post['url'];
	}
	
	$authorTCA = $tca->checkItemAccess($user, $profileModule['moduleId'], $post['author']['userId'], 'user-profile');
	
	$displayName = $post['author']['username'];
	if(isset($post['author']['profile']['real-name']) AND trim($post['author']['profile']['real-name']['value']) != ''){
		$displayName =  $post['author']['profile']['real-name']['value'];
	}
	if($authorTCA){
		$displayName = '<a href="'.SITE_URL.'/profile/user/'.$post['author']['slug'].'">'.$displayName.'</a>';
	}
	
	if($post['formatType'] == 'markdown'){
		$post['excerpt'] = markdown($post['excerpt']);
		$post['content'] = markdown($post['content']);
	}
?>
	<li>
		<?php

		if(trim($post['image']) != '' AND file_exists($imagePath.'/'.$post['image'])){
			echo '<div class="blog-image"><img src="'.SITE_URL.'/files/blogs/'.$post['image'].'" alt="" /></div>';
		}
		elseif(trim($post['coverImage']) != '' AND file_exists($imagePath.'/'.$post['coverImage'])){
			echo '<div class="blog-image"><img src="'.SITE_URL.'/files/blogs/'.$post['coverImage'].'" alt="" /></div>';
		}
		

		?>
		<h2><a href="<?= $post['url'] ?>"><?= $post['title'] ?></a></h2>
		<div class="blog-date">
			Published on <?= date('F jS, Y', strtotime($post['publishDate'])) ?> by 
			<?= $displayName ?>
		</div>
		<div class="blog-excerpt">
			<?php
			if(isset($post['soundcloud-id'])){
				echo '<iframe src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F'.$post['soundcloud-id'].'&auto_play=false&show_artwork=true&color=ff7700" width="400" height="100"></iframe>
				<br><br>';
			}
			?>
			<?php
			if(isset($category) AND $category['slug'] == 'uncoinventional-living'){
				echo $post['content'];
			}
			else{
				echo $post['excerpt'];
			}
			?>
			<a href="<?= $post['url'] ?>" class="blog-more">Read More</a>
		</div>
		<div class="blog-extra">
			<div class="blog-social">
				<?php
				$shareURL = SITE_URL.'/'.$app['url'].'/post/'.$post['url'];
				?>
				<a href="http://www.reddit.com/submit?url=<?= $shareURL ?>" target="_blank"><img src="<?= THEME_URL ?>/images/reddit.png" alt="Post on Reddit" /></a>
				<a href="http://www.facebook.com/sharer.php?u=<?= $shareURL ?>" target="_blank"><img src="<?= THEME_URL ?>/images/facebook.png" alt="Share this Post" /></a>
				<a href="http://twitter.com/share?text=<?= urlencode($post['title']) ?>&url=<?= $shareURL ?>" target="_blank"><img src="<?= THEME_URL ?>/images/twitter.png" alt="Tweet this Post" /></a>
				<a href="https://plus.google.com/share?url=<?= $shareURL ?>" target="_blank"><img src="<?= THEME_URL ?>/images/gplus.png" alt="+1 this Post" /></a>
			</div>
			<?php
			if(isset($post['tip-address']) OR (isset($post['author']['profile']['bitcoin-address']) AND trim($post['author']['profile']['bitcoin-address']['value']) != '')){
				if(isset($post['tip-address'])){
					$btcAddress = $post['tip-address'];
				}
				else{
					$btcAddress = $post['author']['profile']['bitcoin-address']['value'];
				}
			?>
			Tip This Post: <a href="https://blockchain.info/address/<?= $btcAddress ?>" target="_blank"><?= $btcAddress ?></a><br>
			
			<?php
			}//endif
			?>
			<div class="blog-cats">
				<?php
				if(count($post['categories']) > 0){
					echo 'Categories: ';
					$catList = array();
					foreach($post['categories'] as $cat){
						$catList[] =  '<a href="'.SITE_URL.'/'.$app['url'].'/category/'.$cat['slug'].'">'.$cat['name'].'</a>';
					}
					echo join(', ', $catList);
				}
				?>
			</div>
			<?php
			if($commentsEnabled == 1){
			?>
			<div class="blog-commentCount">
				<a href="<?= $post['url'] ?>#disqus_thread"><!--<?= $post['commentCount'] ?> <?= pluralize('Comment', $post['commentCount'], true) ?>--></a>
			</div>
			<?php
			}//endif
			?>
		</div>
		<div class="clear"></div>
	</li>
<?php
}//endforeach
?>
</ul>
<?php
if($numPages > 1){
?>
<div class="blog-paging">
Pages:
<?php

for($i = 1; $i <= $numPages; $i++){
	$active = '';
	if((isset($_GET['page']) AND $_GET['page'] == $i) OR (!isset($_GET['page']) AND $i == 1)){
		$active = 'active';
	}
	echo '<a href="?page='.$i.'" class="'.$active.'">'.$i.'</a> ';
}

?>
</div>
<?php
}//endif
if(isset($category) AND $category['slug'] == 'uncoinventional-living'){
	?>
		</div>
		<div class="ltb-data-tab" id="uncoin-live" style="display: none;">
			<div class="clear"></div>
			<iframe src="http://uncoinventional.com/2014/11/west-coast-tour-live-blog/" width="100%" height="600"></iframe>
		</div>
	</div>
		<script type="text/javascript">
			$(document).ready(function(){
				$('.ltb-stat-tabs').find('.tab').click(function(e){
					e.preventDefault();
					var tab = $(this).data('tab');
					var type = $(this).parent().parent().parent().data('tab-type');
					$('.ltb-data-tab').hide();
					$('.ltb-data-tab#' + tab).show();
					$(this).parent().parent().parent().find('.tab').removeClass('active');
					$(this).addClass('active');
				});
				
			});
		</script>	
	<?php
}
?>
<script type="text/javascript">
var disqus_shortname = '<?= DISQUS_DEFAULT_FORUM ?>'; // required: replace example with your forum shortname

/* * * DON'T EDIT BELOW THIS LINE * * */
(function () {
var s = document.createElement('script'); s.async = true;
s.type = 'text/javascript';
s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
(document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
}());
</script>
