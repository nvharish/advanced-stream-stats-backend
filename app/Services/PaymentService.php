<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\GatewayWrappers\BrainTree;

class PaymentService {

    private const SUBSCRIPTION_PLANS = array(
        'silver' => [
            'currency' => 'USD',
            'price' => '199'
        ],
        'gold' => [
            'currency' => 'USD',
            'price' => '399'
        ],
    );

    private $braintree_wrapper;

    public function __construct(BrainTree $braintree_wrapper) {
        $this->braintree_wrapper = $braintree_wrapper;
    }

    public function purchaseSubscription($args = array()) {
        $params = self::SUBSCRIPTION_PLANS[$args['plan_code']];
        $params['email'] = 'admin_harrysoftechhub.com@yopmail.com';
        $params['payment_method_nonce'] = $args['payment_method_nonce'];
        $result = $this->braintree_wrapper->doPayment($params);
        if ($result['success']) {
            //save in DB
            $result = array(
                'message' => 'Subscription purchased'
            );
        }
        return $result;
    }

    public function authorizePayment($args = array()) {
        $result = $this->braintree_wrapper->authorizePayment($args);
        return $result;
    }

}