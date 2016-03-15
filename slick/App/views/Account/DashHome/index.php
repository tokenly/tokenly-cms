<?php
$model = new \Core\Model;
$stats = new \Tags\LTBStats;
$pm = new \App\Account\Message_Model;
$blog_model = new \App\Blog\Submissions_Model;
$home_model = new \App\Account\Home_Model;
$inventory = new \App\Tokenly\Inventory_Model;
$magic_words = new \App\Blog\MagicWords_Model;
$board_model = new \App\Forum\Board_Model;
$message_model = new \App\Account\Message_Model;
$meta = new \App\Meta_Model;

//get current user PoP score
$time = time();
$pop_update_time = $meta->getUserMeta($user['userId'], 'recent_pop_update_time');
$update_pop = true;
if(intval($pop_update_time) > 0){
	$diff = $time - $pop_update_time;
	if($diff < 600){
		$update_pop = false;
	}
}
if($update_pop){
	exec('nohup php '.SITE_BASE.'/scripts/updateUserCurrentPop.php '.$user['userId'].' > /dev/null &');
}
$getScore = json_decode($meta->getUserMeta($user['userId'], 'recent_pop_score'), true);
if(!is_array($getScore)){
	$getScore = array('score' => 0);
}

//get LTBC rank
$ltb_rank = 0;
if(isset($user['meta']['pop_rank_cache'])){
	$ltb_rank = $user['meta']['pop_rank_cache'];
}
$ltb_content_rank = 0;
if(isset($user['meta']['content_rank_cache'])){
	$ltb_content_rank = $user['meta']['content_rank_cache'];
}


//get unread messages
$count_unread = $pm->getNumUnreadMessages($user['userId']);

//count published articles
$getPosts = $blog_model->getAll('blog_posts', array('siteId' => $site['siteId'],
													 'userId' => $user['userId'],
													 'trash' => 0), array(), 'postId');
													 
$getContribPosts = $blog_model->getUserContributedPosts($this->data);
$getPosts = array_merge($getPosts, $getContribPosts);
$num_published = 0;
if(is_array($getPosts)){
	foreach($getPosts as $post){
		if($post['status'] == 'published'){
			$check_approved = $blog_model->checkPostApproved($post['postId']);
			if($check_approved){
				$num_published++;
			}
		}
	}
}

//get forum postcount
$num_posts = $home_model->getUserPostCount($user['userId']);

//get inventory
$token_inventory = $inventory->getUserBalances($user['userId'], true);
$num_ltbc = 0;
if(isset($token_inventory['LTBCOIN'])){
	$num_ltbc = $token_inventory['LTBCOIN'];
}

$asset_descs = array();

