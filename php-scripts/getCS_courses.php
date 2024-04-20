<?php
    $connect = new mysqli("db", "root" , "example", "jbu_catalog");
    $sql = ("select * from all_classes where course_code like 'CS-%'");
    $result = $connect->query($sql);
    $records = array();

    while ($row = $result->fetch_array()) {
        $records[] = array("course_code"=>$row['course_code'],
        "course_title"=>$row['course_title'],
        "credits_count"=>$row['credits_count'],
        "description"=>['description'],
        "time_and_place_list"=>$row['time_and_place_list'],
        "location"=>$row["location"]
        ,"honors"=>$row["honors"],
        "core"=>$row["core"]);
    }

    echo json_encode($records);
?>