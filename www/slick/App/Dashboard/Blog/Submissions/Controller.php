<?php
class Slick_App_Dashboard_Blog_Submissions_Controller extends Slick_App_ModControl
{
    
    function __construct()
    {
        parent::__construct();
        $this->model = new Slick_App_Dashboard_Blog_Submissions_Model;
        $this->user = Slick_App_Account_Home_Model::userInfo();
		$this->tca = new Slick_App_LTBcoin_TCA_Model;
		$this->inventory = new Slick_App_Dashboard_LTBcoin_Inventory_Model;
		$this->meta = new Slick_App_Meta_Model;
		$this->postModule = $this->model->get('modules', 'blog-post', array(), 'slug');
		$this->catModule = $this->model->get('modules', 'blog-category', array(), 'slug');        
		$this->blogApp = $this->model->get('apps', 'blog', array(), 'slug');
		$this->blogSettings = $this->meta->appMeta($this->blogApp['appId']);
        
    }
    
    function __install($moduleId)
    {
		$install = parent::__install($moduleId);
		if(!$install){
			return false;
		}
		
		$meta = new Slick_App_Meta_Model;
		$blogApp = $meta->get('apps', 'blog', array(), 'slug');
		$meta->updateAppMeta($blogApp['appId'], 'submission-fee', 1000, 'Article Submission Fee', 1);
		$meta->updateAppMeta($blogApp['appId'], 'submission-fee-token', 'LTBCOIN', 'Submission Fee Token', 1);
		
		$meta->addAppPerm($blogApp['appId'], 'canBypassSubmitFee');
		
		return $install;
	}
    
