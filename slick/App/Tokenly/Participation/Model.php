<?php
namespace App\Tokenly;
use Core, UI;
class Participation_Model extends Core\Model
{
	private $startDate = '2014-06-27 00:00:00';
	private $realStart = '2014-01-01';

	protected function getPOPForm()
	{
		$form = new UI\Form;
		
		$label = new UI\Textbox('label');
		$label->setLabel('Label (optional)');
		$form->add($label);
		
		$startDate = $this->startDate;
		$curDate = timestamp();
		
		$chooseWeek = new UI\Select('week');
		$chooseWeek->setLabel('Report Week');
		
		$diff = strtotime($startDate, 0) - strtotime($curDate, 0);
		$weeks = intval(abs(floor($diff / 604800))) - 1;
		for($i = $weeks; $i >= 0; $i--){
			if($i == 0){
				$weekStart = strtotime($this->realStart);
			}
			else{
				$weekStart = strtotime($startDate) + (604800 * $i) + 86400;
			}
			$weekEnd = strtotime($startDate) + ((604800 * $i) + 604800 + 86340);
			$weekStartDate = date('F jS, Y', $weekStart);
			$weekEndDate = date('F jS, Y', $weekEnd);
			$weekName = 'Week '.($i + 1).' '.$weekStartDate.' - '.$weekEndDate;
			$chooseWeek->addOption($i, $weekName);
		}
		$chooseWeek->setSelected($weeks);
		$form->add($chooseWeek);
		
		/*
		$start = new Date('startDate');
		$start->setLabel('Start Date');
		$start->setMinYear(2014);
		$start->setMaxYear(date('Y'));		
		$form->add($start);
		
		$end = new Date('endDate');
		$end->setLabel('End Date');
		$end->setMinYear(2014);
		$end->setMaxYear(date('Y'));
		$form->add($end);
		*/
		
		$fields = new UI\CheckboxList('fields');
		$fields->setLabel('Choose Metrics');
		
		$fieldList = array('comments' => 'Blog Comments', 'posts' => 'Forum Posts', 'threads' => 'Forum Threads',
							'views' => 'First Page Views', 'register' => 'New Registrations', 'magic-words' => 'Magic Words (Proof of Listening)',
							'likes' => 'Post Likes', 'referrals' => 'Referrals', 'blog-posts' => 'Published Posts (Proof of Publication)',
							'pov' => 'Proof of Value');
		
		foreach($fieldList as $field => $fieldLabel){
			$fields->addOption($field, $fieldLabel);
	
		}
		$fields->setLabelDir('R');
		
		$form->add($fields);
		
		$form->setSubmitText('Generate Report');
		return $form;
	}
	
	protected function generateReport($data)
	{
		/*
		$data['startDate'] = date('Y-m-d', strtotime($data['startDate']));
		$data['endDate'] = date('Y-m-d', strtotime($data['endDate']));
		*/
		//figure out dates based on selected week
		$weekNum = intval($data['week']);
		$startTime = strtotime($this->startDate);
		if($weekNum == 0){
			$weekStartTime = strtotime($this->realStart);
		}
		else{
			$weekStartTime = $startTime + (604800 * $weekNum) + 86400;
		}
		
		$weekEndTime = $startTime + ((604800 * $weekNum) + 604800 + 86340);
		
		
		$data['startDate'] = date('Y-m-d H:i:s', $weekStartTime);
		$data['endDate'] = date('Y-m-d H:i:s', $weekEndTime);
		
		$timeframe = array('start' => $data['startDate'], 'end' => $data['endDate']);
		$pop = new POP_Model;
		$getScores = $pop->getPopScoreList($timeframe, $data['fields']);
		if(!$getScores){
			throw new \Exception('Error generating report');
		}
		
		if(!isset($data['label'])){
			$data['label'] = '';
		}

		$useData = array('totalPoints' => $getScores['totalPoints'], 'info' => json_encode($getScores['data']), 'reportDate' => timestamp(),
						  'startDate' => $data['startDate'], 'endDate' => $data['endDate'], 'label' => $data['label']);
						  

		$insert = $this->insert('pop_reports', $useData);
		if(!$insert){
			throw new \Exception('Error saving report');
		}
		
		return $insert;	
	}
	
	protected function getEditReportForm()
	{
		$form = new UI\Form;
		
		$label = new UI\Textbox('label');
		$label->setLabel('Label');
		$form->add($label);
		
		return $form;
	}
}
