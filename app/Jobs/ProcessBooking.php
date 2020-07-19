<?php

namespace App\Jobs;

use App\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ProcessBooking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $booking;


    /**
     * Create a new job instance.
     *
     * @param Booking $booking
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Process booking

        $response = Http::retry(3, 60)->post('https://some-provider.test/api/v1/bookings', [
            'body' => '{
                  "roomId": "' . $this->booking->room_id . '",
                  "guest": {
                    "firstName": "' . $this->booking->customer_first_name . '",
                    "lastName": "' . $this->booking->customer_last_name . '",
                    "email": "' . $this->booking->email . '",
                    "phoneNumber": "' . $this->booking->phone_number . '"
                  },
                  "serviceDate": {
                    "checkin": "' . $this->booking->checkin_date . '",
                    "checkout": "' . $this->booking->checkout_date . '"
                  }
                }'
        ]);

        if ($response->ok()) {
            $this->booking->update([
                'reference_number' => $response['referenceNumber']
            ]);

            //Inform the customer of successful booking via email
        }

    }
}
