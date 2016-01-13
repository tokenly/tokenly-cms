<?php
namespace App\Tokenly;
/*
 * @module-type = dashboard
 * @menu-label = Participation Reports
 * 
 * */
class Participation_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Participation_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->container->viewReport($output);
					break;
				case 'delete':
					$output = $this->container->deleteReport($output);
					break;
				case 'download':
					$output = $this->container->downloadReport($output);
					break;
				case 'edit':
					$output = $this->container->editReport($output);
					break;
				default:
					$output['view'] = '404';
					break;
				
			}
			return $output;
		}
		
		$output['view'] = 'index';
		$output['form'] = $this->model->getPOPForm();
		$output['reports'] = $this->model->getAll('pop_reports', array(), array(), 'reportId');
		$output['message'] = '';
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$generate = $this->model->generateReport($data);
			}
			catch(\Exception $e){
				$output['message'] = $e->getMessage();
				$generate = false;
			}
			
			if($generate){
				redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/view/'.$generate);
			}
		}
		
		return $output;
		
	}
	
	protected function viewReport($output)
	{
		$output['view'] = 'report';
		$getReport = $this->model->get('pop_reports', $this->args[3]);
		if(!$getReport){
			$output['view'] = '404';
			return $output;
		}
		
		$getReport['info'] = json_decode($getReport['info'], true);
		$output['report'] = $getReport;
		
		$metrics = array();
		foreach($getReport['info'] as $row){
			foreach($row['info'] as $metric => $val){
				if(!isset($metrics[$metric])){
					$metrics[$metric] = $val;
				}
				else{
					$metrics[$metric] += $val;
				}
			}
		}
		$output['report']['metrics'] = $metrics;
		
		return $output;
	}
	
	protected function editReport($output)
	{
		$getReport = $this->model->get('pop_reports', $this->args[3]);
		if(!$getReport){
			$output['view'] = '404';
			return $output;
		}
		
		$getReport['info'] = json_decode($getReport['info'], true);
		$output['report'] = $getReport;
		$output['view'] = 'edit';
		$output['form'] = $this->model->getEditReportForm();
		$output['message'] = '';
		
		if(posted()){
			$data = $output['form']->grabData();
			$edit = $this->model->edit('pop_reports', $getReport['reportId'], $data);
			if(!$edit){
				$output['message'] = 'Error editing report info';
			}
			else{
				redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/view/'.$getReport['reportId']);
			}
		}
		
		$output['form']->setValues($getReport);		
				
		return $output;
	}
	
	
	protected function deleteReport($output)
	{
		$getReport = $this->model->get('pop_reports', $this->args[3]);
		if(!$getReport){
			$output['view'] = '404';
			return $output;
		}
		
		$delete = $this->model->delete('pop_reports', $getReport['reportId']);
		redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url']);
	}
	
	protected function downloadReport($output)
	{
		$getReport = $this->model->get('pop_reports', $this->args[3]);
		if(!$getReport){
			$output['view'] = '404';
			return $output;
		}
		
		$getReport['info'] = json_decode($getReport['info'], true);
		$reportData = $getReport['info'];
		
		$infoData = array();
		$headings = array('Username', 'Percent of Total', 'Address', 'Total PoP Earned');
		foreach($reportData as $rep){
			foreach($rep['info'] as $head => $num){
				if(!in_array(ucfirst($head), $headings)){
					array_push($headings, ucfirst($head));
				}
			}
		}
		array_push($headings, 'Negative Points');

		$infoData[] = $headings;
		foreach($reportData as $rep){
			$row = array($rep['username'], $rep['percent'], $rep['address'], $rep['score']);
			foreach($rep['info'] as $head => $num){
				$row[] = $num;
			}
			$row[] = $rep['negativeScore'];
			$infoData[] = $row;
		}
		
		$filename = 'POP-Report-'.$getReport['reportId'].'_'.timestamp().'.csv';
		$getCsv = arrayToCSV($infoData);
		
		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");

		// force download  
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");

		// disposition / encoding on response body
		header("Content-Disposition: attachment;filename={$filename}");
		header("Content-Transfer-Encoding: binary");		
		
		ob_end_clean();
		echo $getCsv;
		die();
	}
}
