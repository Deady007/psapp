<?php

namespace App\Models;

use App\Services\ProjectBoardProvisioner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUSES = ['draft', 'active', 'on_hold', 'completed'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'name',
        'code',
        'issue_prefix',
        'issue_sequence',
        'description',
        'status',
        'start_date',
        'due_date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function kickoff(): HasOne
    {
        return $this->hasOne(ProjectKickoff::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(ProjectRequirement::class);
    }

    public function driveFolders(): HasMany
    {
        return $this->hasMany(DocumentFolder::class);
    }

    public function driveDocuments(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function rfpDocuments(): HasMany
    {
        return $this->hasMany(RfpDocument::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function boards(): HasMany
    {
        return $this->hasMany(ProjectBoard::class);
    }

    public function developmentBoard(): HasOne
    {
        return $this->hasOne(ProjectBoard::class)->where('type', ProjectBoard::TYPE_DEVELOPMENT);
    }

    public function testingBoard(): HasOne
    {
        return $this->hasOne(ProjectBoard::class)->where('type', ProjectBoard::TYPE_TESTING);
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
            'issue_sequence' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Project $project) {
            app(ProjectBoardProvisioner::class)->ensureBoards($project);
        });
    }
}
