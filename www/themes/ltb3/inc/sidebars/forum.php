<div class="sidebar-inner-content">
	<div style="margin-bottom: 20px;">
		<?= $this->displayTag('DISPLAY_ADSPACE', array('slug' => 'networkforum-sidebar')) ?>					
	</div>	
	<h2>Network <span>Forums</span></h2>
<?php
if(isset($forum_home)){
	unset($board);
}

$activeBoard = '';
if(isset($board)){
	$activeBoard = $board['slug'];
}

$realBoard = null;
if(isset($board)){
	$realBoard = $board;		
}
$catList = '';
$model = new \App\Forum\Board_Model;
$boardModule = get_app('forum.forum-board');
$tca = new \App\Tokenly\TCA_Model;
$getCats = $model->getAll('forum_categories', array('siteId' => $site['siteId']), array(), 'rank', 'asc');
$meta = new \App\Meta_Model;
$tokenApp = $model->get('apps', 'tokenly', array(), 'slug'); 
$tokenSettings = $meta->appMeta($tokenApp['appId']); 
foreach($getCats as $cat){
	$checkTCA = $tca->checkItemAccess($user, $boardModule['moduleId'], $cat['categoryId'], 'category');
	if(!$checkTCA){
		continue;
	}	
	
	$getBoards = $model->getAll('forum_boards', array('categoryId' => $cat['categoryId'], 'active' => 1), array(), 'rank', 'asc');
	foreach($getBoards as $bk => $board){
		$checkTCA = $tca->checkItemAccess($user, $boardModule['moduleId'], $board['boardId'], 'board');
		if(!$checkTCA){
			unset($getBoards[$bk]);
			continue;
		}
	}
	if(count($getBoards) > 0){
		$catClass = '';
		if($cat['categoryId'] == $tokenSettings['tca-forum-category']){
			$catClass = 'tcv-category';
		}
		$catActive = '';
		$catCaret = 'right';
		if(isset($realBoard['categoryId']) AND is_array($realBoard) AND $realBoard['categoryId'] == $cat['categoryId']){
			$catActive = 'active';
			$catCaret = 'down collapse';
		}
		$catList .= '<li class="children '.$catActive.'"><i class="fa fa-caret-'.$catCaret.'" title="Click to expand or collapse"></i> <a href="#">'.$cat['name'].'</a>
						<ul class="sub">';
		foreach($getBoards as $board){
			$itemClass = $catClass;
			if($board['slug'] == $activeBoard){
				$itemClass .= ' active';
			}
			$boardImage = '';
			$access_token = extract_row($model::$boardMeta, array('boardId' => $board['boardId']));

			if(count($access_token) > 0){
				$access_token = $access_token[0];
				$getAsset = $model->get('xcp_assetCache', $access_token['value'], array(), 'asset');
				if($getAsset){
					if(trim($getAsset['image']) != ''){
						$boardImage = '<span class="mini-board-img"><img  src="'.$data['site']['url'].'/files/tokens/'.$getAsset['image'].'" alt="" /></span>';
					}
				}
			}			
			$catList .= '<li class="'.$itemClass.'"><a href="'.SITE_URL.'/'.$app['url'].'/board/'.$board['slug'].'">'.$boardImage.$board['name'].'</a></li>';
		}
		$catList .= '</ul></li>';
	}
}
$board = $realBoard;

$forumMenu = array();
$forumMenu[] = array('url' => SITE_URL.'/forum', 'label' => 'Home');
$forumMenu[] = array('url' => SITE_URL.'/forum/board/all', 'label' => 'Recent Posts');
			$forumMenu[] = array('url' => SITE_URL.'/forum-search', 'label' => 'Search');
			
$homeLinkCaret = 'right';
$homeLinkActive = '';
if($realBoard['slug'] == 'all' OR $realBoard['slug'] == 'subscriptions' OR $realBoard['slug'] == 'tca-posts'){
	$homeLinkCaret = 'down collapse';
	$homeLinkActive = 'active';
}

$recentActive = '';
if($realBoard['slug'] == 'all'){
	$recentActive = 'active';
}

echo '<div class="forum-menu">
	<ul class="side-menu">
		<li class="children '.$homeLinkActive.'"><i class="fa fa-caret-'.$homeLinkCaret.'"></i> <a href="'.SITE_URL.'/forum">Forum Home</a>
		<ul class="sub">
		<li class="'.$recentActive.'"><a href="'.SITE_URL.'/forum/board/all">Recent Posts</a></li>';

if($user){
	
	$subscribeActive = '';
	if($realBoard['slug'] == 'subscriptions'){
		$subscribeActive = 'active';
	}
	
	$tcaActive = '';
	if($realBoard['slug'] == 'tca-posts'){
		$tcaActive = 'active';
	}	
	
	echo '
		<li class="'.$subscribeActive.'"><a href="'.SITE_URL.'/forum/board/subscriptions">Subscribed Posts</a></li>
		<li class="'.$tcaActive.'"><a href="'.SITE_URL.'/forum/board/tca-posts">TCA Board Posts</a></li>
		';
}
echo '</ul></li>';
echo $catList;
echo '</ul></div>';
?>
</div><!-- sidebar-inner-content -->
