<?php
class Slick_App_Forum_Model extends Slick_Core_Model
{
	public function getForumCategories($site, $app)
	{
		$siteId = $site['siteId'];
		$getCats = $this->getAll('forum_categories', array('siteId' => $siteId), array(), 'rank', 'asc');
		$profModel = new Slick_App_Profile_User_Model;
		foreach($getCats as $k => $cat){
			$getBoards = $this->getAll('forum_boards', array('categoryId' => $cat['categoryId'], 'siteId' => $siteId,
														'active' => 1), array(), 'rank', 'asc');
			foreach($getBoards as $bk => $board){
				$getBoards[$bk]['numTopics'] = $this->count('forum_topics', 'boardId', $board['boardId']);
				$countReplies = $this->fetchSingle('SELECT COUNT(*) as total 
													FROM forum_posts p
													LEFT JOIN forum_topics t ON t.topicId = p.topicId
													WHERE t.boardId = :boardId', array(':boardId' => $board['boardId']));
				$getBoards[$bk]['numReplies'] = $countReplies['total'];
				$lastTopic = $this->fetchSingle('SELECT * 
												FROM forum_topics
												WHERE boardId = :id
												ORDER BY topicId DESC
												LIMIT 1', array(':id' => $board['boardId']));
				$lastPost = $this->fetchSingle('SELECT p.* 
												FROM forum_posts p
												LEFT JOIN forum_topics t ON t.topicId = p.topicId
												WHERE t.boardId = :id AND p.buried = 0
												ORDER BY p.postId DESC
												LIMIT 1', array(':id' => $board['boardId']));
												
				$topicTime = 0;
				if($lastTopic){
					$topicTime = strtotime($lastTopic['postTime']);
				}
				$postTime = 0;
				if($lastPost){
					$postTime = strtotime($lastPost['postTime']);
				}
				
				if($topicTime === 0 AND $postTime === 0){
					$getBoards[$bk]['mostRecent'] = '';
				}
				elseif($topicTime > $postTime){
					//recent topic
					$lastAuthor = $profModel->getUserProfile($lastTopic['userId'], $site['siteId']);
					
					$getBoards[$bk]['mostRecent'] = '<a href="'.$site['url'].'/'.$app['url'].'/post/'.$lastTopic['url'].'"  title="'.str_replace('"', '', shorten(strip_tags($lastTopic['content']), 150)).'">'.$lastTopic['title'].'</a> by
													<a href="'.$site['url'].'/profile/user/'.$lastAuthor['slug'].'">'.$lastAuthor['username'].'</a>';
				}
				else{
					//recent post
					$lastAuthor = $profModel->getUserProfile($lastPost['userId'], $site['siteId']);
					$lastTopic = $this->get('forum_topics', $lastPost['topicId']);
					$numReplies = $this->count('forum_posts', 'topicId', $lastPost['topicId']);
					$numPages = ceil($numReplies / $app['meta']['postsPerPage']);
					$andPage = '';
					if($numPages > 1){
						$andPage = '?page='.$numPages;
					}
					$getBoards[$bk]['mostRecent'] = 'Reply to <a href="'.$site['url'].'/'.$app['url'].'/post/'.$lastTopic['url'].$andPage.'#post-'.$lastPost['postId'].'" title="'.str_replace('"', '', shorten(strip_tags($lastPost['content']), 150)).'">'.$lastTopic['title'].'</a> by
													<a href="'.$site['url'].'/profile/user/'.$lastAuthor['slug'].'">'.$lastAuthor['username'].'</a>';
				}
				
			}
			$getCats[$k]['boards'] = $getBoards;
		}
		
		return $getCats;
	}
	
	
}
