<?php
namespace App\Tokenly;
class Controller extends \App\AppControl
{
    function __construct()
    {
        parent::__construct();
    }
    
    protected function init()
    {
		$output = parent::init();
		if(!$output['module']){
			$output['view'] = '404';
		}
		return $output;
    }
    
    protected function __install($appId)
    {
		parent::__install($appId);
	
		$meta = new \App\Meta_Model;
		
		$meta->updateAppMeta($appId, 'distribute-fee', 0.00001, 'Share Distributor - per address miner fee', 1);
		$meta->updateAppMeta($appId, 'distribute-dust', 0.000055, 'Share Distributor - dust output BTC value', 1);
		$meta->updateAppMeta($appId, 'distribute-decimals', 2, 'Share Distributor - Round Values to x Decimals', 1);
		$meta->updateAppMeta($appId, 'distribute-batch-size', 25, 'Share Distributor - # Transactions per Batch', 1);
		$meta->updateAppMeta($appId, 'pop-comment-weight', 4, 'PoP points per blog comment made', 1);
		$meta->updateAppMeta($appId, 'pop-forum-post-weight', 4, 'PoP points per forum post made', 1);
		$meta->updateAppMeta($appId, 'pop-forum-topic-weight', 4, 'PoP points per forum thread made', 1);
		$meta->updateAppMeta($appId, 'pop-register-weight', 2, 'PoP bonus points for new registrants', 1);
		$meta->updateAppMeta($appId, 'pop-view-weight', 1, 'PoP points per first page view', 1);
		$meta->updateAppMeta($appId, 'pop-listen-weight', 10, 'PoP - Proof of Listening Weight', 1);
		
		$meta->addAppPerm($appId, 'canDistribute');
		$meta->addAppPerm($appId, 'canDeleteDistribution');
		$meta->addAppPerm($appId, 'canChangeDistributeStatus');
		$meta->addAppPerm($appId, 'canChangeDistributeLabels');
	}
}
