<?php

namespace App\Billing;

class FakePaymentGateway implements PaymentGateway
{
    private $charges;

    public function __construct()
    {
        $this->charges = collect();  // initialize charges property to be a new collection
    }

    public function getValidTestToken()
    {
        // we can have this return any string we want because we are going to keep this encapsulated in this class
        //
        return "valid-token";
    }

    public function charge($amount, $token)
    {
        // for now since we have nothing telling us what should happen when the token is not valid
        // lets just always store this charge
        $this->charges[] = $amount;

        // event though we are missing some stuff like failing if the token is invalid
        // just going to leave it and jump back to our integration test and not worry about
        // driving out any behavior that the FakePaymentGatway needs yet that hasn't been driving by our acceptance test
    }

    public function totalCharges()
    {
        return $this->charges->sum();  // take advantage of some collection stuff here
    }
}