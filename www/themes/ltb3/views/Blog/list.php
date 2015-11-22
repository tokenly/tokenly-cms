<div class="blog-listings">
<div style="margin-bottom: 20px;">
	<?= $this->displayTag('DISPLAY_ADSPACE', array('slug' => 'ltb-blog-top-ad')) ?>
</div>
<h1><?= $title ?></h1>
<?php
if(isset($category)){
	if(trim($category['description']) != ''){
		echo '<div class="blog-description">'.$category['description'].'</div>';
	}
}
$tca = new \App\Tokenly\TCA_Model;
$profileModule = $tca->get('modules', 'user-profile', array(), 'slug');
$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
$catModule = $tca->get('modules', 'blog-category', array(), 'slug');

$listViewStyle = 'display: none;';
if(!isset($_COOKIE['blog-list-type']) OR $_COOKIE['blog-list-type'] == 'list'){
	$listViewStyle = '';
}
?>
<div class="clear"></div>
<ul class="blog-list list" style="<?= $listViewStyle ?>">
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
		$avImage = $post['author']['avatar'];
		if(!isExternalLink($post['author']['avatar'])){
			$avImage = SITE_URL.'/files/avatars/'.$post['author']['avatar'];
		}
		$avImage = '<img src="'.$avImage.'" alt="" />';
		$avImage = '<span class="mini-avatar inline">'.$avImage.'</span>';			
		$displayName = '<a href="'.SITE_URL.'/profile/user/'.$post['author']['slug'].'">'.$avImage.' '.$displayName.'</a>';
	}
	
	if($post['formatType'] == 'markdown'){
		$post['excerpt'] = markdown($post['excerpt']);
		$post['content'] = markdown($post['content']);
	}
?>
	<li>
		<div class="blog-extra">
			<?php
			if(trim($post['image']) != '' AND file_exists($imagePath.'/'.$post['image'])){
				echo '<div class="blog-image"><a href="'.$post['url'].'"><img src="'.SITE_URL.'/files/blogs/'.$post['image'].'" alt="" /></a></div>';
			}
			elseif(trim($post['coverImage']) != '' AND file_exists($imagePath.'/'.$post['coverImage'])){
				echo '<div class="blog-image"><a href="'.$post['url'].'"><img src="'.SITE_URL.'/files/blogs/'.$post['coverImage'].'" alt="" /></a></div>';
			}
			?>
			<?php
			if(isset($post['tip-address']) OR (isset($post['author']['profile']['bitcoin-address']) AND trim($post['author']['profile']['bitcoin-address']['value']) != '')){
				if(isset($post['tip-address'])){
					$btcAddress = $post['tip-address'];
				}
				else{
					$btcAddress = $post['author']['profile']['bitcoin-address']['value'];
				}
			?>
			<div class="blog-tipping">
				<a href="#<?= $post['postId'] ?>-btc-tip" class="fancy" target="_blank"><i class="fa fa-btc" title="Tip this post"></i></a>	
				<span class="companion-tip-button pockets-payment-button" data-color="icon" data-address="<?= $btcAddress ?>" data-label="<?= $post['author']['username'] ?> <br> (<?= $post['title'] ?>)" data-tokens="all"></span>				
				<div id="<?= $post['postId'] ?>-btc-tip" style="display: none;">
					<div class="text-center">
						<h3><a href="bitcoin:<?= $btcAddress ?>">Tip <?= $post['author']['username'] ?></a></h3>
						<h4><?= $btcAddress ?></h4>
						<h5><a href="https://blockscan.com/address/<?= $btcAddress ?>" target="_blank">(blockscan)</a></h5>
						<p>
							<img src="<?= SITE_URL ?>/qr.php?q=<?= $btcAddress ?>" alt="" />
						</p>
					</div>
				</div>
			</div>
			<?php
			}//endif
			?>		
			<div class="blog-social">
				<?php
				$shareURL = SITE_URL.'/'.$app['url'].'/post/'.$post['url'];
				?>
				<a href="http://www.reddit.com/submit?url=<?= $shareURL ?>" target="_blank"><img src="<?= THEME_URL ?>/images/reddit-black.png" title="Post on Reddit" /></a>
				<a href="http://www.facebook.com/sharer.php?u=<?= $shareURL ?>" target="_blank"><img src="<?= THEME_URL ?>/images/facebook-black.png" title="Share this Post" /></a>
				<a href="http://twitter.com/share?text=<?= urlencode($post['title']) ?>&url=<?= $shareURL ?>" target="_blank"><img src="<?= THEME_URL ?>/images/twitter-black.png" title="Tweet this Post" /></a>
				<a href="https://plus.google.com/share?url=<?= $shareURL ?>" target="_blank"><img src="<?= THEME_URL ?>/images/gplus-black.png" title="+1 this Post" /></a>
			</div>
			<?php
			if($commentsEnabled == 1){
				$post['commentCount'] = intval($post['commentCount']);
			?>
			<div class="clear"></div>
			<div class="blog-commentCount">
				<a href="<?= $post['url'] ?>#disqus_thread"><i class="fa fa-comments"></i> <?= $post['commentCount'] ?> <?= pluralize('Comment', $post['commentCount'], true) ?></a><br>
				<span><i class="fa fa-eye"></i> <?= number_format($post['views']) ?> views</span>
			</div>
			<?php
			}//endif
			?>			
			<div class="blog-cats">
				<?php
				if(count($post['categories']) > 0){
					echo 'Categories: ';
					echo '<span>';
					$catList = array();
					foreach($post['categories'] as $cat){
						$catList[] =  '<a href="'.SITE_URL.'/'.$app['url'].'/category/'.$cat['slug'].'">'.$cat['name'].'</a>';
					}
					echo join(', ', $catList);
					echo '</span>';
				}
				?>
			</div>
		</div>		
		<div class="blog-list-info">
			<h2 class="post-title"><a href="<?= $post['url'] ?>"><?= $post['title'] ?></a></h2>
			<div class="blog-date">
				<?= date('F jS, Y', strtotime($post['publishDate'])) ?> by 
				<?= $displayName ?>
			</div>
			<div class="blog-excerpt">
				<?php
				/*if(isset($post['soundcloud-id'])){
					echo '<iframe src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F'.$post['soundcloud-id'].'&auto_play=false&show_artwork=true&color=ff7700" width="400" height="100"></iframe>
					<br><br>';
				}*/
				?>
				<?php
				if(isset($category) AND $category['slug'] == 'uncoinventional-living'){
					echo $post['content'];
				}
				else{
					echo $post['excerpt'];
				}
				?>
				<a href="<?= $post['url'] ?>" class="blog-more">Read More <i class="fa fa-chevron-right"></i></a>
			</div>
		</div><!-- blog-list-info -->
		<div class="clear"></div>
	</li>
