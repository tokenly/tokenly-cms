<?php
if(isset($profile) AND $profile['primary_group']){
	$groupStyle = '';
	if(trim($profile['primary_group']['displayView']) != ''){
		$groupStyle = 'style="color: #'.$profile['primary_group']['displayView'].';"';
	}
	$moreGroups = '';
	if(count($profile['groups']) > 1){
		$displayGroups = array();
		foreach($profile['groups'] as $ugroup){
			if($ugroup['silent'] == 0){
				$displayGroups[] = $ugroup;
			}
		}
		if(count($displayGroups) > 0 AND (!isset($primary_only) OR !$primary_only)){
			$displayGroupNames = array();
			$display_name = $profile['username'];
			foreach($displayGroups as $dg){
				$dgColor = '';
				if(trim($dg['displayView']) != ''){
					$dgColor = 'style="color: #'.$dg['displayView'].';"';
				}
				$displayGroupNames[] = '<span '.$dgColor.'">'.$dg['displayName'].'</span>';
			}
			$moreGroups = '';
			if(count($displayGroups) > 1){
				$moreGroups = ' <a href="#user-group-list" class="fancy" title="View more groups"><i class="fa fa-group"></i></a>
								<div style="display: none;" id="user-group-list">
									<h3>'.$display_name.'\'s Groups</h3>
									<p>
										'.join(', ', $displayGroupNames).'
									</p>
								</div>';
			}
		}
	}
	echo '<span class="user-group" '.$groupStyle.'">'.$profile['primary_group']['displayName'].$moreGroups.'</span>';
}
