<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendingEmailProjectPM extends BaseJobsSendingEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param $subjectMail
     * @param $contentMail
     * @param $arrMailAddressFrom
     * @param $mailNameFrom
     * @param $arrMailAddressTo
     * @param $arrMailCC
     * @param null $dateTimeAction
     */
    public function __construct($subjectMail, $contentMail, $arrMailAddressFrom, $mailNameFrom, $arrMailAddressTo, $arrMailCC, $dateTimeAction = null)
    {
        //
        parent::__construct($subjectMail, $contentMail, $arrMailAddressFrom, $mailNameFrom, $arrMailAddressTo, $arrMailCC, $dateTimeAction);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $this->sendEmailHTML();
    }
}
