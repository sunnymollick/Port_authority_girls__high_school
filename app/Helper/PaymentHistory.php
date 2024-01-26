<?php

namespace App\Helper;

use DB;

class PaymentHistory
{
   public static function PaymentHistory($class_id, $section_id, $month, $year)
   {

      DB::statement(DB::raw("set @rownum=0, @class_id='$class_id', @section_id='$section_id', @month='$month',  @year='$year'"));

      $students = DB::select("SELECT inv.id as inv_id, std.id as std_id, std.name, std.std_code, stdClass.name AS class,sections.name AS section,
                fee.name AS title, fee.month, inv.amount, inv.paid, inv.due, inv.payment_date, inv.status , @rownum  := @rownum  + 1 AS rownum
      FROM enrolls
      JOIN students AS std ON std.id = enrolls.student_id
      JOIN std_classes AS stdClass ON stdClass.id = enrolls.class_id
      JOIN sections ON sections.id = enrolls.section_id
      Right JOIN invoices AS inv ON inv.std_id = std.id AND inv.year = @year  
      JOIN std_fee_categories AS fee ON fee.id = inv.roles_id AND fee.class_id = @class_id AND fee.month = @month AND fee.year = @year  
      WHERE enrolls.class_id= @class_id AND enrolls.section_id = @section_id AND enrolls.year = @year");

      return $students;
   }

   public static function invoiceStudent($class_id, $section_id, $year)
   {

      DB::statement(DB::raw("set @rownum=0, @class_id='$class_id', @section_id='$section_id',  @year='$year'"));

      $students = DB::select("SELECT  std.name, std.id as std_id, std.std_code,stdClass.name AS class,sections.name AS section,
                 @rownum  := @rownum  + 1 AS rownum
      FROM enrolls
      JOIN students AS std ON std.id = enrolls.student_id
      JOIN std_classes AS stdClass ON stdClass.id = enrolls.class_id
      JOIN sections ON sections.id = enrolls.section_id 
      WHERE enrolls.class_id= @class_id AND enrolls.section_id = @section_id AND enrolls.year = @year");

      return $students;
   }

}