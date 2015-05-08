<?php
include(THEME_PATH.'/inc/header.php');

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
$boardModule = $model->get('modules', 'forum-board', array(), 'slug');
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
		$catList .= '<li><h3>'.$cat['name'].'</h3></li>';
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
						$boardImage = '<img class="mini-board-img" src="'.$data['site']['url'].'/files/tokens/'.$getAsset['image'].'" alt="" />';
					}
				}
			}			
			$catList .= '<li class="'.$itemClass.'"><a href="'.SITE_URL.'/'.$app['url'].'/board/'.$board['slug'].'">'.$boardImage.$board['name'].'</a></li>';
		}
	}
}
$board = $realBoard;
						

?>

<div class="main forums">
	<div class="container">
		<div class="title-bar">
			<h1>LTB Forums</h1>
		</div><!-- title-bar -->
		<div class="forum-bar">
			<div class="forum-mobile-pull-cont">
				<a href="#" class="forum-mobile-pull"><i class="fa fa-bars"></i></a>
			</div>
			<?php
			$forumMenu = array();
			$forumMenu[] = array('url' => SITE_URL.'/forum', 'label' => 'Home');
			$forumMenu[] = array('url' => SITE_URL.'/forum/board/all', 'label' => 'Recent Posts');
                        $forumMenu[] = array('url' => SITE_URL.'/forum-search', 'label' => 'Search');
			echo $this->displayMenu($forumMenu, 0, '', $pageRequest['params']);
			?>
		</div><!-- forum-bar -->
		<div class="forum-mobile-menu">
			<div class="sidebar forum-sidebar">
				<ul class="forum-menu">
					<li>
						<ul class="sub">
							<?= $catList ?>
						</ul>
					</li>
				</ul>
			</div><!-- sidebar -->
		</div>
		<div class="main-content">
			<div class="content">
			<?php include($viewPath); ?>
			</div><!-- content -->
		</div><!-- main-content -->
		<h2 class="forum-sidebar-title">Categories</h2>
		<div class="sidebar forum-sidebar">
			
			<ul class="forum-menu">
				<li>
					<ul class="sub">
						<?= $catList ?>
					</ul>
				</li>
			</ul>
		</div><!-- sidebar -->
		<div class="clear"></div>
	</div><!-- container -->
</div><!-- main -->
<?php
include(THEME_PATH.'/inc/footer.php');
?>
