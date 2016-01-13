<?php
namespace App\Account;
use Core;
class Referral_Model extends Core\Model
{
	protected function getUserRefs($userId)
	{
		$getRefs = $this->fetchAll('SELECT r.*, u.username, u.slug
									FROM user_referrals r 
									LEFT JOIN users u ON u.userId = r.userId
									WHERE r.affiliateId = :id
									ORDER BY r.referralId DESC', array(':id' => $userId));
		
		return $getRefs;
	}
}
