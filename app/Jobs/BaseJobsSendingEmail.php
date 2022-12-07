<?php


namespace App\Jobs;


use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class BaseJobsSendingEmail
{
    protected $subjectMail;
    protected $contentMail;
    protected $arrMailAddressFrom;
    protected $mailNameFrom;
    protected $arrMailAddressTo;
    protected $arrMailCC;
    protected $dateTimeAction;


    public function __construct($subjectMail, $contentMail, $arrMailAddressFrom, $mailNameFrom, $arrMailAddressTo, $arrMailCC, $dateTimeAction)
    {
        $this->subjectMail = $subjectMail;
        $this->contentMail = $contentMail;
        $this->arrMailAddressFrom = $arrMailAddressFrom;
        $this->mailNameFrom = $mailNameFrom;
        $this->arrMailAddressTo = $arrMailAddressTo;
        $this->arrMailCC = $arrMailCC;
        $this->dateTimeAction = $dateTimeAction;
    }

    protected function sendEmailHTML()
    {
        try {
            Mail::send([], [], function (Message $message) {
                $message->from($this->arrMailAddressFrom, $this->mailNameFrom)
                    ->to($this->arrMailAddressTo)
                    ->cc($this->arrMailCC !== null ? $this->arrMailCC : [])
                    ->subject($this->subjectMail)
                    ->setBody($this->contentMail, 'text/html');
            });
        } catch (\Exception $e) {

        }
    }
}
