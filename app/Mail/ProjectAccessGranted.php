<?php

namespace App\Mail;

use App\Models\Contact;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectAccessGranted extends Mailable
{
    use Queueable, SerializesModels;

    public Contact $contact;
    public Project $project;
    public string $accessToken;
    public \Carbon\Carbon $expiresAt;

    public function __construct(Contact $contact, Project $project, string $accessToken, Carbon $expiresAt)
    {
        $this->contact = $contact;
        $this->project = $project;
        $this->accessToken = $accessToken;
        $this->expiresAt = $expiresAt;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Accès accordé au projet ' . $this->project->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.project-access-granted',
            with: [
                'contact' => $this->contact,
                'project' => $this->project,
                'accessToken' => $this->accessToken,
                'expiresAt' => $this->expiresAt,
                'accessUrl' => route('projects.show', [
                    'project' => $this->project->id,
                    'token' => $this->accessToken
                ])
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
