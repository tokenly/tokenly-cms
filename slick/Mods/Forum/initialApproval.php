<?php

\App\Forum\Controller::$noModulePages[] = 'approve-user';

\Util\Filter::addFilter('App\Forum\Board_Model', 'postTopic', 
    function($data, $appData){
		$meta = new \App\Meta_Model;
		if(!isset($appData['user']['meta']['forum_approved']) OR intval($appData['user']['meta']['forum_approved']) == 0){
			//not approved to post on forums, auto mark this user a potential spammer
			$appData['perms']['isTroll'] = true;
			$meta->updateUserMeta($appData['user']['userId'], 'needs_forum_approval', 1);
			//check if any previous spam posts
			$get_spam = $meta->getAll('forum_topics', array('userId' => $appData['user']['userId'], 'trollPost' => 1), array('topicId'));
			$get_spam2 = $meta->getAll('forum_posts', array('userId' => $appData['user']['userId'], 'trollPost' => 1), array('postId'));
			if(($get_spam AND count($get_spam) > 0) OR ($get_spam2 AND count($get_spam2) > 0)){
				throw new \Exception('You have 1 or more posts awaiting moderator approval before you may continue posting on this forum');
			}
		}
		
		
		//continue with rest of processing code
        return array($data, $appData);
    },
 true);
			
\Util\Filter::addFilter('App\Forum\Post_Model', 'postReply', 
    function($data, $appData){
		$meta = new \App\Meta_Model;
		if(!isset($appData['user']['meta']['forum_approved']) OR intval($appData['user']['meta']['forum_approved']) == 0){
			//not approved to post on forums, auto mark this user a potential spammer
			$appData['perms']['isTroll'] = true;
			$meta->updateUserMeta($appData['user']['userId'], 'needs_forum_approval', 1);
			//check if any previous spam posts
			$get_spam = $meta->getAll('forum_topics', array('userId' => $appData['user']['userId'], 'trollPost' => 1), array('topicId'));
			$get_spam2 = $meta->getAll('forum_posts', array('userId' => $appData['user']['userId'], 'trollPost' => 1), array('postId'));
			if(($get_spam AND count($get_spam) > 0) OR ($get_spam2 AND count($get_spam2) > 0)){
				throw new \Exception('You have 1 or more posts awaiting moderator approval before you may continue posting on this forum');
			}
		}
		
		
		//continue with rest of processing code
        return array($data, $appData);
    },
 true);
 
 
\Util\Filter::addFilter('App\Forum\Controller', 'forumHome', 
    function($output){
		if(isset($this->args[1]) AND $this->args[1] == 'approve-user'){
			if($output['user'] AND isset($output['perms']) AND $output['perms']['canApproveUsers']){
				$userId = intval($this->args[2]);
				$get_user = user($userId);
				$site = currentSite();
				if(intval($get_user['meta']['needs_forum_approval']) == 1){
					$meta = new \App\Meta_Model;
					$meta->updateUserMeta($userId, 'forum_approved', 1);
					$meta->updateUserMeta($userId, 'needs_forum_approval', 0);
					$meta->sendQuery('UPDATE forum_topics SET trollPost = 0 WHERE userId = :id', array(':id' => $userId));
					$meta->sendQuery('UPDATE forum_posts SET trollPost = 0 WHERE userId = :id', array(':id' => $userId));
					redirect($site['url'].'/profile/user/'.$get_user['slug']);
				}
			}
		}
		
        return $output;
    });
