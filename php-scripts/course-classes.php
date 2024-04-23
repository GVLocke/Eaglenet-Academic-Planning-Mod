<?php
    enum Term: int {
        case FALL = 0;
        case SPRING = 1;
        case BOTH = 2;
    }

    interface Comparable {
        public function equals($object) : bool;
    }

    class Course implements Comparable {
        // Properties
        public string $course_code;
        public string $course_title;
        public int $credits_count;
        public CourseRequisites $requisites;
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

            $this->requisites = new CourseRequisites(fetchRequisites($this->course_code, $connect));

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

        public function printCourse() {
            echo "Course Code: " . $this->course_code . "<br>";
            echo "Course Title: " . $this->course_title . "<br>";
            echo "Credits Count: " . $this->credits_count . "<br>";
            echo "Description: " . $this->description . "<br>";
            echo "Location: " . $this->location . "<br>";
            if ($this->honors == 1) {
                echo "Honors<br>";
            }
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
            if (!($object instanceof Course)) {
                return false;
            }
            return $this->course_code == $object->course_code;
        }

        public function requirementsMet(Plan $plan, Term $term, int $year) : bool {
            
        }
    }

    class CourseRequisites {
        public $course_code;
        public $prerequisite;
        public $corequisite;
        public $grade_level_req;
        public $major_prioritized;
        public $major_required;
        public $department_prioritized;
        public $instructor_consent;
        public $honors;
        public $further_notes;

        public function __construct($sql_array) {
            $this->course_code = $sql_array['course_code'];
            $this->prerequisite = $sql_array['prerequisite'];
            $this->corequisite = $sql_array['corequisite'];
            $this->grade_level_req = $sql_array['grade_level_req'];
            $this->major_prioritized = $sql_array['major_prioritized'];
            $this->major_required = $sql_array['major_required'];
            $this->department_prioritized = $sql_array['department_prioritized'];
            $this->instructor_consent = $sql_array['instructor_consent'];
            $this->honors = $sql_array['honors'];
            $this->further_notes = $sql_array['further_notes'];
        }
    }


    class CourseOption {
        // represents a list of courses, any of which could count for meeting degree requirements
        public array $courses = array();
        public array $recommended = array(); // all the courses that meet this requirement that are recommended by the registrar.
        
        public function addOption(Course $course, int $recommended) {
            if ($recommended == 1) {
                $this->recommended[] = $course;
            }
            $this->courses[] = $course;
        }

        public function meetsRequirement(Course $course) {
            foreach ($this->courses as $option) {
                if ($option->equals($course)) {
                    return true;
                }
            }
            return false;
        }
    }

    class Semester {
        // Properties
        public array $courses; // array of Course objects or CourseOption objects
        public int $grade_level;
        public Term $term;
        public int $num_credits; // represents the sum of the credits_count for each course in the array
        public int $year; // the calendar year of the semester

        // Methods
        public function __construct(int $grade_level, Term $term, int $year) {
            $this->courses = array();
            $this->grade_level = $grade_level;
            $this->term = $term;
            $this->num_credits = 0;
            $this->year = $year;
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

    class Plan {
        // Properties
        public array $semesters; // array of Semester objects
        public array $all_courses; // array of all courses in every semester
        public int $starting_year; // calendar year of the freshman fall semester

        // Methods
        public function __construct(int $starting_year) {
            $this->starting_year = $starting_year;
            $this->semesters = array();
            for ($i = 1; $i <= 4; $i++) {
                $this->semesters[] = new Semester($i, Term::FALL, ($this->starting_year + ($i - 1)));
                $this->semesters[] = new Semester($i, Term::SPRING, ($this->starting_year + $i));
            }
        }

        public function addCourse(Course $course, int $grade_level, Term $term): bool {
            foreach ($this->semesters as $semester) {
                if ($semester->grade_level == $grade_level && $semester->term == $term) {
                    return $semester->addCourse($course);
                }
            }
            return false;
        }
    }

    function fetchCourses($sql, $connect) {
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

    function fetchSingleCourse($course_code, $connect) {
        $sql = "select * from majors_minors_classes where course_code = '" . $course_code . "'"; 
        $course = fetchCourses($sql, $connect);
        $course = new Course($course[$course_code], $connect);
        return $course;
    }

    function fetchRequisites($course_code, $connect) {
        $sql = "select * from requisites where course_code = '" . $course_code . "'";
        $result = $connect->query($sql);
        $requisites = array();
        if ($row = $result->fetch_array()) {
            $requisites = array(
            "course_code" => $row["course_code"],
            "prerequisite" => $row["prerequisite"],
            "corequisite" => $row["corequisite"],
            "grade_level_req" => $row["grade_level_req"],
            "major_prioritized" => $row['major_prioritized'],
            "major_required" => $row['major_required'],
            "department_prioritized" => $row['department_prioritized'],
            "instructor_consent" => $row["instructor_consent"],
            "honors" => $row["honors"],
            "further_notes" => $row["further_notes"]
            );
        }
        return $requisites;
    }
?>