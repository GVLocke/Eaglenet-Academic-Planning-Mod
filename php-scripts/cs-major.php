<?php
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

    class Course {
        // Properties
        public string $course_code;
        public string $course_title;
        public int $credits_count;
        public string $description;
        public string $location;
        public int $honors;
        public int $offered_main_campus_fall;
        public int $offered_main_campus_spring;
        public int $offered_main_campus_even_year;
        public int $offered_main_campus_odd_year;

        // Methods
        public function parse_sql_arry($sql_array) {
            // Takes an array from the response array generated by fetchCourses and constructs a course object.
            $this->course_code = $sql_array["course_code"];
            $this->course_title = $sql_array["course_title"];
            $this->credits_count = $sql_array["credits_count"];
            $this->description = $sql_array["description"];
            $this->location = $sql_array["location"];
            $this->honors = $sql_array["honors"];
            $this->offered_main_campus_fall= $sql_array["offered_main_campus_fall"];
            $this->offered_main_campus_spring = $sql_array["offered_main_campus_spring"];
            $this->offered_main_campus_even_year= $sql_array["offered_main_campus_even_year"];
            $this->offered_main_campus_odd_year = $sql_array["offered_main_campus_odd_year"];
        }

        public function printCourse() {
            echo "Course Code: " . $this->course_code . "<br>";
            echo "Course Title: " . $this->course_title . "<br>";
            echo "Credits Count: " . $this->credits_count . "<br>";
            echo "Description: " . $this->description . "<br>";
            echo "Location: " . $this->location . "<br>";
            if ($this->honors == 1) {
                echo "Honors<br>";
            }
            echo "Offered in Fall: " . ($this->offered_main_campus_fall ? "Yes" : "No") . "<br>";
            echo "Offered in Spring: " . ($this->offered_main_campus_spring ? "Yes" : "No") . "<br>";
            echo "Offered in Even Year: " . ($this->offered_main_campus_even_year ? "Yes" : "No") . "<br>";
            echo "Offered in Odd Year: " . ($this->offered_main_campus_odd_year ? "Yes" : "No") . "<br>";
        }
    }

    class CourseOption {
        // Properties
        public array $courses; // array of course objects
        public array $recommended_courses; // array of course objects marked as recommended
        public int $credits_value; // represents the number of credits that all the courses in the array are worth

        public function __construct(int $credits_value) {
            $this->credits_value = $credits_value;
            $this->courses = array();
        }

        public function addOption(Course $course, int $recommended): bool {
            if ($course->credits_count != $this->credits_value) {
                return false;
            } else {
                $this->courses[] = $course;
                if ($recommended = 1) {
                    $this->recommended_courses[] = $course;
                }
                return true;
            }
        }
    }

    enum Term: int {
        case FALL = 0;
        case SPRING = 1;
    }

    class Semester {
        // Properties
        public array $courses; // array of Course objects or CourseOption objects
        public int $grade_level;
        public Term $term;
        public int $num_credits; // represents the sum of the credits_count for each course in the array

        // Methods
        public function __construct(int $grade_level, Term $term) {
            $this->courses = array();
            $this->grade_level = $grade_level;
            $this->term = $term;
            $this->num_credits = 0;
        }

        public function addCourse(Course $course): bool {
            if ($this->num_credits + $course->credits_count > 18) {
                return false;
            } else {
                $this->courses[] = $course;
                $this->num_credtis += $course->credits_count;
                return true;
            }
        }
    }

    function fetchCourses($sql, $connect) {
        $courses = array();
        $result = $connect->query($sql);
        while ($row = $result->fetch_array()) {
            $courses[] = array(
                "course_code" => $row['course_code'],
                "course_title" => $row['course_title'],
                "credits_count" => $row['credits_count'],
                "description" => $row['description'],
                "location" => $row["location"],
                "honors" => $row["honors"],
                "offered_main_campus_fall"=>$row["offered_main_campus_fall"],
                "offered_main_campus_spring"=>$row["offered_main_campus_spring"],
                "offered_main_campus_even_year"=>$row["offered_main_campus_even_year"],
                "offered_main_campus_odd_year"=>$row["offered_main_campus_odd_year"]);
        }
        return $courses;
    }
?>