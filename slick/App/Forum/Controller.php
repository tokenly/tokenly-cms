<?php
namespace App\Forum;
use App\Account, App\Tokenly;
class Controller extends \App\AppControl
{
	public static $noModulePages = array('');
	
	function __construct()
	{
		parent::__construct();
		$this->model = new Model;
	}
    protected function init()
    {
		$output = parent::init();
		
		if(!$this->module){
			$output = $this->container->noModule($output);
		}
		
		if(!isset($output['template'])){
			$output['template'] = 'forum';
		}

		return $output;
    }
    
    protected function noModule($output)
    {
		if(isset($this->args[1]) AND !in_array($this->args[1], self::$noModulePages)){
			$output['view'] = '404';
			return $output;
		}
		return $this->container->forumHome($output);
	}
	
	protected function forumHome($output)
	{
		$output['categories'] = $this->model->getForumCategories($this->site, $this->app, $output['user']);
		$output['view'] = 'home'; //load forum home
		$output['title'] = $this->app['meta']['forum-title'];
		$numTopics = $this->model->fetchSingle('SELECT count(*) as total
												FROM forum_topics t
												LEFT JOIN forum_boards b ON b.boardId = t.boardId
												WHERE b.siteId = :siteId', array(':siteId' => $this->site['siteId']));
		$output['numTopics'] = $numTopics['total'];

		$numReplies = $this->model->fetchSingle('SELECT count(*) as total
												FROM forum_posts p
												LEFT JOIN forum_topics t ON t.topicId = p.topicId
												LEFT JOIN forum_boards b ON b.boardId = t.boardId
												WHERE b.siteId = :siteId', array(':siteId' => $this->site['siteId']));
		$output['numReplies'] = $numReplies['total'];
		$output['numUsers'] = $this->model->count('users');
		$output['numOnline'] = Account\Home_Model::getUsersOnline();
		$output['mostOnline'] = Account\Home_Model::getMostOnline();
		$output['onlineUsers'] = Account\Home_Model::getOnlineUsers();
		$output['forum_home'] = true;
			
		return $output;
	}
    
	protected function __install($appId)
	{
		$update = parent::__install($appId);
		if(!$update){
			return false;
		}
		
		$meta = new \App\Meta_Model;
		$meta->updateAppMeta($appId, 'forum-title', '', 'Forum Title', 1);
		$meta->updateAppMeta($appId, 'forum-description', '', 'Forum Description', 1, 'textarea');
		$meta->updateAppMeta($appId, 'topicsPerPage', 50, 'Topics Per Page', 1);
		$meta->updateAppMeta($appId, 'postsPerPage', 20, 'Topics Replies (posts) Per Page', 1);
		
		$meta->addAppPerm($appId, 'canPostTopic');
		$meta->addAppPerm($appId, 'canPostReply');
		$meta->addAppPerm($appId, 'canEditSelf');
		$meta->addAppPerm($appId, 'canBurySelf');
		$meta->addAppPerm($appId, 'canDeleteSelfTopic');
		$meta->addAppPerm($appId, 'canLockSelf');
		$meta->addAppPerm($appId, 'canEditOther');
		$meta->addAppPerm($appId, 'canBuryOther');
		$meta->addAppPerm($appId, 'canDeleteOtherTopic');
		$meta->addAppPerm($appId, 'canLockOther');
		$meta->addAppPerm($appId, 'canStickySelf');
		$meta->addAppPerm($appId, 'canStickyOther');
		$meta->addAppPerm($appId, 'canMoveSelf');
		$meta->addAppPerm($appId, 'canMoveOther');
	}
}
