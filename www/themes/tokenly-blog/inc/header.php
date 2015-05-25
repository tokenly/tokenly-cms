<?php
$model = new \Core\Model;
$getBlog = $model->get('blogs', 'tokenly', array(), 'slug');
$blogCats = $model->getAll('blog_categories', array('blogId' => $getBlog['blogId']));
?><!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title><?= $title ?> | <?= $siteName ?></title>
	<?php
	if(!isset($metaDescription)){
		$metaDescription = "Let's Talk Bitcoin is a twice weekly show about the ideas, people and projects building the new digital economy and the future of money.";
	}
	
	?>
	<meta name="description" content="<?= $metaDescription ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<?php
	if($template == 'blog' AND isset($post)){
		$post_url = $site['url'].'/'.$app['url'].'/'.$module['url'].'/'.$post['url'];
		if(isset($canonical)){
			$post_url = $canonical;
		}
		//extra social media fields for Open Graph
		?>
		<meta property="og:site_name" content="Let's Talk Bitcoin" />
		<meta property="og:title" content="<?= $title ?>" />
		<meta property="og:type" content="article" />
		<meta property="og:url" content="<?= $post_url ?>" />
		<?php
		if($post['coverImage'] != ''){
			?>
			<meta property="og:image" content="https://letstalkbitcoin.com/files/blogs/<?= $post['coverImage'] ?>" />
			<?php
		}
		$og_desc = strip_tags($post['excerpt']);
		if($post['formatType'] == 'markdown'){
			$og_desc = strip_tags(markdown($post['excerpt']));
		}
		if(isset($post['social-summary']) AND trim($post['social-summary']) != ''){
			$og_desc = strip_tags($post['social-summary']);
		}
		?>
		<meta property="og:description" content="<?= $og_desc ?>" />
		<?php
		if(isset($post['twitter-summary']) AND trim($post['twitter-summary']) != ''){
		?>
			<meta property="twitter:description" content="<?= strip_tags($post['twitter-summary']) ?>" />
		<?php
		}//endif
		$authorName = $post['author']['username'];
		if(isset($post['author']['profile']['real-name']) AND trim($post['author']['profile']['real-name']['value']) != ''){
			$authorName =  $post['author']['profile']['real-name']['value'];
		}		
		?>
			<meta name="author" content="<?= $authorName ?>" />
			<meta property="article:author" content="<?= SITE_URL ?>/profile/user/<?= $post['author']['slug'] ?>" />
		<?php
	}
	
	if($module AND $module['slug'] == 'user-profile' AND isset($profile)){
		if(isset($profile['profile']['real-name']) AND trim($profile['profile']['real-name']['value']) != ''){
			echo '<meta property="profile:first_name" content="'.$profile['profile']['real-name']['value'].'" />';
		}
		echo '<meta property="profile:username" content="'.$profile['username'].'" />';
	}
	?>
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
		<ul class="icon-list">
			<li><a href="https://github.com/tokenly" title="Tokenly Github" target="_blank"><i class="fa fa-github-alt"></i></a></li>
			<li><a href="<?= SITE_URL ?>/forum/board/all" title="Forums" target="_blank"><i class="fa fa-comments"></i></a></li>
			<li><a href="mailto:team@tokenly.com" title="Contact Us"><i class="fa fa-envelope"></i></a></li>
		</ul><!-- icon-list -->
		<div class="logo">
			<div class="logo-title">
				<a href="<?= SITE_URL ?>/blog/<?= $getBlog['slug'] ?>"><i class="fa fa-btc"></i> Tokenly</a>
			</div><!-- logo-title -->
			<div class="logo-tagline">
				Thinking with tokens; tokenizing the web
			</div><!-- logo-tagline -->
		</div><!-- logo -->
		<div class="menu-cont">
			<ul class="menu">
				<li><a href="<?= SITE_URL ?>/blog/<?= $getBlog['slug'] ?>">Home</a></li>
				<li><a href="<?= SITE_URL ?>">Back to LTB Network</a></li>
				<?php
				foreach($blogCats as $cat){
					echo '<li><a href="'.SITE_URL.'/blog/category/'.$cat['slug'].'">'.$cat['name'].'</a></li>';
				}
				?>
			</ul><!-- menu -->
		</div><!-- menu-cont -->
	</div><!-- left-bar -->
	
	<div class="main">
