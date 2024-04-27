<?php

namespace AcademicPlanningMod;

class Semester {
    // Properties
    public array $courses; // array of Course objects or CourseOption objects
    public Term $term;
    public int $num_credits; // represents the sum of the credits_count for each course in the array

    // Methods
    public function __construct(Term $term) {
        $this->courses = array();
        $this->term = $term;
        $this->num_credits = 0;
    }

    public function addCourse(SingleCourse $course): bool {
        if ($this->num_credits + $course->credits_count > 18) {
            return false;
        } else {
            $this->courses[] = $course;
            $this->num_credits += $course->credits_count;
            return true;
        }
    }
}