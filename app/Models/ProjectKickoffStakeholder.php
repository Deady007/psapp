<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ProjectKickoffStakeholder extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectKickoffStakeholderFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_kickoff_id',
        'stakeholder_type',
        'stakeholder_id',
    ];

    public function kickoff(): BelongsTo
    {
        return $this->belongsTo(ProjectKickoff::class, 'project_kickoff_id');
    }

    public function stakeholder(): MorphTo
    {
        return $this->morphTo();
    }
}
