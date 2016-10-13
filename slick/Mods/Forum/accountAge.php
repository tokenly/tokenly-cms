<?php
/*
 * 
 * 
 * */
 
function check_account_age()
{
    $user = user();
    $regDate = strtotime($user['regDate']);
    $regThreshold = 60*60*1;
    $time = time();
    if(($time - $regDate) < $regThreshold){
        $numHours = round($regThreshold / 3600);
        throw new \Exception('Your account must be active for at least <strong>'.$numHours.' '.pluralize('hour', $numHours, true).'</strong> before you may post in the forums.');
    }
}


\Util\Filter::addFilter('App\Forum\Post_Model', 'postReply', 

function($data, $appData){
        
    check_account_age();
        
    return array($data, $appData);
}, true);


\Util\Filter::addFilter('App\Forum\Board_Model', 'postTopic', 

function($data, $appData){
        
    check_account_age();
    
    return array($data, $appData);
}, true);
