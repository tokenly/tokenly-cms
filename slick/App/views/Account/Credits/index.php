<h1>System Credits</h1>
<?= $this->displayFlash('message') ?>
<p>
    <strong>Credits</strong> are used to perform special actions and gain access to extra features within the platform.<br>
    They may be purchased using Tokens, potentially received as a reward, or even gifted from another user.
</p>
<p>
    Credits are non-withdrawable and can only be used for the following:
</p>
<ul>
    <li><a href="/dashboard/blog/submissions">Submit a Article or Podcast</a></li>
    <li><a href="/token-societies">Create your own "Token Controlled Access" message board</a></li>
</ul>
<div id="transfer-credits-form">
    <h4>Transfer Credit to a Friend</h4>    
    <form action="<?= SITE_URL.'/'.$app['url'].'/'.$module['url'].'/transfer' ?>" method="post">
        <div class="form-group">
            <input type="text" class="form-control" name="transfer-username" placeholder="Username" required />
        </div>
        <div class="form-group">
            <input type="text" class="form-control numeric-only" name="transfer-amount" placeholder="Amount" required />
        </div>
         <div class="form-group">
            <input type="text" class="form-control" name="transfer-note" placeholder="Note (optional)" />
        </div>
        <div class="form-group">
            <input type="submit" value="Transfer" />
        </div>
    </form>
</div>
<h3>My Credit Balance: 
<span class="
<?php
if($balance > 0){
    echo 'text-success';
}
else{
    echo 'text-danger';
}

?>
"><?= round($balance, 4) ?></span></h3>

<?php
if($payment_tokens){
?>
<div id="purchase-credit-form">
    <h4>Purchase System Credits</h4>
    <form action="<?= SITE_URL.'/'.$app['url'].'/'.$module['url'].'/purchase' ?>" method="post">
        <div class="form-group">
            <input type="text" class="form-control numeric-only" id="purchase-amount" name="purchase-amount" placeholder="Quantity" required />
        </div>
        <div class="form-group">
            <select id="payment-method" name="payment-method">
                <?php
                foreach($payment_tokens as $token => $value){
                    echo '<option value="'.$token.'" data-price="'.$value.'">'.rtrim(rtrim(number_format($value, 8),"0"),".").' '.$token.'</option>';
                }
                ?>
            </select>
        </div>        
        <div class="form-group">
            <button type="submit" class="btn btn-lg" id="purchase-credit-btn"><i class="fa fa-check"></i> Submit Order</button>
            <p>
                <strong>Total Cost:</strong>
                <span id="purchase-credit-total">N/A</span>
            </p>
        </div>
    </form>
</div>
<?php
}//endif
?>
<hr>
<h3>Credit/Debit History</h3>
<?php
if(!$credit_entries OR count($credit_entries) == 0){
    echo '<p>No entries found.</p>';
}
else{
?>
<table class="table table-bordered admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Type</th>
            <th>Source</th>
            <th>Amount</th>
            <th>Note</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach($credit_entries as $row){
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td>
                <?php
                switch($row['type']){
                    case 'debit':
                        echo '<span class="text-danger"><i class="fa fa-minus-circle"></i>';
                        break;
                    case 'credit':
                    default:
                        echo '<span class="text-success"><i class="fa fa-plus-circle"></i>';
                        break;
                }
                echo ' '.$row['type'].'</span>';
                ?>
            </td>
            <td><?= $row['source'] ?>
                <?php
                if($row['source'] == 'transfer'){
                    $exp_ref = explode(':', $row['ref']);
                    if(isset($exp_ref[1]) AND $exp_ref[0] == 'user'){
                        $transfer_user = user($exp_ref[1]);
                        if($transfer_user){
                            echo '(<a href="'.route('profile.user-profile', '/'.$transfer_user['slug']).'" target="_blank">'.$transfer_user['username'].'</a>)';
                        }
                    }
                }
                ?>
            </td>
            <td><?= round($row['amount'], 4) ?></td>
            <td><?= $row['note'] ?></td>
            <td><?= formatDate($row['created_at']) ?></td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<?php
}

?>



