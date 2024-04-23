<?php
    include "connect-to-db.php";
    include "course-classes.php";
    $requisites = fetchRequisites("CS-2243", $connect);
    $prequisite_string = $requisites[0]["prerequisite"];
    $completed_courses = [
        "CS-1113",
        // "MTH-1153",
        "THE-1111"
    ];
    foreach ($completed_courses as $course) {
        if (strpos($prequisite_string, $course) !== false) {
            $prequisite_string = str_replace($course, "true", $prequisite_string);
        }
    }

    // Replace substrings that match the pattern with "true"
    $prequisite_string = preg_replace('/\b[A-Za-z]+-\d{4}\b/', 'false', $prequisite_string);
    $result = eval("return $prequisite_string;");
    echo $result ? "true" : "false" ;
?>