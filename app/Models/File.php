<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes;
    protected  $fillable = [
        'subject_id',
        'title',
        'description',
        'size',
        'file',
        'publish_date',
        'type',
        'created_by',
    ];
    protected $casts =[
        'publish_date' => 'datetime',
    ];

    protected $with = ['targets.grade' , 'targets.section.grade'];
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }
    public function targets(): HasMany{
        return $this->hasMany(FileTarget::class);
    }
    public function loadSectionAndGrade(): void
    {
        // THIS FUNCTION IS USED TO LOAD TARGETS FOR STORED OR UPDATED FILE
        $this->load('targets.section.grade', 'targets.grade');
    }
    public function loadDeletedTargets(): static
    {
        if ($this->trashed()) {
            $targets = $this->targets()->onlyTrashed()
                ->where('deleted_at', $this->deleted_at)
                ->get();
            $this->setRelation('targets', $targets);
        }
        return $this;

    }
}
