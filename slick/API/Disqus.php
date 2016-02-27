<?php
namespace API;

class Disqus
{
	private $apiURL = 'https://disqus.com/api/3.0/';
	private $forumId = DISQUS_DEFAULT_FORUM;
	private $secretKey = DISQUS_SECRET;
	private $pubKey = DISQUS_PUBLIC;
	private $adminKey = DISQUS_ADMIN;
	private $reqSecret = false;
	private $reqAdmin = false;
	
	private function call($endpoint, $args = array(), $method = 'GET')
	{
		$url = $this->apiURL;
		$params = array();
		foreach($args as $key => $val){
			$params[] = $key.'='.urlencode($val);
			$args[$key] = urlencode($val);
		}		
		$url .= $endpoint.'.json';
		switch($method){
			case 'GET':
				$secret = '';
				if($this->reqSecret){
					$secret = '&api_secret='.$this->secretKey;
				}
				if($this->reqAdmin){
					$secret = '&access_token='.$this->adminKey;
				}
				$url .= '?'.'api_key='.$this->pubKey.$secret.'&'.join('&', $params);
				$get = @file_get_contents($url);
				if(!$get){
					return false;
				}
				break;
			case 'POST':
				$params[] = 'api_key='.$this->pubKey;
				if($this->reqSecret){
					$params[] = 'api_secret='.$this->secretKey;
				}
				if($this->reqAdmin){
					$params[] = 'access_token='.$this->adminKey;
				}
				
				ob_start();
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_POST, count($params));
				curl_setopt($curl, CURLOPT_POSTFIELDS, join('&', $params));
				$get = curl_exec($curl);
				curl_close($curl);
				$get = ob_get_contents();
				ob_end_clean();
				break;
		}
		
		$decode =  json_decode(trim($get), true);
		if(isset($decode['response'])){
			return $decode['response'];
		}
		return $decode;
	}
	
	public function getThread($link, $andPosts = true)
	{
		$output = $this->call('threads/details', array('thread' => 'link:'.$link, 'forum' => $this->forumId));
		if(!$output){
			return false;
		}
		
		if($andPosts){
			$posts = $this->getThreadPosts($output['id']);
			return array('thread' => $output, 'posts' => $posts);
		}
		
		return array('thread' => $output);
		
	}
	
	public function getForumId()
	{
		return $this->forumId;
	}
	
	public function setForumId($id)
	{
		$this->forumId = $id;
	}
	
	public function getThreadPosts($threadId)
	{
		$output = $this->call('threads/listPosts', array('thread' => $threadId, 'forum' => $this->forumId));
		return $output;
	}
	
	public function getThreads()
	{
		$output = $this->call('threads/list', array('forum' => $this->forumId));
		return $output;
	}
	
	/*
	@params = title, url, slug, date, remote_auth
	*/
	public function createThread($data)
	{
		$input =  array('forum' => $this->forumId, 'title' => $data['title'], 'url' => $data['url'],
														'slug' => $data['slug'], 'identifier' => $data['slug'], 'date' => strtotime($data['date']));
		if(isset($data['remote_auth'])){
			$input['remote_auth'] = $data['remote_auth'];
		}
		$this->reqSecret = true;
		$output = $this->call('threads/create', $input, 'POST');
		return $output;
	}
	
	/*
	@params = threadId, remote_auth, message
	*/
	public function makePost($data)
	{
		$this->reqSecret = true;
		$input = array('thread' => $data['threadId'], 'remote_auth' => $data['remote_auth'], 'message' => $data['message']);
		$output = $this->call('posts/create', $input, 'POST');
		return $output;
	}
	
	public function editPost($data)
	{
		$this->reqAdmin = true;
		$this->reqSecret = true;
		$input = array('post' => $data['postId'], 'message' => $data['message']);
		
		$output = $this->call('posts/update', $input, 'POST');
		return $output;
		
	}
	
	public function deletePost($postId)
	{
		$this->reqAdmin = true;
		$this->reqSecret = true;
		$input = array('post' => $postId);
		
		$output = $this->call('posts/remove', $input, 'POST');
		return $output;
		
	}
	
	public function getPost($postId)
	{
		$output = $this->call('posts/details', array('post' => $postId));
		return $output;
		
	}
	
	public function genRemoteAuth($data)
	{
		$time = time();
		$userData = array('id' => $data['id'], 'username' => $data['username'], 'email' => $data['email']);
		if(isset($data['url'])){
			$userData['url'] = $data['url'];
		}
		if(isset($data['avatar'])){
			$userData['avatar'] = $data['avatar'];
		}
		$remote = base64_encode(json_encode($userData)).' '.hash_hmac('sha1', base64_encode(json_encode($userData)).' '.$time, DISQUS_SECRET).' '.$time;
		return $remote;
	}
	
	public function getUser($username)
	{
		if(!is_int($username)){
			$username = 'username:'.$username;
		}
		$output = $this->call('users/details', array('user' => $username));
		return $output;
	}
	
	public function getUserPosts($username, $since = false, $limit = false)
	{
		if(!is_int($username)){
			$username = 'username:'.$username;
		}
		$vals = array('user' => $username);
		if($since !== false){
			if(!is_int($since)){
				$since = strtotime($since);
			}
			$vals['since'] = $since;
		}
		if($limit !== false){
			$vals['limit'] = $limit;
		}
		$output = $this->call('users/listPosts', $vals);
		return $output;
	}
	
	public function getRecentPosts($max = 10, $use_cached = true, $cache_interval = 600)
	{
		if($use_cached){
			$file = SITE_BASE.'/data/disqus-recent.json';
			$time = time();
			if(file_exists($file)){
				$cached_posts = json_decode(@file_get_contents($file), true);
				if(is_array($cached_posts) AND isset($cached_posts['last_update'])){
					$diff = $time - $cached_posts['last_update'];
					if($diff < $cache_interval){ 
						return $cached_posts['posts'];
					}
				}
			}
			
		}
		$this->reqSecret = true;
		$output = $this->call('forums/listPosts', array('forum' => $this->forumId, 'limit' => $max, 'related' => 'thread'));
		if($use_cached){
			@file_put_contents($file, json_encode(array('last_update' => $time, 'posts' => $output)));
		}
		return $output;
	}
	
}
