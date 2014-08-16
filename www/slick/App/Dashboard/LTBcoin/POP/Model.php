<?php
class Slick_App_Dashboard_LTBcoin_POP_Model extends Slick_Core_Model
{

	public function getPOPForm()
	{
		$form = new Slick_UI_Form;
		
		$label = new Slick_UI_Textbox('label');
		$label->setLabel('Label (optional)');
		$form->add($label);
		
		$start = new Slick_UI_Date('startDate');
		$start->setLabel('Start Date');
		$start->setMinYear(2014);
		$start->setMaxYear(date('Y'));		
		$form->add($start);
		
		$end = new Slick_UI_Date('endDate');
		$end->setLabel('End Date');
		$end->setMinYear(2014);
		$end->setMaxYear(date('Y'));
		$form->add($end);
		
		$fields = new Slick_UI_CheckboxList('fields');
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
	
	public function generateReport($data)
	{
		$data['startDate'] = date('Y-m-d', strtotime($data['startDate']));
		$data['endDate'] = date('Y-m-d', strtotime($data['endDate']));
		
		$timeframe = array('start' => $data['startDate'], 'end' => $data['endDate']);
		$pop = new Slick_App_LTBcoin_POP_Model;
		$getScores = $pop->getPopScoreList($timeframe, $data['fields']);
		if(!$getScores){
			throw new Exception('Error generating report');
		}
		
		if(!isset($data['label'])){
			$data['label'] = '';
		}
		
		$useData = array('totalPoints' => $getScores['totalPoints'], 'info' => json_encode($getScores['data']), 'reportDate' => timestamp(),
						  'startDate' => $data['startDate'], 'endDate' => $data['endDate'], 'label' => $data['label']);
						  

		$insert = $this->insert('pop_reports', $useData);
		if(!$insert){
			throw new Exception('Error saving report');
		}
		
		return $insert;	
	}
	
	public function getEditReportForm()
	{
		$form = new Slick_UI_Form;
		
		$label = new Slick_UI_Textbox('label');
		$label->setLabel('Label');
		$form->add($label);
		
		return $form;
	}


}
