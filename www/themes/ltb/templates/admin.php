<?php
include(THEME_PATH.'/inc/header.php');
$menu = Slick_App_Dashboard_DashMenu_Model::getDashMenu();
$msgModel = new Slick_App_Account_Message_Model;
$numMessages = $msgModel->getNumUnreadMessages($user['userId']);
					
$menuStr = '';
foreach($menu as $heading => $items){
	if(trim($heading) != ''){
		$menuStr .= '<li><h3>'.$heading.'</h3><ul>';
	}
	else{
		$menuStr .=  '<li><h3>Menu</h3>';
	}

	foreach($items as $item){
		if($item['label'] == 'Private Messages' AND $numMessages > 0){
			$item['label'] .= ' <strong>('.$numMessages.')</strong>';
		}
		if($item['label'] == 'Notifications' AND isset($numNotes) AND $numNotes > 0){
			$item['label'] .= ' <strong>('.$numNotes.')</strong>';
		}
		$menuStr .=  '<li><a href="'.$item['url'].'">'.$item['label'].'</a></li>';
	}
	if(trim($heading) != ''){
		$menuStr .=  '</ul></li>';
	}
}
					
?>
<div class="main admin">
	<div class="container">
		<div class="title-bar">
			<div class="mobile-dash-pull"><a href="#" class="dash-pull"><i class="fa fa-gears"></i></a></div>
			<h1>Dashboard</h1>
		</div><!-- title-bar -->
		<div class="mobile-dash-menu">
			<div class="sidebar">
				<div class="blog-sidebar">
					<div class="dash-menu">
						<ul>
							<?= $menuStr ?>
						</ul>
					</div>
				</div>
			</div><!-- sidebar -->
		</div>
		<div class="main-content">
			<div class="content">
			<?php include($viewPath); ?>
			</div><!-- content -->
		</div><!-- main-content -->
		<div class="sidebar">
			<div class="blog-sidebar">
				<div class="dash-menu">
					<ul>
						<?= $menuStr ?>
					</ul>
				</div>
			</div>
		</div><!-- sidebar -->
		<div class="clear"></div>
	</div><!-- container -->
</div><!-- main -->
<?php
include(THEME_PATH.'/inc/footer.php');
?>
