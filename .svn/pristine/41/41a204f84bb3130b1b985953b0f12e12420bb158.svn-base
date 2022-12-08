<?php

namespace Modules\Recruit\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailInteview extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $subject;
    public function __construct($data, $subject)
    {
        $this->data = $data;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->data['typeSendMail'] == 1){
            $view = 'recruit::email.refused-to-interview';
        } else if($this->data['typeSendMail'] == 2) {
            $view = 'recruit::email.interview-mail';
        }
        else if($this->data['typeSendMail'] == 3){
            $view = 'recruit::email.refuse-to-work';
        }
        return $this->view($view)
            ->from($address = 'info@akb.com.vn', $name = 'AKB Tuyển dụng')
            ->subject($this->subject)
            ->replyTo('hr@akb.vn')
            ->with('data', $this->data);
    }
}
