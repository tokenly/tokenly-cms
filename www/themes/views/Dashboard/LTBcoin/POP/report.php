<h1>Proof of Participation Report #<?= $report['reportId'] ?></h1>
<?php
if(trim($report['label']) != ''){
	echo '<h2>'.$report['label'].'</h2>';
}
?>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<ul>
	<li><strong>Date Range:</strong> <?= formatDate($report['startDate']) ?> to <?= formatDate($report['endDate']) ?></li>
	<li><strong>Generated On:</strong> <?= formatDate($report['reportDate']) ?></li>
	<li><strong>Total PoP Points Earned:</strong> <?= $report['totalPoints'] ?></li>
	<?php
	foreach($report['metrics'] as $metric => $mTotal){
		$mLabel = $metric;
		switch($metric){
			case 'comments':
				$mLabel = 'Total Article Comments';
				break;
			case 'posts':
				$mLabel = 'Total Forum Replies';
				break;
			case 'threads':
				$mLabel = 'Total Forum Threads';
				break;
			case 'views':
				$mLabel = 'Total Newly Viewed Content';
				break;
			case 'register':
				$mLabel = 'Total New Users';
				break;
			case 'magic-words':
				$mLabel = 'Total Magic Words Submitted';
				break;
			case 'likes':
				$mLabel = 'Total Posts "liked"';
				break;
			case 'referrals':
				$mLabel = 'Total Active Referred Users';
				break;
			case 'blog-posts':
				$mLabel = 'Users w/ Published Blog Posts';
				break;
			case 'poq':
			case 'pov':
				$mLabel = 'Users w/ Published Blog Posts (PoV)';
				break;
		}
		echo '<li><strong>'.$mLabel.':</strong> '.number_format($mTotal).'</li>';
	}
	?>
	<li><strong># Users Participated:</strong> <?= number_format(count($report['info'])) ?></li>
</ul>
<hr>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/edit/<?= $report['reportId'] ?>"><strong>Edit Label</strong></a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/download/<?= $report['reportId'] ?>" target="_blank"><strong>Download .CSV Report</strong></a>
</p>

<?php


foreach($report['info'] as &$row){
	$row['username'] .= '<ul>';
	foreach($row['info'] as $k => $v){
		if($k == 'views'){
			$k = 'viewed new content';
		}
		if($k == 'register'){
			$k = 'newly registered';
			if($v == 0){
				$v = 'no';
			}
			else{
				$v = 'yes';
			}
		}
		if($k == 'poq' OR $k == 'pov'){
			$k = 'PoV';
			$vList = '<ul>';
			$totalViews = 0;
			$totalComments = 0;
			$totalWords = 0;
			foreach($row['extra'] as $extraRow){
				if(isset($extraRow['views'])){
					$totalViews += $extraRow['views'];
				}
				if(isset($extraRow['comments'])){
					$totalComments += $extraRow['comments'];
				}
				if(isset($extraRow['wordSubmits'])){
					$totalWords = $extraRow['wordSubmits'];
				}
			
			}
			$vList .= '<li># Posts: '.count($row['extra']).'</li>';
			$vList .= '<li>Post Views: '.$totalViews.'</li>';
			$vList .= '<li>Comments: '.$totalComments.'</li>';
			if($totalWords > 0){
				$vList .= '<li>Magic Words: '.$totalWords.'</li>';
			}
			$vList .= '</ul>';
			$v = $vList;
		}
		$row['username'] .= '<li><strong>'.$k.':</strong> '.$v.'</li>';
	}
	if($row['negativeScore'] > 0){
		$row['username'] .= '<li><strong>Negative Points:</strong> '.$row['negativeScore'].'</li>';
	}
	$row['username'] .= '</ul>';
}


$table = $this->generateTable($report['info'], array('fields' => array('username' => 'Username', 'score' => 'Score', 'percent' => '% of Total',
																	'address' => 'Address'),
													'class' => 'pop-report-table'));
echo $table->display();



?>
