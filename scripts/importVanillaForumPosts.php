<?php
/*
* Imports categories/boards/posts/users from "Vanilla" forum software
* 
* Assumes you have a localhost database copy of the forums named "vanilla"
*
*/
ini_set('display_errors', 1);
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');

$model = new Slick_Core_Model;
$meta = new Slick_App_Meta_Model;
$oldDB = new PDO('mysql:host='.MYSQL_HOST.';dbname=vanilla;charset=utf8', MYSQL_USER, MYSQL_PASS);
$oldDB->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);


$getOldBoards = $oldDB->prepare('SELECT * FROM GDN_Category');
$getOldBoards->execute();
$getOldBoards = $getOldBoards->fetchAll(PDO::FETCH_ASSOC);



$boards = array();
foreach($getOldBoards as $board){
	if($board['UrlCode'] == 'root'){
		continue;
	}
	
	$bData = array();
	$bData['categoryId'] = 0;
	$bData['name'] = $board['Name'];
	$bData['slug'] = $board['UrlCode'];
	$bData['rank'] = $board['Sort'];
	$bData['description'] = $board['Description'];
	$bData['siteId'] = 1;
	$bData['active'] = 1;
	
	$get = $model->get('forum_boards', $bData['slug'], array(), 'slug');
	if($get){
		echo 'Board exists - '.$bData['name']."\n";
		$bData['boardId'] = $get['boardId'];
		$bData['oldBoard'] = $board['CategoryID'];
		$boards[] = $bData;
		continue;
	}
	
	$add = $model->insert('forum_boards', $bData);
	if($add){
		echo 'Board added ('.$add.') - '.$bData['name']."\n";
		$bData['boardId'] = $add;
		$bData['oldBoard'] = $board['CategoryID'];
		$boards[] = $bData;
	}
	else{
		echo 'Failed adding board: '.$bData['name']."\n";
		continue;
	}
}


$getOldTopics = $oldDB->prepare('SELECT * FROM GDN_Discussion');
$getOldTopics->execute();
$getOldTopics = $getOldTopics->fetchAll(PDO::FETCH_ASSOC);

$topics = array();
foreach($getOldTopics as $topic){
	$tData = array();
	$tData['boardId'] = 0;
	foreach($boards as $board){
		if($board['oldBoard'] == $topic['CategoryID']){
			$tData['boardId'] = $board['boardId'];
		}
	}
	$tData['title'] = $topic['Name'];
	$tData['url'] = genURL($topic['Name']);
	$tData['content'] = $topic['Body'];
	$tData['locked'] = $topic['Closed'];
	$tData['postTime'] = $topic['DateInserted'];
	$tData['editTime'] = $topic['DateUpdated'];
	$tData['lastPost'] = $topic['DateLastComment'];
	$tData['sticky'] = $topic['Announce'];
	$tData['views'] = $topic['CountViews'];
	$tData['lockTime'] = null;
	$tData['lockedBy'] = 0;
	
	//find user
	$topicUser = $oldDB->prepare('SELECT * FROM GDN_User WHERE UserID = :id');
	$topicUser->execute(array(':id' => $topic['InsertUserID']));
	$topicUser = $topicUser->fetch(PDO::FETCH_ASSOC);
	
	if(!$topicUser){
		echo 'Old user ID not found..'.$topic['InsertUserID']."\n";
		continue;
	}
	
	$getUser = $model->get('users', $topicUser['Email'], array(), 'email');
	if($getUser){
		$tData['userId'] = $getUser['userId'];
	}
	else{
		//generate a new user..
		$user = array();
		$user['username'] = $topicUser['Name'];
		$user['email'] = $topicUser['Email'];
		$randPass = mt_rand(0,10000).':'.time().$user['username'].$topicUser['Password'];
		$genPass = genPassSalt($randPass);
		$user['password'] = $genPass['hash'];
		$user['spice'] = $genPass['salt'];
		$user['regDate'] = $topicUser['DateFirstVisit'];
		$user['auth'] = '';
		$user['lastAuth'] = $topicUser['DateLastActive'];
		$user['lastActive'] = $topicUser['DateLastActive'];
		$user['slug'] = genURL($user['username']);
		
		$addUser = $model->insert('users', $user);
		if(!$addUser){
			echo 'Error adding user for topic ['.$tData['title'].'] - '.$user['username']."\n";
			continue;
		}
		else{
			echo 'User created for ['.$tData['title'].'] - '.$user['username'].' -- password: '.$randPass."\n";
		}
		
		$meta->updateUserMeta($addUser, 'site_registered', 'ltbcoin.com');
		$meta->updateUserMeta($addUser, 'pubProf', 1);
		$meta->updateUserMeta($addUser, 'emailNotify', 1);
		$meta->updateUserMeta($addUser, 'IP_ADDRESS', $topicUser['LastIPAddress']);
		
		$tData['userId'] = $addUser;
		
	}
	
	$getTopic = $model->get('forum_topics', $tData['url'], array(), 'url');
	if($getTopic){
		$tData['topicId'] = $getTopic['topicId'];
		$tData['oldTopic'] = $topic['DiscussionID'];
		$topics[] = $tData;
		echo 'Topic exists ('.$tData['topicId'].'): '.$tData['title']."\n";
		continue;
	}
	
	$addTopic = $model->insert('forum_topics', $tData);
	if(!$addTopic){
		echo 'Failed adding topic.. '.$tData['title']."\n";
		continue;
	}
	else{
		echo 'Topic added! ('.$addTopic.') '.$tData['title']."\n";
		$tData['topicId'] = $addTopic;
		$tData['oldTopic'] = $topic['DiscussionID'];
		$topics[] = $tData;
	}
	
}

