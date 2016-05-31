<?php
namespace Tags;
use Core, App, App\Tokenly, App\Account, UI, Util, API, Exception, App\Forum;

require_once(FRAMEWORK_PATH.'/Mods/TCA/functions.php');

class ForumBuilder
{
	function __construct()
	{
        $this->auth = new Account\Auth_Model;
        $this->credits = new Account\Credits_Model;
		$this->model = new Core\Model;
		$this->inventory = new Tokenly\Inventory_Model; //load inventory model
		$this->user = user(); //load user data
        $tokenApp = get_app('tokenly');
        $this->settings = $tokenApp['meta'];
		$this->pageURL = 'token-societies'; //CMS page URL the form is located on
		$this->site = currentSite();
		$this->tokenCategory = $this->settings['tca-forum-category']; //forum category ID to place newly created boards
		$this->boardModule = $this->model->get('modules', 'forum-board', array(), 'slug'); //get board module data
		$this->forumApp = get_app('forum');
        $this->board_model = new Forum\Board_Model;
	}
	
	public function display()
	{
		ob_start();
		$view = new App\View;
		echo $view->displayBlock('token-societies-content');
		?>
		<?php
		if(!$this->user){
			//user not logged in
			?>
			<p><strong>You must be <a href="<?= $this->site['url'].'/account/auth?r=/'.$this->pageURL ?>">logged in</a> to your account before creating a private forum.</strong></p>
			<?php
		}
		else{
			//logged in
            $this->displayForm();
            ?>
            <h4>
                Creating this forum will cost a total of 
                <strong class="text-success"><?= $this->settings['tca-forum-credit-price'] ?> System Credits</strong>,
                billed now, and again every <strong class="text-success"><?= $this->settings['tca-forum-billing-interval'] ?> <?= pluralize('day', $this->settings['tca-forum-billing-interval'], true) ?></strong>.
                You may de-activate your forum at any time to stop billing or receive a prorated price on the next interval.
            </h4>
            <?php
            $credit_balance = $this->credits->getCreditBalance();
            $credit_class = 'text-success';
            if($credit_balance <= 0 OR $credit_balance < $this->settings['tca-forum-credit-price']){
                $credit_class = 'text-danger';
            }
            ?>
            <h4>
                You have <strong class="<?= $credit_class ?>"><?= $credit_balance ?></strong> System Credits
            </h4>
            <p>
                <strong><a href="<?= $this->site['url'] ?>/dashboard/account/credits">Click here</a> to get more credits.</strong>
            </p>
            <?php
		}//endif
		
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
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
		
		$boardName = new UI\Textbox('board_name');
		$boardName->addAttribute('required');
		$boardName->setLabel('Forum Name *');
		$form->add($boardName);
		
		$boardDesc = new UI\Textarea('board_description');
		$boardDesc->setLabel('Forum Description');
		$form->add($boardDesc);
        
		$tokenName = new UI\Textbox('token_name');
		$tokenName->setLabel('Access Token *');
		$tokenName->addAttribute('required');
		$form->add($tokenName);
        
        $token_req = new \UI\Textbox('token_req');
        $token_req->setLabel('Minimum amount of token required for access *');
        $form->add($token_req);        
		
        $password = new UI\Password('password');
        $password->setLabel('Enter Password to Confirm Submission *');
        $password->addAttribute('required');
        $form->add($password);

		return $form;
	}
	
