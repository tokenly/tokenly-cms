<?php
class Slick_Tags_BlogQuickUpdate
{
	public $params = array();
	public $validUsers = array();
	public $categoryId = 0;
	
	function __construct($params = array())
	{
		if(isset($params['users'])){
			if(!is_array($params['users'])){
				$this->validUsers = array($params['users']);
			}
			else{
				$this->validUsers = $params['users'];
			}
		}
		if(isset($params['categoryId'])){
			$this->categoryId = $params['categoryId'];
		}
		$this->model = new Slick_App_Dashboard_BlogPost_Model;
		$this->meta = new Slick_App_Meta_Model;
		$this->postModel = new Slick_App_Blog_Post_Model;
		$this->site = $this->model->get('sites', $_SERVER['HTTP_HOST'], array(), 'domain');
		$this->blogApp = $this->model->get('apps', 'blog', array(), 'slug');
		$this->blogApp['meta'] = $this->meta->appMeta($this->blogApp['appId']);
		$this->postModule = $this->model->get('modules', 'blog-post', array(), 'slug');
		$this->user = Slick_App_Account_Home_Model::userInfo();
		$this->category = $this->model->get('blog_categories', $this->categoryId);
	}
	
	public function display()
	{
		if(!$this->user){
			header('Location: '.$this->site['url'].'/account?r=/uncoin-update');
			return false;
		}
		if(!in_array(strtolower($this->user['username']), $this->validUsers)){
			header('Location: '.$this->site['url'].'/403');
			return false;
		}
		
		
		if(isset($_GET['edit'])){
			$getItem = $this->model->get('blog_posts', $_GET['edit']);
			if(!$getItem){
				header('Location: '.$_SERVER['REDIRECT_URL']);
				die();
			}
			$this->postItem = $getItem;
			if(posted()){
				$output = $this->submitEditForm();
			}
			else{
				$output = $this->displayEditForm();
			}
		}
		else{
			if(posted()){
				$output = $this->submitForm();
			}
			else{
				$output = $this->displayForm();
			}
		}

		
		return $output;
	}
	
	public function displayEditForm($msg = '')
	{
		$form = $this->getForm($this->postItem['postId']);
		$form->remove('pushFront');
		$form->setSubmitText('Save');
		$form->setValues($this->postItem);
		
		ob_start();

		if(isset($this->params['use-text']) AND $this->params['use-text'] == 'true'){
			echo $this->displayDefaultText();
		}//endif
		
		if($msg != ''){
			echo '<p class="error">'.$msg.'</p>';
		}
		?>
		<div class="clear"></div>
		<p>
			<a href="<?= $_SERVER['REDIRECT_URL'] ?>">&lt;- Back to Add New Post</a>
		</p>
		<?= $form->display() ?>
		
		<?= $this->showPostList() ?>
		
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;	
	}
	
