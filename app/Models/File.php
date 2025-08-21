<?php

namespace App\Models;

use App\Enums\StringsManager\Files\FileStr;
use App\Traits\NewsAndFilesScopes;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use SoftDeletes, NewsAndFilesScopes;

    protected $fillable = [
        'subject_id',
        'title',
        'description',
        'size',
        'file',
        'publish_date',
        'type',
        'created_by',
    ];
    protected $casts = [
        'publish_date' => 'datetime',
    ];

    protected $with = ['targets'];

    public static function boot(): void
    {
        parent::boot();
        static::deleting(function ($file) {
            if (!$file->isForceDeleting()) {
                $file->targets()->delete();
            }
        });


        static::forceDeleting(function ($file) {
            if (Storage::disk(FileStr::StorageDisk->value)->exists($file->file))
                Storage::disk(FileStr::StorageDisk->value)->delete($file->file);
            $file->allTargets()->forceDelete();
        });
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(FileTarget::class)
            ->withTrashed()
            ->where('deleted_at', $this->deleted_at);

    }

    public function allTargets(): HasMany
    {
        return $this->hasMany(FileTarget::class, 'file_id')->withTrashed();
    }

    // Methods
    public function loadSectionAndGrade(): void
    {
        // THIS FUNCTION IS USED TO LOAD TARGETS FOR STORED OR UPDATED FILE
        // TODO : Modify this trash...
        $this->load('targets.section.grade', 'targets.grade');
    }


    public function getDeleteSnapshot(): self
    {
        $clone = clone $this;
        $targets = $this->targets()->get();
        $clone->setRelation('targets', $targets);
        return $clone;
    }

    public function restoreWithTargets(): static
    {
        $targets = $this->targets()->get();

        $this->restore();
        $targets->each->restore();

        $this->setRelation('targets', $targets);
        return $this;
    }

    // TODO: Improve this

    /**
     * @return bool
     * whether the file belongs to only one teacher
     */
    public function belongsToOneTeacher(): bool
    {


        $fileSections = $this->targets()->pluck('section_id')->toArray();
        $teacherSectionsIds = TeacherSectionSubject::where('teacher_id', auth()->user()->teacher->id)
            ->where('is_active', true)
            ->where('subject_id', $this->subject_id)
            ->pluck('section_id')->toArray();
        return empty(array_diff($fileSections, $teacherSectionsIds));
    }

    public function loadTargets(): void
    {
        $this->setRelation('targets', $this->targets()->get());
    }

    #[Scope]
    protected function belongsToTeacher(Builder $query, $teacherId, $subjectID, $SectionId): void
    {
        // This scope for files that targets teachers sections in his subjects ,
        // Note: The file could be for multiple teachers
        // this function should be edited to match year filtering
        $query->join('file_targets', function ($join) {
            $join->on('files.id', '=', 'file_targets.file_id')
                ->whereNull('file_targets.deleted_at');
        })
            ->join('teacher_section_subjects as tss', function ($join) use ($SectionId, $subjectID, $teacherId) {
                $join->on('tss.subject_id', '=', 'files.subject_id')
                    ->on('tss.section_id', '=', 'file_targets.section_id')
                    ->where('tss.teacher_id', $teacherId)
                    ->where('tss.is_active', true);

                if ($subjectID) $join->where('tss.subject_id', $subjectID);
                if ($SectionId) $join->where('tss.section_id', $SectionId);

            })
            ->select('files.*')
            ->distinct();
    }

    #[Scope]
    protected function forSubject(Builder $query, $subjectID): Builder
    {
        return $query->where('subject_id', $subjectID);
    }

    #[Scope]
    protected function forType(Builder $query, $type): Builder
    {
        return $query->where('type', $type);
    }

}
