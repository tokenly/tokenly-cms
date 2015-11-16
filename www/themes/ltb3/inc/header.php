<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title><?= $title ?> | <?= $siteName ?></title>
	<?php
	if(!isset($metaDescription)){
		$metaDescription = "The LTB Network provides a tokenized platform for podcasts, articles, and forums about the ideas, people, and projects building the new digital economy and the future of money.";
	}
	?>
	<meta name="description" content="<?= $metaDescription ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5.0, user-scalable=1">
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
	?>	
	<link rel="stylesheet" href="<?= THEME_URL ?>/css/fonts.css">
	<link rel="stylesheet" href="<?= THEME_URL ?>/css/base.css">
	<link rel="stylesheet" href="<?= THEME_URL ?>/css/legacy.css">	
	<link rel="stylesheet" href="<?= THEME_URL ?>/css/layout.css">
	<link rel="stylesheet" href="<?= THEME_URL ?>/css/jquery.fancybox.css">
	<link rel="stylesheet" href="<?= THEME_URL ?>/css/mobile-tables.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
		<!-- scripts -->
		<script type="text/javascript" src="<?= THEME_URL ?>/js/jquery.js"></script>
		<script type="text/javascript" src="<?= THEME_URL ?>/js/migrate.js"></script>
		<script type="text/javascript" src="<?= THEME_URL ?>/js/jquery-ui.js"></script>
		<script type="text/javascript" src="<?= THEME_URL ?>/js/jcycle.js"></script>
		<script type="text/javascript" src="<?= THEME_URL ?>/js/jquery.fancybox.pack.js"></script>
		<script type="text/javascript" src="<?= THEME_URL ?>/js/base64.js"></script>
		<script type="text/javascript" src="<?= THEME_URL ?>/js/jquery.jplayer.min.js"></script>
		<script type="text/javascript" src="<?= THEME_URL ?>/js/jplayer.playlist.min.js"></script>
		<script type="text/javascript" src="<?= THEME_URL ?>/js/jquery.cookie.js"></script>
		<script type="text/javascript" src="<?= THEME_URL ?>/js/mobile-tables.js"></script>
		<script type="text/javascript" src="<?= THEME_URL ?>/js/menu.js"></script>
		<?= $scripts ?>
		<script type="text/javascript">
			$(document).ready(function(){
				window.sc = '<?= SOUNDCLOUD_ID ?>';
				window.userLogged = false;
				window.siteURL = '<?= SITE_URL ?>';
				<?php
				if(isset($user) AND $user){
				?>
				window.userLogged = true;
				
				<?php
				}
				?>
				
				<?php
				//grab last 10 posts with soundcloud ids
				$scModel = new \App\API\V1\Blog_Model;
				$scPosts = $scModel->getAllPosts(array('page' => 1, 'limit' => 10, 'soundcloud-id' => 'true', 'site' => $site, 'noProfiles' => true,
														'noCategories' => true, 'noComments' => true, 'minimize' => true, 'featured' => 1));
				$mediaPlayer = array();
				foreach($scPosts as $scKey => $scPost){
					$scPost['title'] = '<a href="'.SITE_URL.'/blog/post/'.$scPost['url'].'" title="'.$scPost['title'].'" target="_blank">'.$scPost['title'].'</a>';
					$scPosts[$scKey]['title'] = $scPost['title'];
					$mediaPlayer[] = array('title' => $scPost['title'], 'url' => SITE_URL.'/blog/post/'.$scPost['url'], 'stream' => 'https://api.soundcloud.com/tracks/'.$scPost['soundcloud-id'].'/stream?client_id='.SOUNDCLOUD_ID,
										   'image' => $scPost['coverImage'], 'date' => strtoupper(date('jS F Y', strtotime($scPost['publishDate']))));
				}
				
				echo 'window.headerMedia = '.json_encode($mediaPlayer).';';
				
				?>
			});
		</script>
		<script type="text/javascript" src="<?= THEME_URL ?>/js/scripts.js"></script>
		<script type="text/javascript" src="<?= THEME_URL ?>/js/player.js"></script>
