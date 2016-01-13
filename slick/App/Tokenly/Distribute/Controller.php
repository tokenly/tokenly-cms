<?php
namespace App\Tokenly;
/*
 * @module-type = dashboard
 * @menu-label = Share Distributor
 * 
 * */
class Distribute_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Distribute_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		$this->data['perms'] = \App\Meta_Model::getUserAppPerms($this->data['user']['userId'], 'tokenly');
		$output['perms'] = $this->data['perms'];
		
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'tx':
					$output = $this->container->showTxPage($output);
					break;
				case 'delete':
					$output = $this->container->deleteTx($output);
					break;
				case 'download':
					$output = $this->container->downloadTxReport($output);
					break;
				case 'edit':
					$output = $this->container->editDistribution($output);
					break;
				default:
					$output['view'] = '404';
					break;
			}
		}
		else{
			$output = $this->container->distributeForm($output);
		}
		$output['template'] = 'admin';
		return $output;
	}
	
	protected function distributeForm($output)
	{
		$output['view'] = 'index';
		$output['message'] = '';
		$output['form'] = $this->model->getShareForm();
		$output['distributeList'] = $this->model->getAll('xcp_distribute', array(), array(), 'distributeId');
		if(posted() AND $this->data['perms']['canDistribute']){
			$data = $output['form']->grabData();
			$data['userId'] = $this->data['user']['userId'];
			try{
				$init = $this->model->initDistribution($data);
			}
			catch(\Exception $e){
				$init = false;
				$output['message'] = $e->getMessage();
				$output['form']->setValues($data);
			}
			if($init){
				redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url'].'/tx/'.$init['address']);
			}
		}
		return $output;
	}
	
	protected function showTxPage($output)
	{
		if(!isset($this->args[3]) OR trim($this->args[3]) == ''){
			$output['view'] = '404';
			return $output;
		}
		$getTx = $this->model->get('xcp_distribute', $this->args[3], array(), 'address');
		if(!$getTx){
			$output['view'] = '404';
			return $output;
		}
		$getTx['addressList'] = json_decode($getTx['addressList'], true);
		$getTx['total'] = 0;
		foreach($getTx['addressList'] as $val){
			$getTx['total'] += $val;
		}
		$output['view'] = 'tx';
		$output['distribute'] = $getTx;
		return $output;
	}
	
	protected function deleteTx($output)
	{
		if(!isset($this->args[3]) OR trim($this->args[3]) == ''){
			$output['view'] = '404';
			return $output;
		}
		$getTx = $this->model->get('xcp_distribute', $this->args[3], array(), 'address');
		if(!$getTx OR !$this->data['perms']['canDeleteDistribution']){
			$output['view'] = '404';
			return $output;
		}
		$delete = $this->model->delete('xcp_distribute', $getTx['distributeId']);
		redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);
	}
	
	protected function downloadTxReport($output)
	{
		if(!isset($this->args[3]) OR trim($this->args[3]) == ''){
			$output['view'] = '404';
			return $output;
		}
		$getTx = $this->model->get('xcp_distribute', $this->args[3], array(), 'address');
		if(!$getTx OR $getTx['complete'] == 0){
			$output['view'] = '404';
			return $output;
		}
		
		$distribute = $getTx;
		
		$txInfo = json_decode($distribute['txInfo'], true);
		$infoData = array();
		//headings
		$infoData[] = array('Address', 'Username', 'Asset', 'Asset Amount', 'TX ID');
		foreach($txInfo as $tx){
			if(isset($tx['code']) AND $tx['code'] == 200 AND isset($tx['send_data'])){
				//new format
				if($distribute['divisible'] == 1){
					$tx['send_data']['quantity'] = round($tx['send_data']['quantity'] / SATOSHI_MOD, 8);
				}
				$andUserName = '';
				$lookup = $this->model->lookupAddress($tx['address']);
				if($lookup){
					$andUserName = $lookup['names'];
				}
				
				//address, username, asset, asset amount, txId
				$infoData[] = array($tx['address'], $andUserName, $distribute['asset'], 'amount' => $tx['send_data']['quantity'], $tx['send_tx']);				
			}
			elseif(isset($tx['result']['code']) AND $tx['result']['code'] == 200){
				if($distribute['divisible'] == 1){
					$tx['details'][3] = $tx['details'][3] / SATOSHI_MOD;
				}
				$andUserName = '';
				$lookup = $this->model->lookupAddress($tx['details'][1]);
				if($lookup){
					$andUserName = $lookup['names'];
				}
				
				//address, username, asset, asset amount, txId
				$infoData[] = array($tx['details'][1], $andUserName, $distribute['asset'], 'amount' => $tx['details'][3], $tx['result']['txId']);
									
			}
		}
		
		$getCsv = arrayToCSV($infoData);
		if(trim($distribute['name']) != ''){
			$filename = genURL($distribute['name']).'-'.$distribute['asset'].'-'.$distribute['completeDate'].'.csv';
		}
		else{
			$filename = $distribute['asset'].'-'.$distribute['completeDate'].'.csv';
		}
		
		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");

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
	
	protected function editDistribution($output)
	{
		$getTx = $this->model->get('xcp_distribute', $this->args[3], array(), 'address');
		if(!$getTx){
			$output['view'] = '404';
			return $output;
		}	
		
		if(!$this->data['perms']['canChangeDistributeLabels'] AND !$this->data['perms']['canChangeDistributeStatus']){
			$output['view'] = '404';
			return $output;
		}
		
		$output['view'] = 'form';
		$output['form'] = $this->model->getEditShareForm();
		$output['distribute'] = $getTx;
		
		if(!$this->data['perms']['canChangeDistributeLabels']){
			$output['form']->remove('name');
		}
		if(!$this->data['perms']['canChangeDistributeStatus']){
			$output['form']->remove('status');
			$output['form']->remove('currentBatch');
		}
		
		$output['message'] = '';
		if(posted()){
			$data = $output['form']->grabData();
			$data['distributeId'] = $getTx['distributeId'];
			if(!$this->data['perms']['canChangeDistributeLabels']){
				$data['name'] = $getTx['name'];
			}
			if(!$this->data['perms']['canChangeDistributeStatus']){
				$data['status'] = $getTx['status'];
				$data['currentBatch'] = $getTx['currentBatch'];
			}
			try{
				$edit = $this->model->editDistribution($data);
			}
			catch(\Exception $e){
				$output['message'] = $e->getMessage();
				$edit = false;
			}
			
			if($edit){
				redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url'].'/tx/'.$getTx['address']);
			}
		}
		else{
			$output['form']->setValues($getTx);
		}
		
		return $output;
	}
}
