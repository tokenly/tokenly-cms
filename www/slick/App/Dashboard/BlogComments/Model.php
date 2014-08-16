<?php
class Slick_App_Dashboard_BlogComments_Model extends Slick_Core_Model
{

	public function getCommentList($siteId)
	{
		$get = $this->fetchAll('SELECT c.*, u.username as author, CONCAT(p.url, "#comment-", c.commentId) as postURL, p.title as postTitle
								FROM blog_comments c
								LEFT JOIN users u ON u.userId = c.userId
								LEFT JOIN blog_posts p ON p.postId = c.postId
								WHERE p.siteId = :siteId
								ORDER BY c.commentId DESC',
								array(':siteId' => $siteId));
		
		return $get;
		
	}

	


}

?>
