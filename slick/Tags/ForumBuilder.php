<?php
namespace Tags;
use Core, App, App\Tokenly, App\Account, UI, Util, API;
class ForumBuilder
{

	function __construct()
	{
		$this->model = new Core\Model;
		$this->inventory = new Tokenly\Inventory_Model; //load inventory model
		$this->user = Account\Auth_Model::userInfo(); //load user data
		$tokenApp = $this->model->get('apps', 'tokenly', array(), 'slug'); //get the tokenly/ltbcoin app
		$meta = new \App\Meta_Model;
		$this->settings = $meta->appMeta($tokenApp['appId']); //load some settings
		$this->pageURL = 'token-societies'; //CMS page URL the form is located on
		$this->site = $this->model->get('sites', $_SERVER['HTTP_HOST'], array(), 'domain'); //get basic site data
		$this->tokenCategory = $this->settings['tca-forum-category']; //forum category ID to place newly created boards
		$this->boardModule = $this->model->get('modules', 'forum-board', array(), 'slug'); //get board module data
		$this->forumApp = $this->model->get('apps', $this->boardModule['appId']);
	}
	
	public function display()
	{
		ob_start();
		$view = new \App\View;
		echo $view->displayBlock('token-societies-content');
		?>
		<?php
		if(!$this->user){
			//user not logged in
			?>
			<p><strong>You must be <a href="<?= $this->site['url'].'/account?r=/'.$this->pageURL ?>">logged in</a> to your account before creating a private forum.</strong></p>
			<?php
		}
		else{
			//logged in
			if(isset($_GET['pay'])){
				ob_clean();
				$this->displayPayment();
			}
			else{
				$this->displayForm();
			}
		}//endif
		
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
	
	private function displayPayment()
	{
		$getOrder = $this->model->getAll('payment_order', array('address' => $_GET['pay'], 'orderType' => 'tca-forum'));
		if(!$getOrder OR count($getOrder) == 0 OR $getOrder[0]['complete'] == 1){
			header('Location: '.$this->site['url'].'/'.$this->pageURL);
			return false;
		}
		$getOrder = $getOrder[0];
		
		if(isset($_GET['check'])){
			return $this->checkOrderPayment($getOrder['orderId']);
		}
		
		$displayAmount = number_format($getOrder['amount']);
		if($getOrder['asset'] == 'BTC'){
			$displayAmount = convertFloat($getOrder['amount']);
		}
		
		$orderData = json_decode($getOrder['orderData'], true);
		
		echo '<h2>Confirmation & Payment</h1>';
		echo '<p>Almost done! Please pay the amount displayed below in order to create and activate your private forum.
				  Once the transaction has confirmed, it will automatically activate and add you as a moderator. <br><strong>Please stay on this page until the transaction confirms.</strong></p>';
		echo '<p>Do not forget that you will need to be holding <strong>at least 1 '.$orderData['token'].'</strong> in your wallet in order to view and access your new forum.</p>';
		
		echo '<h3 style="padding: 10px; border: solid 1px #ccc; text-align: center; margin-top: 20px;">Please pay '.$displayAmount.' '.$getOrder['asset'].' to<br>
				<span style="color: #000;">'.$getOrder['address'].'</span></h3>';
		
		echo '<p><strong class="payment-status">Waiting for payment...</strong></p>';
		echo '<br>';
		echo '<h3>Order Details</h3>';
		/* logo here */
		if(!isset($orderData['token_desc']) OR trim($orderData['token_desc']) == ''){
			$orderData['token_desc'] = 'N/A';
		}
		if(trim($orderData['board_desc']) == ''){
			$orderData['board_desc'] = 'N/A';
		}
		
		if(isset($orderData['not_owner']) AND $orderData['not_owner']){
			echo '<p><strong>Notice: Another user already has administrative control over the token <em>'.$orderData['token'].'</em>.
							You cannot customize the description, logo or information link for it. However your private board will still be created.</strong></p>
				<ul>
					<li><strong>Forum Name:</strong> '.$orderData['board'].'</li>';
			
			if(trim($orderData['board_desc']) != ''){
				echo '<li><strong>Forum Description:</strong> '.markdown($orderData['board_desc']).'</li>';
			}
			echo '
					<li><strong>Access Token:</strong> '.$orderData['token'].'</li>					
				</ul>';
		}
		else{
			echo '<ul>';
				echo '<li><strong>Forum Name:</strong> '.$orderData['board'].'</li>';
			if(trim($orderData['board_desc']) != ''){
				echo '<li><strong>Forum Description:</strong> '.markdown($orderData['board_desc']).'</li>';
			}
				echo '<li><strong>Access Token:</strong> '.$orderData['token'].'</li>';
			if(trim($orderData['token_desc']) != ''){
				echo '<li><strong>Token Description:</strong> '.markdown($orderData['token_desc']).'</li>';
			}	
			if(trim($orderData['token_link']) != ''){
				echo '<li><strong>Token Link:</strong> '.markdown($orderData['token_desc']).'</li>';
			}
			if(trim($orderData['logo']) != ''){
				echo '<li><strong>Token Logo:</strong><br> <img src="'.$this->site['url'].'/files/tokens/'.$orderData['logo'].'" alt="" /></li>	';
			}
			echo '</ul>';
		}

		?>
		<script type="text/javascript">
			$(document).ready(function(){
				window.checkTimer = setInterval(function(){
					var url = '<?= $this->site['url'] ?>/<?= $this->pageURL ?>?pay=<?= $getOrder['address'] ?>&check=1';
					$.get(url, function(data){
						console.log(data);
						if(data.result == 'receiving'){
							$('.payment-status').html('Receiving payment (' + data.received + ' seen)...');
						}
						if(data.result == 'complete'){
							$('.payment-status').addClass('success').html('Payment complete! Your new board can be found at this URL:<br> <a href="' + data.board_link + '" target="_blank">' + data.board_link + '</a>');
							clearInterval(window.checkTimer);
						}
					});
					
				}, 15000);
			});
		</script>
		<?php
	}
	
	private function displayForm()
	{
		$form = $this->getBuilderForm();
		$error = '';
		if(posted()){
			$data = $form->grabData();
			try{
				$submit = $this->submitForm($data);
			}
			catch(\Exception $e){
				$error = $e->getMessage();
				$submit = false;
			}
			
			if($submit){
				header('Location: '.$this->site['url'].'/'.$this->pageURL.'?pay='.$submit['address']);
				return true;
			}
		}
		if($error != ''){
			echo '<p class="error">'.$error.'</p>';
		}
		?>
		<?php
		echo $form->display();		
	}
	
	private function getBuilderForm()
	{
		$form = new UI\Form;
		$form->setFileEnc();
		
		$tokenName = new UI\Textbox('token_name');
		$tokenName->setLabel('Token/Asset Name *');
		$tokenName->addAttribute('required');
		$form->add($tokenName);
		
		$tokenDesc = new UI\Textarea('token_description');
		$tokenDesc->setLabel('Token/Asset Description (use markdown formatting)');
		$form->add($tokenDesc);
		
		$tokenLink = new UI\Textbox('token_link');
		$tokenLink->setLabel('Token Information Link');
		$form->add($tokenLink);
		
		$logo = new UI\File('image');
		$logo->setLabel('Token Logo');
		$form->add($logo);		
		
		$boardName = new UI\Textbox('board_name');
		$boardName->addAttribute('required');
		$boardName->setLabel('Forum Name *');
		$form->add($boardName);
		
		$boardDesc = new UI\Textarea('board_description');
		$boardDesc->setLabel('Forum Description');
		$form->add($boardDesc);
		
		$payType = new UI\Select('payment_type');
		$payType->setLabel('Payment Type');
		$payType->addOption('LTBCOIN', number_format($this->settings['tca-forum-token-fee']).' LTBCOIN');
		$payType->addOption('BTC', $this->settings['tca-forum-btc-fee'].' BTC');
		$form->add($payType);
		
		return $form;
	}
	
	private function submitForm($data)
	{
		if(trim($data['token_name']) == ''){
			throw new \Exception('Token Name Required');
		}
		if(trim($data['board_name']) == ''){
			throw new \Exception('Forum Name Required');
		}
	
		$xcp = new API\Bitcoin(XCP_CONNECT);
		try{
			$getAsset = $xcp->get_asset_info(array('assets' => array(strtoupper(trim($data['token_name'])))));
		}
		catch(\Exception $e){
			throw new \Exception('Error connecting to Counterparty');
		}

		if(!$getAsset OR count($getAsset) == 0){
			throw new \Exception('Token not found: '.strtoupper($data['token_name']));
		}
		
		$getAsset = $getAsset[0];
		
		switch($data['payment_type']){
			case 'LTBCOIN':
				$tokenCost = floatval($this->settings['tca-forum-token-fee']);
				$tokenType = 'LTBCOIN';
				break;
			case 'BTC':
				$tokenCost = floatval($this->settings['tca-forum-btc-fee']);
				$tokenType = 'BTC';
				break;
			default:
				throw new \Exception('Invalid Payment Type');
				break;
		}
		
		$orderInfo = array();
		$orderInfo['token'] = $getAsset['asset'];		
		
		$checkAsset = $this->model->get('xcp_assetCache', $getAsset['asset'], array(), 'asset');
		if($checkAsset AND $checkAsset['ownerId'] != 0 AND $checkAsset['ownerId'] != $this->user['userId']){
			$orderInfo['not_owner'] = true;
		}
		else{
			$orderInfo['token_desc'] = strip_tags($data['token_description']);
			$orderInfo['logo'] = $this->uploadLogo();
			$orderInfo['token_link'] = strip_tags($data['token_link']);
		}
		
		$orderInfo['board'] = trim(strip_tags($data['board_name']));
		$orderInfo['board_desc'] = strip_tags($data['board_description']);
		$orderInfo['time'] = timestamp();
		$orderInfo['account'] = 'TCAFORUM_'.md5($getAsset['asset'].$this->user['userId'].$orderInfo['time']);
		$orderInfo['token_cost'] = $tokenCost;
		$orderInfo['payment_token'] = $tokenType;
		$orderInfo['userId'] = $this->user['userId'];
		
		$btc = new API\Bitcoin(BTC_CONNECT);
		try{
			$getAddress = $btc->getaccountaddress($orderInfo['account']);
		}
		catch(\Exception $e){
			throw new \Exception('Error connecting to bitcoin');
		}
		
		$orderInfo['address'] = $getAddress;
		
		$insertData = array();
		$insertData['address'] = $orderInfo['address'];
		$insertData['account'] = $orderInfo['account'];
		$insertData['amount'] = $orderInfo['token_cost'];
		$insertData['asset'] = $orderInfo['payment_token'];
		$insertData['orderTime'] = $orderInfo['time'];
		$insertData['orderType'] = 'tca-forum';
		if(isset($orderInfo['not_owner']) AND $orderInfo['not_owner']){
			$insertData['orderData'] = array('userId' => $orderInfo['userId'], 'token' => $orderInfo['token'],
											 'board' => $orderInfo['board'], 'board_desc' => $orderInfo['board_desc'], 'not_owner' => $orderInfo['not_owner']);
		}
		else{
			$insertData['orderData'] = array('userId' => $orderInfo['userId'], 'token' => $orderInfo['token'],
											'token_desc' => $orderInfo['token_desc'], 'board' => $orderInfo['board'],
											'board_desc' => $orderInfo['board_desc'], 'logo' => $orderInfo['logo'], 'token_link' => $orderInfo['token_link']);
		}

		$insertData['orderData'] = json_encode($insertData['orderData']);
										
		$addOrder = $this->model->insert('payment_order', $insertData);
		if(!$addOrder){
			throw new \Exception('Error submitting order');
		}
		
		$orderInfo['orderId'] = $addOrder;
		
		return $orderInfo;
	}
	
	public function checkOrderPayment($orderId)
	{
		ob_end_clean();
		header('Content-Type: text/json');
		$output = array();
		$getOrder = $this->model->get('payment_order', $orderId);
		if(!$getOrder){
			http_response_code(404);
			$output['error'] = 'Order not found';
		}
		else{
			$getOrder['orderData'] = json_decode($getOrder['orderData'], true);
			$editValues = array();
			switch($getOrder['asset']){
				case 'BTC':
					$btc = new API\Bitcoin(BTC_CONNECT);
					$balance = 0;
					$confirmed = 0;
					try{
						$getTx = $btc->listtransactions($getOrder['account']);
					}
					catch(\Exception $e){
						http_response_code(400);
						$output['error'] = 'Error getting balance';
						echo json_encode($output);
						die();
					}
					if(is_array($getTx)){
						foreach($getTx as $tx){
							if($tx['category'] == 'receive'){
								$balance += $tx['amount'];
								if($tx['confirmations'] > 0){
									$confirmed += $tx['amount'];
								}
							}
						}
					}
					$received = $balance;
					$complete = 0;
					$editValues = array('received' => $received);
					if($confirmed >= $getOrder['amount']){
						$complete = 1;
						$editValues['complete'] = 1;
						$editValues['completeTime'] = timestamp();		
						try{
							$finalize = $this->completeOrder($getOrder);
						}
						catch(\Exception $e){
							$finalize = false;
							http_response_code(400);
							$output['error'] = 'Could not complete order: '.$e->getMessage();
							echo json_encode($output);
							die();							
						}
						$output['board_link'] = $finalize;	
					}
					break;
				default:
					$xcp = new API\Bitcoin(XCP_CONNECT);
					try{
						$getBalances = $xcp->get_balances(array('filters' => array('field' => 'address', 'op' => '=', 'value' => $getOrder['address'])));
					}
					catch(\Exception $e){
						http_response_code(400);
						$output['reror'] = 'Error retrieving balance';
						echo json_encode($output);
						die();
					}
					
					$received = 0;
					$complete = 0;
					foreach($getBalances as $balance){
						if($balance['asset'] == $getOrder['asset']){
							$assetInfo = $this->inventory->getAssetData($balance['asset']);
							if($assetInfo['divisible'] == 1 AND $balance['quantity'] > 0){
								$balance['quantity'] = $balance['quantity'] / SATOSHI_MOD;
							}
							$received += $balance['quantity'];
						}
					}
					
					$editValues = array('received' => $received);
					
					if($received >= $getOrder['amount']){
						$complete = 1;
						$editValues['complete'] = 1;
						$editValues['completeTime'] = timestamp();
						try{
							$finalize = $this->completeOrder($getOrder);
						}
						catch(\Exception $e){
							$finalize = false;
							http_response_code(400);
							$output['error'] = 'Could not complete order: '.$e->getMessage();
							echo json_encode($output);
							die();							
						}
						$output['board_link'] = $finalize;
					}	
					break;
			}
			$edit = $this->model->edit('payment_order', $getOrder['orderId'], $editValues);
			if(!$edit){
				http_response_code(400);
				$output['error'] = 'Error updating order';
				echo json_encode($output);
				die();
			}	
			if($complete === 1){
				$output['result'] = 'complete';
				$output['received'] = $received;
			}
			else{
				$output['result'] = 'none';
				if($received > 0){
					$output['result'] = 'receiving';
				}
				$output['received'] = $received;
			}
		}
		http_response_code(200);
		echo json_encode($output);
		die();
	}
	
	public function completeOrder($order)
	{
		$data = $order['orderData'];
		if(!isset($data['not_owner']) OR !$data['not_owner']){
			//update asset cache with custom info, set ownership
			$getAsset = $this->model->get('xcp_assetCache', $data['token'], array(), 'asset');
			$updateVals = array('link' => $data['token_link'], 'description' => $data['token_desc'], 
								'image' => $data['logo'], 'ownerId' => $data['userId']);
			if($getAsset){
				$updateAsset = $this->model->edit('xcp_assetCache', $getAsset['assetId'], $updateVals);
				if(!$updateAsset){
					throw new \Exception('Error updating asset cache');
				} 
			}
		}
		
		//create new board
		$boardList = $this->model->getAll('forum_boards', array('categoryId' => $this->tokenCategory), array('boardId', 'rank'), 'rank', 'desc');
		$rank = 0;
		if(count($boardList) > 0){
			$rank += $boardList[0]['rank'] + 10;
		}
		$boardData = array('categoryId' => $this->tokenCategory, 'name' => $data['board'], 'slug' => genURL($data['board']),
							'rank' => $rank, 'description' => $data['board_desc'], 'siteId' => $this->site['siteId'],
							'ownerId' => $data['userId']);
		
		$boardData['slug'] = $this->checkDupeSlug($boardData['slug']);
							
		$addBoard = $this->model->insert('forum_boards', $boardData);
		if(!$addBoard){
			throw new \Exception('Error creating new board');
		}
		
		//add user to mod list
		$addMod = $this->model->insert('forum_mods', array('userId' => $data['userId'], 'boardId' => $addBoard));
		if(!$addMod){
			throw new \Exception('Error adding user to forum moderator list');
		}
		
		//create token_access entry
		$accessData = array('userId' => $data['userId'], 'moduleId' => $this->boardModule['moduleId'], 
							'itemId' => $addBoard, 'itemType' => 'board', 'permId' => 0, 'asset' => $data['token'],
							'amount' => 0, 'op' => '>', 'stackOp' => 'AND', 'stackOrder' => 0);
		$addLock = $this->model->insert('token_access', $accessData);
		if(!$addLock){
			throw new \Exception('Error creating token access lock');
		}
		
		//link token to board via boardMeta
		$addMeta = $this->model->insert('forum_boardMeta', array('boardId' => $addBoard, 'metaKey' => 'access_token',
																 'value' => $data['token'], 'lastUpdate' => timestamp()));
		if(!$addMeta){
			throw new \Exception('Error linking token to board');
		}
		
		//activate board
		$activate = $this->model->edit('forum_boards', $addBoard, array('active' => 1));
		if(!$activate){
			throw new \Exception('Error activating new board');
		}
		
		//add user to private forum owner group, dont error out
		$getGroup = $this->model->get('groups', 'private-forum-owner', array(), 'slug');
		if($getGroup){
			$checkGroup = $this->model->getAll('group_users', array('userId' => $data['userId'], 'groupId' => $getGroup['groupId']));
			if(!$checkGroup OR count($checkGroup) == 0){
				$this->model->insert('group_users', array('userId' => $data['userId'], 'groupId' => $getGroup['groupId']));
			}
		}
		
		//refresh token cash, but dont error out
		$balances = $this->inventory->getUserBalances($data['userId'], false, 'btc', true);
		
		//generate a link to the new message board
		$boardLink = $this->site['url'].'/'.$this->forumApp['url'].'/'.$this->boardModule['url'].'/'.$boardData['slug'];
		
		return $boardLink;
	}
	
	private function uploadLogo()
	{
		$output = '';
		if(!isset($_FILES['image']['tmp_name']) OR trim($_FILES['image']['tmp_name']) == ''){
			return $output;
		}
		if(!is_dir(SITE_PATH.'/files/tokens')){
			@mkdir(SITE_PATH.'/files/tokens');
		}
		$imageName = md5(time().$_FILES['image']['name']).'.jpg';
		$image = new Util\Image;
		$meta = new \App\Meta_Model;
		$settings = $meta->appMeta('tokenly');
		$resize = $image->resizeImage($_FILES['image']['tmp_name'], SITE_PATH.'/files/tokens/'.$imageName, intval($settings['token-logo-width']), intval($settings['token-logo-height']));
		if($resize){
			$output = $imageName;
		}
		return $output;
	}
	
	public function checkDupeSlug($slug)
	{
		$get = $this->model->fetchSingle('SELECT count(*) as total FROM forum_boards WHERE slug LIKE :slug', array(':slug' => $slug.'%'));
		if(!$get OR $get['total'] == 0){
			return $slug;
		}
		return $slug.'-'.($get['total'] + 1);
	}
}
