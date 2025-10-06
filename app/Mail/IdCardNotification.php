<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class IdCardNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $studentName;
    public $courseName;
    public $idCardPath;

    /**
     * Create a new message instance.
     */
    public function __construct($studentName, $courseName, $idCardPath)
    {
        $this->studentName = $studentName;
        $this->courseName = $courseName;
        $this->idCardPath = $idCardPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎓 Your Student ID Card for ' . $this->courseName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.id-card-notification',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath(public_path($this->idCardPath))
                ->as('Student_ID_Card.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