	public function submitEditForm()
	{
		$output = '';
		$form = $this->getForm($this->postItem['postId']);
		$data = $form->grabData();
		
		$useData = array();
		$useData['title'] = $data['title'];
		$useData['url'] = $this->postItem['url'];
		$useData['siteId'] = $this->site['siteId'];
		$useData['content'] = $data['content'];
		$useData['excerpt'] = $this->postItem['excerpt'];
		$useData['userId'] = $this->user['userId'];
		$useData['formatType'] = $this->postItem['formatType'];
		$useData['publishDate'] = $this->postItem['publishDate'];
		$useData['featured'] = $this->postItem['featured'];
		$useData['status'] = 'draft';
		if($this->postItem['published'] == 1){
			$useData['status'] = 'published';
		}
		elseif($this->postItem['ready'] == 1){
			$useData['status'] = 'ready';
		}
		$useData['notes'] = $this->postItem['notes'];


		$appData = array();
		$appData['user'] = $this->user;
		$appData['site'] = $this->site;
		$appData['perms'] = Slick_App_Meta_Model::getUserAppPerms($this->user['userId'], 'blog');
		
		try{
			$editPost = $this->model->editPost($this->postItem['postId'], $useData, $appData);
		}
		catch(Exception $e){
			return $this->displayEditForm($e->getMessage());
		}

		ob_start();
		?>
		<p>
			Post saved successfully! <a href="<?= $_SERVER['REDIRECT_URL'] ?>">Click here</a> to go back to quick update.
		</p>
		<?php

		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
	
	public function displayDefaultText()
	{
		ob_start();
		if($this->category['image'] != '' AND file_exists(SITE_PATH.'/files/blogs/'.$this->category['image'])){
			echo '<div class="blog-category-image"><img src="'.$this->site['url'].'/files/blogs/'.$this->category['image'].'" alt="" /></div>';
		}
		?>
		<p>
			Use this form to quickly create a new blog post. You may save as a draft or have the post published immediately.
			These posts will automatically be added to the <strong>"<?= $this->category['name'] ?>"</strong> category. Use
			<a href="<?= $this->site['url'] ?>/markdown-formatting" target="_blank">Markdown Formatting</a> for writing your post.
			<a href="http://imgur.com" target="_blank">Imgur</a> is recommended for image hosting.
		</p>
		<p>
			If you would like your post to appear not just on the category but on the <?= $this->site['name'] ?> front page, simply check off "Push to Front Page".
			It will ask you to upload a cover image, or if you leave that blank it will use a default image for the category.
		</p>
		<p>
			<a href="<?= $this->site['url'] ?>/blog/category/<?= $this->category['slug'] ?>" target="_blank">Click here</a> to view category front end<br>
			<a href="<?= $this->site['url'] ?>/dashboard/blog-post" target="_blank">Click here</a> to go to the full Blog Post module in the dashboard.
		</p>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	public function displayForm($msg = '')
	{
		$form = $this->getForm();
		
		ob_start();

		if(isset($this->params['use-text']) AND $this->params['use-text'] == 'true'){
			echo $this->displayDefaultText();
		}//endif
		
		if($msg != ''){
			echo '<p class="error">'.$msg.'</p>';
		}
		?>
		<div class="clear"></div>
		<?= $form->open() ?>
		<?= $form->displayFields() ?>
		<div id="quick-cover-image" style="display: none;">
			<label for="coverImage">Upload Cover Image (leave empty to use default)</label>
			<input type="file" name="coverImage" id="coverImage" />
		</div>
		<input type="submit" name="updateType" value="Save Draft" />
		<input type="submit" name="updateType" value="Save & Publish" />
		<?= $form->close() ?>
		
		<?= $this->showPostList() ?>
		
		<script type="text/javascript">
			$(document).ready(function(){
				$('input[name="pushFront"]').click(function(e){
					if($(this).is(':checked')){
						$('#quick-cover-image').slideDown();
					}
					else{
						$('#quick-cover-image').slideUp();
					}
				});
			});
		</script>
		
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;		
		
	}
	
	public function getForm($postId = 0)
	{
		$form = $this->model->getPostForm($postId, 1, false);
		$form->remove('url');
		$form->remove('excerpt');
		$form->remove('categories');
		$form->remove('editor');
		$form->remove('userId');
		$form->remove('editedBy');
		$form->remove('publishDate');
		$form->remove('image');
		$form->remove('featured');
		$form->remove('coverImage');
		$form->remove('status');
		$form->remove('formatType');
		$form->remove('notes');
		
		$form->field('content')->setLabel('Content (use markdown for formatting)');
		
		$push = new Slick_UI_Checkbox('pushFront', 'pushFront');
		$push->setLabel('Push to Front Page?');
		$push->setBool(1);
		$push->setValue(1);
		$form->add($push);
		
		return $form;
	}
	
	public function submitForm()
	{
		$output = '';
		$form = $this->getForm();
		$data = $form->grabData();
		
		$useData = array();
		$useData['title'] = $data['title'];
		$useData['url'] = genURL($data['title']);
		$useData['siteId'] = $this->site['siteId'];
		
		if(isset($_POST['updateType']) AND $_POST['updateType'] == 'Save & Publish'){
			$useData['status'] = 'published';
			$published = true;
		}
		else{
			$useData['status'] = 'draft';
			$published = false;
			
		}
		$useData['content'] = $data['content'];
		if($data['pushFront'] == 1){
			$useData['excerpt'] = shortenMsg($data['content'], 500);
		}
		else{
			$useData['excerpt'] = $data['content'];
		}
		$useData['userId'] = $this->user['userId'];
		$useData['publishDate'] = timestamp();
		$useData['featured'] = 0;
		$useData['formatType'] = 'markdown';
		$useData['notes'] = '';
		$useData['categories'] = array($this->categoryId);
		
		$appData = array();
		$appData['user'] = $this->user;
		$appData['site'] = $this->site;
		$appData['perms'] = Slick_App_Meta_Model::getUserAppPerms($this->user['userId'], 'blog');
		
		try{
			$addPost = $this->model->addPost($useData, $appData);
		}
		catch(Exception $e){
			return $this->displayForm($e->getMessage());
		}
		
		$getPost = $this->model->get('blog_posts', $addPost);
		
		if($data['pushFront'] == 1){
			if((!isset($_FILES['coverImage']['tmp_name']) OR trim($_FILES['coverImage']['tmp_name']) == '') AND trim($this->category['image']) != ''){
				$this->model->edit('blog_posts', $getPost['postId'], array('coverImage' => $this->category['image']));
			}
		}
		
		ob_start();
		
		if($published){
		?>
		<p>
			Your post "<?= $getPost['title'] ?>" has been published! <a href="<?= $this->site['url'] ?>/uncoin-update">Go back</a>.
		</p>
		<p>
			<a href="<?= $this->site['url'] ?>/blog/post/<?= $getPost['url'] ?>">Click here</a> to view this post on the website.<br>
			<a href="<?= $this->site['url'] ?>/dashboard/blog-post/edit/<?= $getPost['postId'] ?>">Click here</a> to edit this post in your dashboard.
		</p>
		<?php
		}
		else{
			header('Location: '.$this->site['url'].'/dashboard/blog-post/edit/'.$getPost['postId']);
			return true;
		}

		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
	
	public function showPostList()
	{
		
		$getPosts = $this->model->fetchAll('SELECT p.*
										    FROM blog_postCategories c
										    LEFT JOIN blog_posts p ON p.postId = c.postId
										    WHERE c.categoryId = :catId
										    GROUP BY p.postId
										    ORDER BY postId DESC', array(':catId' => $this->categoryId));

		
		ob_start();
		?>
		<h3>Posts in <?= $this->category['name'] ?></h3>
		<table class="admin-table">
			<thead>
				<tr>
					<th>Title</th>
					<th>Status</th>
					<th>Views</th>
					<th>Publish Date</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach($getPosts as $post){
					?>
					<tr>
						<td><a href="<?= $this->site['url'] ?>/blog/post/<?= $post['url'] ?>" target="_blank"><?= $post['title'] ?></a></td>
						<td>
						<?php
						$post['status'] = '';
						if($post['published'] == 1){
							$post['status'] = 'Published';
						}
						elseif($post['ready'] == 1){
							$post['status'] = 'Ready';
						}
						elseif($post['status'] == 'editing'){
							$post['status'] = 'Editing';
						}
						else{
							$post['status'] = 'Draft';
						}
						echo $post['status'];						
						?>
						</td>
						<td><?= number_format($post['views']) ?></td>
						<td><?= formatDate($post['publishDate']) ?></td>
						<td>
							<a href="?edit=<?= $post['postId'] ?>">Quick Edit</a><br>
							<a href="<?= $this->site['url'] ?>/dashboard/blog-post/edit/<?= $post['postId'] ?>" target="_blank">Edit Post</a><br>
							<a href="<?= $this->site['url'] ?>/dashboard/blog-post/delete/<?= $post['postId'] ?>" target="_blank" class="delete">Delete</a>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
}
