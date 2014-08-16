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
$model = new Slick_Core_Model;
$getCats = $model->getAll('forum_categories', array('siteId' => $site['siteId']), array(), 'rank', 'asc');
foreach($getCats as $cat){
	$catList .= '<li><h3>'.$cat['name'].'</h3></li>';
	$getBoards = $model->getAll('forum_boards', array('categoryId' => $cat['categoryId'], 'active' => 1), array(), 'rank', 'asc');
	foreach($getBoards as $board){
		$itemClass = '';
		if($board['slug'] == $activeBoard){
			$itemClass .= ' active';
		}
		$catList .= '<li class="'.$itemClass.'"><a href="'.SITE_URL.'/'.$app['url'].'/board/'.$board['slug'].'">'.$board['name'].'</a></li>';
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
