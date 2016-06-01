<?php
ini_set('display_errors', 1);
$noForceSSL = true;
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');

//load models and various data
$tokenly_app = get_app('tokenly');
$forum_app = get_app('forum');
$boards_module = get_app('forum.forum-boards');
$model = new App\Forum\Board_Model;
$credits = new App\Account\Credits_Model;

$billing_interval = intval(intval($tokenly_app['meta']['tca-forum-billing-interval']) * 86400);
//$billing_interval = 30;
$forum_price = floatval($tokenly_app['meta']['tca-forum-credit-price']);
$sub_price = floatval($tokenly_app['meta']['tca-sub-forum-credit-price']);
$stamp = timestamp();
$time = strtotime($stamp);

//get list of relevant boards
$get_boards = $model->fetchAll('SELECT b.*, b.ownerId as userId, u.username, u.slug as user_slug, u.email
                                FROM forum_boardMeta m
                                LEFT JOIN forum_boards b ON b.boardId = m.boardId
                                LEFT JOIN users u ON u.userId = b.ownerId
                                WHERE m.metaKey = "billed_user_board" AND m.value = "1"
                                AND b.active = 1
                                GROUP BY b.boardId');

$site = currentSite();

foreach($get_boards as $k => $board){
    
    $get_meta = $model->boardMeta($board['boardId']);
    
    if(!isset($get_meta['last_billing_time'])){
        $get_meta['last_billing_time'] = 0;
    }
    else{
        $get_meta['last_billing_time'] = intval($get_meta['last_billing_time']);
    }
    
    if(!isset($get_meta['total_billed'])){
        $get_meta['total_billed'] = 0;
    }
    else{
        $get_meta['total_billed'] = floatval($get_meta['total_billed']);
    }

    $time_diff = $time - $get_meta['last_billing_time'];
    
    //bill the owner of this board another 1 intervals worth of system credits
    if($time_diff >= $billing_interval){

        //figure out total to debit them, prorate for time spent inactive
        $user_credits = $credits->getCreditBalance($board['userId']);
        $bill_total = $forum_price;
        if($board['parentId'] > 0){
            $bill_total = $sub_price;
        }
        $price_per_second = $bill_total / $billing_interval;
        if(isset($get_meta['billing_seconds_inactive']) AND intval($get_meta['billing_seconds_inactive']) > 0){
            $seconds_inactive = intval($get_meta['billing_seconds_inactive']);
            $bill_total -= $seconds_inactive * $price_per_second;
            $bill_total = round($bill_total, 4);
        }
        
        //check if they have enough credits
        if($user_credits < $bill_total){
            //force deactivate the board and notify the user
            $model->updateBoardMeta($board['boardId'], 'insufficient_credits', 1);
            $model->updateBoardMeta($board['boardId'], 'last_billing_time', $time);
            $model->updateBoardMeta($board['boardId'], 'last_inactive_time', $time);
            $model->updateBoardMeta($board['boardId'], 'billing_seconds_inactive', false);
            $model->edit('forum_boards', $board['boardId'], array('active' => 0));
            echo $board['name'].' [#'.$board['boardId'].'] owner has insufficient credits, deactivating forum'.PHP_EOL;
            $notifyData = array();
            $notifyData['board'] = $board;       
            \App\Meta_Model::notifyUser($board['userId'], 'notifications.boardExpireNotice', $board['boardId'], 'forum-expiration', true, $notifyData);
        }
        else{
            //deduct from their credit balance, notify them
            $debit = $credits->debit($bill_total, 'board:'.$board['boardId'],
            'Renewed forum board "'.$board['name'].'" for another '.$tokenly_app['meta']['tca-forum-billing-interval'].' days'
            ,$board['userId']);
            
            if($debit){
                $model->updateBoardMeta($board['boardId'], 'last_billing_time', $time);
                $model->updateBoardMeta($board['boardId'], 'total_billed', $get_meta['total_billed']+$bill_total);
                $model->updateBoardMeta($board['boardId'], 'billing_seconds_inactive', false);
                echo $board['name'].' [#'.$board['boardId'].'] forum renewed for another interval @ '.$bill_total.' credits'.PHP_EOL;
                
                $notifyData = array();
                $notifyData['board'] = $board;
                $notifyData['site'] = $site;
                $notifyData['app'] = $forum_app;
                $notifyData['module'] = $boards_module;
                $notifyData['board_price'] = $bill_total;
                $notifyData['billing_interval'] = $tokenly_app['meta']['tca-forum-billing-interval'];
                
                \App\Meta_Model::notifyUser($board['userId'], 'notifications.boardRenewalNotice', $board['boardId'], 'forum-renewal', true, $notifyData);
            }
        }
    }
}
