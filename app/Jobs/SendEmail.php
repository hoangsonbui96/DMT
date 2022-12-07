<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const SEND_HTML = 1;
    const SEND_VIEW = 2;
    private $type_arr = ["send_html" => self::SEND_HTML, "send_view" => self::SEND_VIEW];
    private $type_send;
    private $attributes;
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @param string $type_send
     * @param $attributes
     */
    public function __construct(string $type_send, $attributes)
    {
        //
        $this->type_send = $this->type_arr[$type_send];
        $this->attributes = $attributes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        if ($this->type_send == self::SEND_HTML) {
            $this->_sendMailHTML();
        }
        if ($this->type_send == self::SEND_VIEW) {
            $this->_sendMailView();
        }
    }

    private function _sendMailHTML()
    {
        try {
            Mail::send([], [], function (Message $message) {
                $message->from($this->attributes["arrMailAddressFrom"], $this->attributes["mailNameFrom"])
                    ->to($this->attributes["arrMailAddressTo"])
                    ->cc(null !== $this->attributes["arrMailAddressCC"] ? $this->attributes["arrMailAddressCC"] : [])
                    ->subject($this->attributes["subjectMail"])
                    ->setBody($this->attributes["contentMail"], 'text/html');
            });
        } catch (\Exception $e) {
            Log::debug("Mail HTML: " . $e->getMessage());
        }
    }

    private function _sendMailView()
    {
        $configMailFrom = config('mail.from', []);
        $mailAddressFrom = array_key_exists('address', $configMailFrom) ? $configMailFrom['address'] : '';
        $mailNameFrom = array_key_exists('name', $configMailFrom) ? $configMailFrom['name'] : '';
        $subjectMail = array_key_exists("subjectMail", $this->attributes) ? trim($this->attributes["subjectMail"]) : '';
        $viewBladeMail = array_key_exists("viewBladeMail", $this->attributes) ? trim($this->attributes["viewBladeMail"]) : '';
        $dataBinding = array_key_exists("dataBinding", $this->attributes) && is_array($this->attributes["dataBinding"]) ? $this->attributes["dataBinding"] : [];
        $mailNameFrom = array_key_exists("mailNameFrom", $this->attributes) ? $this->attributes["mailNameFrom"] : $mailNameFrom;
        $arrMailAddressTo = array_key_exists("arrMailAddressTo", $this->attributes) ? $this->attributes["arrMailAddressTo"] : '';
        $arrMailCC = array_key_exists("arrMailCC", $this->attributes) ? $this->attributes["arrMailCC"] : null;

        if (is_array($arrMailAddressTo) && !empty($arrMailAddressTo)) {
            $arrMailAddressTo = array_filter($arrMailAddressTo);
        }

        if ('' === $subjectMail || '' === $viewBladeMail || '' === $arrMailAddressTo || !isset($arrMailAddressTo) || empty($arrMailAddressTo)) {
            return;
        }
        try {
            Mail::send($viewBladeMail, $dataBinding,
                function (Message $message) use ($arrMailAddressTo, $arrMailCC, $subjectMail, $mailNameFrom, $mailAddressFrom) {
                    $message->from($mailAddressFrom, $mailNameFrom);
                    $message->to($arrMailAddressTo)
                        ->cc(null !== $arrMailCC ? $arrMailCC : [])
                        ->subject($subjectMail);
                });
        } catch (\Exception $e) {
            Log::debug("Mail: " . $e->getMessage());
        }
    }

    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
        info($exception->getMessage());
    }
}
