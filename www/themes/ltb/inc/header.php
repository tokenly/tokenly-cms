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
	<link rel="stylesheet" href="<?= THEME_URL ?>/css/base.css">
	<link rel="stylesheet" href="<?= THEME_URL ?>/css/layout.css">
	<link rel="stylesheet" href="<?= THEME_URL ?>/css/mobile-tables.css">
	<link rel="stylesheet" href="<?= THEME_URL ?>/css/jquery.fancybox.css">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css">
	<link href='https://fonts.googleapis.com/css?family=PT+Sans+Narrow:700' rel='stylesheet' type='text/css'>
	<?php
	if(isset($canonical)){
	?>
		<link rel="canonical" href="<?= $canonical ?>" />
	<?php
	}//endif
	?>
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script type="text/javascript" src="<?= THEME_URL ?>/js/jquery.js"></script>
	<script type="text/javascript" src="<?= THEME_URL ?>/js/migrate.js"></script>
	<script type="text/javascript" src="<?= THEME_URL ?>/js/jquery-ui.js"></script>
	<script type="text/javascript" src="<?= THEME_URL ?>/js/jcycle.js"></script>
	<script type="text/javascript" src="<?= THEME_URL ?>/js/jquery.fancybox.pack.js"></script>
	<script type="text/javascript" src="<?= THEME_URL ?>/js/base64.js"></script>
	<script type="text/javascript" src="<?= THEME_URL ?>/js/mobile-tabls.js"></script>
	<script type="text/javascript" src="<?= THEME_URL ?>/js/jquery.jplayer.min.js"></script>
	<script type="text/javascript" src="<?= THEME_URL ?>/js/jplayer.playlist.min.js"></script>
	<script type="text/javascript" src="<?= SITE_URL ?>/resources/ckeditor/ckeditor.js"></script>
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
				$mediaPlayer[] = array('title' => $scPost['title'], 'url' => SITE_URL.'/blog/post/'.$scPost['url'], 'stream' => 'https://api.soundcloud.com/tracks/'.$scPost['soundcloud-id'].'/stream?client_id='.SOUNDCLOUD_ID);
			}
			
			echo 'window.headerMedia = '.json_encode($mediaPlayer).';';
			
			?>
		});
	</script>
	<script type="text/javascript" src="<?= THEME_URL ?>/js/scripts.js"></script>
	<script type="text/javascript" src="<?= THEME_URL ?>/js/player.js"></script>
	<?= $scripts ?>
</head>
<body class="body-<?= $template ?>">
	<div class="header">
		<div class="header-wrap">
			<div class="container">
				<div class="logo">
					<a href="<?= SITE_URL ?>"></a>
				</div><!-- logo -->
				<div class="header-main">
					<div class="mobile-header">
                                            <a href="#" class="menu-pull" title="Menu"><i class="fa fa-bars"></i></a>
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
						<a href="<?= SITE_URL ?>/account" class="login-pull logged-in-pull" title="Dashboard"><i class="fa fa-gear"></i><span><?= $user['username'] ?></span></a>
						<?php
						}
						else{
						?>
						<a href="<?= SITE_URL ?>/account" class="login-pull" title="Login"><i class="fa fa-sign-in"></i></a>
						<?php
						}
						?>
					</div>
					<div class="header-top">
						<div class="nav-cont">
							<?php
							echo $this->displayMenu('main', 1, 'menu', $pageRequest['params']);
							?>
							<div class="user-panel">
								<?php
								
								if(isset($user) AND $user){
								

									
									?>
									<a href="<?= SITE_URL ?>/account"><?= $user['username'] ?></a> 
									
									<span class="notifications <?= $notifyClass ?>">
										<a href="#" class="notify-pull"><?= $numNotes ?></a>
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
									<a href="<?= SITE_URL ?>/account/logout">Logout</a>
									<?php
								}
								else{
									?>
									<a href="<?= SITE_URL ?>/account">Login / Register</a>
									<?php
								}
								?>
							</div>
							<div class="search">
								<!--<form action="" method="get">
									<input type="text" placeholder="Search LTB" />
									<input type="submit" value="" />
								</form>-->
							</div><!-- search -->
						</div><!-- nav-cont -->
						<div class="clear"></div>
					</div><!-- header-top -->
					<div class="header-bottom">
						<div class="sub-nav">
							<?php
							echo $this->displayMenu('header-sub', 0, '', $pageRequest['params']);
							?>

						</div><!-- sub-nav -->
						<div class="header-social">
							<?= $this->displayBlock('header-social') ?>
						</div>
						<div class="media-player-cont">
							<div class="media-player-holder"></div>
							<div class="media-player">
								<div class="player-controls">
									<span class="prev" title="Previous"><i class="fa fa-step-backward"></i></span>
									<span class="pause jp-pause" style="display: none;" title="Pause"><i class="fa fa-pause"></i></span>
									<span class="play jp-play" title="Play"><i class="fa fa-play"></i></span>
									<span class="next" title="Next"><i class="fa fa-step-forward"></i></span>
								</div>
								<div class="track-title">
									<span class="track"><?= @$scPosts[0]['title'] ?></span>
								</div>
								<div class="player-pop">
									<span title="Pop out media player" class="pop-out"><i class="fa fa-caret-square-o-up"></i></span>
								</div>
							</div><!-- media-player -->
						</div><!-- media-player-cont -->
						<div class="clear"></div>
					</div><!-- header-bottom --> 
				</div><!-- header-main -->
			</div><!-- container -->
		</div><!-- header-wrap -->
		<div class="header-shadow"></div>
	</div><!-- header -->
	<div class="mobile-nav" style="display: none;">
		<div class="container">
			<?php
			echo $this->displayMenu('main', 1, 'mobile-menu', $pageRequest['params']);
			?>
			<?php
			echo $this->displayMenu('header-sub', 0, 'mobile-menu', $pageRequest['params']);
			?>
		</div>
	</div>	<!-- mobile-nav -->
	<div class="container">
		<div class="ltb-top-ad">
			<?= $this->displayBlock('top-ad') ?>
		</div>
	</div>
