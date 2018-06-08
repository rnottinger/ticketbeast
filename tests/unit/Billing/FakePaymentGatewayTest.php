<?php
/**
 * Created by PhpStorm.
 * User: richardottinger
 * Date: 2/9/17
 * Time: 1:42 PM
 */

use App\Billing\FakePaymentGateway;
 use Illuminate\Foundation\Testing\WithoutMiddleware;
 use Illuminate\Foundation\Testing\DatabaseMigrations;
 use Illuminate\Foundation\Testing\DatabaseTransactions;

 class FakePaymentGatewayTest extends TestCase
 {
     // now we have a place to think about how we exactly want this payment Gateway to work
     // so instead of testing specific methods on this class
     // I want to use this as an opportunity to create some specifications for the behavior of this class
     // and then in the test of those behaviors we are going to make use of the api of this class
     // and therefore get implicit coverage for those methods
     /** @test */
     function charges_with_a_valid_payment_token_are_successful()
     {
         // so not think about what the steps of this might be
         // our first step might be to just create a paymentGateway
         $paymentGateway = new FakePaymentGateway;

         // our next statement might be maybe we want to charge something against this payment gateway
         // we have to pass in the token to use for the charge
         //     since we want a valid payment token
         //     we can ask for that from the $payment
         $paymentGateway->charge(2500,$paymentGateway->getValidTestToken());

         // then our assertion could be that we get back 25.00 back when we ask for the total charges of the $paymentGateway
         $this->assertEquals(2500, $paymentGateway->totalCharges());
         // now lets run this test and drive out this implementation

     }
 }
