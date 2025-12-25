<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoardDocument extends Model
{
    /** @use HasFactory<\Database\Factories\BoardDocumentFactory> */
    use HasFactory;

    public const TYPE_USER_MANUAL = 'User Manual';

    public const TYPE_VALIDATION_REPORT = 'Validation Report';

    public const TYPES = [
        self::TYPE_USER_MANUAL,
        self::TYPE_VALIDATION_REPORT,
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_board_id',
        'type',
        'content',
        'storage_path',
        'file_name',
        'generated_by',
        'generated_at',
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(ProjectBoard::class, 'project_board_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }
}
