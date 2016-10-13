<?php


\Util\Filter::addFilter('App\CMS\Accounts_Model', 'updateAccount', 
    function($id, $data){
        $model = new \Core\Model;
        $group_slug = 'forum-troll';
        $troll_group = $model->get('groups', $group_slug, array(), 'slug');
        $is_troll = false;
        if($troll_group){
            if(isset($data['groups']) AND is_array($data['groups'])){
                foreach($data['groups'] as $groupId){
                    if($groupId == $troll_group['groupId']){
                        //set their entire post history to troll post
                        $is_troll = true;
                        break;
                    }
                }
            }
        }
        $vals = array(':id' => $id, ':troll' => intval($is_troll));
        $model->sendQuery('UPDATE forum_posts SET trollPost = :troll WHERE userId = :id', $vals);
        $model->sendQuery('UPDATE forum_topics SET trollPost = :troll WHERE userId = :id', $vals);            
        
        //continue with rest of processing code
        return array($id, $data);
    },
 true);
			
