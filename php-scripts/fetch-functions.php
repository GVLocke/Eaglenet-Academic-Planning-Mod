<?php
    namespace AcademicPlanningMod;
    function fetchCourses($sql, $connect): array
    {
        $courses = array();
        $result = $connect->query($sql);
        while ($row = $result->fetch_array()) {
            $course = array(
                "course_code" => $row['course_code'],
                "course_title" => $row['course_title'],
                "credits_count" => $row['credits_count'],
                "description" => $row['description'],
                "location" => $row["location"],
                "honors" => $row["honors"],
                "offered_main_campus_fall"=>$row["offered_main_campus_fall"],
                "offered_main_campus_spring"=>$row["offered_main_campus_spring"],
                "offered_main_campus_even_year"=>$row["offered_main_campus_even_year"],
                "offered_main_campus_odd_year"=>$row["offered_main_campus_odd_year"]
            );
            $courses[$course['course_code']] = $course;
        }
        return $courses;
    }

    function fetchSingleCourse($course_code, $connect) : SingleCourse {
        $sql = "select * from majors_minors_classes where course_code = '" . $course_code . "'"; 
        $course = fetchCourses($sql, $connect);
        return new SingleCourse($course[$course_code], $connect);
    }

    function fetchRequisites($course_code, $connect): Requisites
    {
        $sql = "select * from requisites where course_code = '" . $course_code . "'";
        $result = $connect->query($sql);
        $requisites = array();
        if ($row = $result->fetch_array()) {
            $requisites = array(
            "course_code" => $row["course_code"],
            "prerequisite" => $row["prerequisite"],
            "corequisite" => $row["corequisite"],
            "grade_level_req" => $row["grade_level_req"],
            "prerequisite_corequisite_interchangeable" => $row["prerequisite_corequisite_interchangeable"],
            "major_prioritized" => $row['major_prioritized'],
            "major_required" => $row['major_required'],
            "department_prioritized" => $row['department_prioritized'],
            "instructor_consent" => $row["instructor_consent"],
            "honors" => $row["honors"],
            "further_notes" => $row["further_notes"]
            );
        }
        return Requisites::requisitesConstructor($requisites);
    }