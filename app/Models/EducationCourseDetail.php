<?php

namespace App\Models;

class EducationCourseDetail extends ProductDetail
{
    protected $table = 'education_course_details';

    protected function casts(): array
    {
        return [
            'curriculum' => 'array',
            'learning_outcomes' => 'array',
            'class_schedule' => 'array',
            'certificate_available' => 'boolean',
            'batch_start_date' => 'date',
            'batch_end_date' => 'date',
        ];
    }
}