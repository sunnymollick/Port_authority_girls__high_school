<?php

namespace App\Helper;

use App\Models\Enroll;
use App\Models\Invoice;
use App\Models\Std_Fee_Category;
use App\Models\Student;
use DB;
use PDF;

class Accounts
{

   //   <!====-----  Start Print Monthly Fee Book   ------===== !>

   public static function printMonthlyfeeBook($roles_id, $student_id)
   {

      $year = config('running_session');
      $student = Student::findOrFail($student_id);
      $enroll = Enroll::where('student_id', $student_id)
        ->where('year', $year)
        ->first();
      $invoice = Invoice::where('roles_id', $roles_id)->where('std_id', $student_id)->where('year', $year)->first();
      $feecategory = Std_Fee_Category::where('id', $roles_id)->first();
      $pdf = PDF::loadView('backend.admin.accounts.invoice.printFeeBook', compact('enroll', 'invoice', 'student', 'feecategory'))->setPaper('a4', 'landscape');
      return $pdf->download('Monthly_Fee_' . date("F_Y", mktime(0, 0, 0, $feecategory->month, 10)) . '_' . $student->std_code . '.pdf');
   }


   //   <!====-----  End Print Monthly Fee Book   ------===== !>


   public static function printfeeBook($class_id, $section_id, $months, $student_id, $year)
   {

      // dd($months);
      DB::statement(DB::raw("SET @class_id = '$class_id', @section_id = '$section_id', @month = $months, @year = '$year',@student_code =  $student_id"));

      $data = DB::select("select *
               from (
               select students.name,students.std_code, std_classes.id as class_id, std_classes.name as class_name, 
               sections.id as section_id,sections.name as section_name,enrolls.roll ,
               accounts_heads.name as acccounts_head,accounts_fee_items.amount ,accounts_fee_items.month, 
               CONCAT(students.std_code, LPAD(accounts_fee_items.month, 2, '0'),DATE_FORMAT(now(), '%y') ) AS barcode
               from accounts_fee_items
               inner join accounts_heads
               on accounts_heads.id =accounts_fee_items.accounts_head_id
               inner join std_classes
               on std_classes.id =accounts_fee_items.class_id 
               inner join sections
               on sections.id =accounts_fee_items.section_id
               inner join enrolls
               on enrolls.class_id =accounts_fee_items.class_id and enrolls.section_id = accounts_fee_items.section_id AND enrolls.year=@year
               inner join students
               on students.id =enrolls.student_id 
               where accounts_fee_items.class_id = @class_id 
               AND accounts_fee_items.section_id = COALESCE(@section_id, accounts_fee_items.section_id)  
               and students.std_code = COALESCE(@student_code, students.std_code)
               and accounts_fee_items.month = COALESCE(@month, accounts_fee_items.month)
               and students.std_code not in(select accounts_exceptional_students.student_id from
               accounts_exceptional_students WHERE 
               accounts_exceptional_students.student_id = COALESCE(@student_code, accounts_exceptional_students.student_id) 
               and accounts_exceptional_students.month = COALESCE(@month, accounts_exceptional_students.month)
               and accounts_exceptional_students.year = @year)
               union ALL
               select students.name,students.std_code,std_classes.id as class_id, std_classes.name as class_name,sections.id as section_id,sections.name as section_name,
               enrolls.roll ,accounts_heads.name as acccounts_head,accounts_exceptional_students.amount ,accounts_exceptional_students.month,
                CONCAT(students.std_code, LPAD(accounts_exceptional_students.month, 2, '0'),DATE_FORMAT(now(), '%y') ) AS barcode
               from accounts_exceptional_students
               inner join accounts_heads
               on accounts_heads.id =accounts_exceptional_students.accounts_head_id
               inner join std_classes
               on std_classes.id =accounts_exceptional_students.class_id
               inner join sections
               on sections.id =accounts_exceptional_students.section_id
               inner join students
               on students.std_code = accounts_exceptional_students.student_id
               inner join enrolls
               on enrolls.student_id =students.id and enrolls.year=@year
               where accounts_exceptional_students.student_id = COALESCE(@student_code, accounts_exceptional_students.student_id) 
               and accounts_exceptional_students.class_id = COALESCE(@class_id, accounts_exceptional_students.class_id)
               and accounts_exceptional_students.section_id = COALESCE(@section_id, accounts_exceptional_students.section_id)
               and accounts_exceptional_students.month = COALESCE(@month, accounts_exceptional_students.month)
               and accounts_exceptional_students.year = @year
               ) a
               order BY a.std_code, a.month asc");

      return $data;
   }


   public static function searchFeeDetailsBarode($barcode, $student_id, $month, $year)
   {

      // dd($months);
      DB::statement(DB::raw("SET @barcode = '$barcode', @month = $month, @year = '$year', @student_code = '$student_id'"));

      $data = DB::select("SELECT a.std_code, a.name, a.class_id, a.class_name, a.section_id, a.section_name, a.roll, SUM(a.amount) AS total_amount, a.month, a.barcode, a.year
               FROM (
               SELECT students.std_code, students.name, std_classes.id AS class_id, std_classes.name AS class_name, 
               sections.id AS section_id,sections.name AS section_name,enrolls.roll,
               accounts_fee_items.amount,accounts_fee_items.month, CONCAT(students.std_code, LPAD(accounts_fee_items.month, 2, '0'), DATE_FORMAT(NOW(), '%y')) AS barcode, accounts_fee_items.year
               FROM accounts_fee_items
               INNER JOIN std_classes ON std_classes.id =accounts_fee_items.class_id
               INNER JOIN sections ON sections.id =accounts_fee_items.section_id
               INNER JOIN enrolls ON enrolls.class_id =accounts_fee_items.class_id AND enrolls.section_id = accounts_fee_items.section_id AND enrolls.year=@year
               INNER JOIN students ON students.id =enrolls.student_id
               WHERE students.std_code = @student_code AND accounts_fee_items.month = @month AND accounts_fee_items.year = @year
               AND students.std_code NOT IN(
               SELECT accounts_exceptional_students.student_id
               FROM
               accounts_exceptional_students
               WHERE 
               accounts_exceptional_students.student_id = @student_code and accounts_exceptional_students.month = @month AND accounts_exceptional_students.year = @year) 
               UNION ALL
               SELECT students.std_code,students.name,std_classes.id AS class_id, std_classes.name AS class_name,sections.id AS section_id,sections.name AS section_name,
               enrolls.roll,accounts_exceptional_students.amount,accounts_exceptional_students.month, CONCAT(students.std_code, LPAD(accounts_exceptional_students.month, 2, '0'), DATE_FORMAT(NOW(), '%y')) AS barcode, accounts_exceptional_students.year
               FROM accounts_exceptional_students
               INNER JOIN std_classes ON std_classes.id =accounts_exceptional_students.class_id
               INNER JOIN sections ON sections.id =accounts_exceptional_students.section_id
               INNER JOIN students ON students.std_code = accounts_exceptional_students.student_id
               INNER JOIN enrolls ON enrolls.student_id =students.id AND enrolls.year=@year
               WHERE accounts_exceptional_students.student_id = @student_code and accounts_exceptional_students.month = @month AND accounts_exceptional_students.year = @year
               ) a
               WHERE a.barcode = @barcode
               GROUP BY a.barcode
               ORDER BY a.std_code, a.month ASC");

      return $data;
   }

   public static function stdFeePaymentHistoryReports($class_id, $section_id, $month, $year)
   {

      // dd($months);
      DB::statement(DB::raw("SET @class_id = '$class_id', @month = $month, @year = '$year', @section_id = '$section_id'"));

      $data = DB::select("SELECT a.name, a.std_code, a.class_name, a.roll, a.section_name, ap.amount, ap.barcode, ap.fee_month, case when ap.amount IS NOT NULL then 'Paid' ELSE 'Not Paid' END AS 'status'
               FROM (
               SELECT 
               std.name, std.std_code, stdClass.name AS class_name, enrolls.roll, sections.name AS section_name
                     FROM enrolls
                     JOIN students AS std ON std.id = enrolls.student_id
                     JOIN std_classes AS stdClass ON stdClass.id = enrolls.class_id
                     JOIN sections ON sections.id = enrolls.section_id 
                     WHERE enrolls.class_id= @class_id AND enrolls.section_id = @section_id 
                   AND enrolls.year = @year
                   ) a
                   
               LEFT OUTER JOIN accounts_payments AS ap ON ap.student_id = a.std_code AND ap.fee_month = @month");

      return $data;
   }


   public static function studentFeeIncomeStatementSummeryReports($class_id, $section_id, $from_date, $to_date)
   {

      // dd($class_id, $section_id, $from_date, $to_date);
      DB::statement(DB::raw("set @class_id=$class_id, @section_id=$section_id, @from_date='$from_date', @to_date='$to_date'"));

      $data = DB::select("SELECT accounts_payments.student_id,std_classes.name AS class_name,
            sections.name AS section_name,accounts_payments.student_roll, SUM(accounts_payments.amount) AS total_amount 
            FROM accounts_payments
            JOIN std_classes ON std_classes.id = accounts_payments.class_id
            JOIN sections ON sections.id = accounts_payments.section_id
            WHERE 
            accounts_payments.class_id = COALESCE(@class_id, accounts_payments.class_id)
            AND accounts_payments.section_id = COALESCE(@section_id, accounts_payments.section_id)
            AND (accounts_payments.payment_date BETWEEN @from_date AND @to_date)
            
            GROUP BY accounts_payments.class_id, accounts_payments.section_id");

      return $data;
   }

   public static function studentFeeIncomeStatementDetailsReports($class_id, $section_id, $from_date, $to_date)
   {

      // dd($class_id, $section_id, $from_date, $to_date);
      DB::statement(DB::raw("set @class_id=$class_id, @section_id=$section_id, @from_date='$from_date', @to_date='$to_date'"));

      $data = DB::select("SELECT accounts_payments.student_id,students.name AS std_name ,std_classes.name AS class_name,sections.name AS section_name,accounts_payments.student_roll, SUM(accounts_payments.amount) AS total_amount 
            FROM accounts_payments
            JOIN students ON students.std_code = accounts_payments.student_id
            JOIN std_classes ON std_classes.id = accounts_payments.class_id
            JOIN sections ON sections.id = accounts_payments.section_id
            WHERE 
            
            accounts_payments.class_id = COALESCE(@class_id, accounts_payments.class_id)
            AND accounts_payments.section_id = COALESCE(@section_id, accounts_payments.section_id)
            AND (accounts_payments.payment_date BETWEEN @from_date AND @to_date)
            
            GROUP BY accounts_payments.student_id
            ORDER BY accounts_payments.class_id asc");

      return $data;
   }

   public static function accountsCategoryStatementSummaryReports($category_type, $income_cat_id, $from_date, $to_date)
   {

      // dd($class_id, $section_id, $from_date, $to_date);
      DB::statement(DB::raw("set @category_type='$category_type', @income_cat_id=$income_cat_id, @from_date='$from_date', @to_date='$to_date'"));

      $data = DB::select("SELECT accounts_head_categories.category_name, SUM(income_expense_stores.amount) AS total_amount 
         FROM income_expense_stores
         JOIN accounts_head_categories ON accounts_head_categories.id = income_expense_stores.category_id
         WHERE 
         income_expense_stores.category_id = COALESCE(@income_cat_id, income_expense_stores.category_id)
         and income_expense_stores.store_type = @category_type
         AND (income_expense_stores.store_date BETWEEN @from_date AND @to_date)
         
         GROUP BY income_expense_stores.category_id");

      return $data;
   }

   public static function accountsCategoryStatementDetailsReports($category_type, $income_cat_id, $from_date, $to_date)
   {

      // dd($class_id, $section_id, $from_date, $to_date);
      DB::statement(DB::raw("set @category_type='$category_type', @income_cat_id=$income_cat_id, @from_date='$from_date', @to_date='$to_date'"));

      $data = DB::select("SELECT accounts_head_categories.category_name, accounts_head_category_items.category_item_name, income_expense_stores.amount AS total_amount 
         FROM income_expense_stores
         JOIN accounts_head_categories ON accounts_head_categories.id = income_expense_stores.category_id
         JOIN accounts_head_category_items ON accounts_head_category_items.id = income_expense_stores.item_id
         WHERE 
         income_expense_stores.category_id = COALESCE(@income_cat_id, income_expense_stores.category_id)
         and income_expense_stores.store_type = @category_type
         AND (income_expense_stores.store_date BETWEEN @from_date AND @to_date)
         
         ORDER BY income_expense_stores.category_id asc");

      return $data;
   }


   public static function allIncomeStatementSummaryReports($category_type, $from_date, $to_date)
   {

      // dd($class_id, $section_id, $from_date, $to_date);
      DB::statement(DB::raw("set @category_type='$category_type',  @from_date='$from_date', @to_date='$to_date'"));

      $data = DB::select("SELECT 'Total Student Fee' AS category_name, sum(std_fee.total_amount) AS total_amount from
               (SELECT  SUM(accounts_payments.amount) AS total_amount, 'student_fee' AS income_type
               FROM accounts_payments
               WHERE accounts_payments.payment_date BETWEEN @from_date AND @to_date
               GROUP BY accounts_payments.class_id
               ) std_fee
               GROUP BY std_fee.income_type                
               UNION ALL               
               SELECT accounts_head_categories.category_name, SUM(income_expense_stores.amount) AS total_amount 
               FROM income_expense_stores
               JOIN accounts_head_categories ON accounts_head_categories.id = income_expense_stores.category_id
               WHERE 
               income_expense_stores.store_type = @category_type
               AND income_expense_stores.store_date BETWEEN @from_date AND @to_date           
               GROUP BY income_expense_stores.category_id");

      return $data;
   }

   public static function expenseStatementSummaryReports($category_type, $income_cat_id, $from_date, $to_date)
   {

      // dd($class_id, $section_id, $from_date, $to_date);
      DB::statement(DB::raw("set @category_type='$category_type', @income_cat_id=$income_cat_id, @from_date='$from_date', @to_date='$to_date'"));

      $data = DB::select("SELECT accounts_head_categories.category_name, SUM(income_expense_stores.amount) AS total_amount 
            FROM income_expense_stores
            JOIN accounts_head_categories ON accounts_head_categories.id = income_expense_stores.category_id
            WHERE 
            income_expense_stores.category_id = COALESCE(@income_cat_id, income_expense_stores.category_id)
            and income_expense_stores.store_type = @category_type
            AND (income_expense_stores.store_date BETWEEN @from_date AND @to_date)            
            GROUP BY income_expense_stores.category_id");

      return $data;
   }


   // student panel accounts

   public static function studentFeeBooks($class_id, $section_id, $months, $student_id, $year)
   {

      // dd($months);
      DB::statement(DB::raw("SET @class_id = '$class_id', @section_id = '$section_id', @month = $months, @year = '$year',@student_code =  $student_id"));

      $data = DB::select("SELECT   a.std_code, a.name, a.class_id, a.class_name, a.section_id, a.section_name, a.roll, SUM(a.amount) AS total_amount, a.month, a.barcode, a.year
               FROM (
               SELECT students.std_code, students.name, std_classes.id AS class_id, std_classes.name AS class_name, 
               sections.id AS section_id,sections.name AS section_name,enrolls.roll,
               accounts_fee_items.amount,accounts_fee_items.month, CONCAT(students.std_code, LPAD(accounts_fee_items.month, 2, '0'), DATE_FORMAT(NOW(), '%y')) AS barcode, accounts_fee_items.year
               FROM accounts_fee_items
               INNER JOIN std_classes ON std_classes.id =accounts_fee_items.class_id
               INNER JOIN sections ON sections.id =accounts_fee_items.section_id
               INNER JOIN enrolls ON enrolls.class_id =accounts_fee_items.class_id AND enrolls.section_id = accounts_fee_items.section_id AND enrolls.year=@year
               INNER JOIN students ON students.id =enrolls.student_id
               WHERE 
               students.std_code = @student_code 
               AND accounts_fee_items.month = COALESCE(@month, accounts_fee_items.month)
               AND accounts_fee_items.year = @year
               AND students.std_code NOT IN(
               SELECT accounts_exceptional_students.student_id
               FROM
               accounts_exceptional_students
               WHERE 
               accounts_exceptional_students.student_id = @student_code 
               AND accounts_fee_items.section_id = @section_id
               and accounts_fee_items.month = COALESCE(@month, accounts_fee_items.month)
               AND accounts_exceptional_students.year = @year) 
               UNION ALL
               SELECT students.std_code,students.name,std_classes.id AS class_id, std_classes.name AS class_name,sections.id AS section_id,sections.name AS section_name,
               enrolls.roll,accounts_exceptional_students.amount,accounts_exceptional_students.month, CONCAT(students.std_code, LPAD(accounts_exceptional_students.month, 2, '0'), DATE_FORMAT(NOW(), '%y')) AS barcode, accounts_exceptional_students.year
               FROM accounts_exceptional_students
               INNER JOIN std_classes ON std_classes.id =accounts_exceptional_students.class_id
               INNER JOIN sections ON sections.id =accounts_exceptional_students.section_id
               INNER JOIN students ON students.std_code = accounts_exceptional_students.student_id
               INNER JOIN enrolls ON enrolls.student_id =students.id AND enrolls.year=@year
               WHERE accounts_exceptional_students.student_id = @student_code 
               and accounts_exceptional_students.month = COALESCE(@month, accounts_exceptional_students.month)
               AND accounts_exceptional_students.year = @year
               ) a
               GROUP BY a.barcode
               ORDER BY a.std_code, a.month ASC");

      return $data;
   }


}