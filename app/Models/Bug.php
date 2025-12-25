<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bug extends Model
{
    /** @use HasFactory<\Database\Factories\BugFactory> */
    use HasFactory;

    public const SEVERITY_LOW = 'Low';

    public const SEVERITY_MEDIUM = 'Medium';

    public const SEVERITY_HIGH = 'High';

    public const SEVERITY_CRITICAL = 'Critical';

    public const SEVERITIES = [
        self::SEVERITY_LOW,
        self::SEVERITY_MEDIUM,
        self::SEVERITY_HIGH,
        self::SEVERITY_CRITICAL,
    ];

    public const STATUS_OPEN = 'Open';

    public const STATUS_IN_PROGRESS = 'In Progress';

    public const STATUS_RESOLVED = 'Resolved';

    public const STATUS_CLOSED = 'Closed';

    public const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_IN_PROGRESS,
        self::STATUS_RESOLVED,
        self::STATUS_CLOSED,
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_board_id',
        'story_id',
        'testing_card_id',
        'issue_key',
        'issue_number',
        'title',
        'description',
        'severity',
        'steps_to_reproduce',
        'status',
        'assignee_id',
        'reported_by',
        'found_at',
        'resolved_at',
        'closed_at',
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(ProjectBoard::class, 'project_board_id');
    }

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    public function testingCard(): BelongsTo
    {
        return $this->belongsTo(TestingCard::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    protected function casts(): array
    {
        return [
            'found_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }
}
