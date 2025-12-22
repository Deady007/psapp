<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfpDocument extends Model
{
    /** @use HasFactory<\Database\Factories\RfpDocumentFactory> */
    use HasFactory;

    public const STATUSES = ['queued', 'processing', 'completed', 'failed'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'requested_by',
        'status',
        'file_name',
        'file_path',
        'started_at',
        'completed_at',
        'failed_at',
        'error_message',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }
}
