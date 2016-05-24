<h1>System/Account Credits Management</h1>
<?= $this->displayFlash('message') ?>
<div id="transfer-credits-form">
    <h4>Manual Credit Submission</h4>    
    <form action="" method="post">
        <div class="form-group">
            <input type="text" class="form-control" name="transfer-username" placeholder="Username" required />
        </div>
        <div class="form-group">
            <select name="transfer-type" class="form-control">
                <option>credit</option>
                <option>debit</option>
            </select>
        </div>
        <div class="form-group">
            <input type="text" class="form-control numeric-only" name="transfer-amount" placeholder="Amount" required />
        </div>
         <div class="form-group">
            <input type="text" class="form-control" name="transfer-note" placeholder="Note (optional)" />
        </div>
        <div class="form-group">
            <input type="submit" value="Submit" />
        </div>
    </form>
</div>
<ul>
    <li><strong>Total # Entries:</strong> <?= number_format($num_entries) ?></li>
    <li><strong># Credit Entries:</strong> <?= number_format($num_credits) ?> (<?= formatFloat($total_credit) ?> credits)</li>
    <li><strong># Debit Entries:</strong> <?= number_format($num_debits) ?> (<?= formatFloat($total_debit) ?> credits)</li>
    <li><strong>System Credit Active Supply:</strong> <?= formatFloat($total_supply) ?></li>
</ul>
<div id="user-credits-search">
    <form action="" method="get">
        <div class="form-group inline">
            <label for="user-search">Search by Username</label>
            <input type="text" id="user-search" class="form-control" name="search" value="<?= $user_search ?>" required />
            <input type="submit" value="Go" />
        </div>
    </form>
</div>
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
            <th>User</th>
            <th>Type</th>
            <th>Source</th>
            <th>Amount</th>
            <th>Note</th>
            <th>Ref Data</th>
            <th>Date</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach($credit_entries as $row){
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><a href="<?= route('profile.user-profile', '/'.$row['user_slug']) ?>" target="_blank"><?= $row['username'] ?></a></td>
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
            <td><?= $row['ref'] ?></td>
            <td><?= formatDate($row['created_at']) ?></td>
            <td>
                <a href="<?= SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/'.$row['id'] ?>" class="delete text-danger btn btn-sm btn-small" title="Delete"><i class="fa fa-close"></i></a>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<?= $pager ?>
<?php
}

?>
