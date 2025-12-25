<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectBoard extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectBoardFactory> */
    use HasFactory;

    public const TYPE_DEVELOPMENT = 'Development';

    public const TYPE_TESTING = 'Testing';

    public const DEVELOPMENT_COLUMNS = [
        'Todo',
        'Assigned',
        'In Process',
        'Review',
        'Blocker',
        'Completed',
    ];

    public const TESTING_COLUMNS = [
        'Todo',
        'Assigned',
        'In Process',
        'Completed',
        'Result',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'name',
        'type',
        'database_changes',
        'page_mappings',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function columns(): HasMany
    {
        return $this->hasMany(ProjectBoardColumn::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function testingCards(): HasMany
    {
        return $this->hasMany(TestingCard::class);
    }

    public function bugs(): HasMany
    {
        return $this->hasMany(Bug::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(BoardDocument::class);
    }

    public function isDevelopment(): bool
    {
        return $this->type === self::TYPE_DEVELOPMENT;
    }

    public function isTesting(): bool
    {
        return $this->type === self::TYPE_TESTING;
    }

    /**
     * @return array<string, list<string>>
     */
    public static function columnDefinitions(): array
    {
        return [
            self::TYPE_DEVELOPMENT => self::DEVELOPMENT_COLUMNS,
            self::TYPE_TESTING => self::TESTING_COLUMNS,
        ];
    }

    protected function casts(): array
    {
        return [
            'database_changes' => 'array',
            'page_mappings' => 'array',
        ];
    }
}
