<?php


use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;
    /** @test */
    function can_get_formatted_date()
    {
        // Test code
        //Act phase
        /*
         * Arrange phase
         * Create a concert with a known date
         *
         */
        $concert = factory(Concert::class)->make([
           'date' => Carbon::parse('2016-12-01 8:00pm'),
        //  now our test is a lot clearer because its focused to the stuff that is relevant to the test
        ]);
        /*$
        concert = Concert::create([
        ]);
        */

        /*
         * Assert phase
         * Retrieve the formatted date
         */
        // inline this below --> refactoring for speed --> $date = $concert->formatted_date;

        /*
         * Assert Phase
         * Verify the date is formatted as expected
         */
//        $this->assertEquals('December 1, 2016', $date);
        $this->assertEquals('December 1, 2016', $concert->formatted_date);  // now have only 2 line test which is nice and easy to read

        // phpunit assertions you always put (expected_value, actual_value) else you'll get confusing errors in console
    }

    /** @test */
    function can_get_formatted_start_time()
    {
        // similiar to Test code above
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 17:00:00'),
            //  now our test is a lot clearer because its focused to the stuff that is relevant to the test
        ]);
        // since we kind of want to assert that what we are actually getting back is this actual format
        // lets choose a different format it so we actually know that we are doing some real formatting here
        // so instead of doing 8:00pm do 17:00:00 which is 5:00pm and now we can make an assertion that we actually get back 5:00pm
        // and we know something is actually happening to this string
        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    /** @test */
    function can_get_ticket_price_in_dollars()
    {
        // Test code
        $concert = factory(Concert::class)->make([
            'ticket_price' => 6750,
            //  now our test is a lot clearer because its focused to the stuff that is relevant to the test
        ]);
        return $this->assertEquals('67.50', $concert->ticket_price_in_dollars);

    }

    /** @test */
    function concerts_with_a_published_at_date_are_published()
    {
        // Test code
        // Arrange
        $publishedConcertA = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 week')]);
        $publishedConcertB = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 week')]);
        $unpublishedConcert = factory(Concert::class)->create(['published_at' => null]);

        // Act
        // try and retrieve some published concerts
        $publishedConcerts = Concert::published()->get();

        // Assert
        // we want to make sure $publishedConcerts contains the 2 published concerts above
        // but doesn't contain the unpublishedConcert
        // we can do that with just some true/false assertions
        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));


    }

    // name of test is just a simple name to get started
    // then maybe later we will add more as we find more edge cases but this is how I get the ball rolling
    /** @test */
    function can_order_concert_tickets()
    {
        // Test code
        // Arrange Phase
        //      test needs a concert
        $concert = factory(Concert::class)->create(); // not anything need to specify about the concert in this case but if there is we will come back and add it


        // Act Phase
        //      we want to be able to order tickets the way we talked about
//        $order = $concert->orderTickets(email, ticketQuantity);
        $order = $concert->orderTickets('jane@example.com', 3);

        // Assert Phase
        //      just want to basically make the same sort of assertions that we are making in that integration test
        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3, $order->tickets()->count());
        // so lets run this test and work towards implementing this

    }

    /** @test */
    function can_add_tickets()
    {
        // Test code

        // Arrange ... we will just create a concert
        $concert = factory(Concert::class)->create();
        // Act  ... will try to add some tickets to that concert
        $concert->addTickets(50);
        // Assert ... that the concert does in fact have 50 tickets remaining
        $this->assertEquals(50, $concert->ticketsRemaining());  // ticketsRemaining() method doesn't exist yet but we will try and drive out in this test
        
    }
}
