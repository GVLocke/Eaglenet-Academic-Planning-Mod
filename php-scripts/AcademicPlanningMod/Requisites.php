<?php

namespace AcademicPlanningMod;

abstract class Requisites
{
    public bool $has_prerequisites;
    abstract function allRequisitesMet(
        array $completed_courses,
        Semester $current_semester,
        GradeLevel $gradeLevel,
        bool $honors_status
    ) : bool;
    
    abstract function printRequisites() : void;

    static function requisitesConstructor($sql_array) : Requisites {
        return empty($sql_array) ? new CourseRequisitesNone : new CourseRequisites($sql_array);
    }
}