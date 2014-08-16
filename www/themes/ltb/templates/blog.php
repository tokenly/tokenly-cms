<?php
include(THEME_PATH.'/inc/header.php');
$catModel = new Slick_App_Dashboard_BlogCategory_Model;
$getCats = array_merge(array(array('url' => SITE_URL.'/blog', 'label' => 'All')), $catModel->getCategories($site['siteId'], 0, 1));
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
