<?php

namespace App\Listeners;

use App\Events\PaymentApproved;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPaymentApprovedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the queued listener may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * 
     * Status of the notification after http request has been sent to api
     * 
     * @var boolean
     */
    private $status;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PaymentApproved  $event
     * @return void
     */
    public function handle(PaymentApproved $event)
    {
        $response = Http::post('https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04', [
            'user_id' => $event->payee,
            'message' => 'Aprovado',
        ]);

        $this->status = $response->ok() ? 1 : 0;

        if (!$this->status) {
            throw new Exception("Falha ao enviar notificação ao usuario {$event->payee->nome}", 408);
        }
    }
}
