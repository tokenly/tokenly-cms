<?php
class Slick_Tags_LTBStats
{
	
	private static $reportData = array();
	
	function __construct()
	{
		$this->model = new Slick_App_Meta_Model;
		$this->site = $this->model->get('sites', $_SERVER['HTTP_HOST'], array(), 'domain');
		$this->accountModel = new Slick_App_Account_Home_Model;
		
	}
	
	public function display()
	{
		
		if(isset($_GET['details'])){
			$getReport = $this->model->get('pop_reports', $_GET['details']);
			if($getReport){
				return $this->showReportDetails($getReport);
			}
		}
		
		$getUser = Slick_App_Account_Home_Model::userInfo();
		$model = $this->model;
		$xcp = new Slick_API_Bitcoin(XCP_CONNECT);
		
		$stats = array();
		try{
			$getAsset = $xcp->get_asset_info(array('assets' => array('LTBCOIN')));
			$asset = $getAsset[0];
			$stats['totalIssued'] = $asset['supply'] / SATOSHI_MOD;
			
			$balances = $xcp->get_balances(array('filters' => array('field' => 'asset', 'op' => '==', 'value' => 'LTBCOIN')));
			$balances2 = $xcp->get_balances(array('filters' => array('field' => 'asset', 'op' => '==', 'value' => 'LTBCOIN'), 'offset' => 1000));
			$balances3 = $xcp->get_balances(array('filters' => array('field' => 'asset', 'op' => '==', 'value' => 'LTBCOIN'), 'offset' => 2000));
			$balances4 = $xcp->get_balances(array('filters' => array('field' => 'asset', 'op' => '==', 'value' => 'LTBCOIN'), 'offset' => 3000));
			$balances5 = $xcp->get_balances(array('filters' => array('field' => 'asset', 'op' => '==', 'value' => 'LTBCOIN'), 'offset' => 4000));
			$balances6 = $xcp->get_balances(array('filters' => array('field' => 'asset', 'op' => '==', 'value' => 'LTBCOIN'), 'offset' => 5000));
			$balances = array_merge($balances, $balances2);
			$balances = array_merge($balances, $balances3);
			$balances = array_merge($balances, $balances4);
			$balances = array_merge($balances, $balances5);
			$balances = array_merge($balances, $balances6);
			$uniqueBalances = array();
			foreach($balances as $balance){
				if($balance['quantity'] == 0){
					continue;
				}
				if(!isset($uniqueBalances[$balance['address']])){
					$uniqueBalances[] = $balance;
				}
			}
			$stats['totalHolders'] = count($uniqueBalances);
			
		}
		catch(Exception $e){
			$stats['totalIssued'] = 'N/A';
			$stats['totalHolders'] = 'N/A';
		}
		
		$launchDay = '2014-06-27';
		$launchTime = strtotime($launchDay);
		$diff = time() - $launchTime;		
		$stats['launchDays'] = ceil($diff / 60 / 60 / 24);
		$stats['launchWeeks'] = round(($stats['launchDays'] / 7), 1);		
		
		$getMelotic = json_decode(file_get_contents('https://www.melotic.com/api/markets/ltbc-btc/ticker'), true);
		$getExchange = json_decode(file_get_contents('https://poloniex.com/public?command=returnTicker'), true);
		$getDEX = json_decode(file_get_contents('http://xcp.blockscan.com/api2.aspx?module=price&asset1=LTBCOIN&asset2=BTC'), true);
		if(isset($getExchange['BTC_LTBC'])){
			$getExchange = $getExchange['BTC_LTBC'];
		}
		else{
			$getExchange = false;
		}
		if($getExchange){
			$stats['latestPrice'] = convertFloat($getExchange['last']);
			$stats['volume'] = $getExchange['baseVolume'];
			
			if($stats['totalIssued'] != 'N/A'){
				$stats['marketCap'] = round(floatval($stats['latestPrice'] * $stats['totalIssued']), 3).' BTC';	
			}
			else{
				$stats['marketCap'] = 'N/A';
			}
		}
		else{
			$stats['latestPrice'] = 'N/A';
			$stats['volume'] = 'N/A';
			$stats['marketCap'] = 'N/A';
		}
		
		$ltbApp = $model->get('apps', 'ltbcoin', array(), 'slug');
		$appMeta = $model->appMeta($ltbApp['appId']);

		
		ob_start();
		?>
		<a name="stats"></a>
		<div class="ltb-stat-links">
			<h4>Quick Links</h4>
			<p>
				<a href="#stats">General</a><br>
				<?php
				if($getUser){
					echo '<a href="#my-stats">My Stats</a><br>';
				}
				?>
				<a href="#weekly">Weekly Distributions</a><br>
				<a href="#metrics">Earning Metrics</a><br>
				<a href="#leaderboard">Leaderboard</a>
			</p>
		</div>
		<h2>General Statistics</h2>
		<ul class="ltb-stats">
			<li><strong>Total Tokens Issued:</strong> <?= number_format($stats['totalIssued']) ?> LTBcoin</li>
			<li><strong>Max Supply:</strong> 510,000,000 LTBcoin (<?= round((($stats['totalIssued'] / 510000000) * 100), 2) ?>% issued)
			<li><strong>Total Token Holders:</strong> <?= number_format($stats['totalHolders']) ?></li>
			<li><strong>Latest BTC/LTBcoin Price:</strong>
				<ul>
						<li><?= $stats['latestPrice'] ?> BTC / 1 LTBc (Poloniex)</li>
						<li><?= convertFloat($getMelotic['latest_price']) ?> BTC / 1 LTBc (Melotic)</li>
						<li><?= $getDEX['result'] ?> BTC / 1 LTBc (DEX)</li>
				</ul>
			</li>
			<li><strong>24h Exchange Volume:</strong>
				<ul>
					<li><?= round($stats['volume'], 3) ?> BTC (Poloniex)</li>
					<li><?= round($getMelotic['volume'], 3) ?> BTC (Melotic)</li>
				</ul>
			</li>
			<li><strong>Market Cap:</strong> <?= $stats['marketCap'] ?> (based on poloniex)
			<li><strong>Days Since Launch:</strong> <?= $stats['launchDays'] ?> (<?= $stats['launchWeeks'] ?> weeks)</li>
			<li><a href="https://docs.google.com/spreadsheets/d/1GzytNblMx8xBmUczX7sPC8QitkJrrRirNrh2AJtjy1Q/edit#gid=508171322" target="_blank">Distribution Schedule</a></li>
			<li><a href="http://blockscan.com/assetInfo.aspx?q=LTBCOIN" target="_blank">Blockscan</a></li>
			<li><a href="http://joelooney.org/ltbcoin/" target="_blank">LTBc Toolbox</a></li>
			<li><a href="/trade-ltbcoin" target="_blank"><strong>Trade LTBCOIN</strong></a></li>
		</ul>
		<br>
		<hr>
		<?= $this->showPersonalStats() ?>
		<a name="weekly"></a>
		<h2>Weekly Distributions</h2>
			<ul class="ltb-stat-tabs" data-tab-type="distro-list">
				<li><a href="#" class="tab active" data-tab="pop">PoP</a></li>
				<li><a href="#" class="tab" data-tab="poq">PoQ</a></li>
				<li><a href="#" class="tab" data-tab="pov">PoV</a></li>
			</ul>
			<div class="clear"></div>
			<div class="ltb-weekly-cont distro-list">
				<div class="ltb-data-tab" id="pop">
					<h4>Proof of Participation</h4>
					<?php
					$tableData = $this->getPoolData('pop');
					if(count($tableData) == 0){
						echo '<p>No distributions found</p>';
					}
					else{
					?>
					<table class="admin-table mobile-table">
						<thead>
							<tr>
								<th>Period</th>
								<th>LTBC Rewarded</th>
								<th>Users Participating</th>
								<th>Points Earned</th>
								<th>LTBc / Point</th>
								<th>Average LTBc Earned</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php
							
							foreach($tableData as $tRow){
								echo '<tr>';
								echo '<td>'.$tRow['period'].'</td>';
								echo '<td>'.$tRow['totalRewards'].'</td>';
								echo '<td>'.$tRow['totalUsers'].'</td>';
								echo '<td>'.$tRow['totalPoints'].'</td>';
								echo '<td>'.$tRow['perPoint'].'</td>';
								echo '<td>'.$tRow['avgEarned'].'</td>';
								echo '<td><a href="?details='.$tRow['reportId'].'">Details</a></td>';
								echo '</tr>';
							}
							?>
						</tbody>
					</table>
					<?php
					}//endif
					?>
				</div>
				<div class="ltb-data-tab" id="poq" style="display: none;">
					<h4>Proof of Quality</h4>
					<?php
					$tableData = $this->getPoolData('poq');
					if(count($tableData) == 0){
						echo '<p>No distributions found</p>';
					}
					else{
					?>
					<table class="admin-table mobile-table">
						<thead>
							<tr>
								<th>Period</th>
								<th>LTBC Rewarded</th>
								<th>Users Participating</th>
								<th>Points Earned</th>
								<th>LTBc / Point</th>
								<th>Average LTBc Earned</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php
							
							foreach($tableData as $tRow){
								echo '<tr>';
								echo '<td>'.$tRow['period'].'</td>';
								echo '<td>'.$tRow['totalRewards'].'</td>';
								echo '<td>'.$tRow['totalUsers'].'</td>';
								echo '<td>'.$tRow['totalPoints'].'</td>';
								echo '<td>'.$tRow['perPoint'].'</td>';
								echo '<td>'.$tRow['avgEarned'].'</td>';
								echo '<td><a href="?details='.$tRow['reportId'].'">Details</a></td>';
								echo '</tr>';
							}
							?>
						</tbody>
					</table>
					<?php
					}//endif
					?>
				</div>	
				<div class="ltb-data-tab" id="pov" style="display: none;">
					<h4>Proof of Value</h4>
					<?php
					$tableData = $this->getPoolData('pov');
					if(count($tableData) == 0){
						echo '<p>No distributions found</p>';
					}
					else{
					?>
					<table class="admin-table mobile-table">
						<thead>
							<tr>
								<th>Period</th>
								<th>LTBC Rewarded</th>
								<th>Users Participating</th>
								<th>Points Earned</th>
								<th>LTBc / Point</th>
								<th>Average LTBc Earned</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php
							
							foreach($tableData as $tRow){
								echo '<tr>';
								echo '<td>'.$tRow['period'].'</td>';
								echo '<td>'.$tRow['totalRewards'].'</td>';
								echo '<td>'.$tRow['totalUsers'].'</td>';
								echo '<td>'.$tRow['totalPoints'].'</td>';
								echo '<td>'.$tRow['perPoint'].'</td>';
								echo '<td>'.$tRow['avgEarned'].'</td>';
								echo '<td><a href="?details='.$tRow['reportId'].'">Details</a></td>';
								echo '</tr>';
							}
							?>
						</tbody>
					</table>
					<?php
					}//endif
					?>
				</div>	
				<?php
				/*				
				<div class="ltb-data-tab" id="ref" style="display: none;">
					<h4>Affiliate Referrals</h4>
					<?php
					$tableData = $this->getPoolData('ref');
					if(count($tableData) == 0){
						echo '<p>No distributions found</p>';
					}
					else{
					?>
					<table class="admin-table mobile-table">
						<thead>
							<tr>
								<th>Period</th>
								<th>LTBC Rewarded</th>
								<th>Users Participating</th>
								<th>Points Earned</th>
								<th>LTBc / Point</th>
								<th>Average LTBc Earned</th>
							</tr>
						</thead>
						<tbody>
							<?php
							
							foreach($tableData as $tRow){
								echo '<tr>';
								echo '<td>'.$tRow['period'].'</td>';
								echo '<td>'.$tRow['totalRewards'].'</td>';
								echo '<td>'.$tRow['totalUsers'].'</td>';
								echo '<td>'.$tRow['totalPoints'].'</td>';
								echo '<td>'.$tRow['perPoint'].'</td>';
								echo '<td>'.$tRow['avgEarned'].'</td>';
								echo '</tr>';
							}
							?>
						</tbody>
					</table>
					<?php
					}//endif
					?>
				</div>		
				*/
				?>								
			</div>
			
		<br>
		<hr>
		<a name="metrics"></a>
		<h2>Earning Metrics</h2>
		<p>
			Weekly distributions are determined based on each users' individual point score relative to the total points earned for that category or "distribution pool".
		</p>
		<p>
			Below is a breakdown of how points are earned. Scoring weights and calculations may change as we evolve.
		</p>
		<ul class="ltb-metric-stats">
			<li>
				<strong>Proof of Participation (PoP)</strong>
				<ul>
					<li><em>Article comment:</em> <?= $appMeta['pop-comment-weight'] ?></li>
					<li><em>Forum post:</em> <?= $appMeta['pop-forum-post-weight'] ?></li>
					<li><em>Unique page view:</em> <?= $appMeta['pop-view-weight'] ?></li>
					<li><em>Magic word:</em> <?= $appMeta['pop-listen-weight'] ?></li>
					<li><em>Post "like" received:</em> 0.05 - 5 *</li>
					<li><em>Active Referral:</em> <?= $appMeta['pop-referral-weight'] ?></li>
					<li><strong>Note:</strong> Article comments, forum posts and page views have daily diminishing returns.
												<br>
												Additionally, "likes" are diminished on a per-user-per-day basis (e.g liking the same user more than once in the same day)
												<br>
												Example: 1 forum post a day will get you 10 points each time, but 2 forum posts will only get you 15 points.<br>
							<a href="https://docs.google.com/document/d/1L7HmE8IupFiSrfqk9BgNa4Zg9XogqtScyQjTw0k2xCc" target="_blank">See here</a>
							 for more information.</li>
					<li>
						* Each "like" on a post holds a different weight depending on how much LTBcoin each user holds in their wallet.<br>
						For instance, you will receive more points if a user with more LTBc than you likes one of your posts (up to a maximum of 5 points).<br>
						<a href="http://letstalkbitcoin.com/forum/post/ltbcoin-update-weighted-likes-article-submission-fees" target="_blank">More details here</a>.
					</li>
				</ul>
			</li>
			<li>
				<strong>Proof of Quality/Publication (PoQ)</strong>
				<ul>
					<li><em>Published article:</em> <?= $appMeta['pop-publish-weight'] ?></li>
				</ul>
			</li>
			<li>
				<strong>Proof of Value (PoV)</strong>
				<ul>
					<li><em>Article pageview:</em> <?= $appMeta['pop-view-weight'] ?></li>
					<li><em>Article comment:</em> <?= $appMeta['pop-comment-weight'] ?></li>
					<li><em>Magic Word (podcast only):</em> <?= $appMeta['pop-listen-weight'] ?></li>
					<li><strong>Note:</strong> <?= $appMeta['pop-editor-cut'] ?>% of points from Proof of Quality and Proof of Value
							calculations are given to blog editors</li>					
				</ul>
			</li>
			<!--<li>
				<strong>Affiliate Referrals</strong>
				<ul>
					<li><em>Active Referral:</em> <?= $appMeta['pop-referral-weight'] ?></li>
				</ul>
			</li>-->
			<li>
				<strong><em>Negative</em> Proof of Participation</strong>
				<ul>
					<li>
						There are some instances where you can receive <em>negative</em> Proof of Participation points. Such as:
						<ul>
							<li>
						* If you make a forum post and it gets deleted/buried (e.g is a spam post) by our moderators, you lose a full <strong><?= $appMeta['pop-forum-post-weight'] ?> points</strong>
						for each offense.
							</li>
						</ul>
					</li>
				</ul>
		</ul>
		
		<br>
		<hr>
		<a name="leaderboard"></a>
		<h2>Leaderboards</h2>
		<?php
		echo $this->displayLeaderboards();
		?>
		
		<p><a href="#">Back to Top</a></p>
		
		<script type="text/javascript">
			$(document).ready(function(){
				$('.ltb-stat-tabs').find('.tab').click(function(e){
					e.preventDefault();
					var tab = $(this).data('tab');
					var type = $(this).parent().parent().data('tab-type');
					$('.ltb-weekly-cont.' + type).find('.ltb-data-tab').hide();
					$('.ltb-weekly-cont.' + type).find('.ltb-data-tab#' + tab).show();
					$(this).parent().parent().find('.tab').removeClass('active');
					$(this).addClass('active');
				});
				
			});
		</script>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
		
	}
	
