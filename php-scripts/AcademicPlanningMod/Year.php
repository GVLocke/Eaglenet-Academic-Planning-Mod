<?php

namespace AcademicPlanningMod;

class Year
{
    public Semester $fall;
    public Semester $spring;
    public GradeLevel $gradeLevel;

    public function __construct(GradeLevel $gradeLevel) {
        $this->fall = new Semester(Term::FALL);
        $this->spring = new Semester(Term::SPRING);
        $this->gradeLevel = $gradeLevel;
    }

    public function getAllCourses() : array {
        $semesters = [$this->fall, $this->spring];
        $courses = array();
        foreach ($semesters as $semester) {
            foreach ($semester->courses as $course) {
                $courses[] = $course;
            }
        }
        return $courses;
    }
}