$ltbc_price = $stats->getExchangeRate('BTC_LTBC');
$btc_price = $stats->getBTCPrice();
$ltbc_usd_value = 0;
if($num_ltbc > 0 AND $ltbc_price AND $btc_price){
	$ltbc_usd_value = round(round($btc_price * $ltbc_price, 6) * $num_ltbc, 3);
}
?>
<div class="dash-home-stats">
	<ul class="stats-list">
		<li>
			<span class="stat-total  <?php if($getScore['score'] == 0){ echo 'null-stat'; } ?>"><?= number_format($getScore['score']) ?></span>
			<span class="stat-name">PoP this Week</span>
		</li>
		<li>
			<?php
			if($ltb_rank <= 0){
			?>
				<span class="stat-total null-stat">N/A</span>
			<?php
			}
			else{
			?>
				<span class="stat-total">
					#<?= number_format($ltb_rank) ?>			
				</span>
			<?php
			}//endif
			?>
			<span class="stat-name">LTBcoin Rank</span>
			<span class="stat-extra">(Participation)</span>			
		</li>
		<li>
			<?php
			if($ltb_content_rank <= 0){
			?>
				<span class="stat-total null-stat">N/A</span>
			<?php
			}
			else{
			?>
				<span class="stat-total">
					#<?= number_format($ltb_content_rank) ?>
				</span>
			<?php
			}//endif
			?>
			<span class="stat-name">
				LTBcoin Rank
			</span>
			<span class="stat-extra">(Content)</span>
		</li>		
		<li>
			<a href="<?= SITE_URL ?>/account/messages">
				<span class="stat-total <?php if($count_unread == 0){ echo 'null-stat'; } ?>" ><?= number_format($count_unread) ?></span>
				<span class="stat-name">New Messages</span>
			</a>
		</li>
		<li>
			<a href="<?= SITE_URL ?>/blog/submissions">
				<span class="stat-total <?php if($num_published == 0){ echo 'null-stat'; } ?>"><?= number_format($num_published) ?></span>
				<span class="stat-name"><?= pluralize('Article', $num_published) ?> Published</span>
			</a>
		</li>
		<li>
			<a href="<?= SITE_URL ?>/forum">
				<span class="stat-total <?php if($num_posts == 0){ echo 'null-stat'; } ?>"><?= number_format($num_posts) ?></span>
				<span class="stat-name">Forum <?= pluralize('Post', $num_posts) ?></span>
			</a>
		</li>
		<li>
			<a href="<?= SITE_URL ?>/tokenly/inventory" title="<?= number_format($num_ltbc, 8) ?> LTBcoin">
				<span class="stat-total <?php if($num_ltbc == 0){ echo 'null-stat'; } ?>"><?= number_format($num_ltbc) ?> <span class="ltbc-small"></span></span>
				<span class="stat-name">LTBcoin Balance</span>
			</a>
			<?php
			if($ltbc_usd_value > 0.01){
				echo '<span class="stat-extra">($'.number_format($ltbc_usd_value, 2).' USD)</span>';
			}
			?>			
		</li>										
	</ul><!-- stats-list -->
	<div class="clear"></div>
