<?php
namespace App\CMS;
use Core, UI, App\Profile;
class Stats_Model extends Core\Model
{
	protected function getStats()
	{
		$output = array();
		
		$output['numUsers'] = $this->count('users');
		$output['numGroups'] = $this->count('groups');
		$output['numSites'] = $this->count('sites');
		
		$output['numBlogCats'] = $this->count('blog_categories');
		$output['numBlogPosts'] = $this->count('blog_posts');
		$output['numBlogComments'] = $this->count('blog_comments');
		
		$output['numForumCats'] = $this->count('forum_categories');
		$output['numForumBoards'] = $this->count('forum_boards');
		$output['numForumTopics'] = $this->count('forum_topics');
		$output['numForumPosts'] = $this->count('forum_posts');
		
		$profModel = new Profile\User_Model;
		$output['LTBcoinUsers'] = $profModel->getUsersWithProfile(PRIMARY_TOKEN_FIELD);
		$output['numLTBcoinUsers'] = count($output['LTBcoinUsers']);
		$output['numLTBcoinUsersToday'] = 0;
		$today = date('Y-m-d');
		foreach($output['LTBcoinUsers'] as $ltbuser){
			$ltbDate = date('Y-m-d', strtotime($ltbuser['lastUpdate']));

			if($ltbDate == $today){
				$output['numLTBcoinUsersToday']++;
			}
		}
		return $output;
	}
}
