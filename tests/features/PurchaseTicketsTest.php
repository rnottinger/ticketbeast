<?php
/**
 * Created by PhpStorm.
 * User: richardottinger
 * Date: 2/8/17
 * Time: 3:20 PM
 */


use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Concert;
use Illuminate\Foundation\Testing\WithoutMiddleware;
 use Illuminate\Foundation\Testing\DatabaseMigrations;
 use Illuminate\Foundation\Testing\DatabaseTransactions;

 class PurchaseTicketsTest extends TestCase
 {
     use DatabaseMigrations;


     protected function setUp() {
         parent::setUp();

         $this->paymentGateway = new FakePaymentGateway;
         $this->app->instance(PaymentGateway::class, $this->paymentGateway);

     }

     /** @test */
     function customer_can_purchase_concert_tickets(){
//     {
//         $paymentGateway = new FakePaymentGateway;
//         $this->app->instance(PaymentGateway::class, $paymentGateway);
         // later on we might change this as we add more edge cases and maybe this test reveals itself to be something more specific
         // but to get started this is usually the approach that I take
         // just one test that is named pretty similar to the actual Test Class itself

         // Arrange
         // Act
         // Assert

         // Arrange
         // Create a concert
         /*
         $concert = factory(Concert::class)->states('published')->create([

         ]);->states('published')
         */
         $concert = factory(Concert::class)->create(['ticket_price' => 3250 ]);

         // Act
         // Purchase Concert tickets
         //     We are going to be driving this out as an endpoint test
         //     so we are going to get the payment token from stripe
         //     as well as the email address of the customer
         //     and the number of tickets that they want to purchase
         //     then we are going to send this data structure back to the server to kick off the process of actually purchasing those tickets
         //     since we are going to be sending this data as a ajax call from the frontend
         //     I think the nicest format for the data would be JSON
         //         so we can make a JSON request to some endpoint using the json helper that we get inside of laravel's Test Case
//         $this->json('POST',"/purchase-tickets")
             // create an endpoint as kind of like a action name
            // but I prefer to stick with a resourcefull routing setup
            // and think how can I shoehorn this into a create, read, update, delete action (generic CRUD action)
         // so what are we trying to do
         //     we are trying to make sure that by the time this is done that an order exists for a customer
         // so it sounds like what we are trying to do is create an order
         $this->json('POST',"/concerts/{$concert->id}/orders", [
             'email' => 'john@example.com',
             'ticket_quantity' => 3,
             'payment_token' => $this->paymentGateway->getValidTestToken(),  // write this code that we wish existed and comeback to it later
         ]);




         // valid payment token that is going to allow us to charge this customer against stripe
         // how do we get a valid payment token from
         // well stripe's testing environment can give us a valid payment token if we pass along a testing card that stripe knows about
         // so in theory if we had stripe encapsulated by something behind like a $paymentGateway->getValidTestToken(),
         //  that would give us back a valid token that we could use for testing
         $this->assertResponseStatus(201);  // http response code for created
         // so now getting error about 404 instead of 201 which makes more sense
         // but how can we see what the actual error is that caused this 404
         // what is happening Laravel is throwing an exception which is getting caught by the Exception handler
         // App\Exceptions\handler

         // Assert
         // Make sure the customer was charged the correct amount
         // we can also use stripe's api to figure out what charges were made during this request
         // we could add a new method to this $paymentGateway
         $this->assertEquals(9750,$this->paymentGateway->totalCharges());

         // Make sure that an order exists for this customer
        // we are introducing this new concept of a new order
         // maybe an order is something that belongs to a concert... a concert has a bunch of orders

         $order = $concert->orders()->where('email', 'john@example.com')->first();
         // currently getting error cause there is no orders relationship defined on the concert
         //     add method to Concert Model class
         $this->assertNotNull($order);  // from here we can work towards trying to get this test to pass
         // so in the next video we'll run this test and see what errors we will get and start implementing this feature

//         $this->assertTrue($concert->orders->contains(function ($order) {  // call back is going to take an order
//             return $order->email == 'john@example.com';
//         }));  // this says this concert does have an order for this person

         // maybe we also want to verify that that order also has 3 tickets associated with it
         // get that order

         $this->assertEquals(3, $order->tickets()->count());  // again this is code that doesn't exist...we are just playing around and just kind of designing our api and sketch things out
         $this->assertEquals(3, $order->tickets()->count());  // again this is code that doesn't exist...we are just playing around and just kind of designing our api and sketch things out
         // instead of using tickets as a property...which is going to execute the query
         // which to me is one of the biggest benefits of working in this sort of Test Driven Approach
         // the tests give you a good place to play with the code and figure out exactly what you want to land on in terms of design
         // so looking at this it looks like we can simplify it a little bit
         // we could probably change the first assertion to just look at the order
         // get order first the assertNotNull
     }


     /** @test */
     function email_is_required_to_purchase_tickets()
     {
         // Test code
         // Arrange
//         $this->disableExceptionHandling();

//         $paymentGateway = new FakePaymentGateway;
//         $this->app->instance(PaymentGateway::class, $paymentGateway);
         $concert = factory(Concert::class)->states('published')->create();  // we don't need to specify any details because this test is going to fail early
         // Act
         $this->json('POST',"/concerts/{$concert->id}/orders", [
//             'email' => 'john@example.com',   do not provide the email so we can prove that it is required
             'ticket_quantity' => 3,
             'payment_token' => $this->paymentGateway->getValidTestToken(),  // write this code that we wish existed and comeback to it later
         ]);

         // Assert
         /*
          * so what is the http status code that we should expect to get back
          * Laravel is kind of standardized on using the 422 status code for validation stuff
          *
          */
         $this->assertResponseStatus(422);  // lets get this passing and then maybe adding another assertion or 2

         // also would like to verify that the errors that comeback contain some sort of errors related to the emails so that we know that is the cause
         // to do that we will need to inspect the response that comes back and make some assertions about its contents

         $this->assertArrayHasKey('email',$this->decodeResponseJson());
//         dd($this->decodeResponseJson());
         // which is going to return the decoded json of the response so if we dump this out we can see what we get back
         /*
          *
                .array:1 [
                  "email" => array:1 [
                    0 => "The email field is required."
                  ]
                ]
          */
     }

     /** @test */
     function cannot_purchase_more_tickets_than_remain()
     {
         // Test code
         // Arrange
//         $concert = factory(Concert::class)->states('published')->create([
//             'total_tickets_available' => 200
//         ]);

         $concert = factory(Concert::class)->states('published')->create([]);
         // add a method to the concert that lets us generate tickets for the concert
         $concert->addTickets(50);
         // even though we know this addTickets() method doesn't exist yet...lets finish scaffolding out the test
         // and let the test tell us what we need to implement first
         // Act
         $this->json('POST',"/concerts/{$concert->id}/orders", [
             'email' => 'john@example.com',
             'ticket_quantity' => 51,  // we want to order more than remain for the concert
             'payment_token' => $this->paymentGateway->getValidTestToken(),  // write this code that we wish existed and comeback to it later
         ]);
         // Assert
        $this->assertResponseStatus(422);  // 422 to say that the request could not be processed
         // we also want to make sure that an order was not created for this customer
         $order = $concert->orders()->where('email', 'john@example.com')->first();
         $this->assertNull($order);  // no order was created
         // now since no order was created we should probably verify that the customer was not charged
         // we don't want to charge them for 51 tickets and then not even give them any tickets
         $this->assertEquals(0, $this->paymentGateway->totalCharges());
         // finally I want to assert that there is still 50 tickets left for this concert
         $this->assertEquals(50, $concert->ticketsRemaining());  // will return the number of tickets that are unsold for this concert
         

     }
 }