</div><!-- dash-home-stats -->
<div class="dash-home-cols">
	<div class="dash-col one-third">
		<div class="dash-col-content">
			<p class="pull-right">
				<a href="<?= SITE_URL ?>/dashboard/blog/magic-words" title="View all submitted magic words">View all</a>
			</p>
			<h3><i class="fa fa-magic"></i> Magic Words Entry</h3>
			<div class="dash-magic-words-cont">
			<?php
			$get_words = $magic_words->getUserWordSubmissions($user['userId']);
			if(count($get_words) == 0){
				echo '<p><strong>No words submitted.</strong></p>';
			}
			else{
			?>
			<p>
				<strong>Recent entries:</strong>
			</p>
			<?php

			$limit = 5;
			$num = 0;
			echo '<ul class="recent-magic-words">';
			foreach($get_words as $word){
				$num++;
				if($num > $limit){
					break;
				}
				echo '<li>';
				echo  '<span class="word-title">'.$word['word'].'</span>';
				echo '<span class="word-link"><a href="'.SITE_URL.'/'.$word['itemUrl'].'" target="_blank" title="'.$word['itemName'].'">'.shortenMsg($word['itemName'], 25).'</a></span>';
				echo '</li>';
			}
			echo '</ul>';
			}//endif
			?>
			<p>
				Enter in the "magic word" as heard on any participating podcast for bonus rewards!
			</p>
			<div class="quick-word-form">
				<form action="<?= SITE_URL ?>/dashboard/blog/magic-words" method="post">
					<input type="text" name="word" required placeholder="Enter magic word" />
					<input type="submit" value="Submit" />
				</form>
			</div><!-- quick-word-form -->
			</div><!-- dash-magic-words-cont -->
		</div>
	</div>
	<div class="dash-col two-thirds">
		<ul class="dash-col-tabs">
			<li class="active" data-tab="forums">
				<a href="#" ><span class="pull-right"><i class="fa fa-comments"></i></span> Forum Activity</a>
			</li>
			<li data-tab="messages">
				<a href="#"><span class="pull-right"><i class="fa fa-envelope"></i></span> Private Messages
				<?php
				if(isset($numMessages) AND $numMessages > 0){
					echo '('.number_format($numMessages).')';
				}
				?>
				</a>
			</li>
		</ul><!-- dash-col-tabs -->
		<div class="dash-tabs-cont">
			<div class="dash-tab" id="forums-tab">
				<?php
				$subscribed_threads = $board_model->getUserSubscribedThreads(false, 5);
				if(count($subscribed_threads) == 0){
					echo '<p>No subscribed topics found! Head to the <strong><a href="'.SITE_URL.'/forum/board/all">forums</a></strong> to get started.</p>';
				}
				else{
					
				?>
				<ul class="dash-thread-list">
				<?php
				foreach($subscribed_threads as $thread){
					$post_avatar = 'default.jpg';
					$post_username = '';
					$post_profile = '';
					$post_text = 'started a topic:';
					if($thread['mostRecent']){
						$post_text = 'commented on a topic:';
						$post_username = $thread['mostRecent']['user']['username'];
						$post_profile = $thread['mostRecent']['user']['slug'];
						if(trim($thread['mostRecent']['user']['real_avatar']) != ''){
							$post_avatar = $thread['mostRecent']['user']['avatar'];
						}
						else{
							$post_avatar = 'default.jpg';
						}							
					}
					else{
						$post_username = $thread['user']['username'];
						$post_profile = $thread['user']['slug'];
						if(trim($thread['user']['real_avatar']) != ''){
							$post_avatar = $thread['user']['avatar'];
						}
						else{
							$post_avatar = 'default.jpg';
						}						
					}
					
					if($thread['lastPost'] == '' OR $thread['lastPost'] == '0000-00-00 00:00:00'){
						$last_update = strtotime($thread['postTime']);
					}
					else{
						$last_update = strtotime($thread['lastPost']);
					}
					$time_diff = time() - $last_update;
					if($time_diff < 86400){
						$post_time = date('h:i A', $last_update);
					}
					else{
						$post_time = date('d/m/Y', $last_update);
					}
				?>
				<li>
					<div class="dash-thread-item">
						<span class="pull-right dash-thread-time" title="<?= formatDate($last_update) ?>">
							<i class="fa fa-clock-o"></i> <?= $post_time ?>
						</span>
						<a href="<?= SITE_URL ?>/profile/user/<?= $post_profile ?>" class="dash-thread-user" target="_blank">
							<span class="mini-avatar"><img src="<?= SITE_URL ?>/files/avatars/<?= $post_avatar ?>" alt="" /></span>
							<span class="username"><?= $post_username ?></span>
						</a>
						<?= $post_text ?>
						<a href="<?= SITE_URL ?>/forum/post/<?= $thread['url'] ?>" target="_blank" class="dash-thread-title" title="<?= $thread['title'] ?>"><?= shortenMsg($thread['title'], 50) ?></a>
					</div>
				</li>
				<?php
				}
				?>
				</ul>
				<p class="pull-right view-more">
					<a href="<?= SITE_URL ?>/forum/board/subscriptions" target="_blank">View more <i class="fa fa-angle-double-down"></i></a>
				</p>
				<?php
				}//endif
				?>
			</div>
			<?php

			?>
			<div class="dash-tab" id="messages-tab" style="display: none;">
				<?php
				$message_model->appData = $this->data;
				$get_inbox = $message_model->getUserInbox($user['userId'], 5);
				if(count($get_inbox) == 0){
					echo '<p>No private messages found.</p>';
				}
				else{

				?>
				<ul class="dash-thread-list">
				<?php
				foreach($get_inbox as $thread){
					$post_avatar = 'default.jpg';
					$post_username = '';
					$post_profile = '';

					$post_username = $thread['from']['username'];
					$post_profile = $thread['from']['slug'];
					if(trim($thread['from']['real_avatar']) != ''){
						$post_avatar = $thread['from']['avatar'];
					}	
					
					$last_update = strtotime($thread['sendDate']);
					
					$time_diff = time() - $last_update;
					if($time_diff < 86400){
						$post_time = date('h:i A', $last_update);
					}
					else{
						$post_time = date('d/m/Y', $last_update);
					}
				?>
				<li class="<?php if($thread['isRead'] == 0){ echo "unread"; } ?>">
					<div class="dash-thread-item">
						<span class="pull-right dash-thread-time" title="<?= formatDate($last_update) ?>">
							<i class="fa fa-clock-o"></i> <?= $post_time ?>
						</span>
						<a href="<?= SITE_URL ?>/profile/user/<?= $post_profile ?>" class="dash-thread-user" target="_blank">
							<span class="mini-avatar"><img src="<?= SITE_URL ?>/files/avatars/<?= $post_avatar ?>" alt="" /></span>
							<span class="username"><?= $post_username ?></span>
						</a>
						<a href="<?= SITE_URL ?>/account/messages/view/<?= $thread['messageId'] ?>" class="dash-thread-title" title="<?= $thread['subject'] ?>"><?= shortenMsg($thread['subject'], 50) ?></a>
					</div>
				</li>
				<?php
				}
				?>
				</ul>
				<p class="pull-right view-more">
					<a href="<?= SITE_URL ?>/account/messages" target="_blank">View all <i class="fa fa-angle-double-down"></i></a>
				</p>
				<?php
				}//endif
				?>
			</div>
			<div class="clear"></div>
		</div><!-- dash-tabs-cont -->
	</div>
	<div class="clear"></div>
