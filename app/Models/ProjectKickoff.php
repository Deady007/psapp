<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectKickoff extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectKickoffFactory> */
    use HasFactory, SoftDeletes;

    public const STATUSES = ['draft', 'scheduled', 'completed'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'purchase_order_number',
        'scheduled_at',
        'meeting_mode',
        'stakeholders',
        'requirements_summary',
        'timeline_summary',
        'notes',
        'status',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }
}
