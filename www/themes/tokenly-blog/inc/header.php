<?php
if(!isset($blog) OR !$blog){
	die('This theme is currently only functional as a multi-blog custom theme');
}
$model = new \Core\Model;
$blogCats = $model->getAll('blog_categories', array('blogId' => $blog['blogId']));
if(!isset($metaDescription)){
	$metaDescription = '';
}
?><!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title><?= $title ?> | <?= $siteName ?></title>
	<meta name="description" content="<?= $blog['settings']['meta_description'] ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel='stylesheet' id='editor-fonts-css'  href='//fonts.googleapis.com/css?family=Source+Sans+Pro%3A400%2C600%2C700%2C400italic%2C600italic%2C700italic%7CRoboto+Condensed%3A300%2C400%2C700%2C300italic%2C400italic%2C700italic&#038;subset=latin%2Clatin-ext' type='text/css' media='all' />
	<link rel="stylesheet" href="<?= THEME_URL ?>/css/base.css">
	<link rel="stylesheet" href="<?= THEME_URL ?>/css/screen.css">
	<link rel="stylesheet" href="<?= THEME_URL ?>/css/layout.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
<div class="wrap">
	<div class="left-bar">
		<?= $blog['settings']['header_html'] ?>
		<div class="logo">
			<div class="logo-title">
				<?php
				$logoText = $blog['name'];
				if(trim($blog['image']) != '' AND file_exists(SITE_PATH.'/files/blogs/'.$blog['image'])){
					$logoText = '<img src="'.SITE_URL.'/files/blogs/'.$blog['image'].'" alt="'.$blog['name'].'" />';
				}
				$logoLink = '<a href="'.SITE_URL.'/blog/'.$blog['slug'].'">'.$logoText.'</a>';
				echo $logoLink;
				?>
			</div><!-- logo-title -->
			<div class="logo-tagline">
				<?= $blog['settings']['blog_tagline'] ?>
			</div><!-- logo-tagline -->
		</div><!-- logo -->
		<div class="menu-cont">
			<ul class="menu">
				<li><a href="<?= SITE_URL ?>/blog/<?= $blog['slug'] ?>">Home</a></li>
				<li><a href="<?= SITE_URL ?>">Back to LTB Network</a></li>
				<?php
				$loginText = 'Login/Register';
				$loginExtra = '?r=/blog/'.$blog['slug'];
				if(isset($user) AND $user){
					$loginText = 'My Dashboard';
					$loginExtra = '';
				}
				echo '<li><a href="'.route('account.home').$loginExtra.'">'.$loginText.'</a></li>';
				?>
				<?php
				foreach($blogCats as $cat){
					echo '<li><a href="'.SITE_URL.'/blog/category/'.$cat['slug'].'">'.$cat['name'].'</a></li>';
				}
				?>
			</ul><!-- menu -->
		</div><!-- menu-cont -->
	</div><!-- left-bar -->
	
	<div class="main">