</div><!-- dash-home-cols -->
<div class="dash-home-charts">
	<?php

	$display_ltbc_price = 'N/A';
	if($ltbc_price){
		$display_ltbc_price = convertFloat($ltbc_price).' BTC';
		if($btc_price){
			$ltbc_usd = round($btc_price * $ltbc_price, 6);
			$display_ltbc_price .= ' ($'.convertFloat($ltbc_usd).' USD)';
		}
	}
	?>	
	<div class="dash-chart-cont price-chart-cont">
		<h3><i class="fa fa-usd"></i> <strong class="chart-price-type">LTBcoin</strong> Price History <span>(<a href="#" class="chart-view-switch ltbc-view">switch to BTC</a>)</span></h3>
		<div id="ltbc-price-chart-cont">
			<div id="ltbc-price-chart"></div>
			<span class="chart-current-price">
				Current LTBCOIN Price: <?= $display_ltbc_price ?>
			</span>
			<small class="pull-right">Data provided by <a href="https://poloniex.com" target="_blank" rel="nofollow">Poloniex</a></small>
		</div>
		<div id="btc-price-chart-cont" style="display: none; width: 100%;">
			<div id="btc-price-chart"></div>
			<?php
			if($btc_price){
			?>
			<span class="chart-current-price">
				Current BITCOIN Price: $<?= number_format($btc_price, 2) ?> USD
			</span>			
			<?php
			}//endif
			?>
			<small class="pull-right">Data provided by <a href="https://bitcoinaverage.com" target="_blank" rel="nofollow">Bitcoin Average</a></small>
		</div>
		<div class="clear"></div>
	</div>	
	<div class="dash-chart-cont">
		<h3><i class="fa fa-group"></i> <strong class="chart-point-name">Participation</strong> Points Earned
		<div class="pop-chart-opts">
				<a href="#" class="active" data-chart="pop" data-chart-name="Participation" >Proof of Participation</a> |
				<a href="#" data-chart="poq" data-chart-name="Quality" >Proof of Quality</a> |
				<a href="#" data-chart="pov" data-chart-name="Value" >Proof of Value</a>
		</div>				
		</h3>
		<div id="chart-master-cont">
			<div id="pop-chart-cont">
				<div id="participation-chart"></div>
			</div>
			<div id="poq-chart-cont" style="display: none; margin-top: 20px;">
				<div id="quality-chart"></div>
			</div>
			<div id="pov-chart-cont" style="display: none; margin-top: 20px;">
				<div id="value-chart"></div>
			</div>				
		</div>
	</div>
	<div class="clear"></div>	
