<?php
include(THEME_PATH.'/inc/header.php');
$catModel = new Slick_App_Dashboard_BlogCategory_Model;
$categories = $catModel->getCategories($site['siteId'], 0, 1);
$tca = new Slick_App_LTBcoin_TCA_Model;
$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
foreach($categories as $ck => $cv){
	$checkCatTCA = $tca->checkItemAccess($user, $catModule['moduleId'], $cv['categoryId'], 'blog-category');
	if(!$checkCatTCA){
		unset($categories[$ck]);
		continue;
	}
}
$getCats = array_merge(array(array('url' => SITE_URL.'/blog', 'label' => 'All')), $categories);
$getArchive = $catModel->getArchiveList($site['siteId']);
?>
<div class="main">
	<div class="container">
		<div class="title-bar">
			<div class="blog-mobile-pull">
				<a class="blog-pull" href="#"><i class="fa fa-bars"></i></a>
			</div>
			<h1>LTB Blog</h1>
		</div><!-- title-bar -->
		<div class="blog-mobile-nav">
			<div class="sidebar">
				<div class="blog-sidebar">
					<h3>Categories</h3>
					<?php

					echo $this->displayMenu($getCats, 1, '', $pageRequest['params']);
					?>
					<h3>Archive</h3>
					<?php
					
					echo $this->displayMenu($getArchive, 1, '', $pageRequest['params']);
					
					?>
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
				<h3>Categories</h3>
				<?php

				echo $this->displayMenu($getCats, 1, '', $pageRequest['params']);
				?>
				<h3>Archive</h3>
				<?php
				
				echo $this->displayMenu($getArchive, 1, '', $pageRequest['params']);
				
				?>
			</div>
		</div><!-- sidebar -->
		<div class="clear"></div>
	</div><!-- container -->
</div><!-- main -->

<?php
include(THEME_PATH.'/inc/footer.php');
?>
