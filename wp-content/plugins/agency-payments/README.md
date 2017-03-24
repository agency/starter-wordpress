# Agency Payments Plugin
A customisable payment solution built on gateways and actions for easy extensions.


## The Rules
Do not update any files in this plugin. We should treat this plugin like a third party plugin. Template files can be overwritten in the theme by adding them to a 'payments' folder. Use actions and filter to add custom functionality. If you notice that something is missing and should be in the core. Submit a request and we can add it.

------

#Hooks & Filters
`add_action('payments_form_screen_one')`

Add form fields to the first screen of the form


`add_action('payments_form_screen_two')`

Add form fields to the second screen of the form


`add_action('payments_form_screen_three')`

Add form fields to the third screen of the form


#### Stripe Hooks

`add_action('payments_stripe_charge_success')`

Runs on a successful charge and passes the response from the charge in.


`add_action('payments_stripe_add_customer_success')`

Runs on a successful charge and passes the response in.


#### Transaction Hooks
`add_action('payments_new_transaction_success')`

Runs when a new transaction entry is saved into wordpress and passes in the transaction.

`add_action('payments_update_transaction_success')`

Runs when a transaction has been updated

`add_filter('payments_format_transaction_export')`

Runs before an export and passes in a Transaction object.



#### Subscription Hooks
`add_action('payments_new_subscription_success')`

Runs when a new subscription entry is saved into wordpress and passes in the subscription.

`add_action('payments_update_subscription_success')`

Runs when a subscription is updated

------

#Javascript Hooks & Filters :)
Use javascript actions to add custom javascript to the form.

#### Adding An Action
```

Payments.form.on('PAYMENTS_FORM_SCREEN_NEXT',function(element){ alert("CALLED A DO ACTION"); })

```

#### Actions List

`PAYMENTS_FORM_SCREEN_NEXT` 

Runs on the screen change forward


`PAYMENTS_FORM_SCREEN_PREV` 

Runs on the screen change to previous


`PAYMENTS_FORM_FREQUENCY_CHANGE`

Runs when frequency changes


`PAYMENTS_FORM_DOLLAR_HANDLE_CLICK`

Runs when the dollar handle is clicked



`PAYMENTS_FORM_OTHER_INPUT_CHANGE`

Runs when the other input has changed


`PAYMENTS_FORM_SET_AMOUNT`

Runs when the amount is set


`PAYMENTS_FORM_ON_SUCCESS`

Runs on form success

`PAYMENTS_FORM_ON_FAIL`

Runs on form fail





