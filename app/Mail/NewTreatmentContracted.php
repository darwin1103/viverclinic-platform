<?php

namespace App\Mail;

use App\Models\ContractedTreatment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewTreatmentContracted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * La instancia del tratamiento contratado.
     */
    public $contractedTreatment;

    /**
     * Create a new message instance.
     */
    public function __construct(ContractedTreatment $contractedTreatment)
    {
        // Cargamos las relaciones necesarias para no hacer consultas en la vista
        $this->contractedTreatment = $contractedTreatment->load(['user', 'branch', 'treatment']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Compra: ' . $this->contractedTreatment->treatment->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.treatments.contracted',
            with: [
                'appName' => config('app.name'),
                'logoUrl' => $this->contractedTreatment->branch->logo
                    ? asset('storage/' . $this->contractedTreatment->branch->logo)
                    : null,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
