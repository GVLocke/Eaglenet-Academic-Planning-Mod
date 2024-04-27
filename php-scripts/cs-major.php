<?php
global $connect;
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
        $course = new SingleCourse($value, $connect);
        if ($course->offered_main_campus_term == Term::FALL || $course->offered_main_campus_term == Term::BOTH) {
            $fall_courses[$course->course_code] = $course;
        }
        if ($course->offered_main_campus_term == Term::SPRING || $course->offered_main_campus_term == Term::BOTH) {
            $spring_courses[$course->course_code] = $course;
        }
        if ($course->offered_main_campus_even_year) {
            $even_year_courses[$course->course_code] = $course;
        }
        if ($course->offered_main_campus_odd_year) {
            $odd_year_courses[$course->course_code] = $course;
        }
    }

    // create the statistics option:
    $business_stats = fetchSingleCourse("BUS-2193", $connect);
    $prob_and_stat = fetchSingleCourse("MTH-3183", $connect);
    $applied_stat = fetchSingleCourse("MTH-2103", $connect);
    $stats_for_behavioral = fetchSingleCourse("PSY-2383", $connect);

    $stats_option = new CourseOption();
    $stats_option->addOption($business_stats, 1);
    $stats_option->addOption($prob_and_stat, 0);
    $stats_option->addOption($applied_stat, 0);
    $stats_option->addOption($stats_for_behavioral, 0);

    $requirements = array(
        "CS-1233",
        "CS-1513",
        "CS-2173",
        "CS-2243",
        "CS-2423",
        "CS-2823",
        "CS-3363",
        "CS-3533",
        "MTH-1153",
        "MTH-1163",
        "MTH-2213",
        $stats_option,
        "CS-3213",
        "EE-2211",
        "EE-2212",
        "EN-1112",
        "EN-3222",
        "EN-4113",
        "EN-4123"        
    );
    foreach ($requirements as $object) {
        if (is_string($object)) {
            $course = fetchSingleCourse($object, $connect);
            $course->printCourse();
            echo "--------------------------------------------------<br>";
            $requisites = fetchRequisites($course->course_code, $connect);
            echo "<pre>";
            print_r($requisites);
            echo "</pre>"; 
        }
    }


    // echo "<pre>";
    // print_r($stats_option);
    // echo "</pre>";