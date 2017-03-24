<?php $current_user = wp_get_current_user(); ?>
<?php
/*
update_user_meta($user_id, 'street_address', $payment->street);
update_user_meta($user_id, 'suburb', $payment->suburb);
update_user_meta($user_id, 'country', $payment->country);
update_user_meta($user_id, 'postcode', $payment->postcode);
update_user_meta($user_id, 'state', $payment->state);
update_user_meta($user_id, 'phone', $payment->phone);
*/
?>
<fieldset class="fieldset-two">

	<!-- <div class="field -half">
        <label for>Title</label>
        <select name="payment[title]" required>
			<option value="" disabled selected>Title</option>
			<option value="Mr.">Mr.</option>
			<option value="Mrs.">Mrs.</option>
			<option value="Ms.">Ms.</option>
			<option value="Miss.">Miss.</option>
			<option value="Dr.">Dr.</option>
        </select>
    </div> -->

    <div class="field -half">
        <label for="payment-first-name">First Name</label>
        <input id="payment-first-name" type="text" name="payment[first_name]" placeholder="First Name" value="<?php if($current_user && $current_user->user_firstname) echo $current_user->user_firstname; ?>"  required/>
    </div>

    <div class="field -half">
        <label for="payment-last-name">Last Name</label>
        <input id="payment-last-name" type="text" name="payment[last_name]" placeholder="Last Name"  value="<?php if($current_user && $current_user->user_lastname) echo $current_user->user_lastname; ?>"  required/>
    </div>

    <div class="field -full">
        <label for="payment-address-street">Street Address</label>
        <input id="payment-address-street" type="text" name="payment[address][street]" placeholder="Street Address" value="<?php echo get_post_meta($current_user->ID, 'street_address', true); ?>"  required/>
    </div>

    <div class="field -half">
        <label for="payment-address-suburb">Suburb</label>
        <input id="payment-address-suburb" type="text" name="payment[address][suburb]" placeholder="Suburb" value="<?php echo get_post_meta($current_user->ID, 'suburb', true); ?>"  required/>
    </div>

    <div class="field -half">
        <label for="payment-address-postcode">Postcode</label>
        <input id="payment-address-postcode" type="text" name="payment[address][postcode]" placeholder="Postcode" value="<?php echo get_post_meta($current_user->ID, 'postcode', true); ?>" required/>
    </div>

    <div class="field -half">
        <label for="payment-address-state">State</label>
        <select id="payment-address-state" name="payment[address][state]"  required>
			<option value="" disabled selected>State</option>
			<option value="VIC">VIC</option>
			<option value="NSW">NSW</option>
			<option value="QLD">QLD</option>
			<option value="NT">NT</option>
			<option value="SA">SA</option>
			<option value="WA">WA</option>
        </select>
    </div>

    <div class="field -half">
        <label for="payment-address-country">Country</label>
        <select id="payment-address-country" name="payment[address][country]"  required>
			<option value="" disabled selected>Country</option>
			<option value="Australia">Australia</option>
        </select>
    </div>

    <div class="field -half">
        <label for="payment-email"></label>
        <input id="payment-email" type="email" name="payment[email]"  value="<?php if($current_user && $current_user->user_email) echo $current_user->user_email; ?>" placeholder="email"  required>
    </div>

    <div class="field -half">
        <label for="payment-phone"></label>
        <input id="payment-phone" type="text" name="payment[phone]" value="<?php echo get_post_meta($current_user->ID, 'phone', true); ?>" placeholder="Phone">
    </div>


</fieldset>