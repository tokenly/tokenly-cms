<h1>Purchase System Credits</h1>
<?php
if($order['received'] == 0){
?>
<p>
    <a href="<?= SITE_URL.'/'.$app['url'].'/'.$module['url'].'/pay/'.$order['address'].'/cancel' ?>" id="cancel-link" class="delete">Cancel</a>
</p>
<?php
}//endif
?>
<p>
    You are about to purchase <strong><?= $order_data['total_credits'] ?> system <?= pluralize('credit', $order_data['total_credits'], true) ?></strong>
    for <strong><?= rtrim(rtrim(number_format($order['amount'], 8),"0"),".") ?> <?= $order['asset'] ?></strong>
</p>
<p>
    Please send <?= rtrim(rtrim(number_format($order['amount'], 8),"0"),".") ?> <?= $order['asset'] ?>
    to the address below:
</p>
<div class="text-center" id="system-credits-payment">
    <p>
        <span class="credit-btc-address">
            <?= $order['address'] ?>
            <span class="dynamic-payment-button" data-label="Purchase System Credits" data-address="<?= $order['address'] ?>" data-tokens="<?= $order['asset'] ?>" data-amount="<?= $order['amount'] ?>"></span>
        </span>
    </p>
    <img src="<?= SITE_URL ?>/qr.php?q=<?= $order['address'] ?>" alt="" style="width: 150px;" />
</div>
<p>
    <strong id="payment-status">Waiting for payment...</strong>
</p>
<div class="pockets-url" style="display: none;"></div>
<div class="pockets-image-blue" style="display: none;"></div>
<span id="check-payment-url" data-url="<?= SITE_URL.'/'.$app['url'].'/'.$module['url'].'/check/'.$order['address'] ?>"></span>
