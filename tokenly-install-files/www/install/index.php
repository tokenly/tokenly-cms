<?php
require_once('../../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');

class Tokenly_Install extends \Core\Model
{
	public function installForm()
	{
		$forms = array();
		
		$forms['site'] = $this->getSiteForm();
		$forms['user'] = $this->getUserForm();
		
		
		return $forms;
	}
	
	public function getSiteForm()
	{
		$model = new \App\CMS\Sites_Model;
		$form = $model->getSiteForm(1);
		$form->remove('isDefault');
		$form->remove('image');
		$form->field('name')->addAttribute('placeholder', 'My Awesome Website');
		$form->field('domain')->addAttribute('placeholder', 'example.com');
		$form->field('url')->addAttribute('placeholder', 'http://example.com');
		$form->field('apps')->setLabel('Site Apps <br><em>(Dashboard, Accounts & Pages required for working basic functionality - these can be changed whenever)</em>');
		return $form;
	}
	
	public function getUserForm()
	{
		$model = new \App\Account\Home_Model;
		$form = $model->getRegisterForm();
		return $form;
	}
	
	public function initInstall()
	{
		$getSites = $this->getAll('sites');
		$getUsers = $this->getAll('users');
		
		$useAdmin = false;
		foreach($getUsers as $user){
			if($user['slug'] == 'admin'){
				$useAdmin = $user;
			}
		}
		
		$useSite = false;
		if($useAdmin){
			foreach($getSites as $site){
				if($site['isDefault'] == 1){
					$useSite = $site;
				}
			}
		}
		
		if(!$useAdmin AND count($getSites) > 0 AND count($getUsers) > 1){
			throw new Exception('Looks like the system is installed already!');
		}
		
		if(!$useAdmin){
			//might have been a screwup.. clean up just in case
			$this->sendQuery('DELETE FROM sites');
			$this->sendQuery('DELETE FROM users');
		}
		else{
			$this->sendQuery('DELETE FROM site_apps');
			$this->sendQuery('DELETE FROM group_sites');
		}
		
		$forms = $this->installForm();
		$siteData = $forms['site']->grabData();
		$userData = $forms['user']->grabData();
		$siteReq = array('name', 'url', 'domain');
		$userReq = array('username', 'password', 'email');
		foreach($siteReq as $req){
			if(!isset($siteData[$req]) OR trim($siteData[$req]) == ''){
				throw new Exception($req.' required');
			}
		}
		foreach($userReq as $req){
			if(!isset($userData[$req]) OR trim($userData[$req]) == ''){
				throw new Exception($req.' required');
			}
		}
		
		if(!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)){
			throw new Exception('Invalid email address');
		}
		
		$reqApps = array(1,2,5);
		if(!is_array($siteData['apps'])){
			throw new Exception('Please select at least the Dashboard, Accounts and Pages apps');
		}
		foreach($reqApps as $reqApp){
			if(!in_array($reqApp, $siteData['apps'])){
				throw new Exception('Please select at least the Dashboard, Accounts and Pages apps');
			}
		}
		
		$appList = array();
		foreach($siteData['apps'] as $appId){
			$getApp = $this->get('apps', $appId);
			if(!$getApp){
				throw new Exception('App with ID '.$appId.' does not exist in database');
			}
			$appList[] = $getApp['appId'];
		}
		
		if(substr($siteData['url'], -1) == '/'){
			$siteData['url'] = substr($siteData['url'], 0, -1);
		}
		
		//create site
		if($useSite){
			$addSite = $this->edit('sites', $useSite['siteId'], array('name' => $siteData['name'], 'domain' => $siteData['domain'], 'url' => $siteData['url'], 'isDefault' => 1, 'themeId' => 1));
			if($addSite){
				$addSite = $useSite['siteId'];
			}
		}
		else{
			$addSite = $this->insert('sites', array('siteId' => 1, 'name' => $siteData['name'], 'domain' => $siteData['domain'], 'url' => $siteData['url'], 'isDefault' => 1, 'themeId' => 1));
		}
		if(!$addSite){
			throw new Exception('Error adding to sites table');
		}
		
		//add apps to site
		foreach($appList as $appId){
			$this->insert('site_apps', array('siteId' => $addSite, 'appId' => $appId));
		}
		
