<?php
    include "course-classes.php";
    include "connect-to-db.php";
    $start_year = 2022; // change this to a session var or HTTP post or whatever
    $odd_year_courses = array();
    $even_year_courses = array();
    $spring_courses = array();
    $fall_courses = array();
    
    $sql = "select * from majors_minors_classes where course_code like 'CS-%'";
    $cs_courses = fetchCourses($sql, $connect);

    foreach ($cs_courses as $key => $value) {
        $course = new Course();
        $course->parse_sql_arry($value);
        if ($course->offered_main_campus_fall) {
            $fall_courses[$course->course_code] = $course;
        }
        if ($course->offered_main_campus_spring) {
            $spring_courses[$course->course_code] = $course;
        }
        if ($course->offered_main_campus_even_year) {
            $even_year_courses[$course->course_code] = $course;
        }
        if ($course->offered_main_campus_odd_year) {
            $odd_year_courses[$course->course_code] = $course;
        }

        $course->printCourse();
        echo "---------------------------------------------<br>";
    }
    
    // $freshman_fall = new Semester(1, Term::FALL);
?>