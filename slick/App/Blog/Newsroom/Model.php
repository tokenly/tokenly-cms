<?php
namespace App\Blog;
use Core, UI, Util, API, App\Tokenly, App\Profile, App\Account;
class Newsroom_Model extends Core\Model
{
	public static $posts = false;
	public static $blogs = false;
	public static $post_cats = false;
	public static $categories = false;
	public static $roles = false;
	public static $contribs = false;
	public static $comments = false;
	public static $words = false;
	
	function __construct()
	{
		parent::__construct();
		$this->site = currentSite();
		$this->multiblogs = new Multiblog_Model;
		if(!self::$posts){
			self::$posts = $this->fetchAll('SELECT p.postId, p.title, p.url, p.userId, p.siteId, p.postDate,
												p.publishDate, p.image, p.coverImage, p.views, p.commentCount,
												p.commentCheck, p.formatType, p.status, p.trash, p.version, p.editTime, p.editedBy
										FROM blog_posts p
										WHERE p.siteId = :siteId AND p.trash = 0
										ORDER BY p.postId DESC
										', array(':siteId' => $this->site['siteId']));
		}
		
		if(!self::$blogs){
			self::$blogs = $this->getAll('blogs', array('siteId' => $this->site['siteId'], 'active' => 1));
		}
		
		if(!self::$roles){
			self::$roles = array();
			foreach(self::$blogs as $blog){
				$getRoles = $this->multiblogs->getBlogUserRoles($blog['blogId']);
				foreach($getRoles as $role){
					if($role['userId'] == 0){
						continue;
					}
					$role['blogId'] = $blog['blogId'];
					self::$roles[] = $role;
				}
			}
		}
		
		if(!self::$categories){
			self::$categories = $this->fetchAll('SELECT c.categoryId, c.name, c.slug, c.parentId, c.slug, c.siteId, c.image, c.blogId, c.public
											FROM blog_categories c
											LEFT JOIN blogs b ON b.blogId = c.blogId
											WHERE b.active = 1 AND b.siteId = :siteId',
											array(':siteId' => $this->site['siteId']));
		}
		
		if(!self::$post_cats){
			self::$post_cats = 	$this->fetchAll('SELECT c.categoryId, c.parentId, c.name, c.slug, c.blogId, c.public, pc.approved,
														pc.postId, b.name as blog_name, b.slug as blog_slug, b.userId as blog_owner
										FROM blog_postCategories pc
										LEFT JOIN blog_categories c ON c.categoryId = pc.categoryId
										LEFT JOIN blogs b ON b.blogId = c.blogId
										WHERE b.siteId = :siteId AND b.active = 1
										ORDER BY pc.postId, c.rank ASC, c.name ASC', array(':siteId' => $this->site['siteId']));
		}
		
		if(!self::$contribs){
			self::$contribs = $this->fetchAll('SELECT u.userId, u.username, u.slug, c.role, c.share, c.inviteId, c.contributorId, i.accepted, c.postId
												FROM blog_contributors c
												LEFT JOIN user_invites i ON c.inviteId = i.inviteId
												LEFT JOIN users u ON i.userId = u.userId
												ORDER BY c.postId ASC, c.share DESC', array());
			
		}
		
		if(!self::$comments){
			self::$comments = $this->fetchAll('SELECT commentId, commentDate
											   FROM blog_comments
											   WHERE editorial = 1 AND buried = 0
											   ORDER BY commentId DESC
											   ');
		}
		
		if(!self::$words){
			self::$words = $this->fetchAll('SELECT count(userId) as total, itemId
											FROM pop_words
											GROUP BY itemId');
											
		}
	}
	
	protected function initBlogData()
	{
		
		
	}
	
	protected function getBlogRooms($data, $useBlog = false, $min_load = false)
	{
		$model = new Multiblog_Model;
		$submitModel = new Submissions_Model;
		$meta = new \App\Meta_Model;
		$postModule = $this->get('modules', 'blog-post', array(), 'slug');
	
		$getPosts = self::$posts;
		
		$myRoles = extract_row(self::$roles, array('userId' => $data['user']['userId']), true, 'blog_roles');
		$viewedComments = $meta->getUserMeta($data['user']['userId'], 'viewed-editorial-comments');
		if($viewedComments){
			$viewedComments = explode(',', $viewedComments);
		}
		
		$output = array();
		$postNum = 0;
		$blogPostNum = 0;
		foreach($getPosts as $post){
			$post['published'] = 0;
			$getCats = extract_row(self::$post_cats, array('postId' => $post['postId']), true, 'blog_postCategories');
			$foundInBlog = true;
			if($useBlog){
				$foundInBlog = false;
			}
			foreach($getCats as $cat){
				if($useBlog AND $useBlog == $cat['blogId']){
					$foundInBlog = true;
				}
				if($cat['approved'] == 1){
					$post['published'] = 1;
					break;
				}
			}
			if($foundInBlog){
				$blogPostNum++;
			}
			if(!$foundInBlog OR ($min_load AND $postNum >= $min_load)){
				continue; //skip this item, not part of correct blog
			}
			$postNum++;
			//$post['published'] = $submitModel->checkPostApproved($post['postId']);

			$splitCats = array();
			$allowed_roles = array('admin', 'editor');
			$hasCats = false;
			foreach($getCats as $cat){
				if($cat['blog_owner'] != $data['user']['userId']  AND !$data['perms']['canManageAllBlogs']){
					if(!isset($myRoles[0])){
						continue;
					}

					foreach($myRoles as $role){
						if($role['blogId'] == $cat['blogId'] AND !in_array($role['type'], $allowed_roles)){
							continue 2;
						}
					}
				}				
				if(!isset($splitCats[$cat['blogId']])){
					$splitCats[$cat['blogId']] = array();
				}
				if(!$hasCats){
					$hasCats = true;
				}
				$splitCats[$cat['blogId']][] = $cat;
			}
			if(!$hasCats){
				continue;
			}
			$post['categories'] = $splitCats;
			
			$getUser = $this->get('users', $post['userId'], array('userId', 'username', 'slug', 'email'));
			$post['username'] = $getUser['username'];
			$post['user_slug'] = $getUser['slug'];
			$post['new_comments'] = false;
			/*$getLastComment = $this->fetchSingle('SELECT commentId FROM blog_comments
												  WHERE postId = :postId AND userId != :userId AND editorial = 1
												  ORDER BY commentId DESC',
												array(':postId' => $post['postId'], ':userId' => $data['user']['userId']));*/
												
			$get_comments = extract_row(self::$comments, array('postId' => $post['postId'], 'userId' => $data['user']['userId'], true, 'blog_comments'));
			$getLastComment = false;
			if(isset($get_comments[0])){
				$getLastComment = $get_comments[0];
			}
												
			//$post['contributors'] = $submitModel->getPostContributors($post['postId']);
			$post['contributors'] = extract_row(self::$contribs, array('postId' => $post['postId'], 'accepted' => 1), true, 'blog_contributors');
			$post['is_contributor'] = false;
			foreach($post['contributors'] as $contrib){
				if($contrib['userId'] == $data['user']['userId']){
					$post['is_contributor'] = true;
					break;
				}
			}
			
			$post['edit_user'] = false;
			if($post['editTime'] != '0000:00:00 00:00' AND $post['editTime'] != null){
				if($post['editedBy'] == 0){
					$post['edit_user'] = $getUser;
				}
				else{
					$editUser = $this->get('users', $post['editedBy'], array('userId', 'username', 'slug', 'email'));
					if($editUser){
						$post['edit_user'] = $editUser;
					}
				}
			}
			
			if($getLastComment){
				$post['new_comments'] = true;
				if($viewedComments){
					foreach($viewedComments as $viewed){
						$expViewed = explode(':', $viewed);
						if($expViewed[0] == $post['postId']){
							if($expViewed[1] == $getLastComment['commentId']){
								$post['new_comments'] = false;
								break;
							}
						}
					}
				}
			}
			
			$post['magic_words'] = 0;
			if($post['status'] == 'published'){
				$getWords = extract_row(self::$words, array('itemId' => $post['postId']), true, 'pop_words');
				$countWords = false;
				if(isset($getWords[0])){
					$countWords = $getWords[0];
				}

				if($countWords){
					$post['magic_words'] = $countWords['total'];
				}
			}
			
			
			foreach($splitCats as $blogId => $cat){
				if(!isset($output[$blogId])){
					$output[$blogId] = array();
				}
				
				$blogPost = $post;
				$blogPost['table_status'] = array($blogId => array());
				$blogPost['table_status'][$blogId] = $blogPost['status'];
				
				$pendingCats = 0;
				$approveCats = 0;
				foreach($cat as $c){
					if($c['approved'] == 0){
						$pendingCats++;
					}
					else{
						$approveCats++;
					}
				}
				if($blogPost['status'] == 'published' AND $approveCats == 0){
					$blogPost['table_status'][$blogId] = 'finish-pending';
				}
				$output[$blogId][] = $blogPost;
			}
		}
		
		if($useBlog){
			$output['num_posts'] = $blogPostNum;
		}

		return $output;
	}
	
	
	protected function getBlogs($data)
	{
		$model = new Multiblog_Model;
		$catModel = new Categories_Model;
		$myRoles = extract_row(self::$roles, array('userId' => $data['user']['userId']), true, 'blog_roles');
		$allowed_roles = array('admin', 'editor');
		$getBlogs = $this->getAll('blogs', array('siteId' => $data['site']['siteId'], 'active' => 1));
		foreach($getBlogs as $k => &$blog){
			$allowed_roles = array('admin', 'editor');
			if($blog['userId'] != $data['user']['userId'] AND !$data['perms']['canManageAllBlogs']){
				if(count($myRoles) == 0){
					unset($getBlogs[$k]);
					continue;
				}
				$found = false;
				foreach($myRoles as $role){
					if($role['blogId'] == $blog['blogId']){
						$found = true;
					}
				}
				if(!$found){
					unset($getBlogs[$k]);
					continue;
				}
			}					
			$blog['stats'] = $this->container->getBlogStats($blog);
			$blog['team'] = $this->container->getBlogTeam($blog);
			$blog['categories'] = $catModel->getCategoryFormList($data['site']['siteId'], false, array(), false, 0, $blog['blogId']);
		}
		$blogArray = array();
		foreach($getBlogs as $thisBlog){
			$blogArray[$thisBlog['blogId']] = $thisBlog;
		}
		return $blogArray;
	}
	
	protected function getBlogStats($blog)
	{
		$getPosts = $this->container->getPostsInBlog($blog['blogId']);
		$submitModel = new Submissions_Model;
		
		$output = array();
		$output['posts_published'] = 0;
		$output['posts_submitted'] = count($getPosts);
		$output['posts_process'] = 0;
		$output['total_views'] = 0;
		$output['total_comments'] = 0;
		$output['total_contribs'] = 0;
		$usedAuthors = array();
		$contribList = array();
		foreach($getPosts as $post){
			switch($post['status']){
				case 'published':
					//check approved categories
					$postCats = extract_row(self::$post_cats, array('postId' => $post['postId']), true, 'blog_postCategories');
					foreach($postCats as $postCat){
						if($postCat['approved'] == 1){
							$output['posts_published']++;
							break;
						}
					}
					break;
				case 'editing':
				case 'processing':
				case 'review':
					$output['posts_process']++;
					break;
			}

			if($post['status'] == 'published'){
				if(!in_array($post['userId'], $usedAuthors)){
					$usedAuthors[] = $post['userId'];
					$output['total_contribs']++;
				}
				if(!isset($contribList[$post['userId']])){
					$getAuthor = $this->get('users', $post['userId'], array('userId', 'username', 'slug', 'email'));
					$getAuthor['count'] = 0;
					$contribList[$post['userId']] = $getAuthor;
				}
				$contribList[$post['userId']]['count']++;
				
				//$getContribs = $submitModel->getPostContributors($post['postId']);
				$getContribs = extract_row(self::$contribs, array('postId' => $post['postId'], 'accepted' => 1), true, 'blog_contributors');
				foreach($getContribs as $contrib){
					if(!isset($contribList[$contrib['userId']])){
						$getContribUser = $this->get('users', $contrib['userId'], array('userId', 'username', 'slug', 'email'));
						$getContribUser['count'] = 0;
						$contribList[$contrib['userId']] = $getContribUser;
					}
					$contribList[$contrib['userId']]['count']++;
				}
				$output['total_contribs'] += count($getContribs);				
			}

			$output['total_views'] += $post['views'];
			$output['total_comments'] += $post['commentCount'];
		}
		aasort($contribList, 'count');
		$output['contrib_list'] = array_reverse($contribList);

		return $output;		
	}
	
	protected function getBlogTeam($blog)
	{
		$multiblogs = new Multiblog_Model;
		$getRoles = $multiblogs->getBlogUserRoles($blog['blogId']);
		//$getRoles = $this->getAll('blog_roles', array('blogId' => $blog['blogId']), array(), 'type', 'ASC');
		$usedUsers = array();
		$output = array();
		if($blog['userId'] != 0){
			$getOwner = $this->get('users', $blog['userId'], array('userId', 'username', 'slug', 'email'));
			$getOwner['role'] = 'owner';
			$getOwner['role_nice'] = 'Owner, Chief Admin';
			$usedUsers[] = $getOwner['userId'];
			$output[] = $getOwner;
		}
		foreach($getRoles as $role){
			$getUser = $this->get('users', $role['userId'], array('userId', 'username', 'slug', 'email'));
			if(in_array($getUser['userId'], $usedUsers)){
				continue;
			}
			$usedUsers[] = $getUser['userId'];
			if($getUser){
				$getUser['role'] = $role['type'];
				$getUser['role_nice'] = $role['type'];
				switch($role['type']){
					case 'admin':
						$getUser['role_nice'] = 'Admin';
						break;
					case 'editor':
						$getUser['role_nice'] = 'Editor';
						break;
					case 'independent-writer':
						$getUser['role_nice'] = 'Independent Writer';
						break;
					case 'writer':
						$getUser['role_nice'] = 'Writer';
						break;
				}
				$output[] = $getUser;
			}
		}
		
		return $output;
	}
	
	protected function getPostsInBlog($blogId)
	{
		$get = $this->fetchAll('SELECT p.postId, p.userId, p.views, p.commentCount, p.status
								FROM blog_postCategories pc
								LEFT JOIN blog_categories c ON c.categoryId = pc.categoryId
								LEFT JOIN blog_posts p ON p.postId = pc.postId
								WHERE c.blogId = :blogId AND p.trash = 0
								GROUP BY p.postId', array(':blogId' => $blogId));
		
		return $get;
	}
}