</div><!-- dash-home-charts -->
<div class="dash-home-cols bottom-cols">
	<div class="dash-col one-third">
		<div class="dash-col-content">
			<span class="pull-right"><a href="<?= SITE_URL ?>/dashboard/blog/submissions">View all</a></span>
			<h3><i class="fa fa-file-o"></i> Drafts in Progress</h3>
			<?php
			$posts = $blog_model->getUserPostsWithContributed(array('user' => $user, 'site' => $site));
			$drafts = array();
			foreach($posts as $post){
				if($post['status'] != 'published'){
					$drafts[] = $post;
				}
			}
			if(count($drafts) == 0){
				echo '<p>No drafts in progress.<br> Visit the <strong><a href="'.SITE_URL.'/dashboard/blog/submissions">submissions page</a></strong> to start your first blog post!</p>';
			}
			else{
				echo '<ul class="dash-draft-list">';
				$draft_limit = 5;
				$num = 0;
				foreach($drafts as $draft){
					$num++;
					if($num > $draft_limit){
						break;
					}
					?>
					<li>
						<span class="draft-options pull-right">
							<a href="<?= SITE_URL ?>/dashboard/blog/submissions/preview/<?= $draft['postId'] ?>" target="_blank">Preview</a>
							<a href="<?= SITE_URL ?>/dashboard/blog/submissions/edit/<?= $draft['postId'] ?>" target="_blank">Edit</a>
						</span>
						<span class="draft-title" title="<?= $draft['title'] ?>"><?= shortenMsg($draft['title'], 30) ?></span>
					</li>				
					<?php
				}
				
				echo '</ul>';
			}
			?>
			<p class="pull-right">
				<a href="<?= SITE_URL ?>/dashboard/blog/submissions/add" class="dash-new-article"><i class="fa fa-plus-circle"></i> New Submission</a>
			</p>
		</div>
	</div>
	<div class="dash-col one-third">
		<div class="dash-col-content">
			<span class="pull-right"><a href="<?= SITE_URL ?>/tokenly/inventory/transactions">View all</a></span>
			<h3><i class="fa fa-exchange"></i> Recent Transactions</h3>
			<?php
			$transactions = $inventory->getUserInventoryTransactions($user['userId'], 10, 2);

			if($transactions AND count($transactions) > 0){
				echo '<ul class="dash-tx-list">';
				$time = time();
				foreach($transactions as $tx){
					$tx_symbol = 'fa-plus';
					$tx_class = 'text-success';
					if(isset($tx['txInfo']['to'])){
						if($tx['txInfo']['to'] != $tx['address']){
							$tx_symbol = 'fa-minus';
							$tx_class = 'text-error';
						}
					}
					$time_diff = $time = $tx['time'];
					if($time_diff < 86400){
						$show_time = date('h:i A', $tx['time']);
					}
					else{
						$show_time = date('d/m/Y', $tx['time']);
					}
					$from_display = '<span title="'.$tx['txInfo']['from'].'"><a href="https://chain.so/address/BTC/'.$tx['txInfo']['from'].'" target="_blank">'.substr($tx['txInfo']['from'], 0, 8).'</a></span>';
					if(is_array($tx['txInfo']['from_user'])){
						$from_display = '<span title="'.$tx['txInfo']['from_user']['username'].'"><a href="'.SITE_URL.'/profile/user/'.$tx['txInfo']['from_user']['slug'].'" target="_blank">'.shortenMsg($tx['txInfo']['from_user']['username'], 10).'</a></span>';
					}
					$to_display = '<span title="'.$tx['txInfo']['to'].'"><i class="fa fa-arrow-right"></i> <a href="https://chain.so/address/BTC/'.$tx['txInfo']['to'].'" target="_blank">'.substr($tx['txInfo']['to'], 0, 8).'</a></span>';
					if(is_array($tx['txInfo']['to_user'])){
						$to_display = '<span title="'.$tx['txInfo']['to_user']['username'].'"><i class="fa fa-arrow-right"></i> <a href="'.SITE_URL.'/profile/user/'.$tx['txInfo']['to_user']['slug'].'" target="_blank">'.shortenMsg($tx['txInfo']['to_user']['username'], 10).'</a></span>';
					}					
					
					if(!in_array($tx['asset'], $asset_descs)){
						$asset_descs[] = $tx['asset'];
					}
					?>
					<li>
						<div class="pull-right dash-tx-right">
							<div class="dash-tx-to-user">
								<?= $to_display ?>
							</div>										
							<div class="dash-tx-actions">
								<?php
								if($tx['type'] == 'xcp'){
									echo '<a href="https://blockscan.com/tx?txhash='.$tx['txId'].'" target="_blank">View</a>';
								}
								else{
									echo '<a href="https://chain.so/tx/BTC/'.$tx['txId'].'" target="_blank">View</a>';
								}
								?>
							</div>			
						</div>
						<div class="pull-left dash-tx-left">
							<div class="dash-tx-from-user">
								<?= $from_display ?>
							</div>							
							<div class="dash-tx-time" title="<?= formatDate($tx['time']) ?>">
								<i class="fa fa-clock-o"></i> <?= $show_time ?>
							</div>
						</div>
						<span class="dash-tx-asset <?= $tx_class ?>">
							<span class="dash-tx-symbol"><i class="fa <?= $tx_symbol ?>"></i></span>
							<span class="dash-tx-amount"><?= convertFloat(abs($tx['amount'])) ?></span>							
							<span class="dash-tx-asset-link"><a href="#asset-desc-<?= $tx['asset'] ?>" class="fancy"><?= $tx['asset'] ?></a></span>
						</span>
					</li>
					<?php
				}
				echo '</ul>';
			}
			else{
				\Core\Model::$cacheMode = false;
				$check_updating = $meta->getUserMeta($user['userId'], 'tx_list_updating');
				\Core\Model::$cacheMode = true;
				if(intval($check_updating) === 1){
					echo '<p>Reading blockchain...check again in a few minutes.</p>';
				}
				else{
					echo '<p>No transactions found in your inventory.<br> Have you <strong><a href="'.SITE_URL.'/tokenly/address-manager">registered and verified</a></strong> any bitcoin addresses yet?</p>';
				}
			}
			?>
		</div>
	</div>
	<div class="dash-col one-third last">
		<div class="dash-col-content">
			<span class="pull-right"><a href="<?= SITE_URL ?>/dashboard/tokenly/inventory">View all</a></span>
			<h3><i class="fa fa-btc"></i> Token Inventory</h3>
			<?php
				if(!$token_inventory OR count($token_inventory) == 0){
					echo '<p>No tokens found in your inventory. <br>
							Have you <strong><a href="'.SITE_URL.'/dashboard/tokenly/address-manager">registered and verified</a></strong> any <strong><a href="https://counterparty.io" target="_blank">Counterparty</a></strong> compatible bitcoin addresses yet?
						</p>';
				}		
				else{
					echo '<ul class="dash-inventory-list">';
					foreach($token_inventory as $token => $amnt){
						if(!in_array($token, $asset_descs)){
							$asset_descs[] = $token;
						}						
					 ?>
					 <li>
						<span class="pull-right dash-inv-balance">
							<?= rtrim(rtrim(number_format($amnt, 8), "0"),".") ?>
						</span>
						<span class="dash-inv-asset">
							<a href="#asset-desc-<?= $token ?>" class="fancy"><?= $token ?></a>
						</span>
						<div class="clear"></div>
					 </li>
					 <?php					 
					}
					echo '</ul>';
				}	
			?>
			<div class="inventory-update-btn">
				<form action="<?= SITE_URL ?>/tokenly/inventory" method="post">
					<input type="submit" name="forceRefresh" id="forceRefresh" value="Refresh Inventory" />
				</form>
			</div>
		</div>
	</div>
	<div class="clear"></div>