	private function submitForm($data)
	{
        //check some required fields
		if(trim($data['token_name']) == ''){
			throw new Exception('Token Name Required');
		}
		if(trim($data['board_name']) == ''){
			throw new Exception('Forum Name Required');
		}
        
        //check password
        if(!isset($data['password'])){
            throw new Exception('Password required');
        }
        try{
            $check_pass = $this->auth->checkAuth(array('username' => $this->user['username'], 'password' => $data['password'], 'isAPI' => true, 'no_token' => true, 'site' => $this->site));
        }
        catch(Exception $e){
            $message = $e->getMessage();
            if($message != 'Already logged in!'){ //temporary hack
                throw new Exception($message);
            }
        }
		
        //check users credit balance
        $price = floatval($this->settings['tca-forum-credit-price']);
        $balance = $this->credits->getCreditBalance();
        if($balance < $price){
            throw new Exception('Insufficient system credits');
        }
        
        //set up order data
		$orderInfo = array();
		$orderInfo['token'] = strtoupper(trim($data['token_name']));	
        $orderInfo['token_req'] = trim($data['token_req']);
		
		$orderInfo['board'] = trim(strip_tags($data['board_name']));
		$orderInfo['board_desc'] = strip_tags($data['board_description']);
		$orderInfo['time'] = timestamp();
		$orderInfo['credit_cost'] = $price;
		$orderInfo['userId'] = $this->user['userId'];
        
        $submit =  $this->completeOrder($orderInfo); 
        if(!$submit){
            throw new Exception('Error completing order');
        }
        
        Util\Session::flash('message', 'TCA forum created!', 'text-success');
        redirect($submit);
	}

	public function completeOrder($data)
	{
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
			throw new Exception('Error creating new board');
		}
        
        //deduct credits from account
        $debit = $this->credits->debit($data['credit_cost'], 'forum:'.$addBoard, 'Created new TCA board "'.$data['board'].'"');
        if(!$debit){
            $this->model->delete('forum_boards', $addBoard);
            throw new Exception('Error deducting credits from account');
        }
		
		//add user to mod list
		$addMod = $this->model->insert('forum_mods', array('userId' => $data['userId'], 'boardId' => $addBoard));
		if(!$addMod){
			throw new Exception('Error adding user to forum moderator list');
		}
		
		//create token_access entry
        $board_module = get_app('forum.forum-board');
        $parse_input = parse_tca_token($data['token']);
        $parse_amount = parse_tca_amount($data['token_req']);
        $add_locks = add_tca_locks($data['userId'], $board_module['moduleId'], $addBoard, 'board', $parse_input, $parse_amount);        
        
        $timestamp = timestamp();
        
		//link token to board via boardMeta
        //mark this as a board that requires credits to stay active
        $exp_tokens = explode(',', $data['token']);
        $first_token = false;
        if(isset($exp_tokens[0])){
            $first_token = trim($exp_tokens[0]);
        }
        $this->board_model->updateBoardMeta($addBoard, 'access_token', $first_token);
        $this->board_model->updateBoardMeta($addBoard, 'access-token', $data['token']);
        $this->board_model->updateBoardMeta($addBoard, 'token-req', $data['token_req']);
        $this->board_model->updateBoardMeta($addBoard, 'billed_user_board', 1);
        $this->board_model->updateBoardMeta($addBoard, 'last_billing_time', strtotime($timestamp));
        $this->board_model->updateBoardMeta($addBoard, 'total_billed', $data['credit_cost']);
        $this->board_model->updateBoardMeta($addBoard, 'last_inactive_time', 0);
        $this->board_model->updateBoardMeta($addBoard, 'billing_seconds_inactive', 0);
        
		//activate board
		$activate = $this->model->edit('forum_boards', $addBoard, array('active' => 1));
		if(!$activate){
			throw new Exception('Error activating new board');
		}
		
		//add user to private forum owner group, dont error out
		$getGroup = $this->model->get('groups', 'private-forum-owner', array(), 'slug');
		if($getGroup){
			$checkGroup = $this->model->getAll('group_users', array('userId' => $data['userId'], 'groupId' => $getGroup['groupId']));
			if(!$checkGroup OR count($checkGroup) == 0){
				$this->model->insert('group_users', array('userId' => $data['userId'], 'groupId' => $getGroup['groupId']));
			}
		}
		
		//refresh token cache, but dont error out
		$balances = $this->inventory->getUserBalances($data['userId'], false, 'btc', true);
		
		//generate a link to the new message board
		//$boardLink = $this->site['url'].'/'.$this->forumApp['url'].'/'.$this->boardModule['url'].'/'.$boardData['slug'];
		$boardLink = $this->site['url'].'/dashboard/'.$this->forumApp['url'].'/forum-boards';
		
		return $boardLink;
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
