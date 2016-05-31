<?php

\Util\Filter::addFilter('App\Forum\Boards_Model', 'editBoard', 

function($id, $data){
    $active = 0;
    if(isset($data['active']) AND intval($data['active']) == 1){
        $active = 1;
    }
    $data['active'] = $active;
    $time = time();
    $board_model = new \App\Forum\Board_Model;
    $get = $board_model->get('forum_boards', $id);
    if($get AND $get['ownerId'] > 0){
        $get['meta'] = $board_model->boardMeta($get['boardId']);
        if(!isset($get_meta['last_billing_time'])){
            $get['meta']['last_billing_time'] = 0;
        }
        else{
            $get['meta']['last_billing_time'] = intval($get['meta']['last_billing_time']);
        }
        
        if(!isset($get['meta']['total_billed'])){
            $get['meta']['total_billed'] = 0;
        }
        else{
            $get['meta']['total_billed'] = floatval($get['meta']['total_billed']);
        }            
        $forum_app = get_app('forum');
        $tokenly_app = get_app('tokenly');
        $boards_module = get_app('forum.forum-boards');
        if(isset($tokenly_app['meta']['tca-forum-credit-price'])){
            $forum_price = floatval($tokenly_app['meta']['tca-forum-credit-price']);
            $credits_model = new \App\Account\Credits_Model;
            if($data['active'] == 0 AND $get['active'] == 1){
                //keep track of time set inactive
                $board_model->updateBoardMeta($id, 'last_inactive_time', time());
            }
            elseif($data['active'] == 1 AND $get['active'] == 0){
                $check_insufficient = $board_model->getBoardMeta($id, 'insufficient_credits');
                if(intval($check_insufficient) == 1){
                    //attempt renewal
                    $balance = $credits_model->getCreditBalance($get['ownerId']);
                    if($balance < $forum_price){
                        throw new \Exception('Insufficient credits available to reactivate board');
                    }
                    $debit = $credits_model->debit($forum_price, 'board:'.$get['boardId'],
                    'Renewed forum board "'.$get['name'].'" for another '.$tokenly_app['meta']['tca-forum-billing-interval'].' days'
                    ,$get['ownerId']);
                    
                    if($debit){
                        $board_model->updateBoardMeta($get['boardId'], 'last_billing_time', $time);
                        $board_model->updateBoardMeta($get['boardId'], 'total_billed', $get['meta']['total_billed']+$forum_price);
                        $board_model->updateBoardMeta($get['boardId'], 'last_inactive_time', false);
                        $board_model->updateBoardMeta($get['boardId'], 'insufficient_credits', false);
                        $board_model->updateBoardMeta($get['boardId'], 'billing_seconds_inactive', false);
                        
                        $notifyData = array();
                        $notifyData['board'] = $get;
                        $notifyData['site'] = currentSite();
                        $notifyData['app'] = $forum_app;
                        $notifyData['module'] = $boards_module;
                        $notifyData['board_price'] = $forum_price;
                        $notifyData['billing_interval'] = $tokenly_app['meta']['tca-forum-billing-interval'];
                        
                        \App\Meta_Model::notifyUser($get['ownerId'], 'notifications.boardRenewalNotice', $get['boardId'], 'forum-renewal', true, $notifyData);
                        
                        \Util\Session::flash('message', 'Forum renewed for '.$forum_price.' System Credits!', 'text-success');
                    }                        
                }
                else{
                    //update their billing_seconds_inactive stat
                    $last_inactive = 0;
                    if(isset($get['meta']['last_inactive_time'])){
                        $last_inactive = intval($get['meta']['last_inactive_time']);
                    }
                    if($last_inactive > 0){
                        $diff = time() - $last_inactive;
                        $billing_inactive = 0;
                        if(isset($get['meta']['billing_seconds_inactive'])){
                            $billing_inactive = intval($get['meta']['billing_seconds_inactive']);
                        }
                        if($diff > 0){
                            $billing_inactive += $diff;
                            $board_model->updateBoardMeta($get['boardId'], 'billing_seconds_inactive', $billing_inactive);
                        }
                        
                    }
                }
            }
        }
    }
						
    //continue with rest of processing code
    return array($id, $data);
}, true);
						