$getOldPosts = $oldDB->prepare('SELECT * FROM GDN_Comment');
$getOldPosts->execute();
$getOldPosts = $getOldPosts->fetchAll(PDO::FETCH_ASSOC);

$posts = array();
foreach($getOldPosts as $post){
	
	$pData = array();
	foreach($topics as $topic){
		if($topic['oldTopic'] == $post['DiscussionID']){
			$pData['topicId'] = $topic['topicId'];
		}
	}
	if(!isset($pData['topicId'])){
		echo 'Discussion not found for post '.$post['CommentID']."\n";
		continue;
	}
	
	$pData['content'] = $post['Body'];
	$pData['buried'] = 0;
	$pData['postTime'] = $post['DateInserted'];
	$pData['editTime'] = $post['DateUpdated'];
	
	//find user
	$postUser = $oldDB->prepare('SELECT * FROM GDN_User WHERE UserID = :id');
	$postUser->execute(array(':id' => $post['InsertUserID']));
	$postUser = $postUser->fetch(PDO::FETCH_ASSOC);
	
	if(!$postUser){
		echo 'Old user ID not found..'.$post['InsertUserID']."\n";
		continue;
	}
	
	$getUser = $model->get('users', $postUser['Email'], array(), 'email');
	if($getUser){
		$pData['userId'] = $getUser['userId'];
	}
	else{
		//generate a new user..
		$user = array();
		$user['username'] = $postUser['Name'];
		$user['email'] = $postUser['Email'];
		$randPass = mt_rand(0,10000).':'.time().$user['username'].$postUser['Password'];
		$genPass = genPassSalt($randPass);
		$user['password'] = $genPass['hash'];
		$user['spice'] = $genPass['salt'];
		$user['regDate'] = $postUser['DateFirstVisit'];
		$user['auth'] = '';
		$user['lastAuth'] = $postUser['DateLastActive'];
		$user['lastActive'] = $postUser['DateLastActive'];
		$user['slug'] = genURL($user['username']);
		
		$addUser = $model->insert('users', $user);
		if(!$addUser){
			echo 'Error adding user for post ['.$post['CommentID'].'] - '.$user['username']."\n";
			continue;
		}
		else{
			echo 'User created for post: ['.$post['CommentID'].'] - '.$user['username'].' -- password: '.$randPass."\n";
		}
		
		$meta->updateUserMeta($addUser, 'site_registered', 'ltbcoin.com');
		$meta->updateUserMeta($addUser, 'pubProf', 1);
		$meta->updateUserMeta($addUser, 'emailNotify', 1);
		$meta->updateUserMeta($addUser, 'IP_ADDRESS', $postUser['LastIPAddress']);
		
		$pData['userId'] = $addUser;
	}
	
	if(!isset($pData['userId'])){
		echo 'No user set for post: ['.$post['CommentID'].']'."\n";
		continue;
	}
	
	$checkPost = $model->getAll('forum_posts', array('userId' => $pData['userId'], 'topicId' => $pData['topicId'], 'postTime' => $pData['postTime']));
	if($checkPost AND count($checkPost) > 0){
		echo 'Post already exists ['.$post['CommentID'].'] - '.$checkPost[0]['postId']."\n";
		continue;
	}
	
	$addPost = $model->insert('forum_posts', $pData);
	if(!$addPost){
		echo 'Failed adding post: '.$post['CommentID']."\n";
		continue;
	}
	else{
		$pData['postId'] = $addPost;
		$pData['oldPost'] = $post['CommentID'];
		$posts[] = $pData;
		echo 'Posted added! '.$post['CommentID'].' - '.$addPost."\n";
		
	}
	
}