		//create root group and default group
		$getRoot = $this->get('groups', 'root-admin', array(), 'slug');
		$getDefault = $this->get('groups', 'default', array(), 'slug');
		
		if($getRoot){
			$addRoot = $getRoot['groupId'];
		}
		else{
			$addRoot = $this->insert('groups', array('name' => 'Root Admin', 'slug' => 'root-admin', 'siteId' => $addSite));
		}
		
		if($getDefault){
			$addDefault = $getDefault['groupId'];
		}
		else{
			$addDefault = $this->insert('groups', array('name' => 'Default', 'slug' => 'default', 'siteId' => $addSite, 'isDefault' => 1));
		}
		
		if(!$addRoot OR !$addDefault){
			throw new Exception('Error generating user groups');
		}
		
		//add basic site access to groups
		$this->insert('group_sites', array('groupId' => $addRoot, 'siteId' => $addSite));
		$this->insert('group_sites', array('groupId' => $addDefault, 'siteId' => $addSite));
		
		//add root access to everything
		if(!$getRoot){
			$getModules = $this->getAll('modules');
			$getPerms = $this->getAll('app_perms');
			foreach($getModules as $module){
				$this->insert('group_access', array('moduleId' => $module['moduleId'], 'groupId' => $addRoot));
			}
			foreach($getPerms as $perm){
				if($perm['permKey'] != 'isTroll'){
					$this->insert('group_perms', array('permId' => $perm['permId'], 'groupId' => $addRoot));
				}
			}
		}
		
		//register user
		$useData = array();
		$useData['username'] = preg_replace('/\s+/', '', $userData['username']);
		$genPass = genPassSalt($userData['password']);
		$useData['password'] = $genPass['hash'];
		$useData['spice'] = $genPass['salt'];
		$useData['regDate'] = timestamp();
		$useData['slug'] = genURL($useData['username']);		
		$useData['email'] = trim($userData['email']);
		$useData['activated'] = 1;
		
		if($useAdmin){
			$addUser = $this->edit('users', $useAdmin['userId'], $useData);
		}
		else{
			$addUser = $this->insert('users', $useData);
		}
		if(!$addUser){
			throw new Exception('Error registering account');
		}
		
		if(!$useAdmin){
			//add to root group
			$addToRoot = $this->insert('group_users', array('groupId' => $addRoot, 'userId' => $addUser));
			if(!$addToRoot){
				throw new Exception('Error adding new user to root group');
			}
			//add to default group
			$addToDefault = $this->insert('group_users', array('groupId' => $addDefault, 'userId' => $addUser));
			if(!$addToDefault){
				throw new Exception('Error adding new user to default group');
			}
		}
		
		return true;
	}
	
	
}

$install = new Tokenly_Install;
$forms = $install->installForm();
$error = '';
$success = false;
if(posted()){
	try{
		$init = $install->initInstall();
	}
	catch(Exception $e){
		$error = $e->getMessage();
		$init = false;
		$forms['site']->setValues($_POST);
		$forms['user']->setValues($_POST);
	}
	
	if($init){
		$success = true;
	}
}


?>
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title>Install Tokenly</title>
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="css/base.css">
	<link rel="stylesheet" href="css/skeleton.css">
	<link rel="stylesheet" href="css/layout.css">
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
	<div class="main">
		<div class="container">
			<h1>Install Tokenly CMS</h1>
			<hr>
			<?php
			if(!$success){
			?>
			<p>
				Complete the form below to complete CMS installation.
			</p>
			<?php
			if($error != ''){
				echo '<p class="error">'.$error.'</p>';
			}
			?>
			<form action="" method="post">
				<h2>Default Site Info</h2>
				<?= $forms['site']->displayFields() ?>
				<h2>Root Admin User</h2>
				<?= $forms['user']->displayFields() ?>
				<input type="submit" value="Complete Installation" />
			</form>
			<?php
			}//endif
			else{
				?>
			<p>
				Installation complete! Try logging in here: <a href="<?= $_POST['url'] ?>/account" target="_blank"><?= $_POST['url'] ?>/account</a>
			</p>
			<p>
				<strong>After leaving this page, please delete the install folder</strong>
			</p>
				
				<?php
			}
			?>
		</div><!-- container -->
	</div>
</body>
</html>

