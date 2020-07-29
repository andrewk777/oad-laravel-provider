<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\File;

class SendEmail extends Notification /* implements ShouldQueue  */{

    // use Queueable;

    protected $subject;
    protected $body;
    protected $docs_list;

    public function __construct($subject = '', $body = '', $docs_list = []) {
        $this->subject      = $subject;
        $this->body         = $body;
        $this->docs_list    = $docs_list;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {

        $email = (new MailMessage)
                        ->subject($this->subject)
                        ->greeting($this->body);
                        
        if (count($this->docs_list)) {
            foreach ($this->docs_list as $doc) {
                $file = File::find($doc['hash']);
                $email->attach( storage_path('app/' . $file->path ) );
            }
        }

        return $email;

    }

}
