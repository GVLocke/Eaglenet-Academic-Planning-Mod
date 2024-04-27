<?php

namespace AcademicPlanningMod;

class SingleCourse extends Course implements Comparable {
    // Properties
    public string $course_code;
    public string $course_title;
    public int $credits_count;
    public Requisites $requisites;
    public string $description;
    public string $location;
    public int $honors;
    public Term $offered_main_campus_term;
    public int $offered_main_campus_even_year;
    public int $offered_main_campus_odd_year;

    // Methods
    public function __construct($sql_array, $connect) {
        // Takes an array from the response array generated by fetchCourses and constructs a course object.
        $this->course_code = $sql_array["course_code"];
        $this->course_title = $sql_array["course_title"];
        $this->credits_count = $sql_array["credits_count"];

        $this->requisites = fetchRequisites($this->course_code, $connect);

        $this->description = $sql_array["description"];
        $this->location = $sql_array["location"];
        $this->honors = $sql_array["honors"];
        if ($sql_array['offered_main_campus_fall'] && !$sql_array['offered_main_campus_spring']) {
            $this->offered_main_campus_term = Term::FALL;
        } else if (!$sql_array['offered_main_campus_fall'] && $sql_array['offered_main_campus_spring']) {
            $this->offered_main_campus_term = Term::SPRING;
        } else if ($sql_array['offered_main_campus_fall'] && $sql_array['offered_main_campus_spring']) {
            $this->offered_main_campus_term = Term::BOTH;
        }
        $this->offered_main_campus_even_year= $sql_array["offered_main_campus_even_year"];
        $this->offered_main_campus_odd_year = $sql_array["offered_main_campus_odd_year"];
    }

    public function printCourse(): void
    {
        echo "Course Code: " . $this->course_code . "<br>";
        echo "Course Title: " . $this->course_title . "<br>";
        echo "Credits Count: " . $this->credits_count . "<br>";
        echo "Description: " . $this->description . "<br>";
        echo "Location: " . $this->location . "<br>";
        if ($this->honors == 1) {
            echo "Honors<br>";
        }
        echo "Requisites:<br>";
        $this->requisites->printRequisites();
        if ($this->offered_main_campus_term == Term::FALL) {
            echo "Offered: Fall<br>";
        } else if ($this->offered_main_campus_term == Term::SPRING) {
            echo "Offered: Spring<br>";
        } else if ($this->offered_main_campus_term == Term::BOTH) {
            echo "Offered: Both Fall and Spring<br>";
        }
        echo "Offered Main Campus Even Year: " . $this->offered_main_campus_even_year . "<br>";
        echo "Offered Main Campus Odd Year: " . $this->offered_main_campus_odd_year . "<br>";
    }

    public function equals($object) : bool {
        if (!($object instanceof SingleCourse)) {
            return false;
        }
        return $this->course_code == $object->course_code;
    }

    public function requisitesMet(
        array $completed_courses,
        GradeLevel $gradeLevel,
        Semester $current_semester,
        bool $honors_status) : bool
    {
        return $this->requisites->allRequisitesMet(
            $completed_courses,
            $current_semester,
            $gradeLevel,
            $honors_status
        );
    }
}