<?php
namespace App\Blog;

class Archive_Controller extends \App\ModControl
{
	public $args;
	public $data;
	
    function __construct()
    {
        parent::__construct();
        $this->model = new Archive_Model;
    }
    
    protected function init()
    {
		$output = parent::init();
		
		if(!isset($this->args[2])){
			$output['view'] = '404';
			return $output;
		}
		

		$month = 1;
		$useMonth = 0;
		if(isset($this->args[3])){
			$month = intval($this->args[3]);
			$useMonth = 1;
		}
		$day = 1;
		$useDay = 0;
		if(isset($this->args[4])){
			$useDay = 1;
			$day = intval($this->args[4]);
		}
		$minYear = 2013;
		$maxYear = date('Y');
		$year = intval($this->args[2]);
		
		
		if($year < $minYear || $year > $maxYear || $day < 1 || $day > 31 || $month < 1 || $month > 12){
			$output['view'] = '404';
			return $output;
		}
		
		$title = $year;
		if($useMonth == 1 AND $useDay == 0){
			$title = date('F, Y', strtotime($year.'-'.$month.'-1'));
		}
		if($useMonth == 1 AND $useDay == 1){
			$title = date('F jS, Y', strtotime($year.'-'.$month.'-'.$day));
		}
		
		$output['view'] = '../list';
		$output['title'] = $title.' Archives';
		$postLimit = $this->data['app']['meta']['postsPerPage'];
		$output['commentsEnabled'] = $this->data['app']['meta']['enableComments'];
		if(!$postLimit){
			$postLimit = 10;
		}
		
		$output['posts'] = $this->model->getArchivePosts($this->data['site']['siteId'], $postLimit, $year, $month, $day, $useMonth, $useDay);
		$output['numPages'] = $this->model->getArchivePages($this->data['site']['siteId'], $postLimit, $year, $month, $day, $useMonth, $useDay);
		return $output;
	}
}