<?php
}//endforeach
?>
</ul>
<!-- grid view -->
<?php
	$gridViewStyle = 'display: none;';
	if(isset($_COOKIE['blog-list-type']) AND $_COOKIE['blog-list-type'] == 'grid'){
		$gridViewStyle = '';
	}
	echo '<ul class="blog-list grid" style="'.$gridViewStyle.'">';
	if(count($posts) > 0){
		foreach($posts as $post){
			$postTCA = $tca->checkItemAccess($user, $postModule['moduleId'], $post['postId'], 'blog-post');
			if(!$postTCA){
				continue;
			}
			
			foreach($post['categories'] as $cat){
				$catTCA = $tca->checkItemAccess($user, $catModule['moduleId'], $cat['categoryId'], 'blog-category');
				if(!$catTCA){
					continue 2;
				}
			}

			$class = '';
			if(isset($post['soundcloud-url'])){
				$class .= ' show';
			}
			$avatar = '';
			$author = $post['author'];
			
			$authorTCA = $tca->checkItemAccess($user, $profileModule['moduleId'], $author['userId'], 'user-profile');
			$avImage = $author['avatar'];
			if(!isExternalLink($author['avatar'])){
				$avImage = SITE_URL.'/files/avatars/'.$author['avatar'];
			}
			$avImage = '<img src="'.$avImage.'" alt="" />';
			if($authorTCA){
				$avImage = '<a href="'.$data['site']['url'].'/profile/user/'.$author['slug'].'">'.$avImage.'</a>';
			}
			$avatar = '<span class="circle-avatar">'.$avImage.'</span>';
	
			?>
			<li>
				<a href="<?= SITE_URL ?>/blog/post/<?= $post['url'] ?>" class="blog-link" title="<?= str_replace('"', '\'', $post['title']) ?>"></a>
				<div class="blog-image">
					<?php
					if(trim($post['coverImage']) != ''){
					?>
					<img src="<?= SITE_URL ?>/files/blogs/<?= $post['coverImage'] ?>" alt="" />
					<?php
					}//endif
					?>
				</div><!-- blog-image -->
				<div class="blog-date">
					<span><?= date('F d, Y', strtotime($post['publishDate'])) ?></span>
				</div><!-- blog-date -->
				<div class="blog-title">
					<span><?= shortenMsg($post['title'], 105) ?></span>
				</div>
			</li>			
			<?php	
		}
	}
	echo '</ul>
	<div class="clear"></div><br>';
	?>			
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
</script><br>
<!-- <div class="ad large-banner center"></div> -->
</div><!-- blog-listings -->
