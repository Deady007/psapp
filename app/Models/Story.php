<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Story extends Model
{
    /** @use HasFactory<\Database\Factories\StoryFactory> */
    use HasFactory;

    public const PRIORITY_LOW = 'Low';

    public const PRIORITY_MEDIUM = 'Medium';

    public const PRIORITY_HIGH = 'High';

    public const PRIORITY_CRITICAL = 'Critical';

    public const PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_MEDIUM,
        self::PRIORITY_HIGH,
        self::PRIORITY_CRITICAL,
    ];

    public const BLOCKER_REASON_WAITING_DEPENDENCY = 'waiting_dependency';

    public const BLOCKER_REASON_UNCLEAR_REQUIREMENTS = 'unclear_requirements';

    public const BLOCKER_REASON_BUG_FOUND = 'bug_found';

    public const BLOCKER_REASON_ENVIRONMENT_ISSUE = 'environment_issue';

    public const BLOCKER_REASON_PERFORMANCE = 'performance';

    public const BLOCKER_REASON_OTHER = 'other';

    public const BLOCKER_REASONS = [
        self::BLOCKER_REASON_WAITING_DEPENDENCY,
        self::BLOCKER_REASON_UNCLEAR_REQUIREMENTS,
        self::BLOCKER_REASON_BUG_FOUND,
        self::BLOCKER_REASON_ENVIRONMENT_ISSUE,
        self::BLOCKER_REASON_PERFORMANCE,
        self::BLOCKER_REASON_OTHER,
    ];

    public const BLOCKER_REASON_LABELS = [
        self::BLOCKER_REASON_WAITING_DEPENDENCY => 'Waiting dependency',
        self::BLOCKER_REASON_UNCLEAR_REQUIREMENTS => 'Unclear requirements',
        self::BLOCKER_REASON_BUG_FOUND => 'Bug found',
        self::BLOCKER_REASON_ENVIRONMENT_ISSUE => 'Environment issue',
        self::BLOCKER_REASON_PERFORMANCE => 'Performance',
        self::BLOCKER_REASON_OTHER => 'Other',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_board_id',
        'project_board_column_id',
        'issue_key',
        'issue_number',
        'title',
        'priority',
        'due_date',
        'description',
        'acceptance_criteria',
        'notes',
        'labels',
        'estimate',
        'estimate_unit',
        'reference_links',
        'database_changes',
        'database_changes_confirmed',
        'page_mappings',
        'page_mappings_confirmed',
        'blocker_reason',
        'assignee_id',
        'created_by',
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(ProjectBoard::class, 'project_board_id');
    }

    public function column(): BelongsTo
    {
        return $this->belongsTo(ProjectBoardColumn::class, 'project_board_column_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(StoryTask::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(StoryStatusHistory::class);
    }

    public function latestStatusHistory(): HasOne
    {
        return $this->hasOne(StoryStatusHistory::class)->latestOfMany('moved_at');
    }

    public function testingCard(): HasOne
    {
        return $this->hasOne(TestingCard::class);
    }

    public function bugs(): HasMany
    {
        return $this->hasMany(Bug::class);
    }

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'labels' => 'array',
            'reference_links' => 'array',
            'database_changes' => 'array',
            'database_changes_confirmed' => 'boolean',
            'page_mappings' => 'array',
            'page_mappings_confirmed' => 'boolean',
        ];
    }
}