	public function getPoolData($pool = 'pop', $fullData = false)
	{
		$sql = 'SELECT * FROM pop_reports WHERE label LIKE "%['.$pool.':%" ORDER BY startDate DESC';
		$get = $this->model->fetchAll($sql);
		$reports = array();
		foreach($get as $row){
			preg_match_all('/\[(.+?)\]/',$row['label'],$matches);
			foreach($matches[1] as $match){
				$expMatch = explode(':', $match);
				if(!isset($expMatch[1])){
					continue;
				}
				else{
					$distId = intval($expMatch[1]);
					$getDist = $this->model->get('xcp_distribute', $distId);
					if(!$getDist OR $getDist['complete'] == 0){
						continue;
					}
					else{
						$getDist['addressList'] = json_decode($getDist['addressList'], true);
						$getDist['txInfo'] = json_decode($getDist['txInfo'], true);
						$row['info'] = json_decode($row['info'], true);
						$reports[] = array('reportId' => $row['reportId'], 'report' => $row, 'distribute' => $getDist);
					}
				}
			}
		}
		
		if($fullData){
			return $reports;
		}
		
		
		$output = array();
		foreach($reports as $report){
			$newRow = array();
			$newRow['reportId'] = $report['reportId'];
			$newRow['period'] = date('M jS', strtotime($report['report']['startDate'])).' - '.date('M jS, Y', strtotime($report['report']['endDate']));
			$newRow['totalRewards'] = number_format($report['distribute']['tokenReceived'], 2);
			$newRow['totalUsers'] = number_format(count($report['report']['info']));
			$newRow['totalPoints'] = number_format($report['report']['totalPoints'], 2);
			$newRow['perPoint'] = number_format(($report['distribute']['tokenReceived'] / $report['report']['totalPoints']), 2);
			$newRow['avgEarned'] = number_format(($report['distribute']['tokenReceived'] / count($report['report']['info'])), 2);
			
			$output[] = $newRow;
		}
		
		return $output;
	}
	
