<?php

namespace AcademicPlanningMod;

class CourseRequisites extends Requisites
{
    public string $course_code;
    public string $prerequisite;
    public bool $has_prerequisites;
    public string $corequisite;
    public GradeLevel $grade_level_req;
    public bool $prerequisite_corequisite_interchangeable;
    public bool $major_prioritized;
    public bool $major_required;
    public bool $department_prioritized;
    public bool $instructor_consent;
    public bool $honors;
    public string $further_notes;

    public function __construct($sql_array)
    {
        $this->course_code = $sql_array['course_code'];
//            $this->prerequisite = $sql_array['prerequisite'] == null ? "" : $sql_array['prerequisite'];
        if ($sql_array['prerequisite'] == null) {
            $this->prerequisite = "";
            $this->has_prerequisites = false;
        } else {
            $this->prerequisite = $sql_array['prerequisite'];
            $this->has_prerequisites = true;
        }
        $this->corequisite = $sql_array['corequisite'] == null ? "" : $sql_array['corequisite'];
        $this->grade_level_req =
            $sql_array['grade_level_req'] == null ? GradeLevel::FRESHMAN : $sql_array['grade_level_req'];
        $this->prerequisite_corequisite_interchangeable =
            (bool)$sql_array['prerequisite_corequisite_interchangeable'];
        $this->major_prioritized = (bool)$sql_array['major_prioritized'];
        $this->major_required = (bool)$sql_array['major_required'];
        $this->department_prioritized = (bool)$sql_array['department_prioritized'];
        $this->instructor_consent = (bool)$sql_array['instructor_consent'];
        $this->honors = (bool)$sql_array['honors'];
        $this->further_notes = $sql_array['further_notes'] == null ? "" : $sql_array['further_notes'];
    }

    public function allRequisitesMet(
        array      $completed_courses,
        Semester   $current_semester,
        GradeLevel $gradeLevel,
        bool       $honors_status): bool
    {
        return ($this->prerequisiteMet($completed_courses)
                && $this->corequisiteMet($current_semester)
                && $this->gradeLevelRequirementMet($gradeLevel)
                && $this->honorsRequirementMet($honors_status))
            || (($this->prerequisiteMet($completed_courses) || $this->corequisiteMet($current_semester))
                && $this->prerequisite_corequisite_interchangeable);
    }

    function prerequisiteMet(array $completed_courses): bool
    {
        if ($this->prerequisite == "") {
            return true;
        }
        $prerequisite_string = $this->prerequisite;
        foreach ($completed_courses as $course) {
            if (str_contains($prerequisite_string, $course)) {
                $prerequisite_string = str_replace($course, "true", $prerequisite_string);
            }
        }
        // Replace substrings that match the pattern with "true"
        $prerequisite_string = preg_replace(
            '/\b[A-Za-z]+-\d{4}\b/', 'false', $prerequisite_string
        );
        return eval("return $prerequisite_string;");
    }

    function corequisiteMet(Semester $current_semester): bool
    {
        if ($this->corequisite == "") {
            return true;
        }
        $corequisite_string = $this->corequisite;
        foreach ($current_semester->courses as $course) {
            if (str_contains($corequisite_string, $course)) {
                $corequisite_string = str_replace($course, "true", $corequisite_string);
            }
        }

        $corequisite_string = preg_replace(
            '/\b[A-Za-z]+\d{4}\b/', 'false', $corequisite_string
        );
        return eval("return $corequisite_string;");
    }

    function gradeLevelRequirementMet(GradeLevel $gradeLevel): bool
    {
        return $this->grade_level_req <= $gradeLevel;
    }

    function honorsRequirementMet(bool $honors_status): bool
    {
        return !$this->honors || $honors_status;
    }

    function printRequisites(): void
    {
        echo "Prerequisite: " . ($this->prerequisite === "" ? "None" : $this->prerequisite) . "<br>";
        echo "Corequisite: " . ($this->corequisite === "" ? "None" : $this->corequisite) . "<br>";
        $gradeLevelString = "Grade Level Required: ";
        if ($this->grade_level_req == GradeLevel::FRESHMAN) {
            echo $gradeLevelString . "No <br>";
        } elseif ($this->grade_level_req == GradeLevel::SOPHOMORE) {
            echo $gradeLevelString . "Sophomore <br>" ;
        } elseif ($this->grade_level_req == GradeLevel::JUNIOR) {
            echo $gradeLevelString . "Junior <br>";
        } elseif ($this->grade_level_req == GradeLevel::SENIOR) {
            echo $gradeLevelString . "Senior <br>";
        }
        echo "Prerequisite/Corequisite Interchangeable: " . ($this->prerequisite_corequisite_interchangeable ? "Yes" : "No") . "<br>";
        echo "Major Prioritized: " . ($this->major_prioritized ? "Yes" : "No") . "<br>";
        echo "Major Required: " . ($this->major_required ? "Yes" : "No") . "<br>";
        echo "Department Prioritized: " . ($this->department_prioritized ? "Yes" : "No") . "<br>";
        echo "Instructor Consent: " . ($this->instructor_consent ? "Yes" : "No") . "<br>";
        echo "Honors: " . ($this->honors ? "Yes" : "No") . "<br>";
        echo "Further Notes: " . $this->further_notes . "<br>";
    }
}