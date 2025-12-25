<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectBoardColumn extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectBoardColumnFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_board_id',
        'name',
        'position',
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(ProjectBoard::class, 'project_board_id');
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class, 'project_board_column_id');
    }

    public function testingCards(): HasMany
    {
        return $this->hasMany(TestingCard::class, 'project_board_column_id');
    }

    public function fromHistories(): HasMany
    {
        return $this->hasMany(StoryStatusHistory::class, 'from_column_id');
    }

    public function toHistories(): HasMany
    {
        return $this->hasMany(StoryStatusHistory::class, 'to_column_id');
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }
}
