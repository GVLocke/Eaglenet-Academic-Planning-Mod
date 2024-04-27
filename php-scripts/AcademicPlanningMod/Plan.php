<?php

namespace AcademicPlanningMod;

use InvalidArgumentException;

class Plan {
    // Properties
    public array $years; // array of year objects
    public array $all_courses; // array of all courses in every semester
    public mixed $connect;

    // Methods
    public function __construct($connect) {
        $this->connect = $connect;
        $this->years = array();
        for ($i = GradeLevel::FRESHMAN; $i <= GradeLevel::SENIOR; $i++) {
            $this->years[(int)$i] = new Year($i);
        }
    }

    public function getCompletedCoursesBeforeSemester(GradeLevel $gradeLevel, Term $term) : array {
        $completed_courses = array();
        for ($i = GradeLevel::FRESHMAN; $i < $gradeLevel; $i++) {
            foreach ($this->years[(int)$i]->getAllCourses() as $course) {
                $completed_courses[] = $course;
            }
        }
        switch ($term) {
            case Term::FALL:
                break;
            case Term::SPRING:
                foreach ($this->years[(int)$gradeLevel]->fall->courses as $course) {
                    $completed_courses[] = $course;
                }
                break;
            case Term::BOTH:
                throw new InvalidArgumentException('Term must be Fall or Spring, not both.');
        }
        return $completed_courses;
    }
}