</head>
<body class="template-<?= $template ?> <?php if(isset($bodyClass)){ echo $bodyClass; } ?>">
	<div class="outer-wrap">
		<div class="slide-menuCont">
			<?= $this->displayMenu('main', 1, 'mobile-nav') ?>
		</div>
		<div class="wrap">
			<div class="roof">
				<div class="pull-right header-social">
					<?= $this->displayBlock('header-social') ?>
				</div>
			</div><!-- roof -->		
			<div class="container">
				<div class="main">
					<header>
						<div class="header-top">
							<div class="logo">
								<a href="<?= SITE_URL ?>" title="The Let's Talk Bitcoin! Network" alt="LTB Network"><img src="<?= THEME_URL ?>/images/logo.jpg" alt="" /></a>
							</div><!-- logo -->
							<div class="header-menu">
								<a href="#" class="mobile-pull">
									<i class="fa fa-chevron-circle-left"></i> Menu
								</a>								
								<div class="menu-top">
									<div class="pull-left header-links">
										<?= $this->displayTag('DISPLAY_ADSPACE', array('slug' => 'top-header-ad')) ?>
										<?= $this->displayBlock('header-extra-links') ?>										
									</div><!-- account-links -->
									<div class="pull-right account-links">
										<?php 
										$model = new \Core\Model;
										if(isset($user) AND $user){
											$numNotes = $model->fetchSingle('SELECT count(*) as total FROM user_notifications WHERE userId = :userId
																			 AND isRead = 0', array(':userId' => $user['userId']));
											$numNotes = $numNotes['total'];
											$noteLimit = '';
											$andRead = 'AND isRead = 0';
											if($numNotes <= 5){
												$andRead = '';
												$noteLimit = 'LIMIT 0,5';
											}
											$getNotes = $model->fetchAll('SELECT * FROM user_notifications WHERE userId = :userId '.$andRead.'
																		  ORDER BY noteId DESC
																		   '.$noteLimit, array(':userId' => $user['userId']));

											$notifyClass = '';
											if($numNotes > 0){
												$notifyClass = 'has-notes';
											}
										?>
										<span class="notifications <?= $notifyClass ?>">
										<a href="<?= SITE_URL ?>/account/notifications" class="notify-pull"><?= $numNotes ?></a>
										<ul class="notify-list">
											<?php
											if(count($getNotes) == 0){
												echo '<li>No Notifications</li>';
											}
											else{
												foreach($getNotes as $note){
													echo '<li>
													<div class="note-text">'.$note['message'].'</div>
													<div class="note-date">'.formatDate($note['noteDate']).'</div>
													</li>';
												}
												echo '<li><a href="'.SITE_URL.'/account/notifications">View All</a></li>';
											}
											?>

										</ul>
										</span>
										<?php }
										if(isset($user) AND $user){
										?>
											<a href="<?= SITE_URL ?>/account" class="login-pull logged-in-pull" title="Dashboard">
											<?php
											if(isset($user['meta']['avatar']) AND trim($user['meta']['avatar']) != ''){
												echo '<span class="mini-avatar"><img src="'.SITE_URL.'/files/avatars/'.$user['meta']['avatar'].'" alt="" /></span>';
											}
											?>
											<span><?= shortenMsg($user['username'],13) ?></span></a>
											<a href="<?= SITE_URL ?>/account/logout" title="Logout" style="margin-left: 10px;"><i class="fa fa-sign-out"></i></a>
										<?php
										}
										else{									
						?>	
										<a href="<?= SITE_URL ?>/account"><i class="fa fa-user"></i> Login/Register</a>
						<?php
						}//endif
						?>
									</div><!-- header-links -->
								</div><!-- menu-top -->
								<div class="main-menu">
									<?= $this->displayMenu('main', 1, 'nav') ?>
								</div><!-- main-menu -->
							</div><!-- header-menu -->
						</div><!-- header-top -->
						<div class="header-bottom">
							<span class="logo-caret"></span>
							<div class="header-actions pull-right">
								<?php if($template == 'home' OR ($template == 'blog' AND !isset($post))){ //only show on home and blog template for now
									$listViewActive = '';
									$gridViewActive = '';
							
									if(isset($_COOKIE['blog-list-type'])){
										$cookie = $_COOKIE['blog-list-type'];
										switch($cookie){
											case 'list':
												$listViewActive = 'active';
												break;
											case 'grid':
												$gridViewActive = 'active';
												break;
										}
									}
								?>								
								 <ul class="header-actions-menu">
									<li><a href="#" class="list-switch <?= $listViewActive ?>" data-switch="list" title="List view"><i class="fa fa-bars"></i></a></li>
									<li><a href="#" class="list-switch <?= $gridViewActive ?>" data-switch="grid" title="Grid view"><i class="fa fa-th"></i></a></li>
								</ul><!-- header-actions-menu -->
								<?php }//endif ?>
							</div>
							<div class="sub-header-links">
								<?php if($template == 'home' OR ($template == 'blog' AND !isset($post))){ //only show on home and blog template for now
								
								?>
								<ul class="sub-header-menu nav"> 
									<li class="children">
										<a>Sort By</a>
										<ul class="sub">
											<li><a href="?sort=new">Newest</a></li>
											<li><a href="?sort=old">Oldest</a></li>
											<li><a href="?sort=top">Top</a></li>
										</ul>
									</li>
								</ul><!-- sub-header-menu -->
								<?php }//endif ?>
							</div><!-- sub-header-links -->
						</div><!-- header-bottom -->
					</header>
