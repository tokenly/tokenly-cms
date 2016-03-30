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
		$metaDescription = "";
	}
	?>
	<meta name="description" content="<?= $metaDescription ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5.0, user-scalable=1">
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
		<script type="text/javascript" src="<?= THEME_URL ?>/js/jquery.cookie.js"></script>
		<script type="text/javascript" src="<?= THEME_URL ?>/js/mobile-tables.js"></script>
		<script type="text/javascript" src="<?= THEME_URL ?>/js/menu.js"></script>
		<?= $scripts ?>
		<script type="text/javascript" src="<?= THEME_URL ?>/js/scripts.js"></script>
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
								<a href="<?= SITE_URL ?>" title="TokenTalk.org" alt="LTB Network">
									<?php
									$logo = THEME_URL.'/images/logo.jpg';
									if(isset($site['image']) AND $site['image'] != ''){
										$logo = SITE_URL.'/files/sites/'.$site['image'];
									}
									?>
									<img src="<?= $logo ?>" alt="" />
								</a>
							</div><!-- logo -->
							<div class="header-menu">
								<a href="#" class="mobile-pull">
									<i class="fa fa-chevron-circle-left"></i> Menu
								</a>								
								<div class="menu-top">
									<div class="pull-left header-links">
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
											<a href="<?= SITE_URL ?>/account/auth/logout" title="Logout" style="margin-left: 10px;"><i class="fa fa-sign-out"></i></a>
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
								
							</div>
							<div class="sub-header-links">

							</div><!-- sub-header-links -->
						</div><!-- header-bottom -->
					</header>
