<?php


namespace App\Helper;

use DB;


class AttendanceHelper
{
   public static function updateStudentDailyAttendance($atten_date)
   {
      $data = DB::update("UPDATE attendance_students         
         JOIN std_classes ON std_classes.name = attendance_students.class_id
         JOIN sections ON sections.name = attendance_students.section_id         
         SET attendance_students.class_id = std_classes.id, attendance_students.section_id = sections.id         
         WHERE attendance_students.attendance_date = '$atten_date'");
      return $data;
   }

   public static function updateStudentMontlyAttendance($month, $year)
   {
      $data = DB::update("UPDATE attendance_monthly_students         
         JOIN std_classes ON std_classes.name = attendance_monthly_students.class_id
         JOIN sections ON sections.name = attendance_monthly_students.section_id         
         SET attendance_monthly_students.class_id = std_classes.id, attendance_monthly_students.section_id = sections.id         
         WHERE attendance_monthly_students.month = '$month' and attendance_monthly_students.year = '$year' ");
      return $data;
   }
}