<?php
include(THEME_PATH.'/inc/header.php');
?>
<div class="main">
	<div class="container">
		<div class="title-bar">
			<h1><?= $title ?></h1>
		</div><!-- title-bar -->
		<div class="main-content full">
			<div class="content">
				<?php include($viewPath); ?>
			</div><!-- content -->
		</div><!-- main-content -->
		<div class="clear"></div>
	</div><!-- container -->
</div><!-- main -->
<?php
include(THEME_PATH.'/inc/footer.php');
?>
