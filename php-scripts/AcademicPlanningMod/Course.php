<?php
namespace AcademicPlanningMod;
abstract class Course
{
    abstract public function requisitesMet(
        array $completed_courses,
        GradeLevel $gradeLevel,
        Semester $current_semester,
        bool $honors_status
    ) : bool;
}