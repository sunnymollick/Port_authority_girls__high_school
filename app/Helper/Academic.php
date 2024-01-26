<?php

namespace App\Helper;

use DB;

class Academic
{

   //   <!====-----  Start Student Export   ------===== !>


   public static function exportStudent($class_id, $section_id)
   {
      $result = DB::table('enrolls')
        ->join('students', 'students.id', '=', 'enrolls.student_id')
        ->join('std_classes', 'std_classes.id', '=', 'enrolls.class_id')
        ->join('sections', 'sections.id', '=', 'enrolls.section_id')
        ->select('enrolls.roll', 'students.*', 'std_classes.name as class_name', 'sections.name as section', DB::raw('@rownum  := @rownum  + 1 AS rownum'))
        ->where('enrolls.class_id', $class_id)
        ->where('enrolls.section_id', $section_id)
        ->where('enrolls.year', config('running_session'))->get();
      return $result;
   }

   //   <!====-----  End Student Export  ------===== !>


   //   <!====-----  Start Academic Class Routine   ------===== !>

   public static function generateClassRoutine($class_id, $section_id)
   {

      DB::statement(DB::raw("set @class_id='$class_id', @section_id='$section_id'"));
      $result = DB::table('class_routines')
        ->join('subjects', 'subjects.id', '=', 'class_routines.subject_id')
        ->join('sections', 'sections.id', '=', 'class_routines.section_id')
        ->join('class_rooms', 'class_rooms.id', '=', 'class_routines.class_room_id')
        ->join('teachers', 'teachers.id', '=', 'class_routines.teacher_id')
        ->select('class_routines.*', 'subjects.name as subject_name', 'sections.name as section_name',
          'teachers.name as teacher_name', 'class_rooms.name as class_room')
        ->where('class_routines.class_id', $class_id)
        ->where('class_routines.section_id', $section_id)
        ->where('class_routines.year', config('running_session'))
        ->orderby('class_routines.time_start', 'asc')->get();

      return $result;
   }


   //   <!====-----  End Academic Class Routine   ------===== !>


   //   <!====-----  Start Get Marks   ------===== !>

   public static function getSubjectMarks($class_id, $section_id, $subject_id, $exam_id, $year)
   {
      // dd($class_id, $section_id, $from_date, $to_date);
      DB::statement(DB::raw("set @class_id=$class_id, @section_id=$section_id, @subject_id=$subject_id, @exam_id=$exam_id, @year='$year'"));

      $data = DB::select("SELECT  `students`.`id`, `students`.`std_code`,  `students`.`name` as `std_name`,
               `std_classes`.`id` as `class_id`, `std_classes`.`name` as `class_name`, `sections`.`id` as `section_id`, `sections`.`name` as `section`,
                `enrolls`.`roll` as `std_roll`, `exams`.`id` as `exam_id`, `exams`.`name` as `exam_name`,
                `subjects`.`id` as `sub_id`, `subjects`.`name` as `sub_name`,
                `marks`.`theory_marks`, `marks`.`mcq_marks`,
                `marks`.`practical_marks`,`marks`.`ct_marks`, `marks`.`total_marks`
               FROM enrolls
               left join `marks` on `marks`.`student_code` = `enrolls`.`student_code` and `marks`.`exam_id` = @exam_id and `marks`.`subject_id` = @subject_id
               left join `students` on `students`.`id` = `enrolls`.`student_id`
               inner join `std_classes` on `std_classes`.`id` = `enrolls`.`class_id`
               inner join `sections` on `sections`.`id` = `enrolls`.`section_id`
               left join `subjects` on `subjects`.`id` = `marks`.`subject_id`
               left join `exams` on `exams`.`id` = `marks`.`exam_id`
               where `enrolls`.`class_id` = @class_id and `enrolls`.`section_id` = COALESCE(@section_id, enrolls.section_id) AND `enrolls`.`year` = @year
               order by `enrolls`.`roll` asc");

      return $data;
   }
   public static function getSpecialSubjectMarks($class_id, $section_id, $subject_id, $exam_id, $year)
   {
      // dd($class_id, $section_id, $from_date, $to_date);
      DB::statement(DB::raw("set @class_id=$class_id, @section_id=$section_id, @subject_id=$subject_id, @exam_id=$exam_id, @year='$year'"));

      $data = DB::select("SELECT  `students`.`id`, `students`.`std_code`,  `students`.`name` as `std_name`,
               `std_classes`.`id` as `class_id`, `std_classes`.`name` as `class_name`, `sections`.`id` as `section_id`, `sections`.`name` as `section`,
                `enrolls`.`roll` as `std_roll`, `exams`.`id` as `exam_id`, `exams`.`name` as `exam_name`,
                `special_session_subjects`.`id` as `sub_id`, `special_session_subjects`.`name` as `sub_name`,
                `special_marks`.`written_marks`, `special_marks`.`assignment_marks`,
                `special_marks`.`other_marks`, `special_marks`.`total_marks`
               FROM enrolls
               left join `special_marks` on `special_marks`.`student_code` = `enrolls`.`student_code` and `special_marks`.`exam_id` = @exam_id and `special_marks`.`subject_id` = @subject_id
               left join `students` on `students`.`id` = `enrolls`.`student_id`
               inner join `std_classes` on `std_classes`.`id` = `enrolls`.`class_id`
               inner join `sections` on `sections`.`id` = `enrolls`.`section_id`
               left join `special_session_subjects` on `special_session_subjects`.`id` = `special_marks`.`subject_id`
               left join `exams` on `exams`.`id` = `special_marks`.`exam_id`
               where `enrolls`.`class_id` = @class_id and `enrolls`.`section_id` = COALESCE(@section_id, enrolls.section_id) AND `enrolls`.`year` = @year
               order by `enrolls`.`roll` asc");

      return $data;
   }


   //   <!====-----  End Get Marks   ------===== !>

   public static function generateAdmitCard($exam_id, $class_id, $section_id, $student_id, $year)
   {
      // dd($class_id, $section_id, $from_date, $to_date);
      DB::statement(DB::raw("set @class_id=$class_id, @section_id=$section_id, @student_code=$student_id, @exam_id=$exam_id, @year='$year'"));

      $data = DB::select("SELECT exams.name AS exam_name, exams.start_date, exams.end_date, students.std_code, students.name AS student_name, enrolls.roll,
            std_classes.name AS class_name, sections.name AS section_name,parents.father_name,parents.mother_name,
            students.file_path
            FROM enrolls
            LEFT JOIN students ON students.id = enrolls.student_id
            LEFT JOIN parents ON parents.parent_code = enrolls.student_code
            LEFT JOIN std_classes ON std_classes.id = enrolls.class_id
            LEFT JOIN sections ON sections.id = enrolls.section_id
            JOIN exams ON exams.id = @exam_id
            where enrolls.YEAR = @YEAR AND enrolls.class_id = @class_id
            AND enrolls.section_id = COALESCE(@section_id, enrolls.section_id)
            AND enrolls.student_code = COALESCE(@student_code, enrolls.student_code)
            ORDER BY enrolls.student_code asc");

      return $data;
   }
}
