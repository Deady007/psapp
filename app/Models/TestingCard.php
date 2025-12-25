<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestingCard extends Model
{
    /** @use HasFactory<\Database\Factories\TestingCardFactory> */
    use HasFactory;

    public const RESULT_PASS = 'pass';

    public const RESULT_FAIL = 'fail';

    public const RESULTS = [
        self::RESULT_PASS,
        self::RESULT_FAIL,
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_board_id',
        'project_board_column_id',
        'story_id',
        'tester_id',
        'created_by',
        'result',
        'tested_at',
        'notes',
        'started_at',
        'completed_at',
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(ProjectBoard::class, 'project_board_id');
    }

    public function column(): BelongsTo
    {
        return $this->belongsTo(ProjectBoardColumn::class, 'project_board_column_id');
    }

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    public function tester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tester_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bugs(): HasMany
    {
        return $this->hasMany(Bug::class);
    }

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'tested_at' => 'datetime',
        ];
    }
}
