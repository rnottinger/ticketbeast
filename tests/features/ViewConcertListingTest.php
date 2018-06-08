<?php

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewConcertListingTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function user_can_view_a_concert_listing()
    {
        // Test code
        /*
         * Steps in a traditional test
         *
         */
        // Arrange - Any Setup work
        // Act- where we run the code that we want to test the outcome of
        // Assert - where we assert what happened to verify that we got the outcome that we expected

        // Arrange
            /*
             Create a concert...so that there is a concert for the user to view
             could create concert form and fill it out as the user
                direct model access - means that your test code has direct access to the domain code in your application
                    and you can just create the objects you need directly instead of having to go through the ui
                    this has the benefit of making our code faster and also removing some duplication throughout our tests
                    instead of exercising the exact same pages all the time
                    we just do the fastest thing possible to get setup then move on to the stuff that we are actually worried about testing
                so in order to use the direct model access approach to create a concert
                  we have to start thinking about some design decisions about how we want to actually want to make this work
                  so the approach that I want to take is just basically interacting directly with Laravel's Eloquent ORM
                  to create this concert with the fields that we want
            */
//        $concert = Concert::create([
        $concert = factory(Concert::class)->states('published')->create([
           'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Lethargy',
            'date' => Carbon::parse('December 13, 2016 8:00pm'),
            // since we want this to be a real date object and have it persist to the database in a date column
            // concert date time combined with the start time of the concert
            'ticket_price' => 3250,
            // common recommendation is to not store currency & money as floating point numbers in your database because of floating point math issues
            // instead store currency as cents (INTEGER) which is what stripe does..will be using stripe to handle payments
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Layne',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
            'additional_information' => 'For tickets, call (555) 555-5555.',
//            'published_at' => Carbon::parse('-1 week')
            // store city, state, zip as separate fields so can display in different ways in the UI
        ]);
        // Act
            // View the concert listing.. since that is what we are trying to test
            // so How can we translate this idea "View Concert Listing" into code
            // Laravel gives us alot of Helpers to kind of navigate our application & work with our application ...
        //          visit() helper which lets us specify a URL to visit
        //                  and then it will give us back the response
        //                  and we can use that to make assertions about what is on the page
        $this->visit('/concerts/' . $concert->id);
        // Assert
            // Verify so that when we go to the concert listing can see the concert details
            // Laravel has a few assertion methods that lets you inspect the page and see what is there
            // see() verify that we are seeing some text on the screen
        
            // so we can use this see() helper to verify that we see all of the details about the concert
        $this->see('The Red Chord');
        $this->see('with Animosity and Lethargy');
        $this->see('December 13, 2016');
        $this->see('8:00pm');
        $this->see('32.50');
        $this->see('The Mosh Pit');
        $this->see('123 Example Layne');
        $this->see('Laraville, ON 17916');
        $this->see('For tickets, call (555) 555-5555.');

        // next run this test and using the errors as feedback walk through actually getting our application scaffold out and work towards getting this test to pass
        //  easiest way -->  ./vendor/bin/phpunit   // runs all tests in the /tests directory
        /*
         * will run the version of phpunit that has been required by laravel via composer
         * using the settings specified in phpunit.xml
         * because I like to run my test frequently
         */



    }

    /** @test */
    function user_cannot_view_unpublished_concert_listings()
    {
        // need to figure out the 3 phases of this test
        // Arrange
        //      add a concert
        // Act
        //      visit concert page
        // Assert
        //      make sure we can't see the page

//        $concert = factory(Concert::class)->create([
        $concert = factory(Concert::class)->states('unpublished')->create();
            // could create a published boolean which works ok
            // i prefer to use a nullable timestamp ..gives a little more flexibility down the road
//            'published_at' => null  // this concert hasn't been published yet
            //when a concert is published we will just track the timestamp that it was published at
            // so we know when someone published that concert

//        ]);
//        $this->visit('/concerts/' . $concert->id);
        $this->get('/concerts/' . $concert->id);

//        for our assert step we want to make sure we get a 404
        // we don't want anyone to view this concert page
        $this->assertResponseStatus('404');

    }
}
