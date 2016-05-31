<?php
/*
 * @name = Forum Boards TCA
 * 
 * 
 * */

\Util\Filter::addFilter('App\Forum\Boards_Model', 'getBoardForm', 
    function($form, $args){
        
        $token = new \UI\Textbox('access-token');
        $token->setLabel('Access Token');
        $form->add($token);
        
        $token_req = new \UI\Textbox('token-req');
        $token_req->setLabel('Minimum amount of token required for access');
        $form->add($token_req);
        
        if(isset($args[0]) AND $args[0] > 0){
            //set values if the page already exists
            $model = new \App\Forum\Board_Model;
            $form->field('access-token')->setValue($model->getBoardMeta($args[0], 'access-token'));
            $form->field('token-req')->setValue($model->getBoardMeta($args[0], 'token-req'));
        }
        
        return $form;
    });


\Util\Filter::addFilter('App\Forum\Boards_Model', 'editBoard', 
    function($id, $data){
        $inventory = new \App\Tokenly\Inventory_Model;
        $model = new \App\Forum\Board_Model;
        $asset = false;
        $amount = 0;
        
        $user = user();
        $meta = $model->boardMeta($id);
        $is_billed = false;
        if(isset($meta['billed_user_board']) AND intval($meta['billed_user_board']) == 1){
            $is_billed = true;
        }
            
        if(!isset($data['access-token']) OR !isset($data['token-req'])){
            if($is_billed){
                throw new \Exception('Access token & amount required');
            }
            return array($id, $data);
        }
        
        $access_token = trim(strtoupper($data['access-token']));
        $token_req = trim($data['token-req']);
        
        if($is_billed AND ($access_token == '' OR $token_req == '')){
            throw new \Exception('Access token & amount required');
        }

        $model->updateBoardMeta($id, 'access-token', $access_token);
        $model->updateBoardMeta($id, 'token-req', $token_req);
        $exp_tokens = explode(',', $access_token);
        if(isset($exp_tokens[0])){
            $model->updateBoardMeta($id, 'access_token', trim($exp_tokens[0])); //set first access token to be used for board logo etc.
        }

        $model = new \Core\Model;
        $board_module = get_app('forum.forum-board');

        $remove_locks = remove_tca_locks($board_module['moduleId'], $id, 'board');
        if($access_token != ''){
            $parse_input = parse_tca_token($access_token);
            $parse_amount = parse_tca_amount($token_req);
            $add_locks = add_tca_locks($user, $board_module['moduleId'], $id, 'board', $parse_input, $parse_amount);
        }							

        //continue with rest of processing code
        return array($id, $data);
    }, true);
    
    
\Util\Filter::addFilter('App\Forum\Boards_Model', 'addBoard', 
    function($id, $args){
        $data = $args[0];

        $model = new \App\Forum\Board_Model;
        if(!isset($data['access-token']) OR !isset($data['token-req'])){
            return array($id, $data);
        }

        $model->updateBoardMeta($id, 'access-token', trim(strtoupper($data['access-token'])));
        $model->updateBoardMeta($id, 'token-req', trim($data['token-req']));

        $model = new \Core\Model;
        $board_module = get_app('forum.forum-board');
        
        $user = user();
        
        if(trim($data['access-token']) != ''){
            $parse_input = parse_tca_token($data['access-token']);
            $parse_amount = parse_tca_amount($data['token-req']);	
            $add_locks = add_tca_locks($user, $board_module['moduleId'], $id, 'board', $parse_input, $parse_amount);
        }		
        
        return $id;
    });    
