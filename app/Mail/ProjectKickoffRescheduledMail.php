<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\ProjectKickoff;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectKickoffRescheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Project $project,
        public ProjectKickoff $kickoff,
        public ?\Carbon\Carbon $previousScheduledAt,
    ) {}

    public function build(): self
    {
        return $this->subject('Kick-off rescheduled: '.$this->project->name)
            ->view('emails.project_kickoff_rescheduled', [
                'project' => $this->project,
                'kickoff' => $this->kickoff,
                'previousScheduledAt' => $this->previousScheduledAt,
            ]);
    }
}
