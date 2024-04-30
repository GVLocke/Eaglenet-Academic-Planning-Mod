<?php
namespace AcademicPlanningMod;

use InvalidArgumentException;

class CourseOption extends Course {
    // represents a list of courses, any of which could count for meeting degree requirements
    public array $courses = array(); // array of SingleCourse
    public array $recommended = array(); // all the courses that meet this requirement that are recommended by the registrar.

    public function addOption(SingleCourse $course, int $recommended): void
    {
        if ($recommended == 1) {
            $this->recommended[] = $course;
        }
        $this->courses[] = $course;
    }

    public function filterByCredits(int $credits) : array {
        $courses = array();
        foreach ($this->courses as $course) {
            if ($course->credits_count <= $credits) {
                $courses[] = $course;
            }
        }
        return $courses;
    }

    public function courseIsOption(SingleCourse $course): bool
    {
        foreach ($this->courses as $option) {
            if ($option->equals($course)) {
                return true;
            }
        }
        return false;
    }

    public function requisitesMet(
        // This implementation returns true if any of the optional courses have all their requisites met
        array $completed_courses,
        GradeLevel $gradeLevel,
        Semester $current_semester,
        bool $honors_status): bool {
        foreach ($this->courses as $course) {
            if ($course->requisitesMet($completed_courses, $gradeLevel, $current_semester, $honors_status)) {
                return true;
            }
        }
        return false;
    }

    public function getCoursesWithRequisitesMet(
        $completed_courses,
        $gradeLevel,
        $current_semester,
        $honors_status) : array {
        if (!$this->requisitesMet($completed_courses, $gradeLevel, $current_semester, $honors_status)) {
            throw new InvalidArgumentException("None of the optional courses have their requisites met.");
        }
        $available_courses = array();
        foreach ($this->courses as $course) {
            if ($course->requisitesMet($completed_courses, $gradeLevel, $current_semester, $honors_status)) {
                $available_courses[] = $course;
            }
        }
        return $available_courses;
    }
}