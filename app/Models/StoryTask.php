<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryTask extends Model
{
    /** @use HasFactory<\Database\Factories\StoryTaskFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'story_id',
        'title',
        'notes',
        'is_completed',
        'assignee_id',
    ];

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
        ];
    }
}
