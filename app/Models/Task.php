<?php

namespace App\Models;

use App\Observers\TaskObserver;
use App\Services\TaskService;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use NadLambino\Uploadable\Models\Traits\HasUpload;

#[ObservedBy(TaskObserver::class)]
class Task extends Model
{
    use HasFactory, SoftDeletes, HasUpload;

    protected $fillable = [
        'parent_id',
        'title',
        'content',
        'status_id',
        'published_at'
    ];

    protected $appends = [
        'deleted_since',
        'to_be_deleted_at'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at'   => 'datetime:Y-m-d h:i A',
            'updated_at'   => 'datetime:Y-m-d h:i A',
            'deleted_at'   => 'datetime:Y-m-d h:i A',
            'published_at' => 'datetime:Y-m-d h:i A',
        ];
    }

    /**
     * Get the deleted_since attribute.
     *
     * @return string|null
     */
    public function getDeletedSinceAttribute(): ?string
    {
        return $this->deleted_at ? Carbon::parse($this->deleted_at)->diffForHumans() : null;
    }

    /**
     * Get the to_be_deleted_at attribute.
     *
     * @return string|null
     */
    public function getToBeDeletedAtAttribute(): ?string
    {
        return $this->deleted_at
            ? Carbon::parse($this->deleted_at)->addDays(config('app.delete_trash_days_old', 30))->endOfDay()->diffForHumans()
            : null;
    }

    /**
     * Relation method between Task and Status
     *
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Relation method between Task and User
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation method between child Task and parent Task
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    /**
     * Relation method between parent Task and child Task
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function scopeSiblings($query): Builder
    {
        return $query
            ->where('parent_id', $this->parent_id)
            ->whereNot('id', $this->id)
            ->published();
    }

    public function scopeNotDone($query): Builder
    {
        return $query->where('status_id', '!=', TaskService::DONE);
    }

    public function scopeDone($query): Builder
    {
        return $query->where('status_id', TaskService::DONE);
    }

    /**
     * Scope a query to only include published tasks.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query): Builder
    {
        return $query->whereNotNull('published_at');
    }
}
