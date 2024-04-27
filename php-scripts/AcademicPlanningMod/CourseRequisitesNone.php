<?php

namespace AcademicPlanningMod;

class CourseRequisitesNone extends Requisites {
    public function __construct() {
        $this->has_prerequisites = false;
    }
    public function allRequisitesMet(
        array $completed_courses,
        Semester $current_semester,
        GradeLevel $gradeLevel,
        bool $honors_status): bool
    {
        return true;
    }
    
    public function printRequisites(): void
    {
        echo "This course has no listed prerequisites. <br>";
    }
}