<?php
/**
 * Created by PhpStorm.
 * User: richardottinger
 * Date: 2/14/17
 * Time: 3:02 PM
 */
namespace App\Billing;

interface PaymentGateway
{
    public function charge($amount, $token);  // the other stuff was related to testing specifically
    // now can go to app\Billing\FakePaymentGateway.php
    // class FakePaymentGateway implements PaymentGateway
    // now in our ConcertOrdersController we can import... use App\Billing\PaymentGateway;
}