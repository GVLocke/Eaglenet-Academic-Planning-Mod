<?php
    include "connect-to-db.php";
    $start_year = 2022; // change this to a session var or HTTP post or whatever
    $odd_year_courses = array();
    $even_year_courses = array();
    $spring_courses = array();
    $fall_courses = array();

    $freshman_fall_courses = array();
    $freshman_spring_courses = array();
    $sophomore_fall_courses = array();
    $sophomore_spring_courses = array();
    $junior_fall_courses = array();
    $junior_spring_courses = array();
    $senior_fall_courses = array();
    $freshman_spring_courses = array();
    
    $sql_fall = "select * from majors_minors_classes where course_code like 'CS-%' and offered_main_campus_fall = 1";
    $sql_spring = "select * from majors_minors_classes where course_code like 'CS-%' and offered_main_campus_spring = 1";
    $sql_even = "select * from majors_minors_classes where course_code like 'CS-%' and offered_main_campus_even_year = 1";
    $sql_odd = "select * from majors_minors_classes where course_code like 'CS-%' and offered_main_campus_odd_year = 1";

    $fall_courses = fetchCSCourses($sql_fall, $connect);
    $spring_courses = fetchCSCourses($sql_spring, $connect);
    $even_courses = fetchCSCourses($sql_even, $connect);
    $odd_courses = fetchCSCourses($sql_odd, $connect);

    // echo "<h1>Fall courses</h1><pre>";
    // print_r($fall_courses);
    // echo "</pre>";

    // echo "<h1>Spring courses</h1><pre>";
    // print_r($spring_courses);
    // echo "</pre>";

    // echo "<h1>Even courses</h1><pre>";
    // print_r($even_courses);
    // echo "</pre>";

    // echo "<h1>Odd courses</h1><pre>";
    // print_r($odd_courses);
    // echo "</pre>";



function fetchCSCourses($sql, $connect) {
        $courses = array();
        $result = $connect->query($sql);
        while ($row = $result->fetch_array()) {
            $courses[] = array(
                "course_code" => $row['course_code'],
                "course_title" => $row['course_title'],
                "credits_count" => $row['credits_count'],
                "description" => $row['description'],
                "time_and_place_list" => $row['time_and_place_list'],
                "location" => $row["location"],
                "honors" => $row["honors"]
            );
        }
        return $courses;
    }
?>