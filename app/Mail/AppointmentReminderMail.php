<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $appointment;
    public $user;
    public $branch;
    public $treatment;
    public $confirmationUrl;
    public $appName;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment)
    {
        // Eager load relationships to ensure they are available in the queue
        $this->appointment = $appointment->load('contractedTreatment.user', 'contractedTreatment.branch', 'contractedTreatment.treatment');
        $this->user = $this->appointment->contractedTreatment->user;
        $this->branch = $this->appointment->contractedTreatment->branch;
        $this->treatment = $this->appointment->contractedTreatment->treatment;
        $this->confirmationUrl = route('client.contracted-treatment.index');
        $this->appName = config('app.name');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recordatorio de tu próxima cita en ' . $this->appName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointments.reminder',
            with: [
                'appointment' => $this->appointment,
                'user' => $this->user,
                'branch' => $this->branch,
                'treatment' => $this->treatment,
                'confirmationUrl' => $this->confirmationUrl,
                'appName' => $this->appName
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
