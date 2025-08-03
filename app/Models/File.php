<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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

    public function targets(): HasMany{
        return $this->hasMany(FileTarget::class);
    }

    // Methods
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
    #[Scope]
    protected function belongsToTeacher(Builder $query, $teacherId) : void
    {
        // This scope for files that targets teachers sections in his subjects ,
        // Note: The file could be for multiple teachers
        // this function should be edited to match year filtering
         $query->join('file_targets', 'files.id', '=', 'file_targets.file_id')
            ->join('teacher_section_subjects as tss', function ($join) use ($teacherId) {
                $join->on('tss.subject_id', '=', 'files.subject_id')
                    ->on('tss.section_id', '=', 'file_targets.section_id')
                    ->where('tss.teacher_id', $teacherId)
                    ->where('tss.is_active', true);
            })
            ->select('files.*')
            ->distinct();
    }

}
