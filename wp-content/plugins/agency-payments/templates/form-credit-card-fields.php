<div class="field -full">
    <label for="payment-card-number">Credit Card Number</label>
    <input id="payment-card-number" type="text" name="payment[card][number]" placeholder="0000 0000 0000 0000" value="<?php echo ($source->last4) ? '.... .... .... ' . $source->last4 : null; ?>" required />
</div>

<div class="field -half">
    <label for="payment-card-expiry">Expiry</label>
    <input id="payment-card-expiry" type="text" name="payment[card][expiry]" placeholder="MM / YY" value="<?php echo ($source->exp_month) ? str_pad($source->exp_month, 2, "0", STR_PAD_LEFT) . ' / ' . substr($source->exp_year, 2) : null; ?>" required />
</div>

<div class="field -half">
    <label for="payment-card-cvc">CVC</label>
    <input id="payment-card-cvc" type="text" name="payment[card][cvc]" placeholder="000" required />
</div>

<div class="field -full">
    <label for="payment-card-name">Name on card</label>
    <input id="payment-card-name" type="text" name="payment[card][name]" placeholder="Name on card" value="<?php echo ($source->name) ? $source->name : null; ?>" required />
</div>