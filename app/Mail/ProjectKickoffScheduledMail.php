<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\ProjectKickoff;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectKickoffScheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Project $project, public ProjectKickoff $kickoff) {}

    public function build(): self
    {
        return $this->subject('Kick-off scheduled: '.$this->project->name)
            ->view('emails.project_kickoff_scheduled', [
                'project' => $this->project,
                'kickoff' => $this->kickoff,
            ]);
    }
}