</div><!-- dash-home-cols bottom-cols -->
<?php
foreach($asset_descs as $asset){
	$asset = strtoupper($asset);
	if($asset == 'BTC'){
		echo '<div style="display: none;" id="asset-desc-BTC">';
		echo '<p><strong>Bitcoin!</strong></p>';
		echo '</div>';
	}
	else{
		$getAsset = $inventory->getAssetData($asset);
		if($getAsset){
			echo '<div style="display: none; width: 400px;" id="asset-desc-'.$asset.'">';
			if(trim($getAsset['description']) != ''){
				echo '<p><strong>Description:</strong><br>'.markdown($getAsset['description']).'</p>';
			}
			else{
				echo '<p><strong>No token description available.</strong></p>';
			}
			if(trim($getAsset['link']) != ''){
				echo '<p><strong>Project Information:</strong> <a href="'.$getAsset['link'].'" target="_blank">'.$getAsset['link'].'</a></p>';
			}
			echo '<p><strong>View token data on <a href="http://blockscan.com/assetInfo/'.$getAsset['asset'].'" target="_blank">Blockscan</a></strong> </p>';
			echo '</div>';
		}
	}
}
?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">

google.load('visualization', '1', {packages: ['corechart', 'line']});
google.setOnLoadCallback(drawCharts);

