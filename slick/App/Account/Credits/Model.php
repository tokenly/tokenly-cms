<?php
namespace App\Account;
use Core\Model;

class Credits_Model extends Model
{
    public function getCreditBalance($userId = 0)
    {
        if($userId == 0){
            $user = user();
            if(!$user){
                return false;
            }
            $userId = $user['userId'];
        }
        $get = $this->getCreditEntries($userId);
        $balance = 0;
        foreach($get as $row){
            switch($row['type']){
                case 'debit':
                    $balance -= $row['amount'];
                    break;
                case 'credit':
                default:
                    $balance += $row['amount'];
                    break;
            }
        }
        return $balance;
    }
    
    public function getCreditEntries($userId)
    {
        return $this->getAll('system_credits', array('userId' => $userId), array(), 'id', 'desc');
    }
    
    public function getAllCreditEntries()
    {
        return $this->fetchAll('SELECT c.*, u.username, u.email, u.slug as user_slug
                                FROM system_credits c 
                                LEFT JOIN users u ON u.userId = c.userId
                                ORDER BY id DESC');
    }
    
    public function credit($amount, $ref = null, $note = null, $userId = 0, $source = 'system')
    {
        if($userId == 0){
            $user = user();
            if(!$user){
                return false;
            }
            $userId = $user['userId'];
        }
        return $this->insertCreditEntry($userId, $source, $amount, 'credit', $ref, $note);
    }
    
    public function debit($amount, $ref = null, $note = null, $userId = 0, $source = 'system')
    {
        if($userId == 0){
            $user = user();
            if(!$user){
                return false;
            }
            $userId = $user['userId'];
        }
        return $this->insertCreditEntry($userId, $source, $amount, 'debit', $ref, $note);
    }
    
    public function insertCreditEntry($userId, $source, $amount, $type, $ref = null, $note = null)
    {
        $data = array('userId' => $userId, 'source' => $source, 'amount' => $amount, 'type' => $type, 'ref' => $ref, 'note' => $note);
        $data['created_at'] = timestamp();
        $insert = $this->insert('system_credits', $data);
        return $insert;
    }
    
    public function getCreditPaymentTokens()
    {
        $app = get_app('account');
        if(!isset($app['meta']['system_credit_tokens']) OR !isset($app['meta']['system_credit_token_prices'])){
            return false;
        }
        $exp_tokens = explode(',', $app['meta']['system_credit_tokens']);
        $exp_prices = explode(',', $app['meta']['system_credit_token_prices']);
        $output = array();
        foreach($exp_tokens as $k => $token){
            if(isset($exp_prices[$k])){
                $output[$token] = floatval($exp_prices[$k]);
            }
        }
        return $output;
    }
    
    
    
}
