<?php
$scripts .= '<script type="text/javascript" src="'.SITE_URL.'/resources/ckeditor/ckeditor.js"></script>';
$profileModel = new \App\Profile\User_Model;
$meta = new \App\Meta_Model;
$user['profile'] = $profileModel->getUserProfile($user['userId']);
if(isset($user['profile']['profile'])){
	$user['profile'] = $user['profile']['profile'];
}
$display_name = $user['username'];
$show_username = false;
if(isset($user['profile']['real-name']) AND trim($user['profile']['real-name']['value']) != ''){
	$display_name = $user['profile']['real-name']['value'];
	$show_username = $user['username'];
}

$statuses = array('online' => 'text-success',
				  'away' => 'text-pending',
				  'busy' => 'text-progress',
				  'offline' => 'text-error');

if(isset($_GET['update-hud-status']) AND isset($_GET['status']) AND isset($statuses[$_GET['status']])){
	$update = $meta->updateUserMeta($user['userId'], 'custom_status', $_GET['status']);
	$output = array();
	header('Content-type: text/json');
	$output['result'] = $update;
	ob_end_clean();
	echo json_encode($output);
	die();
}
if(isset($_GET['hide-dash-hud'])){
	$hide_hud = intval($_GET['hide-dash-hud']);
	if($hide_hud === 0){
		$_SESSION['show_dash_hud'] = true;
	}
	elseif($hide_hud === 1){
		$_SESSION['show_dash_hud'] = false;
	}
}
$dash_model = new \App\Dashboard\Model;
$current_menu_label = $dash_model->getModuleDashName($module['moduleId']);
$mock_app = $dash_model->get('apps', $module['appId']);
//dd($data);

$dash_menu = \App\Dashboard\DashMenu_Model::getDashMenu();
$menu_apps = array();
foreach($dash_menu as $mk => $mv){
	$headIcon = '';
	switch($mk){
		case 'Account':
			$headIcon = 'user';
			break;
		case 'Accountant':
			$headIcon = 'book';
			break;
		case 'Ad Manager':
			$headIcon = 'pie-chart';
			break;
		case 'Blog':
			$headIcon = 'edit';
			break;
		case 'CMS':
			$headIcon = 'gears';
			break;
		case 'Forum':
			$headIcon = 'comments';
			break;
		case 'RSS':
			$headIcon = 'rss';
			break;
		case 'Store':
			$headIcon = 'shopping-cart';
			break;
		case 'Tokenly':
			$headIcon = 'bitcoin';
			break;
	}
	$menu_apps[] = array('slug' => genURL($mk), 'name' => $mk, 'icon' => $headIcon);
}

$msgModel = new \App\Account\Message_Model;
$numMessages = $msgModel->getNumUnreadMessages($user['userId']);		

if(!isset($user['meta']['avatar']) OR trim($user['meta']['avatar']) == ''){
	$user['meta']['avatar'] = 'default.jpg';
}

$bodyClass = 'full-template';
if($module AND $module['slug'] == 'dash-home'){
	$bodyClass .= ' dash-home';
}

include(THEME_PATH.'/inc/header.php');

