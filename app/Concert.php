<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Concert
 *
 * @property-read mixed $formatted_date
 * @property-read mixed $formatted_start_time
 * @property-read mixed $ticket_price_in_dollars
 * @mixin \Eloquent
 */
class Concert extends Model
{
    //
    protected $guarded = [];
    protected $dates = [
        'date',
        'published_at'

    ];

    public function scopePublished($query)
    {
        // implement that where clause
        return $query->whereNotNull('published_at');
    }
    // does a little refactoring in the view by pulling some logic from the view into the Model computed property
    // able to drive out a unit test as we created this computed property
    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    public function getTicketPriceInDollarsAttribute()
    {
        return number_format($this->ticket_price / 100 , 2);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // create method and run test again
    public function orderTickets($email, $ticketQuantity)
    {
        $order = $this->orders()->create([
            'email' => $email
        ]);

        // we just need to create some tickets to attach to that order
        foreach (range(1, $ticketQuantity) as $i)
        {
            // foreach iteration of this loop we will just add a new ticket to this order
            $order->tickets()->create([]);  // create all of the new tickets for this order
        }
        return $order;
    }

    public function addTickets($quantity)
    {
        // create method then run test to see the next error


        foreach (range(1, $quantity) as $i)
        {
            // foreach iteration of this loop we will just add a new ticket to this order
            $this->tickets()->create([]);  // create all of the new tickets for this order
        }
    }

    public function ticketsRemaining()
    {
        // so up until now tickets have only been related to orders
        // our tickets table has an order_id column but no concert id column
        // if we are saying that we are going to be able to create tickets and associate them with a concert
        // before we actually sell those tickets and add them to an order
        // then we are going to need to define a relationship between the concerts and the tickets
        // so lets just assume that relationship existed for the purpose of getting through test
        return $this->tickets()->count();

    }
}
