<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Concert;
use Illuminate\Http\Request;

class ConcertOrdersController extends Controller
{
    //

    private $paymentGateway;
    // we will TypeHint this with a PaymentGatway Interface so that we can replace it with
    // a different implementation in production than in our tests

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }


    // in PurchaseTicketsTest we have 'ticket_quantity' coming in post request
    // and we have $concert->id coming in url parameters
    public function store($concertId)
    {
        $this->validate(request(), [
            'email' => 'required'
        ]);  //run test again

        $concert = Concert::find($concertId);

        // Charging the customer.. first refactor this section
        // trying to get rid of temporary variables

//        $ticketQuantity = request('ticket_quantity');
//        $amount = $ticketQuantity * $concert->ticket_price;
//        $token = request('payment_token');
        // so where are we going to get this $paymentGateway from...lets inject it thru the constructor for now
//        $this->paymentGateway->charge($amount, $token);

        // token variable is just being used in one place...inline and git rid of it...Command Option N...or Ctrl Click Variable--> Refactor-->
        $this->paymentGateway->charge(request('ticket_quantity') * $concert->ticket_price, request('payment_token'));
        // above all the work necessary to charge the customer the tickets
        // below is work to create the order

        // Creating the order...now refactor this ...to clean up a bit
        // try to pay attention to in controllers where calls directly interacting with the models relationship
        // ideally I'd rather like to encapsulate this in a method on the model so we don't have to worry about the details of this
        // and instead call something a little bit more expressive
        // all we are trying to do is create a new order here based on the ticket quantity so those 2 things would be the parameters to some method call that we would make that would kind of hide all of these details from us
        // maybe it would look something like
//        $order = $concert->orderTickets($email, ticketQuantity);
        // so what I would like to do is drive out this api through a unit test for the concert
        // and then come back and replace this stuff
        // with a call to that method once we have driven it out... tests/unit/ConcertTest.php
//        $order = $concert->orders()->create([
//            'email' => request('email')
//        ]);
//
//        // we just need to create some tickets to attach to that order
//        foreach (range(1, request('ticket_quantity')) as $i)
//        {
//            // foreach iteration of this loop we will just add a new ticket to this order
//            $order->tickets()->create([]);
//        }
        $order = $concert->orderTickets(request('email'), request('ticket_quantity'));
        return response()->json([], 201);
    }
}