function drawCharts(){
	
	//Proof of participation chart
	var data = new google.visualization.DataTable();
	data.addColumn('date', 'Date');
	data.addColumn('number', 'Average PoP earned by LTBN users');
	data.addColumn('number', 'PoP earned by <?= $user['username'] ?>');
	
	var plot_data = [];
	<?php
	$tokenly_app = get_app('tokenly');
	$user_points = array();
	$cache_path = SITE_BASE.'/data/cache';
	
	$pop_averages = json_decode(@file_get_contents($cache_path.'/pop_chart_averages.json'), true);
	if(!is_array($pop_averages)){
		$pop_averages = array();
	}
	if(isset($user['meta']['pop_chart_scores'])){
		$user_points = json_decode($user['meta']['pop_chart_scores'], true);
	}
	foreach($pop_averages as $chart_date => $average_score){
		$user_score = 0;
		if(isset($user_points[$chart_date])){
			$user_score = $user_points[$chart_date];
		}
		echo 'plot_data.push([new Date('.strtotime($chart_date).' * 1000),'.round($average_score).','.round($user_score).']);'."\n";
	}
	?>
	data.addRows(
		plot_data
	);

	var options = {
		backgroundColor: {fill:'transparent'},
		  legend: { position: 'bottom' },		
		hAxis: {
			title: 'LTBcoin Distribution Date'
		},
		vAxis: {
			title: 'Points earned'
		}
	};

	var chart = new google.visualization.LineChart(document.getElementById('participation-chart'));

	chart.draw(data, options);
	
	//Proof of Quality chart
	var data = new google.visualization.DataTable();
	data.addColumn('date', 'Date');
	data.addColumn('number', 'Average PoQ earned by LTBN contributors');
	data.addColumn('number', 'PoQ earned by <?= $user['username'] ?>');
	
	var plot_data = [];
	<?php

	$user_points = array();
	$poq_averages = json_decode(@file_get_contents($cache_path.'/poq_chart_averages.json'), true);
	if(!is_array($poq_averages)){
		$poq_averages = array();
	}
	if(isset($user['meta']['poq_chart_scores'])){
		$user_points = json_decode($user['meta']['poq_chart_scores'], true);
	}
	foreach($poq_averages as $chart_date => $average_score){
		$user_score = 0;
		if(isset($user_points[$chart_date])){
			$user_score = $user_points[$chart_date];
		}
		echo 'plot_data.push([new Date('.strtotime($chart_date).' * 1000),'.round($average_score).','.round($user_score).']);'."\n";
	}
	?>
	data.addRows(
		plot_data
	);

	var options = {
		backgroundColor: {fill:'transparent'},
		  legend: { position: 'bottom' },		
		hAxis: {
			title: 'LTBcoin Distribution Date'
		},
		vAxis: {
			title: 'Points earned'
		}
	};

	var chart = new google.visualization.LineChart(document.getElementById('quality-chart'));

	chart.draw(data, options);
	
	//Proof of Value chart
	var data = new google.visualization.DataTable();
	data.addColumn('date', 'Date');
	data.addColumn('number', 'Average PoV earned by LTBN contributors');
	data.addColumn('number', 'PoV earned by <?= $user['username'] ?>');
	
	var plot_data = [];
	<?php

	$user_points = array();
	$pov_averages = json_decode(@file_get_contents($cache_path.'/pov_chart_averages.json'), true);
	if(!is_array($pov_averages)){
		$pov_averages = array();
	}
	if(isset($user['meta']['pov_chart_scores'])){
		$user_points = json_decode($user['meta']['pov_chart_scores'], true);
	}
	foreach($pov_averages as $chart_date => $average_score){
		$user_score = 0;
		if(isset($user_points[$chart_date])){
			$user_score = $user_points[$chart_date];
		}
		echo 'plot_data.push([new Date('.strtotime($chart_date).' * 1000),'.round($average_score).','.round($user_score).']);'."\n";
	}
	?>
	data.addRows(
		plot_data
	);

	var options = {
		backgroundColor: {fill:'transparent'},
		  legend: { position: 'bottom' },		
		hAxis: {
			title: 'LTBcoin Distribution Date'
		},
		vAxis: {
			title: 'Points earned'
		}
	};

	var chart = new google.visualization.LineChart(document.getElementById('value-chart'));

	chart.draw(data, options);	
	
	
	//LTBcoin price chart
	var data = new google.visualization.DataTable();
	data.addColumn('date', 'Date');
	data.addColumn({'type': 'string', 'role': 'tooltip', 'p': {'html': true}});
	data.addColumn('number', 'Price (satoshis)');

	var plot_data = [];
	<?php
	$ltbc_history = json_decode(@file_get_contents($cache_path.'/ltbc_price_history.json'), true);
	if(!is_array($ltbc_history)){
		$ltbc_history = array();
	}
	foreach($ltbc_history as $row){
		$ltbc_satoshis = round($row['btc_rate'] * SATOSHI_MOD);
		echo 'plot_data.push([new Date('.strtotime($row['date']).' * 1000),\'<p style="padding: 10px;"><strong>'.date('j M Y', strtotime($row['date'])).'</strong><br>Price (satoshis): <strong>'.$ltbc_satoshis.'</strong><br>Price (USD): $'.convertFloat($row['usd_rate']).'</p>\','.$ltbc_satoshis.']);'."\n";
	}
	?>
	data.addRows(
		plot_data
	);

	var options = {
		backgroundColor: {fill:'transparent'},
		legend: { position: 'none' },
		focusTarget: 'category',
		tooltip: { isHtml: true },
		vAxis: {
			title: 'Price (satoshis)'
		}		
	};

	var chart = new google.visualization.LineChart(document.getElementById('ltbc-price-chart'));

	chart.draw(data, options);	
	
	//BTC price chart
	var data = new google.visualization.DataTable();
	data.addColumn('date', 'Date');
	data.addColumn('number', 'Price (USD)');
		
	var plot_data = [];
	<?php
	$btc_history = json_decode(@file_get_contents($cache_path.'/btc_price_history.json'), true);
	if(!is_array($btc_history)){
		$btc_history = array();
	}
	foreach($btc_history as $row){
		echo 'plot_data.push([new Date('.strtotime($row['datetime']).' * 1000),'.$row['average'].']);'."\n";
	}
	?>
	data.addRows(
		plot_data
	);

	var options = {
		backgroundColor: {fill:'transparent'},
		legend: { position: 'none' },
		vAxis: {
			title: 'Price (USD)'
		}		
	};

	var chart = new google.visualization.LineChart(document.getElementById('btc-price-chart'));

	chart.draw(data, options);		
}	


	$(document).ready(function(){
		$('.dash-col-tabs').find('li a').click(function(e){
			e.preventDefault();
			$('.dash-col-tabs').find('li').removeClass('active');
			$(this).parent().addClass('active');
			var tab = $(this).parent().data('tab');
			$('.dash-tabs-cont').find('.dash-tab').hide();
			$('.dash-tabs-cont').find('#' + tab + '-tab').show();
		});
		
		$('.chart-view-switch').click(function(e){
			e.preventDefault();
			if($(this).hasClass('btc-view')){
				$(this).removeClass('btc-view').addClass('ltbc-view');
				$(this).html('switch to BTC');
				$(this).parent().parent().find('.chart-price-type').html('LTBcoin');
				$('#btc-price-chart-cont').hide();
				$('#ltbc-price-chart-cont').show();
				drawCharts();
			}
			else{
				$(this).removeClass('ltbc-view').addClass('btc-view');
				$(this).parent().parent().find('.chart-price-type').html('Bitcoin');
				$(this).html('switch to LTBcoin');
				$('#btc-price-chart-cont').show();
				$('#ltbc-price-chart-cont').hide();	
				drawCharts();
			}
		});
		
		$('.pop-chart-opts a').click(function(e){
			e.preventDefault();
			var chart = $(this).data('chart');
			var chart_name = $(this).data('chart-name');
			$('#chart-master-cont > div').hide();
			$('#' + chart + '-chart-cont').show();
			$(this).parent().find('a').removeClass('active');
			$(this).addClass('active');
			$('.chart-point-name').html(chart_name);
			drawCharts();
		});
		
	});
</script>

<?php
if(isset($_GET['closeThis'])){
	
	?>
	<script type="text/javascript">
		$(document).ready(function(){
			window.close();
			
		});
	</script>
	<?php
}
?>
