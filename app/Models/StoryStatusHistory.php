<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryStatusHistory extends Model
{
    /** @use HasFactory<\Database\Factories\StoryStatusHistoryFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'story_id',
        'from_column_id',
        'to_column_id',
        'moved_by',
        'moved_at',
        'reason',
        'notes',
    ];

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    public function fromColumn(): BelongsTo
    {
        return $this->belongsTo(ProjectBoardColumn::class, 'from_column_id');
    }

    public function toColumn(): BelongsTo
    {
        return $this->belongsTo(ProjectBoardColumn::class, 'to_column_id');
    }

    public function mover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moved_by');
    }

    protected function casts(): array
    {
        return [
            'moved_at' => 'datetime',
        ];
    }
}