?>
	</div><!-- main -->
	<?php
	include(THEME_PATH.'/inc/sidebar.php');
	?>
	<div class="full-content">
		<div class="content">
			<div class="dashboard-cont">
				<h1>Dashboard</h1>
				<hr>
				<div class="dash-controls pull-right">
					<?php
					if(isset($_SESSION['show_dash_hud']) AND !$_SESSION['show_dash_hud']){
						echo '<a href="#" title="Show account data" id="hide-hud-data" class="collapse"><i class="fa fa-chevron-circle-down"></i></a>';
					}
					else{
						echo '<a href="#" title="Hide account data" id="hide-hud-data"><i class="fa fa-chevron-circle-up"></i></a>';
					}
					?>
					<a href="<?= SITE_URL ?>/account/settings" title="Edit account settings"><i class="fa fa-cog"></i></a>
					<a href="<?= SITE_URL ?>/account/logout" title="Sign out"><i class="fa fa-sign-out"></i></a>
				</div><!-- dash-controls -->				
				<div class="dashboard-hud" <?php if(isset($_SESSION['show_dash_hud']) AND !$_SESSION['show_dash_hud']){ echo 'style="display: none;"'; } ?> >
					<div class="dash-user-data">
						<div class="user-avatar mini-avatar">
							<a href="<?= SITE_URL ?>/account/home"><img src="<?= SITE_URL ?>/files/avatars/<?= $user['meta']['avatar'] ?>" alt="" /></a>
						</div><!-- user-avatar -->
						<div class="user-info">
							<h3 class="user-name"><?= $display_name ?>
							<?php
							if($show_username){
								echo '<span class="hud-alt-username">('.$show_username.')</span>';
							}
							?>
							</h3>
							<div class="user-info-col user-info-left">
								<?php
								$this->includeView('inc/group-title', array('profile' => $user));
								if(isset($user['meta']['pop_score_cache'])){
									echo '<span class="user-rating" title="Total Proof of Participation Earned"><i class="fa fa-comment"></i> '.number_format(round($user['meta']['pop_score_cache'])).' PoP</span>';
								}
								else{
									echo '<span class="user-rating" title="Total Proof of Participation Earned"><i class="fa fa-comment"></i> 0 PoP</span>';
								}
								if(isset($user['meta']['poq_score_cache'])){
									echo '<span class="user-rating" title="Total Proof of Quality/Publication Earned"><i class="fa fa-thumbs-o-up"></i> '.number_format(round($user['meta']['poq_score_cache'])).' PoQ</span>';
								}
								else{
									echo '<span class="user-rating" title="Total Proof of Quality/Publication Earned"><i class="fa fa-thumbs-o-up"></i> 0 PoQ</span>';
								}
								if(isset($user['meta']['pov_score_cache'])){
									echo '<span class="user-rating" title="Total Proof of Value Earned"><i class="fa fa-star"></i> '.number_format(round($user['meta']['pov_score_cache'])).' PoV</span>';
								}	
								else{
									echo '<span class="user-rating" title="Total Proof of Value Earned"><i class="fa fa-star"></i> 0 PoV</span>';
								}															
								?>
								
							</div><!-- user-info-left -->
							<div class="user-info-col user-info-right">
								<span class="user-profile-link"><a href="<?= SITE_URL ?>/profile/user/<?= $user['slug'] ?>" target="_blank">View profile</a> <a href="<?= SITE_URL ?>/account/profile" title="Edit profile"><i class="fa fa-pencil"></i></a></span>
								<span class="user-status">
									Status: 
									<select id="hud-status-select">
										<?php
										$custom_status_class = 'text-success';
										foreach($statuses as $status => $class){
											$status_select = '';
											if(isset($user['meta']['custom_status']) AND $status == $user['meta']['custom_status']){
												$status_select = 'selected';
												$custom_status_class = $class;
											}
											echo '<option value="'.$status.'" data-class="'.$class.'" '.$status_select.'>'.ucfirst($status).'</option>';
										}
										?>
									</select>	
									<i id="hud-status-circle" class="fa fa-circle <?= $custom_status_class ?>"></i>						
									<span class="hud-status-loading"></span>
								</span>
								<?php
								if(isset($user['affiliate']['userId'])){
									echo '<span class="user-sponsor">Referred by: <a href="'.SITE_URL.'/profile/user/'.$user['affiliate']['slug'].'" target="_blank">'.$user['affiliate']['username'].'</a></span>';
								}
								$rewards_address = false;
								if(isset($user['profile']) AND is_array($user['profile'])){
									foreach($user['profile'] as $fk => $field){
										if($field['fieldId'] == PRIMARY_TOKEN_FIELD AND trim($field['value']) != ''){
											$rewards_address = $field['value'];
											break;
										}
									}
								}
								if(!$rewards_address){
									echo '<span class="user-rewards-address">Visit your <a href="'.SITE_URL.'/account/settings" target="_blank">account settings</a> to opt-in to the LTBcoin rewards program (<a href="http://ltbcoin.com" target="_blank">learn more</a>)</span>';
								}
								else{
									echo '<span class="user-rewards-address">
											LTBC rewards: '.$rewards_address.'
											<a href="https://blockscan.com/address/'.$rewards_address.'" target="_blank" title="View on block explorer">
												<i class="fa fa-info-circle"></i>
											</a>
											<a href="#hud_qr" class="fancy" target="_blank">
												<i class="fa fa-qrcode"  title="Show QR code"></i>
											</a>
										</span>';
									echo '<div id="hud_qr" style="display: none;">
											<p class="text-center">
												<img src="'.SITE_URL.'/qr.php?q='.$rewards_address.'" alt="" style="width: 200px;" /><br>
												<strong><a href="bitcoin:'.$rewards_address.'">'.$rewards_address.'</a></strong><br>
												<a href="https://blockchain.info/address/'.$rewards_address.'" target="_blank">Blockchain.info</a><br>
												<a href="https://chain.so/address/'.$rewards_address.'" target="_blank">Chain.so</a><br>
												<a href="https://blockscan.com/address/'.$rewards_address.'" target="_blank">Blockscan</a>
											</p>
											</div>';
								}
								?>
							</div><!-- user-info-right -->
							<div class="clear"></div>
						</div>
					</div><!-- dash-user-data -->
					<div class="clear"></div>
				</div><!-- dashboard-hud -->
				<div class="dashboard-menu-cont">
					<div class="dash-menu-row">
						<div class="dash-menu-select">
							<span class="dash-menu-icon" id="dash-menu-icon">
								<?php
								$first_menu_app = array('name' => 'Account', 'icon' => 'user', 'slug' => 'account');
								foreach($menu_apps as $m_app){
									if($mock_app AND $mock_app['name'] == $m_app['name']){
										$first_menu_app = $m_app;
									}
								}
								?>
								<a href="#" id="dash-menu-selector"><i class="fa fa-<?= $first_menu_app['icon'] ?>" id="selector-app"></i> <?= $first_menu_app['name'] ?> <i class="fa fa-chevron-down" id="selector-caret"></i></a>
								<ul style="display: none;" id="dash-menu-module-list">
									<?php
									foreach($menu_apps as $m_app){
										$item_class = '';
										if($mock_app AND $mock_app['name'] == $m_app['name']){
											$item_class = 'active';
										}										
										echo '<li class="'.$item_class.'"><a href="#" data-app="'.$m_app['slug'].'"><i class="fa fa-'.$m_app['icon'].'"></i> '.$m_app['name'].'</a></li>';
									}
									?>
								</ul>
							</span>					
						</div>
						<?php
				
						$num = 0;
						foreach($dash_menu as $m_app => $items){
							$dash_style = 'display: none;';
							if((!$mock_app AND $num == 0) OR ($mock_app AND $mock_app['name'] == $m_app)){
								$dash_style = '';
							}
							echo '<ul class="dashboard-menu" id="menu-'.genURL($m_app).'" style="'.$dash_style.'">';
							foreach($items as $item){
								$item_class = '';
								if($current_menu_label AND $current_menu_label == $item['label']){
									$item_class .= ' active';
								}
								if($item['label'] == 'Private Messages' AND $numMessages > 0){
									$item['label'] .= ' <strong>('.$numMessages.')</strong>';
								}
								if($item['label'] == 'Notifications' AND isset($numNotes) AND $numNotes > 0){
									$item['label'] .= ' <strong>('.$numNotes.')</strong>';
								}								
								echo '<li class="'.$item_class.'"><a href="'.$item['url'].'">'.$item['label'].'</a></li>';
							}
							echo '</ul>';
							$num++;
						}
						?>											
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
				</div><!-- dashboard-menu-cont -->
				<div class="clear"></div>
			</div><!-- dashboard-cont -->
			<div class="admin-content">
				<?php include($viewPath); ?>
			</div><!-- content -->			
		</div><!-- content -->
	</div><!-- full-content -->
	<div class="mobile-sidebar">
		<?php
		include(THEME_PATH.'/inc/sidebar.php');
		?>		
	</div>
	<script type="text/javascript">
		$(document).ready(function(e){
			$('#hud-status-select').change(function(e){
				var this_class = $(this).find(':selected').data('class');
				var this_status = $(this).val();
				$('#hud-status-circle').attr('class', 'fa fa-circle ' + this_class);
				var url = '?update-hud-status=1&status=' + this_status;
				$.get(url, function(data){

				});
			});
			
			$('#dash-menu-selector').click(function(e){
				e.preventDefault();
				if($(this).hasClass('active')){
					$(this).removeClass('active');
					$('#dash-menu-module-list').hide();
				}
				else{
					$(this).addClass('active');
					$('#dash-menu-module-list').show();
				}
			});
			
			$('html').click(function(e){
				if(e.target.id != 'dash-menu-module-list' && e.target.id != 'dash-menu-selector' && e.target.id != 'dash-menu-icon' 
					&& e.target.id != 'selector-app' && e.target.id != 'selector-caret'){
					$('#dash-menu-module-list').hide();
					$('#dash-menu-selector').removeClass('active');
				}
			});
			
			$('#dash-menu-module-list').find('a').click(function(e){
				e.preventDefault();
				var app = $(this).data('app');
				$('ul.dashboard-menu').hide();
				$('#menu-' + app).show();
				var active_app = $(this).html();
				$('#dash-menu-selector').html(active_app + ' <i class="fa fa-chevron-down"></i>');
			});
			
			$('#hide-hud-data').click(function(e){
				e.preventDefault();
				if($(this).hasClass('collapse')){
					$('.dashboard-hud').slideDown();
					$(this).removeClass('collapse');
					$(this).find('i').attr('class', 'fa fa-chevron-circle-up');
					$(this).attr('title', 'Hide account data');
					var url = '?hide-dash-hud=0';
				}
				else{
					$('.dashboard-hud').slideUp();
					$(this).addClass('collapse');
					$(this).find('i').attr('class', 'fa fa-chevron-circle-down');
					$(this).attr('title', 'Show account data');
					var url = '?hide-dash-hud=1';
				}
				$.get(url, function(data){
				});
				
			});
		});
	</script>
<?php
include(THEME_PATH.'/inc/footer-full.php');
