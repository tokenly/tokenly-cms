<?php
if($post['formatType'] == 'markdown'){
	$post['content'] = markdown($post['content']);
	$post['excerpt'] = markdown($post['excerpt']);
}
$tca = new \App\Tokenly\TCA_Model;
$profileModule = $tca->get('modules', 'user-profile', array(), 'slug');
$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
$authorTCA = $tca->checkItemAccess($user, $profileModule['moduleId'], $post['author']['userId'], 'user-profile');
$imagePath = SITE_PATH.'/files/blogs';
?>
<div class="blog-listings">
	<div style="margin-bottom: 20px;">
		<?= $this->displayTag('DISPLAY_ADSPACE', array('slug' => 'ltb-blog-top-ad')) ?>
	</div>
	<h1><?= $blog['name'] ?></h1>
	<hr>
</div>
<div class="blog-post">
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
			<div class="clear"></div>
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


	<h1 class="blog-post-title"><a href="<?= SITE_URL ?>/blog/post/<?= $post['url'] ?>"><?= $post['title'] ?></a></h1>
<?php
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
		$displayName = '<a href="'.SITE_URL.'/profile/user/'.$post['author']['slug'].'" target="_blank">'.$avImage.' '.$displayName.'</a>';
	}

?>
		<div class="blog-date">
			Published on <?= date('F jS, Y', strtotime($post['publishDate'])) ?> by
			<?= $displayName ?>
		</div>
		<div class="blog-content">
			<?php
			if(isset($post['soundcloud-id']) AND trim($post['soundcloud-id']) != ''){
				echo '<iframe src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F'.$post['soundcloud-id'].'&auto_play=false&show_artwork=true&color=ff7700" width="400" height="100"></iframe>
				<br><br>';
			}
			if(isset($post['audio-url']) AND trim($post['audio-url']) != ''){
				echo '<p><a href="'.$post['audio-url'].'" target="_blank">Click to download audio version</a></p>';
			}
			?>
			<?= $post['content'] ?>
			<?php
			if(isset($post['shapeshift_address'])){
				?>
				<script>function shapeshift_click(a,e){e.preventDefault();var link=a.href;window.open(link,'1418115287605','width=700,height=700,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=0,left=0,top=0');return false;}</script>
				<a href="https://shapeshift.io/shifty.html?destination=<?= $post['shapeshift_address'] ?>&apiKey=&amount=" onclick="shapeshift_click(this, event);"><img class="ss-button" src="<?= THEME_URL ?>/images/shapeshift-tip.png"></a>
				<?php
			}		
			?>
			<p>
				<strong>Views:</strong> <?= number_format($post['views']) ?>
			</p>
		</div>
		
		<?php
		if(!isset($disableComments) OR !$disableComments){
		?>
		<hr>
		<div class="blog-comments-cont">
			<a name="comments"></a>
			<h3>Comments</h3>
			<p>
				Make sure to make use of the  "downvote" button for any spammy posts, and the "upvote" feature for interesting conversation. Be excellent.
			</p>
			<div id="disqus_thread"></div>
    <?php
    
    if($user){
		$profModel = new \App\Profile\User_Model;
		$userProf = $profModel->getUserProfile($user['userId'], $site['siteId']);
		$disqusUser = array('id' => $user['userId'], 'username' => $user['username'],
							'email' => $user['email'], 'avatar' => SITE_URL.'/files/avatars/'.$userProf['avatar'],
							'url' => SITE_URL.'/profile/user/'.$user['slug']);
		$disqusMessage = base64_encode(json_encode($disqusUser));
		$time = time();
		$disqusSig = hash_hmac('sha1', $disqusMessage.' '.$time, DISQUS_SECRET);
		$disqus_hmac = $disqusMessage.' '.$disqusSig.' '.$time;
	}
	
	$postURL = SITE_URL.$_SERVER['REQUEST_URI'];
	if(strtotime($post['publishDate']) < 1420872893){
		$postURL = str_replace('https://', 'http://', $postURL);
	}
    
    ?>
    <script type="text/javascript">
		var disqus_url = '<?= $postURL ?>';
		var disqus_config = function () {
			// The generated payload which authenticates users with Disqus
			<?php
			if($user){
			?>
			this.page.remote_auth_s3 = '<?= $disqus_hmac ?>';
			<?php
			//endif
			}
			?>
			this.page.api_key = '<?= DISQUS_PUBLIC ?>';
			
			this.sso = {
				  name:   "LTB Network",
				  icon:     "<?= SITE_URL ?>/favicon.png",
				  url:        "<?= SITE_URL ?>/account?r=/dashboard/account/home?closeThis=1",
				  logout:  "<?= SITE_URL ?>/account/auth/logout?r=<?= $_SERVER['REQUEST_URI'] ?>",
				  width:   "800",
				  height:  "400"
			};
			
		}
        /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
        var disqus_shortname = '<?= DISQUS_DEFAULT_FORUM ?>'; // required: replace example with your forum shortname

        /* * * DON'T EDIT BELOW THIS LINE * * */
        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
    </script>
    <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
    <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
    
		<div class="comment-form">
			<?php /*
			if($commentForm){
				if($perms['canPostComment']){
				?>
			<a name="comment-form"></a>
			<h4><?= $commentTitle ?></h4>
			<p>Logged in as <?= $user['username'] ?></p>
			<?php
			if(trim($commentError) != ''){
				echo '<p class="error">'.$commentError.'</p>';
			}

			?>
			<?= $commentForm->display() ?>
			<p><em>Disqus comments coming back soon</em></p>
<?php
	echo '<p><em>Use <strong>markdown</strong> formatting for post. See <a href="#" class="markdown-trigger" target="_blank">formatting guide</a>
				for more information.</em></p>
			<div style="display: none;" id="markdown-guide">
			'.$this->displayBlock('markdown-guide').'
			</div>
			';
				
?>

				<?php
			}
			}
			else{
				echo '<p>Please <a href="'.SITE_URL.'/account?r=/'.$app['url'].'/'.$module['url'].'/'.$post['url'].'" >Login</a> to post a comment on this article.</p>';
			}*/
			?>
		</div>
		<?php
		}//endif
		?>
	</div>
	<br>
	<div class="ad large-banner center"></div>
</div>
