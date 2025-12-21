<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectKickoff extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectKickoffFactory> */
    use HasFactory, SoftDeletes;

    public const STATUSES = ['planned', 'scheduled', 'completed'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'purchase_order_number',
        'planned_at',
        'scheduled_at',
        'completed_at',
        'meeting_mode',
        'site_location',
        'meeting_link',
        'requirements_summary',
        'timeline_summary',
        'notes',
        'status',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function stakeholderLinks(): HasMany
    {
        return $this->hasMany(ProjectKickoffStakeholder::class);
    }

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'planned_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }
}