    public function init()
    {
		$output = parent::init();
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
		$this->data['perms'] = Slick_App_Meta_Model::getUserAppPerms($this->data['user']['userId'], 'blog');
		$this->data['perms'] = $tca->checkPerms($this->data['user'], $this->data['perms'], $postModule['moduleId'], 0, '');
		
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->showPosts();
					break;
				case 'add':
					$output = $this->addPost();
					break;
				case 'edit':
					$output = $this->editPost();
					break;
				case 'delete':
					$output = $this->deletePost();
					break;
				case 'preview':
					$output = $this->previewPost($output);
					break;
				case 'check-credits':
					$output = $this->checkCreditPayment();
					break;
				case 'trash':
					if(isset($this->args[3])){
						$output = $this->trashPost();
					}
					else{
						$output = $this->showPosts(1);
					}
					break;
				case 'restore':
					$output = $this->trashPost(true);
					break;
				case 'clear-trash':
					$output = $this->clearTrash();
					break;
				default:
					$output = $this->showPosts();
					break;
			}
		}
		else{
			$output = $this->showPosts();
		}
		$output['postModule'] = $this->postModule;
		$output['blogApp'] = $this->blogApp;
		$output['template'] = 'admin';
        $output['perms'] = $this->data['perms'];
       
        
        return $output;
    }
    
    /**
    * Shows a list of posts that the current user has submitted
    *
    * @return Array
    */
    private function showPosts($trash = 0)
    {
		$output = array('view' => 'list');
		$getPosts = $this->model->getAll('blog_posts', array('siteId' => $this->data['site']['siteId'],
															 'userId' => $this->data['user']['userId'],
															 'trash' => $trash), array(), 'postId');
															
		$output['totalPosts'] = 0;
		$output['totalPublished'] = 0;
		$output['totalViews'] = 0;
		$output['totalComments'] = 0;
		$disqus = new Slick_API_Disqus;
		foreach($getPosts as $key => $row){
			$postPerms = $this->tca->checkPerms($this->data['user'], $this->data['perms'], $this->postModule['moduleId'], $row['postId'], 'blog-post');
			$getPosts[$key]['perms'] = $postPerms;
			$output['totalPosts']++;
			if($row['published'] == 1){
				$output['totalPublished']++;
			}
			$output['totalViews']+=$row['views'];	
			$pageIndex = Slick_App_Controller::$pageIndex;
			$getIndex = extract_row($pageIndex, array('itemId' => $row['postId'], 'moduleId' => $this->postModule['moduleId']));
			$postURL = $this->data['site']['url'].'/blog/post/'.$row['url'];
			if($getIndex AND count($getIndex) > 0){
				$postURL = $this->data['site']['url'].'/'.$getIndex[count($getIndex) - 1]['url'];
			}			
			
			$comDiff = time() - strtotime($row['commentCheck']);
			$commentThread = false;
			if($comDiff > 1800){
				$commentThread = $disqus->getThread($postURL, false);
			}
			if($commentThread){
				$getPosts[$key]['commentCount'] = $commentThread['thread']['posts'];
				$this->model->edit('blog_posts', $row['postId'], array('commentCheck' => timestamp(), 'commentCount' => $commentThread['thread']['posts']));
				$output['totalComments'] += $commentThread['thread']['posts'];
			}
			else{
				$this->model->edit('blog_posts', $row['postId'], array('commentCheck' => timestamp()));
				$output['totalComments'] += $row['commentCount'];
			}
			
		}
		$output['postList'] = $getPosts;
		
		$output['submission_fee'] = $this->blogSettings['submission-fee'];
		$getDeposit = $this->meta->getUserMeta($this->user['userId'], 'article-credit-deposit-address');
		if(!$getDeposit){
			$btc = new Slick_API_Bitcoin(BTC_CONNECT);
			$accountName = XCP_PREFIX.'BLOG_CREDITS_'.$this->user['userId'];
			try{
				$getAddress = $btc->getaccountaddress($accountName);
			}
			catch(Exception $e){
				$getAddress = false;
			}
			$this->meta->updateUserMeta($this->user['userId'], 'article-credit-deposit-address', $getAddress);
			$output['credit_address'] = $getAddress;
		}
		else{
			$output['credit_address'] = $getDeposit;
		}
		$output['num_credits'] = intval($this->meta->getUserMeta($this->user['userId'], 'article-credits'));
		$output['fee_asset'] = strtoupper($this->blogSettings['submission-fee-token']);
		
		$output['trashCount'] = $this->model->countTrashItems($this->user['userId']);
		$output['trashMode'] = $trash;
		
		
		return $output;
	}
	
	
	private function addPost()
	{
		$output = array('view' => 'form');
		if(!$this->data['perms']['canWritePost']){
			$output['view'] = '403';
			return $output;
		}
		
		$output['num_credits'] = intval($this->meta->getUserMeta($this->user['userId'], 'article-credits'));
		if(!$this->data['perms']['canBypassSubmitFee'] AND $output['num_credits'] <= 0){
			Slick_Util_Session::flash('blog-message', 'You do not have enough submission credits to create a new post', 'error');
			$this->redirect($this->site.$this->moduleUrl);
			die();
		}
		
		$output['form'] = $this->model->getPostForm(0, $this->data['site']['siteId']);
		$output['formType'] = 'Submit';

		if(!$this->data['perms']['canPublishPost']){
			$output['form']->field('status')->removeOption('published');
			$output['form']->remove('featured');
		}
		if(!$this->data['perms']['canSetEditStatus']){
			$output['form']->field('status')->removeOption('editing');
		}
		if(!$this->data['perms']['canChangeEditor']){
			$output['form']->remove('editedBy');
		}		
		
		if(isset($this->data['perms']['canUseMagicWords']) AND !$this->data['perms']['canUseMagicWords']){
			$getField = $this->model->get('blog_postMetaTypes', 'magic-word', array(), 'slug');
			if($getField){
				$output['form']->remove('meta_'.$getField['metaTypeId']);
			}
		}
	
		if(!$this->data['perms']['canChangeAuthor']){
			$output['form']->remove('userId');
		}
		else{
			$output['form']->setValues(array('userId' => $this->data['user']['userId']));
		}

		if(posted()){
			$data = $output['form']->grabData();
			if(isset($data['publishDate'])){
				$data['publishDate'] = date('Y-m-d H:i:s', strtotime($data['publishDate']));
			}			
			$data['siteId'] = $this->data['site']['siteId'];
			if(!$this->data['perms']['canChangeAuthor']){
				$data['userId'] = $this->user['userId'];
			}
			if(!$this->data['perms']['canPublishPost']){
				if(isset($data['published'])){
					unset($data['published']);
				}
				if(isset($data['featured'])){
					unset($data['featured']);
				}
				if(isset($data['status']) AND $data['status'] == 'published'){
					$data['status'] = 'draft';
				}
			}
			if(!$this->data['perms']['canSetEditStatus']){
				if(isset($data['status']) AND $data['status'] == 'editing'){
					$data['status'] = 'draft';
				}
			}			
			if($data['autogen-excerpt'] == 0){
				$data['excerpt'] = shortenMsg(strip_tags($data['content']), 500);
			}			
			try{
				$add = $this->model->addPost($data, $this->data);
			}
			catch(Exception $e){
				Slick_Util_Session::flash('blog-message', $e->getMessage(), 'error');
				$add = false;
			}
			
			if($add){
				if(!$this->data['perms']['canBypassSubmitFee']){
					//deduct from their current credits
					$newCredits = $output['num_credits'] - 1;
					$this->meta->updateUserMeta($this->user['userId'], 'article-credits', $newCredits);
				}
				
				$this->redirect($this->site.$this->moduleUrl);
			}
			else{
				$this->redirect($this->site.$this->moduleUrl.'/add');
			}
			
			return;
		}
		
		$output['form']->field('publishDate')->setValue(date('Y/m/d H:i'));
		
		return $output;
		
	}
	

	
	private function editPost()
	{
		if(!isset($this->args[3])){
			return array('view' => '404');
		}
		
		$getPost = $this->model->get('blog_posts', $this->args[3]);
		if(!$getPost){
			return array('view' => '404');
		}

		$tca = new Slick_App_LTBcoin_TCA_Model;
		$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');	

		$this->data['perms'] = $tca->checkPerms($this->data['user'], $this->data['perms'], $postModule['moduleId'], $getPost['postId'], 'blog-post');
		
		if(($getPost['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canEditSelfPost'])
		OR ($getPost['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canEditOtherPost'])){
			return array('view' => '403');
		}
		
		if($getPost['published'] == 1 AND !$this->data['perms']['canPublishPost']){
			return array('view' => '403');
		}
		
		$postTCA = $tca->checkItemAccess($this->data['user'], $postModule['moduleId'], $getPost['postId'], 'blog-post');
		if(!$postTCA){
			return array('view' => '403');
		}
		$getCategories = $this->model->getAll('blog_postCategories', array('postId' => $getPost['postId']));
		foreach($getCategories as $cat){
			$catTCA = $tca->checkItemAccess($this->data['user'], $catModule['moduleId'], $cat['categoryId'], 'blog-category');
			if(!$catTCA){
				return array('view' => '403');
			}
		}	
		
		$getPost['categories'] = $this->model->getPostFormCategories($getPost['postId']);
		$getPost['author'] = $this->model->get('users', $getPost['userId']);
		$getPost['editor'] = $this->model->get('users', $getPost['editedBy']);
		
		$output = array('view' => 'form');
		$output['form'] = $this->model->getPostForm($this->args[3], $this->data['site']['siteId']);
		$output['formType'] = 'Edit';
		$output['post'] = $getPost;
		
		if(isset($this->data['perms']['canUseMagicWords']) AND !$this->data['perms']['canUseMagicWords']){
			$getField = $this->model->get('blog_postMetaTypes', 'magic-word', array(), 'slug');
			if($getField){
				$output['form']->remove('meta_'.$getField['metaTypeId']);
			}
		}		
		
		$this->data['post'] = $getPost;
		
		if(!$this->data['perms']['canPublishPost']){
			$output['form']->field('status')->removeOption('published');
			$output['form']->remove('featured');
		}
		if(!$this->data['perms']['canChangeEditor']){
			$output['form']->remove('editedBy');
		}
		if(!$this->data['perms']['canSetEditStatus']){
			$output['form']->field('status')->removeOption('editing');
		}
		if(!$this->data['perms']['canChangeAuthor']){
			$output['form']->remove('userId');
		}
		
		if(posted()){
			$data = $output['form']->grabData();
			if(isset($data['publishDate'])){
				$data['publishDate'] = date('Y-m-d H:i:s', strtotime($data['publishDate']));
			}
			$data['siteId'] = $this->data['site']['siteId'];
			if(!$this->data['perms']['canChangeAuthor']){
				$data['userId'] = false;
			}
			//$data['userId'] = $this->user['userId'];
			if(!$this->data['perms']['canPublishPost']){
				if(isset($data['status']) AND $data['status'] == 'published'){
					$data['status'] = 'draft';
				}
				if(isset($data['featured'])){
					unset($data['featured']);
				}
			}
			if(!$this->data['perms']['canSetEditStatus']){
				if(isset($data['status']) AND $data['status'] == 'editing'){
					$data['status'] = 'draft';
				}
			}
			if($data['autogen-excerpt'] == 0){
				$data['excerpt'] = shortenMsg(strip_tags($data['content']), 500);
			}
			try{
				$edit = $this->model->editPost($this->args[3], $data, $this->data);
			}
			catch(Exception $e){
				Slick_Util_Session::flash('blog-message', $e->getMessage(), 'error');			
				$edit = false;
			}
			
			if($edit){
				Slick_Util_Session::flash('blog-message', 'Post edited successfully!', 'success');
			}
			$this->redirect($_SERVER['REDIRECT_URL']);
			return true;
		}
		//$getPost['status'] = '';
		if($getPost['published'] == 1){
			$getPost['status'] = 'published';
		}
		elseif($getPost['ready'] == 1){
			$getPost['status'] = 'ready';
		}
		/*else{
			$getPost['status'] = 'draft';
		}*/
		
		
		$output['form']->setValues($getPost);
		$output['form']->field('publishDate')->setValue(date('Y/m/d H:i', strtotime($getPost['publishDate'])));
		
		return $output;
		
	}

	
	private function deletePost()
	{
		if(!isset($this->args[3])){
			$this->redirect($this->site.$this->moduleUrl);
			return false;
		}
		
		$getPost = $this->model->get('blog_posts', $this->args[3]);
		if(!$getPost){
			$this->redirect($this->site.$this->moduleUrl);
			return false;
		}
		
		if(($getPost['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canDeleteSelfPost'])
		OR ($getPost['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canDeleteOtherPost'])){
			return array('view' => '403');
		}

		if($getPost['published'] == 1 AND !$this->data['perms']['canPublishPost']){
			return array('view' => '403');
		}
		
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
		$postTCA = $tca->checkItemAccess($this->data['user'], $postModule['moduleId'], $getPost['postId'], 'blog-post');
		if(!$postTCA){
			return array('view' => '403');
		}
		$getCategories = $this->model->getAll('blog_postCategories', array('postId' => $getPost['postId']));
		foreach($getCategories as $cat){
			$catTCA = $tca->checkItemAccess($this->data['user'], $catModule['moduleId'], $cat['categoryId'], 'blog-category');
			if(!$catTCA){
				return array('view' => '403');
			}
		}			
		
		$delete = $this->model->delete('blog_posts', $this->args[3]);
		Slick_Util_Session::flash('blog-message', $getPost['title'].' deleted successfully', 'success');
		
		$this->redirect($this->site.$this->moduleUrl.'/trash');
		return true;
	}
	
	private function previewPost($output)
	{
		if(!isset($this->args[3])){
			$this->redirect($this->site.$this->moduleUrl);
			return false;
		}
		
		$model = new Slick_App_Blog_Post_Model;
		$getPost = $model->getPost($this->args[3], $this->data['site']['siteId']);
		if(!$getPost){
			$this->redirect($this->site.$this->moduleUrl);
			return false;
		}
		
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
		$this->data['perms'] = $tca->checkPerms($this->data['user'], $this->data['perms'], $postModule['moduleId'], $getPost['postId'], 'blog-post');
		$postTCA = $tca->checkItemAccess($this->data['user'], $postModule['moduleId'], $getPost['postId'], 'blog-post');
		if(!$postTCA){
			return array('view' => '403');
		}
		$getCategories = $this->model->getAll('blog_postCategories', array('postId' => $getPost['postId']));
		foreach($getCategories as $cat){
			$catTCA = $tca->checkItemAccess($this->data['user'], $catModule['moduleId'], $cat['categoryId'], 'blog-category');
			if(!$catTCA){
				return array('view' => '403');
			}
		}				
		
		$cats = array();
		foreach($getCategories as $cat){
			$getCat = $this->model->get('blog_categories', $cat['categoryId']);
			$cats[] = $getCat;
		}
		$getPost['categories'] = $cats;
		
		$output['template'] = 'blog';
		$output['view'] = '';
		$output['force-view'] = 'Blog/Post/post';
		$output['post'] = $getPost;
		$output['disableComments'] = true;
		$output['user'] = Slick_App_Account_Home_Model::userInfo();
		$output['title'] = $getPost['title'];
		$output['commentError'] = '';
		$output['comments'] = array();
		

		return $output;
		
	}
	
	
	protected function checkCreditPayment()
	{
		ob_end_clean();
		header('Content-Type: application/json');		
		$output = array('result' => null, 'error' => null);
		if(isset($_SESSION['blog-credit-check-progress'])){
			unset($_SESSION['blog-credit-check-progress']);
			echo json_encode($output);
			die();
		}
		$_SESSION['blog-credit-check-progress'] = 1;
		
		//get latest deposit address
		$getAddress = $this->meta->getUserMeta($this->user['userId'], 'article-credit-deposit-address');
		if(!$getAddress){
			http_response_code(400);
			$output['error'] = 'No deposit address found';
		}
		else{
			//check balances including the mempool
			$assetInfo = $this->inventory->getAssetData($this->blogSettings['submission-fee-token']);
			$xcp = new Slick_API_Bitcoin(XCP_CONNECT);
			$btc = new Slick_API_Bitcoin(BTC_CONNECT);
			try{
				$getPool = $xcp->get_mempool();
				$getBalances = $xcp->get_balances(array('filters' => array('field' => 'address', 'op' => '=', 'value' => $getAddress)));
				
				$received = 0;
				$confirmCoin = 0;
				$newCoin = 0;
				foreach($getBalances as $balance){
					if($balance['asset'] == $assetInfo['asset']){
						$confirmCoin = $balance['quantity'];
						if($assetInfo['divisible'] == 1 AND $confirmCoin > 0){
							$confirmCoin = $confirmCoin / SATOSHI_MOD;
						}
						$received+= $confirmCoin;
					}
				}
				foreach($getPool as $pool){
					if($pool['category'] == 'sends'){
						$parse = json_decode($pool['bindings'], true);
						if($parse['destination'] == $getAddress AND $parse['asset'] == $assetInfo['asset']){
							//check TX to make sure its an actual unconfirmed transaction
							$getTx = $btc->gettransaction($pool['tx_hash']);
							if($getTx AND $getTx['confirmations'] == 0){
								$newCoin = $parse['quantity'];
								if($assetInfo['divisible'] == 1 AND $newCoin > 0){
									$newCoin = $newCoin / SATOSHI_MOD;
								}
								$received+= $newCoin;
							}
						}
					}
				}
			}
			catch(Exception $e){
				http_response_code(400);
				$output['error'] = 'Error retrieving data from xcp server';
			}
			
			//check for previous payment orders on this address, deduct from total seen
			$prevOrders = $this->model->getAll('payment_order', array('address' => $getAddress, 'orderType' => 'blog-submission-credits'));
			$pastOrdered = 0;
			foreach($prevOrders as $prevOrder){
				$prevData = json_decode($prevOrder['orderData'], true);
				$pastOrdered += $prevData['new-received'];
			}
			
			$received -= $pastOrdered;

			//calculate change, number of credits etc.
			$getChange = floatval($this->meta->getUserMeta($this->user['userId'], 'article-credit-deposit-change'));
			$getCredits = intval($this->meta->getUserMeta($this->user['userId'], 'article-credits'));
			$submitFee = intval($this->blogSettings['submission-fee']);
			$origReceived = $received;
			$received += $getChange;
			$leftover = $received % $submitFee;
			$numCredits = floor($received / $submitFee);
			
			//check if enough for at least 1 credit
			if($numCredits > 0){
				
				//save as store order
				$orderData = array();
				$orderData['userId'] = $this->user['userId'];
				$orderData['credits'] = $numCredits;
				$orderData['credit-price'] = $submitFee;
				$orderData['new-received'] = $origReceived;
				$orderData['previous-change'] = $getChange;
				$orderData['leftover-change'] = $leftover;
				
				$order = array();
				$order['address'] = $getAddress;
				$order['account'] = XCP_PREFIX.'BLOG_CREDITS_'.$this->user['userId'];
				$order['amount'] = $numCredits * $submitFee;
				$order['asset'] = $assetInfo['asset'];
				$order['received'] = $origReceived;
				$order['complete'] = 1;
				$order['orderTime'] = timestamp();
				$order['orderType'] = 'blog-submission-credits';
				$order['completeTime'] = $order['orderTime'];
				$order['orderData'] = json_encode($orderData);
				
				$saveOrder = $this->model->insert('payment_order', $order);
				if(!$saveOrder){
					http_response_code(400);
					$output['error'] = 'Error saving payment order';
					echo json_encode($output);
					die();					
				}
				
				//save credits and leftover change
				$newCredits = $getCredits + $numCredits;
				$updateCredits = $this->meta->updateUserMeta($this->user['userId'], 'article-credits', $newCredits);
				$updateChange = $this->meta->updateUserMeta($this->user['userId'], 'article-credit-deposit-change', $leftover);
			
				//setup response data
				$output['result'] = 'success';
				$output['credits'] = $newCredits;
				$output['new_credits'] = $numCredits;
				$output['received'] = $origReceived;
				$output['old_change'] = $getChange;
				$output['new_change'] = $leftover;
			}
			else{
				$output['result'] = 'none';	
			}
		}
		
		ob_end_clean();
		unset($_SESSION['blog-credit-check-progress']);
		echo json_encode($output);
		die();
	}
	
	private function trashPost($restore = false)
	{
		if(!isset($this->args[3])){
			$this->redirect($this->site.$this->moduleUrl);
			return false;
		}
		
		$getPost = $this->model->get('blog_posts', $this->args[3]);
		if(!$getPost){
			$this->redirect($this->site.$this->moduleUrl);
			return false;
		}
		
		if(($getPost['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canDeleteSelfPost'])
		OR ($getPost['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canDeleteOtherPost'])){
			return array('view' => '403');
		}

		if($getPost['published'] == 1 AND !$this->data['perms']['canPublishPost']){
			return array('view' => '403');
		}
		
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
		$postTCA = $tca->checkItemAccess($this->data['user'], $postModule['moduleId'], $getPost['postId'], 'blog-post');
		if(!$postTCA){
			return array('view' => '403');
		}
		$getCategories = $this->model->getAll('blog_postCategories', array('postId' => $getPost['postId']));
		foreach($getCategories as $cat){
			$catTCA = $tca->checkItemAccess($this->data['user'], $catModule['moduleId'], $cat['categoryId'], 'blog-category');
			if(!$catTCA){
				return array('view' => '403');
			}
		}			
		
		if($restore){
			$restorePost = $this->model->edit('blog_posts', $this->args[3], array('trash' => 0));
			Slick_Util_Session::flash('blog-message', $getPost['title'].' restored from trash', 'success');
			$this->redirect($this->site.$this->moduleUrl.'/trash');
		}
		else{
			$delete = $this->model->edit('blog_posts', $this->args[3], array('trash' => 1));
			Slick_Util_Session::flash('blog-message', $getPost['title'].' moved to trash', 'success');
			$this->redirect($this->site.$this->moduleUrl);
		}
		return true;
	}		
		
	private function clearTrash()
	{

		$trashPosts = $this->model->getAll('blog_posts', array('siteId' => $this->data['site']['siteId'],
															 'userId' => $this->user['userId'], 
															 'trash' => 1));
															 
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');															 
		
		foreach($trashPosts as $getPost){
			if(($getPost['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canDeleteSelfPost'])
			OR ($getPost['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canDeleteOtherPost'])){
				return array('view' => '403');
			}

			if($getPost['published'] == 1 AND !$this->data['perms']['canPublishPost']){
				return array('view' => '403');
			}
			
			$postTCA = $tca->checkItemAccess($this->data['user'], $postModule['moduleId'], $getPost['postId'], 'blog-post');
			if(!$postTCA){
				return array('view' => '403');
			}
			$getCategories = $this->model->getAll('blog_postCategories', array('postId' => $getPost['postId']));
			foreach($getCategories as $cat){
				$catTCA = $tca->checkItemAccess($this->data['user'], $catModule['moduleId'], $cat['categoryId'], 'blog-category');
				if(!$catTCA){
					return array('view' => '403');
				}
			}			
			
			$delete = $this->model->delete('blog_posts', $getPost['postId']);
		}
		
		Slick_Util_Session::flash('blog-message', 'Trash bin emptied!', 'success');
		$this->redirect($this->site.$this->moduleUrl.'/trash');
	
		return true;
	}			

}