	public function showReportDetails($report)
	{
		preg_match_all('/\[(.+?)\]/',$report['label'],$matches);
		$getDist = false;
		$report_type = 'pop';
		foreach($matches[1] as $match){
			$expMatch = explode(':', $match);
			if(!isset($expMatch[1])){
				continue;
			}
			else{
				$report_type = $expMatch[0];
				$distId = intval($expMatch[1]);
				$getDist = $this->model->get('xcp_distribute', $distId);
				if(!$getDist){
					continue;
				}
				else{
					$getDist['addressList'] = json_decode($getDist['addressList'], true);
					$getDist['txInfo'] = json_decode($getDist['txInfo'], true);
				}
			}
		}		
		$perPoint = 0;
		if($getDist){
			$perPoint = $getDist['tokenReceived'] / $report['totalPoints'];
		}
		$report['info'] = json_decode($report['info'], true);
		$totalNegative = 0;
		
		$checkAuth = false;
		if(isset($_SESSION['accountAuth'])){
			$checkAuth = $this->accountModel->checkSession($_SESSION['accountAuth']);
			if($checkAuth){
				$checkAuth = $this->model->get('users', $checkAuth['userId']);
			}			
		}
		
		$metrics = array();
		$useMetrics = array('posts', 'comments', 'threads', 'likes', 'views', 'magic-words', 'referrals');
		$meta = new Slick_App_Meta_Model;
		foreach($report['info'] as &$row){
			if(!isset($row['negativeScore'])){
				$row['negativeScore'] = 0;
			}
			$totalNegative += $row['negativeScore'];
			foreach($row['info'] as $metric => $val){
				if(!isset($metrics[$metric])){
					$metrics[$metric] = $val;
				}
				else{
					$metrics[$metric] += $val;
				}
			}
			foreach($useMetrics as $useM){
				if(!isset($row['info'][$useM])){
					$row['info'][$useM] = 0;
				}
			}
			$getRowUser = $this->model->get('users', $row['username'], array('userId', 'username', 'slug'), 'username');
			$row['displayname'] = $row['username'];
			if($getRowUser){
				$checkPubProf = $meta->getUserMeta($getRowUser['userId'], 'pubProf');
				if(intval($checkPubProf) !== 1){
					$row['displayname'] = '<em>anonymous</em>';
					if($checkAuth AND $checkAuth['userId'] == $getRowUser['userId']){
						$row['displayname'] = '<a href="'.$this->site['url'].'/profile/user/'.$getRowUser['slug'].'" target="_blank" title="Your profile is set to private, your username is not publicly displayed on this list, only while you are logged in."><strong>'.$row['username'].' *</strong></a>';
					}
				}
				else{
					$row['displayname'] = '<a href="'.$this->site['url'].'/profile/user/'.$getRowUser['slug'].'" target="_blank">'.$row['username'].'</a>';
				}
			}		
			if(!isset($row['info']['blog-edits'])){
				$row['info']['blog-edits'] = 0;
			}
		}
		$report['metrics'] = $metrics;
		$report['label'] = preg_replace('/\[(.?)+\]/', '', $report['label']);
		ob_start();
		?>
		<h2><?= $report['label'] ?></h2>
		<p>
			<a href="<?= $this->site['url'] ?>/ltbcoin-stats">Back to Stats</a>
		</p>
<ul class="ltb-pop-stats">
	<li class="full"><strong>Date Range:</strong> <?= formatDate($report['startDate']) ?> to <?= formatDate($report['endDate']) ?></li>
	<li class="full"><strong>Total Points Earned:</strong> <?= number_format($report['totalPoints']) ?></li>
	<?php
	if($getDist){
		$report['perPoint'] = number_format(($getDist['tokenReceived'] / $report['totalPoints']), 2);
		$report['avgEarned'] = number_format(($getDist['tokenReceived'] / count($report['info'])), 2);		
	?>
	<li class="full"><strong>LTBcoin Distributed:</strong> <?= number_format($getDist['tokenReceived'], 2) ?></li>
	<li><strong>LTBc per Point:</strong> <?= $report['perPoint'] ?></li>
	<li><strong>Average LTBc Earned:</strong> <?= $report['avgEarned'] ?></li>
	<?php
	}//endif
	foreach($report['metrics'] as $metric => $mTotal){
		$mLabel = $metric;
		switch($metric){
			case 'comments':
				$mLabel = 'Article Comments';
				break;
			case 'posts':
				$mLabel = 'Forum Replies';
				break;
			case 'threads':
				$mLabel = 'Forum Threads';
				break;
			case 'views':
				$mLabel = 'Newly Viewed Content';
				break;
			case 'register':
				$mLabel = 'New Users';
				break;
			case 'magic-words':
				$mLabel = 'Magic Words Submitted';
				break;
			case 'likes':
				$mLabel = 'Posts "liked"';
				break;
			case 'referrals':
				$mLabel = 'Active Referred Users';
				break;
			case 'blog-posts':
				$mLabel = 'Published Blog Posts';
				break;
			case 'poq':
			case 'pov':
				$mLabel = 'Published Blog Posts (PoV)';
				continue 2;
				break;
			case 'blog-edits':
				$mLabel = 'Article Contributions';
				break;
		}
		echo '<li><strong>'.$mLabel.':</strong> '.number_format($mTotal).'</li>';
	}
		$view = new Slick_App_View;
		aasort($report['info'], 'score');
		$report['info'] = array_reverse($report['info']);	

	if($report_type == 'pop'){
	?>
	<li><strong>Negative Points:</strong> <?= number_format($totalNegative) ?></li>
	<?php
	}//endif
	?>
	<li><strong>Users Participated:</strong> <?= number_format(count($report['info'])) ?></li>
</ul>		
<div class="clear"></div>
		<h3>Participants</h3>		
		<table class="public-pop-report-table data-table">
			<?php
			if($report_type == 'pop'){
			?>
			<thead>
				<tr>
					<th>Rank</th>
					<th>Username</th>
					<th>Score</th>
					<th>LTBc Earned</th>
					<th>% of Total</th>
					<th>Posts</th>
					<th>Likes</th>
					<th>Views</th>
					<th>Magic Words</th>
					<th>Active Referrals</th>
					<th>Negative</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$num = 0;
				foreach($report['info'] as $row){
					$num++;
				?>
				<tr>
					<td><?= $num ?></td>
					<td><?= $row['displayname'] ?></td>
					<td><?= number_format($row['score'], 2) ?></td>
					<td><?= number_format(($perPoint * $row['score']), 2) ?></td>
					<td><?= number_format($row['percent'], 4) ?></td>
					<td><?= ($row['info']['posts'] + $row['info']['comments'] + $row['info']['threads']) ?></td>
					<td><?= $row['info']['likes'] ?></td>
					<td><?= $row['info']['views'] ?></td>
					<td><?= $row['info']['magic-words'] ?></td>
					<td><?= $row['info']['referrals'] ?></td>
					<td><?= $row['negativeScore'] ?></td>
				</tr>
				<?php
				}//endforeach
				?>
			</tbody>
			<?php
			}
			elseif($report_type == 'poq'){
				?>
			<thead>
				<tr>
					<th>Rank</th>
					<th>Username</th>
					<th>Score</th>
					<th>LTBc Earned</th>
					<th>% of Total</th>
					<th>Posts Contributed To</th>
				</tr>
			</thead>
			<tbody>
				<?php
				
				$num = 0;
				foreach($report['info'] as $row){
					$num++;
				
				?>
				<tr>
					<td><?= $num ?></td>
					<td><?= $row['displayname'] ?></td>
					<td><?= number_format($row['score'], 2) ?></td>
					<td><?= number_format(($perPoint * $row['score']), 2) ?></td>
					<td><?= number_format($row['percent'], 4) ?></td>
					<?php
					$totalContribs = 0;
					if(!isset($row['extra'][0]['contribs'])){
						if($row['info']['blog-edits'] == 0 AND $row['info']['blog-posts'] == 0){
							//workaround for missing blog editing numbers
							$row['info']['blog-edits'] = $row['score'] / 5;
						}
						if($row['info']['blog-edits'] > $row['info']['blog-posts']){
							$totalContribs += $row['info']['blog-edits'];
						}
						else{
							$totalContribs += $row['info']['blog-posts'];
						}

					}
					else{
						foreach($row['extra'] as $extraPost){
							if($extraPost['userId'] == $row['userId']){
								$totalContribs++;
							}
							else{
								foreach($extraPost['contribs'] as $contrib){
									if($contrib['userId'] == $row['userId']){
										$totalContribs++;
										continue 2;
									}
								}
							}
						}
					}
					?>
					<td><?= $totalContribs ?></td>
				</tr>
				<?php
				}//endforeach
				?>
			</tbody>
				<?php
			}
			elseif($report_type == 'pov'){
				//debug($report);
				$povList = array();
				$site = currentSite();
				$uniquePosts = array();
				if(isset($report['info'][0]['extra'][0]['contribs'])){
					foreach($report['info'] as $rep){
						foreach($rep['extra'] as $extra){
							$postId = $extra['postId'];
							if(!in_array($postId, $uniquePosts)){
								$uniquePosts[] = $postId;
							}
							if(!isset($povList[$postId])){
								$povList[$postId] = $extra;
								$povList[$postId]['post'] = $extra;
								$povList[$postId]['score'] = $extra['total_score'];
								$povList[$postId]['contributors'] = array();
							}
							$getAuthor = $this->model->get('users', $extra['userId'], array('userId', 'username', 'slug'));
							$getAuthor['displayname'] = '<a href="'.$site['url'].'/profile/user/'.$getAuthor['slug'].'" target="_blank">'.$getAuthor['username'].'</a>';
							$povList[$postId]['contributors'][$getAuthor['userId']] = $getAuthor['displayname'];
							foreach($extra['contribs'] as $contrib){
								if(!isset($povList[$postId]['contributors'][$contrib['userId']])){
									$contribDisplay = '<a href="'.$site['url'].'/profile/user/'.$contrib['slug'].'" target="_blank">'.$contrib['username'].'</a>';
									$povList[$postId]['contributors'][$contrib['userId']] = $contribDisplay;
								}
							}
						}
					}
				}
				else{
					foreach($report['info'] as $row){
						foreach($row['extra'] as $extra){
							if(!isset($extra['post']['wordSubmits'])){
								$extra['post']['wordSubmits'] = 0;
							}
							$postId = $extra['post']['postId'];
							if(!isset($povList[$postId])){
								$povList[$postId] = $extra;
								$povList[$postId]['contributors'] = array();
							}
							$povList[$postId]['contributors'][] = $row['displayname'];
							
						}
					}
				}
				aasort($povList, 'score');
				$povList = array_reverse($povList);
				//debug($povList);
				
				?>
			<thead>
				<tr>
					<th>Rank</th>
					<th>Post</th>
					<th>Score</th>
					<th>LTBc Earned</th>
					<th>Views</th>
					<th>Comments</th>
					<th>Magic Words</th>
					<th>Contributors</th>
				</tr>
			</thead>
			<tbody>
				<?php
				
				$num = 0;
				foreach($povList as $row){
					$num++;
					
				?>
				<tr>
					<td><?= $num ?></td>
					<td><a href="<?= $this->site['url'] ?>/blog/post/<?= $row['post']['url'] ?>" target="_blank"><?= $row['post']['title'] ?></a></td>
					<td><?= number_format($row['score'], 2) ?></td>
					<td> <?= number_format($row['score'] * $perPoint, 2) ?></td>
					<td><?= number_format($row['post']['views']) ?></td>
					<td><?= number_format($row['post']['comments']) ?></td>
					<td><?= number_format($row['post']['wordSubmits']) ?></td>
					<td><?= join(', ', $row['contributors']) ?></td>
				</tr>
				<?php
				}//endforeach
				?>
			</tbody>
				<?php
			}
			?>
		</table>
		<p>
			<em>Note: Some data (mainly with old reports) may be missing some info.</em>
		</p>
		<link href="//cdn.datatables.net/1.10.3/css/jquery.dataTables.css" rel="stylesheet" />
		<script type="text/javascript" src="//cdn.datatables.net/1.10.3/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				$('.data-table').DataTable({
					searching: true,
					lengthChange: false,
					paging: true,
					iDisplayLength: 250,
				});
			});
		</script>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
		
	}
	
	public function displayLeaderboards()
	{
		$output = '';
		$popLeaders = $this->getLeaderboardData('pop');
		$povLeaders = $this->getLeaderboardData('content');
		
		$popLeaders = $this->getTopLeaders($popLeaders);
		$povLeaders = $this->getTopLeaders($povLeaders);
		
		ob_start();
		?>
		<ul class="ltb-stat-tabs" data-tab-type="leaderboard">
			<li><a href="#" class="tab active" data-tab="participation">Audience / Participation</a></li>
			<li><a href="#" class="tab" data-tab="content-creators">Content Creators</a></li>
		</ul>		
		<div class="clear"></div>
		<div class="ltb-weekly-cont leaderboard">
			<div class="ltb-data-tab" id="participation">
				<table class="public-pop-report-table data-table">
					<thead>
						<tr>
							<th>Rank</th>
							<th>Username</th>
							<th>Score</th>
							<th>LTBc Earned</th>
							<th>Posts</th>
							<th>Likes</th>
							<th>Views</th>
							<th>Magic Words</th>
							<th>Referral Rewards</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$num = 0;
						foreach($popLeaders as $user){
							$num++;
						?>
						<tr>
							<td><?= $num ?></td>
							<td><?= $user['displayname'] ?></td>
							<td><?= number_format($user['score'], 2) ?></td>
							<td><?= number_format($user['coin'], 2) ?></td>
							<td><?= ($user['metrics']['posts'] + $user['metrics']['comments'] + $user['metrics']['threads']) ?></td>
							<td><?= $user['metrics']['likes'] ?></td>
							<td><?= $user['metrics']['views'] ?></td>
							<td><?= $user['metrics']['magic-words'] ?></td>
							<td><?= $user['metrics']['referrals'] ?></td>
						</tr>
						<?php
						}//endforeach
						?>
					</tbody>
				</table>
			</div>
			<div class="ltb-data-tab" id="content-creators" style="display: none;">
				<table class="public-pop-report-table data-table">
					<thead>
						<tr>
							<th>Rank</th>
							<th>Username</th>
							<th>Score</th>
							<th>LTBc Earned</th>
							<th>Posts Written</th>
							<th>Posts Contributed To</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$num = 0;
						foreach($povLeaders as $user){
							$num++;
							$numEdited = 0;
							if(isset($user['metrics']['pov'])){

								if(!isset($user['metrics']['blog-posts'])){
									$user['metrics']['blog-posts'] = count($user['extra']);
								}
								foreach($user['extra'] as $extraRow){
									if(isset($extraRpw['post']) AND $user['userId'] != $extraRow['post']['userId']){
										$numEdited++;
									}
									else{
										
									}
								}
							}
							if(isset($user['metrics']['blog-edits'])){
								$numEdited += $user['metrics']['blog-edits'];
							}


						?>
						<tr>
							<td><?= $num ?></td>
							<td><?= $user['displayname'] ?></td>
							<td><?= number_format($user['score'], 2) ?></td>
							<td><?= number_format($user['coin'], 2) ?></td>
							<td><?= $user['metrics']['blog-posts'] ?></td>
							<td><?= $numEdited ?></td>
						</tr>
						<?php
						}//endforeach
						?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="clear"></div>
		<p><br><em>
			Note: The above data is based on the weekly participation reports and LTBcoin distributions and may not be 100% accurate,
			as the data used and included is evolving over time (such as the addition of magic words, or tracking editors for blog articles). </em>
		</p>
		<link href="//cdn.datatables.net/1.10.3/css/jquery.dataTables.css" rel="stylesheet" />
		<script type="text/javascript" src="//cdn.datatables.net/1.10.3/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				$('.data-table').DataTable({
					searching: false,
					lengthChange: false,
					paging: false,
					iDisplayLength: 250,
				});
			});
		</script>		
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		//debug($userList);
		
		return $output;
	}
	
	public function getLeaderboardData($type = 'content', $processAdditional = true)
	{
		if(isset(self::$reportData[$type])){
			return self::$reportData[$type];
		}
		$model = new Slick_App_LTBcoin_POP_Model;
		if($type == 'content'){
			$getPop = $model->fetchAll('SELECT * FROM pop_reports WHERE label LIKE "%[poq:%" OR label LIKE "%[pov:%" ORDER BY reportId DESC');
		}
		else{
			$getPop = $model->fetchAll('SELECT * FROM pop_reports WHERE label LIKE "%['.$type.':%" ORDER BY reportId DESC');
		}
		
		$meta = new Slick_App_Meta_Model;
		$tokenlyApp = get_app('ltbcoin');
		$lastReportHash = $meta->getAppMeta($tokenlyApp['appId'], 'leaderboard-hash-'.$type);
		$thisHash = false;
		if(isset($getPop[0])){
			$thisHash = md5($getPop[0]['reportId']);
		}
		if($lastReportHash == $thisHash){
			$leaderData = $meta->getAppMeta($tokenlyApp['appId'], 'leaderboard-data-'.$type, 0, true);
			$uncompress = gzuncompress($leaderData);
			$getLeaderboard = json_decode($uncompress, true);
			self::$reportData[$type] = $getLeaderboard;
			return $getLeaderboard;
		}
		
		$userList = array();
		foreach($getPop as &$row){
			$row['info'] = json_decode($row['info'], true);
			if($processAdditional){
				$row['extraInfo'] = json_decode($row['extraInfo'], true);
			}
			
			//get distribution data
			preg_match_all('/\[(.+?)\]/',$row['label'],$matches);
			$getDist = false;
			$report_type = 'pop';
			foreach($matches[1] as $match){
				$expMatch = explode(':', $match);
				if(!isset($expMatch[1])){
					continue;
				}
				else{
					$report_type = $expMatch[0];
					$distId = intval($expMatch[1]);
					$getDist = $this->model->get('xcp_distribute', $distId);
					if(!$getDist OR $getDist['complete'] == 0){
						continue;
					}
					else{
						if($processAdditional){
							$getDist['addressList'] = json_decode($getDist['addressList'], true);
							$getDist['txInfo'] = json_decode($getDist['txInfo'], true);
						}
					}
				}
			}		
			if(!$getDist){
				continue;
			}
			
			$perPoint = $getDist['tokenReceived'] / $row['totalPoints'];
			
			foreach($row['info'] as $item){
				//debug($item);
				if(!isset($item['userId'])){
					continue;
				}
				$userId = $item['userId'];
				if(!isset($userList[$userId])){
					$userList[$userId] = array('userId' => $userId, 'username' => $item['username'], 'score' => 0, 'coin' => 0, 'metrics' => array(), 'extra' => array());
				}
				$userList[$userId]['score'] += $item['score'];
				$userList[$userId]['coin'] += $item['score'] * $perPoint;
				if($processAdditional){
					if(isset($item['extra']) AND is_array($item['extra'])){
						$userList[$userId]['extra'] = array_merge($userList[$userId]['extra'], $item['extra']);
					}
					foreach($item['info'] as $metric => $count){
						if(!isset($userList[$userId]['metrics'][$metric])){
							$userList[$userId]['metrics'][$metric] = $count;
						}
						else{
							$userList[$userId]['metrics'][$metric] += $count;
						}
					}
				}
			}
		}
		aasort($userList, 'score');
		$userList = array_reverse($userList);
		
		$checkAuth = false;
		if(isset($_SESSION['accountAuth'])){
			$checkAuth = $this->accountModel->checkSession($_SESSION['accountAuth']);
			if($checkAuth){
				$checkAuth = $this->model->get('users', $checkAuth['userId']);
			}
		}
		
		if($processAdditional){
			foreach($userList as $key => $row){
				$userList[$key]['displayname'] = $row['username'];
				$getRowUser = $this->model->get('users', $row['username'], array('userId', 'username', 'slug'), 'username');
				if($getRowUser){
					$checkPubProf = $meta->getUserMeta($getRowUser['userId'], 'pubProf');
					if(intval($checkPubProf) !== 1){
						$userList[$key]['displayname'] = '<em>anonymous</em>';	
						if($checkAuth AND $checkAuth['userId'] == $getRowUser['userId']){
							$userList[$key]['displayname'] = '<a href="'.$this->site['url'].'/profile/user/'.$getRowUser['slug'].'" target="_blank" title="Your profile is set to private, your username is not publicly displayed on this list, only while you are logged in."><strong>'.$row['username'].' *</strong></a>';
						}
					}
					else{
						$userList[$key]['displayname'] = '<a href="'.$this->site['url'].'/profile/user/'.$getRowUser['slug'].'" target="_blank">'.$row['username'].'</a>';
					}
				}					
			}
			
			//save full report to meta in BLOB format
			$compressed = gzcompress(json_encode($userList), 9);
			$meta->updateAppMeta($tokenlyApp['appId'], 'leaderboard-data-'.$type, $compressed, '', 0, '', true);
			$meta->updateAppMeta($tokenlyApp['appId'], 'leaderboard-hash-'.$type, $thisHash);
		}
		
		self::$reportData[$type] = $userList;
		return $userList;		
	}
	
	public function getTopLeaders($leaders, $max = 100)
	{
		$num = 0;
		foreach($leaders as $key => $row){
			if($num >= $max){
				unset($leaders[$key]);
				continue;
			}
			$num++;
		}
		return $leaders;
	}
	
	public function showPersonalStats()
	{
		$output = '';
		$user = Slick_App_Account_Home_Model::userInfo();
		if(!$user){
			return $output;
		}
		
		$startDate = '2014-06-27 00:00:00';
		$curDate = timestamp();
		$diff = strtotime($startDate, 0) - strtotime($curDate, 0);
		$weeks = intval(abs(floor($diff / 604800))) - 1;
		$weekStart = strtotime($startDate) + (604800 * $weeks) + (86400 + 3600);
		$weekEnd = strtotime($startDate) + ((604800 * $weeks) + 604800 + (86340 + 3600));
		$weekStartDate = date('F jS, Y', $weekStart);
		$weekEndDate = date('F jS, Y', $weekEnd);
		$weekName = 'Week #'.($weeks + 1).' '.$weekStartDate.' - '.$weekEndDate;
		$timeframe = array('start' => date('Y-m-d H:i:s', $weekStart), 'end' => date('Y-m-d H:i:s', $weekEnd));
		
		$popModel = new Slick_App_LTBcoin_POP_Model;
		$getScore = $popModel->getPopScore($user['userId'], $timeframe,
											array('comments', 'posts', 'threads', 'views', 'register', 'magic-words', 'likes'));
											
		$popLeaders = $this->getLeaderboardData('pop');
		$userLeader = false;
		$num = 1;
		foreach($popLeaders as $leader){
			if($leader['userId'] == $user['userId']){
				$leader['rank'] = $num;
				$userLeader = $leader;
				break;
			}
			$num++;
		}
									
		ob_start();
		?>
		<a name="my-stats"></a>
		<h2>My Stats</h2>
		<h3>Participation</h3>
		<ul class="ltb-personal-stats">

			<li><strong>Earning Period:</strong> <?= $weekName ?></li>
			<li><strong>Current PoP Points this Period: <?= number_format($getScore['score'], 2) ?></strong> 
				<ul>
					<li>Article Comments: <?= $getScore['info']['comments'] ?></li>
					<li>Forum Threads: <?= $getScore['info']['threads'] ?></li>
					<li>Forum Replies: <?= $getScore['info']['posts'] ?></li>
					<li>"Likes" Received: <?= $getScore['info']['likes'] ?></li>
					<li>Page Views: <?= $getScore['info']['views'] ?></li>
					<li>Magic Words: <?= $getScore['info']['magic-words'] ?></li>
				</ul>
			</li>
			<?php
			if($userLeader){
			?>
			<li><strong>Leaderboard Rank:</strong> #<?= $userLeader['rank'] ?></li>
			<li><strong>Total LTBcoin Earned:</strong> <?= number_format($userLeader['coin'], 2) ?></li>
			<li><strong>Total PoP Points (as of <?= $weekStartDate ?>): <?= number_format($userLeader['score'], 2) ?></strong> 
				<ul>
					<li>Article Comments: <?= number_format($userLeader['metrics']['comments']) ?></li>
					<li>Forum Threads: <?= number_format($userLeader['metrics']['threads']) ?></li>
					<li>Forum Replies: <?= number_format($userLeader['metrics']['posts']) ?></li>
					<li>"Likes" Received: <?= number_format($userLeader['metrics']['likes']) ?></li>
					<li>Page Views: <?= number_format($userLeader['metrics']['views']) ?></li>
					<li>Magic Words: <?= number_format($userLeader['metrics']['magic-words']) ?></li>
					<li>Referral Rewards: <?= number_format($userLeader['metrics']['referrals']) ?></li>
				</ul>
			</li>
			<?php
			}//endif
			?>
		</ul>
		<br>
		<hr>
		
		
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
}
