<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectRequirement extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectRequirementFactory> */
    use HasFactory, SoftDeletes;

    public const PRIORITIES = ['low', 'medium', 'high'];

    public const STATUSES = ['todo', 'in_progress', 'done'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'module_name',
        'page_name',
        'title',
        'details',
        'priority',
        'status',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
