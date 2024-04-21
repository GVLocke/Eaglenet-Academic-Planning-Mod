<?php
    include "connect-to-db.php";
    $sql = ("select * from majors_minors_classes where course_code like 'CS-%'");
    $result = $connect->query($sql);
    $cs_records = array();

    while ($row = $result->fetch_array()) {
        $cs_records[] = array("course_code"=>$row['course_code'],
        "course_title"=>$row['course_title'],
        "credits_count"=>$row['credits_count'],
        "description"=>$row['description'],
        "time_and_place_list"=>$row['time_and_place_list'],
        "location"=>$row["location"],
        "honors"=>$row["honors"],
        "offered_main_campus_fall"=>$row["offered_main_campus_fall"],
        "offered_main_campus_spring"=>$row["offered_main_campus_spring"],
        "offered_main_campus_even_year"=>$row["offered_main_campus_even_year"],
        "offered_main_campus_odd_year"=>$row["offered_main_campus_odd_year"]);
    }
    // echo '<pre>';
    // print_r($cs_records);
    // echo '</pre>';


    // echo json_encode($records);
?>