<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use Carbon\Carbon;

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

/*
 * $factory used to define a new factory
 * $factory->define([Name of the Class], callback that is going to take a instance of faker...type hint Faker\Generator $faker
 * inside of closure we just want to return a list of attributes to use for our Concert by default
 * so head over to our Concert Class and grab data and stick here
 * so what I would do is go thru this and make it clear that this stuff is sample data
 * there is 2 approaches that are used when creating a factory
 *      one approach is to use the random data using the $faker object
 *          which is more helpful when your using model factories for doing seeds as well as tests
 *      but for just using factories in tests
 *          using Static data is fine & makes debugging almost a little easier because its clear which data is fake and which is not
 */

$factory->define(App\Concert::class, function(Faker\Generator $faker) {
    return [
        'title' => 'Example Band',
        'subtitle' => 'with The Fake Openers',
        // since we want this to be a real date object and have it persist to the database in a date column
        // concert date time combined with the start time of the concert
        // if we just put in some hard coded date, it could cause problems down the road
        // because eventually we are probably going to be working with stuff that actually depends on date either
        // being like in the future or being in the past & depending on what time you run your tests
        // you might have different results which is not really ideal
        // so a trick that I like to use is to always use relative dates
        // so if we want this date to always be a date in the future.. since concerts are in the future and not in the past
        //'date' => Carbon::parse('2016-12-01 8:00pm'),
//        'date' => Carbon::parse('+2 weeks'),
        'date' => Carbon::parse('December 13, 2016 8:00pm'),
        // Carbon will add 2 weeks to the current date
        // so the concert will always be 2 weeks from the time we run our test
        'ticket_price' => 2000,
        // common recommendation is to not store currency & money as floating point numbers in your database because of floating point math issues
        // instead store currency as cents (INTEGER) which is what stripe does..will be using stripe to handle payments
        'venue' => 'The Example Theatre',
        'venue_address' => '123 Example Layne',
        'city' => 'Fakeville',
        'state' => 'ON',
        'zip' => '90210',
        'additional_information' => 'Some sample additional information.'
//        'published_at' => '-1 week',
        // this will allow us to generate concerts on demand
    ];
});

$factory->state(App\Concert::class, 'published', function ($faker) {
   // inside this closure we just need to return any properties that were overwriting in the default factory
    return [
        'published_at' => Carbon::parse('-1 week'),
    ];
});

// this isn't necessary because in the default factory we are not providing a published_at field at all
// so it will be null by default, but being explicit about it makes it easy for anyone coming in after us understanding
//      exactly what is going on
$factory->state(App\Concert::class, 'unpublished', function ($faker) {
   // inside this closure we just need to return any properties that were overwriting in the default factory
    return [
        'published_at' => null,
    ];
});
/*
 * now in unit/ConcertTest.php instead of having to specify all of this stuff
 *      $concert = factory(name of factory class that we want to use)->create([ and pass in just an array of the attributes that we want to override ])
 *      $concert = factory(Concert::class)->create([
 *          'date' => Carbon::parse('2016-12-01 8:00pm'),
 *      ]);
 *      and not pass in any of the stuff that we don't actually care about
 *
 * factory states can make your tests more expressive
 *      and can help you remove implementation details from your acceptance tests
 *      that you should really only worry about specifying at the unit and model level
 *
 * next going to work on the ability for people to purchase concert tickets
 */