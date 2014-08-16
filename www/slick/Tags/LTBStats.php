<?php
class Slick_Tags_LTBStats
{
	function __construct()
	{
		$this->model = new Slick_App_Meta_Model;
		
	}
	
	public function display()
	{
		
		$model = $this->model;
		$xcp = new Slick_API_Bitcoin(XCP_CONNECT);
		
		$stats = array();
		try{
			$getAsset = $xcp->get_asset_info(array('assets' => array('LTBCOIN')));
			$asset = $getAsset[0];
			$stats['totalIssued'] = $asset['supply'] / SATOSHI_MOD;
			
			$balances = $xcp->get_balances(array('filters' => array('field' => 'asset', 'op' => '==', 'value' => 'LTBCOIN')));
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
		
		$getExchange = json_decode(file_get_contents('https://www.melotic.com/api/markets/ltbc-btc/ticker'), true);
		if($getExchange AND isset($getExchange['latest_price'])){
			$stats['latestPrice'] = convertFloat($getExchange['latest_price']);
			$stats['volume'] = $getExchange['volume'];
			
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
			<li><strong>Latest BTC/LTBcoin Price:</strong> <?= $stats['latestPrice'] ?> BTC / 1 LTBc (melotic)</li>
			<li><strong>24h Exchange Volume:</strong> <?= round($stats['volume'], 3) ?> BTC (melotic)</li>
			<li><strong>Market Cap:</strong> <?= $stats['marketCap'] ?>
			<li><strong>Days Since Launch:</strong> <?= $stats['launchDays'] ?> (<?= $stats['launchWeeks'] ?> weeks)</li>
			<li><a href="https://docs.google.com/spreadsheets/d/1GzytNblMx8xBmUczX7sPC8QitkJrrRirNrh2AJtjy1Q/edit#gid=508171322" target="_blank">Distribution Schedule</a></li>
			<li><a href="http://blockscan.com/assetInfo.aspx?q=LTBCOIN" target="_blank">Blockscan</a></li>
			<li><a href="http://joelooney.org/ltbcoin/" target="_blank">LTBc Toolbox</a></li>
		</ul>
		<br>
		<hr>
		<a name="weekly"></a>
		<h2>Weekly Distributions</h2>
			<ul class="ltb-stat-tabs">
				<li><a href="#" class="tab active" data-tab="pop">PoP</a></li>
				<li><a href="#" class="tab" data-tab="poq">PoQ</a></li>
				<li><a href="#" class="tab" data-tab="pov">PoV</a></li>
				<li><a href="#" class="tab" data-tab="ref">Affiliates</a></li>
			</ul>
			<div class="clear"></div>
			<div class="ltb-weekly-cont">
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
					<li><em>Post "like" received:</em> <?= $appMeta['pop-like-weight'] ?></li>
					<li><em>Newly registered (one time):</em> <?= $appMeta['pop-register-weight'] ?></li>
					<li>Note: comments, posts and "likes" have diminishing returns the more frequently they are done.<br>
							<a href="https://docs.google.com/document/d/1L7HmE8IupFiSrfqk9BgNa4Zg9XogqtScyQjTw0k2xCc" target="_blank">See here</a>
							 for more information
				</ul>
			</li>
			<li>
				<strong>Proof of Quality (PoQ)</strong>
				<ul>
					<li><em>Published article:</em> <?= $appMeta['pop-publish-weight'] ?></li>
				</ul>
			</li>
			<li>
				<strong>Proof of Value (PoV)</strong>
				<ul>
					<li><em>Article pageview:</em> <?= $appMeta['pop-view-weight'] ?></li>
					<li><em>Article comment:</em> <?= $appMeta['pop-comment-weight'] ?></li>
				</ul>
			</li>
			<li>
				<strong>Affiliate Referrals</strong>
				<ul>
					<li><em>Active Referral:</em> <?= $appMeta['pop-referral-weight'] ?></li>
				</ul>
			</li>
		</ul>
		
		<br>
		<hr>
		<a name="leaderboard"></a>
		<h2>Leaderboards</h2>
		<p><em>Coming soon!</em></p>
		
		<p><a href="#">Back to Top</a></p>
		
		<script type="text/javascript">
			$(document).ready(function(){
				$('.ltb-stat-tabs').find('.tab').click(function(e){
					e.preventDefault();
					var tab = $(this).data('tab');
					$('.ltb-data-tab').hide();
					$('.ltb-data-tab#' + tab).show();
					$('.ltb-stat-tabs').find('.tab').removeClass('active');
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
						$reports[] = array('report' => $row, 'distribute' => $getDist);
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
			$newRow['period'] = date('Y/m/d', strtotime($report['report']['startDate'])).' <br>to<br> '.date('Y/m/d', strtotime($report['report']['endDate']));
			$newRow['totalRewards'] = number_format($report['distribute']['tokenReceived'], 2);
			$newRow['totalUsers'] = number_format(count($report['report']['info']));
			$newRow['totalPoints'] = number_format($report['report']['totalPoints'], 2);
			$newRow['perPoint'] = number_format(($report['distribute']['tokenReceived'] / $report['report']['totalPoints']), 2);
			$newRow['avgEarned'] = number_format(($report['distribute']['tokenReceived'] / count($report['report']['info'])), 2);
			
			$output[] = $newRow;
		}
		
		return $output;
	}
	
	
}
