<?php

namespace App\Helper;

use DB;

class GenerateMarksheet
{


   public static function jrhalfSummeryResult($exam_id, $class_id, $section_id, $year)
   {
      DB::statement(DB::raw("set @rownum=0, @class_id='$class_id', @resultStatus='PASSED', @obtained_marks=0,@thPmarks=0,@mcqPmarks=0,@practPmarks=0,@ctPmarks=0, @section_id=$section_id, @exam_id='$exam_id', @year='$year'"));

      $createSummeryTempTables = DB::unprepared(
        DB::raw("
            CREATE TEMPORARY TABLE summery_result( exmId INT, examTitle VARCHAR(100), stdCode VARCHAR(50), stdName VARCHAR(100), stdClass VARCHAR(50),
            stdRoll VARCHAR(50), stdSection INT, subId INT, subName VARCHAR(200),  optionalsubId INT, theoryPMarks INT, ctPMarks INT, obtainedMark INT,
            fiftyToHundredMark INT,status VARCHAR(50), grade VARCHAR(5), gpa DECIMAL(10,2))"
        )
      );

      if ($createSummeryTempTables) {
         DB::insert("insert into summery_result select exams.id,exams.name, std.std_code, std.name,marks.class_id,enrolls.roll, marks.section_id,
               marks.subject_id, sub.name, enrolls.subject_id,


               @thPmarks := CASE
                                when exams.ct_marks_percentage != '0' THEN (
                                    CASE
                                        WHEN sub.subject_marks = 100 THEN CEIL(marks.theory_marks * 0.8)
                                        ELSE CEIL(marks.theory_marks*0.6)
                                    END
                                )
                                ELSE CEIL(marks.theory_marks)
                            END AS theoryPMarks,

               @ctPmarks :=  CEIL(marks.ct_marks) AS ctPercentMarks,
               @obtained_marks := (@thPmarks + @ctPmarks) AS obtained_nmarks,

                 IF(sub.subject_marks = 50 ,@obtained_marks := CEIL(@obtained_marks*100/50), @obtained_marks := @obtained_marks) AS fiftyToHundredMark,

                                    @resultStatus :=   case when marks.subject_id != enrolls.subject_id then (
                                       case when  marks.theory_marks>=sub.theory_pass_marks
                                        then (case when marks.mcq_marks>=sub.mcq_pass_marks
                               then (case when marks.practical_marks>=sub.practical_pass_marks
                                       then (case when @obtained_marks >= 40 then 'PASSED' else 'FAILED' end)
                               else 'FAILED' END) else 'FAILED' END) else 'FAILED' END) else 'PASSED' end as result,
                       CASE
                            WHEN  @resultStatus = 'PASSED' THEN (
                           CASE
                              WHEN  @obtained_marks >= 80 THEN 'A+'
                              WHEN  @obtained_marks >= 70 and @obtained_marks <= 79 THEN 'A'
                              WHEN  @obtained_marks >= 60 and @obtained_marks <= 69 THEN 'A-'
                              WHEN  @obtained_marks >= 50 and @obtained_marks <= 59 THEN 'B'
                              WHEN  @obtained_marks >= 40 and @obtained_marks <= 49 THEN 'C'
                              WHEN  @obtained_marks >= 33 and @obtained_marks <= 39 THEN 'D'
                              ELSE   'F' END ) ELSE 'F' END AS grade,
                       CASE
                              WHEN @resultStatus = 'PASSED' THEN (
                              CASE
                              WHEN  @obtained_marks >= 80 THEN '5.00'
                              WHEN  @obtained_marks >= 70 and @obtained_marks <= 79 THEN '4.00'
                              WHEN  @obtained_marks >= 60 and @obtained_marks <= 69 THEN '3.50'
                              WHEN  @obtained_marks >= 50 and @obtained_marks <= 59 THEN '3.00'
                              WHEN  @obtained_marks >= 40 and @obtained_marks <= 49 THEN '2.00'
                              WHEN  @obtained_marks >= 33 and @obtained_marks <= 39 THEN '1.00'
                              ELSE   '0.00' END ) ELSE '0.00' END AS gpa

								from  marks
                        inner JOIN students as std on std.std_code = marks.student_code
                        LEFT JOIN enrolls  on enrolls.student_id = std.id AND enrolls.class_id = @class_id  and enrolls.YEAR= @year
                        LEFT JOIN exams on exams.id = marks.exam_id
                        LEFT JOIN subjects as sub on sub.id = marks.subject_id
                        where marks.class_id =@class_id and marks.exam_id=@exam_id and marks.section_id=COALESCE(@section_id, marks.section_id) AND marks.YEAR =@year
                        order by marks.student_code,sub.subject_order ASC");

         $result = DB::select("SELECT rownum,exmId,examTitle,stdCode,stdName,stdClass,stdRoll,stdSection,subId,totalSubject,hasOptional,mainSubPoint,optionalSubPoint,failedSubject,totalMarks,CASE  WHEN T2.CNT = 0  THEN 'PASSED'  ELSE 'FAILED'  END result
                  FROM (
                  SELECT @rownum  := @rownum  + 1 AS rownum,exmId,examTitle,stdCode,stdName,stdClass,stdRoll,stdSection,subId,
                  sum(case when subId != optionalsubId then 1 END) AS totalSubject,
                  @cgpaPoint := sum(case when subId != optionalsubId then gpa END) AS mainSubPoint,
                  count(case when subId = optionalsubId then 1 END) AS hasOptional,
                  @optionalSubPoint := sum(case when subId = optionalsubId then (case when gpa>2 then gpa-2 END) ELSE 0 END) AS optionalSubPoint,
                  sum(CASE WHEN status = 'FAILED' THEN 1 ELSE 0 END) AS failedSubject,
                  SUM(obtainedMark) AS totalMarks, COUNT(CASE WHEN status = 'FAILED' THEN 1 END) CNT
                       FROM summery_result GROUP BY stdCode) T2");

         DB::unprepared(DB::raw("DROP TABLE IF EXISTS summery_result"));

         return $result;
      }
   }

   public static function jrhalfSummeryResultSpecial($exam_id, $class_id, $section_id, $year)
   {
      DB::statement(DB::raw("set @rownum=0, @class_id='$class_id', @resultStatus='PASSED', @obtained_marks=0,@writtenMarks=0,@assignmentMarks=0,@otherMarks=0, @section_id='$section_id', @exam_id='$exam_id', @year='$year'"));

      $createSummeryTempTables = DB::unprepared(
        DB::raw("
            CREATE TEMPORARY TABLE summery_result( exmId INT, examTitle VARCHAR(100), stdCode VARCHAR(50), stdName VARCHAR(100), stdClass VARCHAR(50),
            stdRoll VARCHAR(50), stdSection INT, subId INT, subName VARCHAR(200), writtenMarks INT, assignmentMarks INT , otherMarks INT, obtainedMark INT, status VARCHAR(50),
            grade VARCHAR(5), gpa DECIMAL(10,2))"
        )
      );

      if ($createSummeryTempTables) {
         DB::insert("insert into summery_result select exams.id,exams.name, std.std_code, std.name,marks.class_id,enrolls.roll, marks.section_id,
         marks.subject_id, sub.name,



               @writtenMarks := CEIL(marks.written_marks),
               @assignmentMarks := CEIL(marks.assignment_marks),
               @otherMarks := CEIL(marks.other_marks),

               @obtained_marks := (@writtenMarks + @assignmentMarks + @otherMarks) AS obtained_nmarks,

               @resultStatus :=   case when marks.subject_id != enrolls.subject_id then (
                case when  @obtained_marks>=33
                then 'PASSED' else 'FAILED' END) else 'PASSED' end as result,
                        CASE
                            WHEN  @resultStatus = 'PASSED' THEN (

                           CASE
                              WHEN  @obtained_marks >= 80 THEN 'A+'
                              WHEN  @obtained_marks >= 70 and @obtained_marks <= 79 THEN 'A'
                              WHEN  @obtained_marks >= 60 and @obtained_marks <= 69 THEN 'A-'
                              WHEN  @obtained_marks >= 50 and @obtained_marks <= 59 THEN 'B'
                              WHEN  @obtained_marks >= 40 and @obtained_marks <= 49 THEN 'C'
                              WHEN  @obtained_marks >= 33 and @obtained_marks <= 39 THEN 'D'
                              ELSE   'F' END ) ELSE 'F' END AS grade,
                              CASE
                                     WHEN @resultStatus = 'PASSED' THEN (
                              CASE
                              WHEN  @obtained_marks >= 80 THEN '5.00'
                              WHEN  @obtained_marks >= 70 and @obtained_marks <= 79 THEN '4.00'
                              WHEN  @obtained_marks >= 60 and @obtained_marks <= 69 THEN '3.50'
                              WHEN  @obtained_marks >= 50 and @obtained_marks <= 59 THEN '3.00'
                              WHEN  @obtained_marks >= 40 and @obtained_marks <= 49 THEN '2.00'
                              WHEN  @obtained_marks >= 33 and @obtained_marks <= 39 THEN '1.00'
                              ELSE   '0.00' END ) ELSE '0.00' END AS gpa

								from  special_marks as marks
                        inner JOIN students as std on std.std_code = marks.student_code
                        LEFT JOIN enrolls  on enrolls.student_id = std.id AND enrolls.class_id = @class_id  and enrolls.YEAR= @year
                        LEFT JOIN exams on exams.id = marks.exam_id
                        LEFT JOIN special_session_subjects as sub on sub.id = marks.subject_id
                        where marks.class_id =@class_id and marks.exam_id=@exam_id and marks.section_id=@section_id AND marks.YEAR =@year
                        order by marks.student_code,sub.subject_order ASC");

         $result = DB::select("SELECT rownum,exmId,examTitle,stdCode,stdName,stdClass,stdRoll,stdSection,subId,totalSubject,mainSubPoint,totalMarks,CASE  WHEN T2.CNT = 0  THEN 'PASSED'  ELSE 'FAILED'  END result
                  FROM (
                  SELECT @rownum  := @rownum  + 1 AS rownum,exmId,examTitle,stdCode,stdName,stdClass,stdRoll,stdSection,subId,
                  sum(1) AS totalSubject,
                  @cgpaPoint := sum(gpa) AS mainSubPoint,
                  SUM(obtainedMark) AS totalMarks, COUNT(CASE WHEN status = 'FAILED' THEN 1 END) CNT
                       FROM summery_result GROUP BY stdCode) T2");

         DB::unprepared(DB::raw("DROP TABLE IF EXISTS summery_result"));

         return $result;
      }
   }


   public static function jrhalfExamMarksheet($exam_id, $class_id, $section_id, $student_code, $year)
   {
      DB::statement(DB::raw("set @class_id='$class_id', @section_id=$section_id, @exam_id='$exam_id', @std_code='$student_code', @year='$year',
      @obtained_marks=0, @resultStatus='PASSED',@thPmarks=0,@mcqPmarks=0,@practPmarks=0,@ctPmarks=0"));

      $createMarksheetTempTables = DB::unprepared(
        DB::raw("
            CREATE TEMPORARY TABLE temp_marksheet_result(stdCode VARCHAR(50),stdRoll VARCHAR(50), subId INT, subName VARCHAR(200),  optionalsubId INT, theoryPMarks INT, ctPMarks INT, obtainedMark INT,
            fiftyToHundredMark INT, result VARCHAR(50))"
        ));

      if ($createMarksheetTempTables) {
         DB::insert("insert into temp_marksheet_result select marks.student_code, enrolls.roll ,marks.subject_id, sub.name, enrolls.subject_id,
            @thPmarks := CASE
                    when exams.ct_marks_percentage != '0' THEN (
                        CASE
                            WHEN sub.subject_marks = 100 THEN CEIL(marks.theory_marks * 0.8)
                            ELSE CEIL(marks.theory_marks*0.6)
                        END
                    )
                    ELSE CEIL(marks.theory_marks)
                END AS theoryPMarks,

            @ctPmarks :=  CEIL(marks.ct_marks) AS ctPercentMarks,
            @obtained_marks := (@thPmarks + @ctPmarks) AS obtained_nmarks,

            IF(sub.subject_marks = 50 ,@obtained_marks := CEIL(@obtained_marks*100/50), @obtained_marks := @obtained_marks) AS fiftyToHundredMark,

                                 @resultStatus :=   case when marks.subject_id != enrolls.subject_id then (
                                    case when  marks.theory_marks>=sub.theory_pass_marks
                                    then (case when marks.mcq_marks>=sub.mcq_pass_marks
                            then (case when marks.practical_marks>=sub.practical_pass_marks
                                    then (case when @obtained_marks >= 40 then 'PASSED' else 'FAILED' end)
                            else 'FAILED' END) else 'FAILED' END) else 'FAILED' END) else 'PASSED' end as result
             FROM marks
             LEFT JOIN exams on exams.id = marks.exam_id
             LEFT JOIN subjects as sub on sub.id = marks.subject_id
             LEFT JOIN students as std on std.std_code = marks.student_code
             LEFT JOIN enrolls  on enrolls.student_id = std.id AND enrolls.class_id = @class_id  and enrolls.year= @year
             WHERE marks.exam_id =@exam_id and marks.class_id = @class_id AND marks.section_id =COALESCE(@section_id, marks.section_id) and marks.student_code = @std_code and marks.year = @year");

         $result = DB::select("SELECT marks.exam_id,std.name,std.std_code,temp.stdRoll,exams.name AS exam_name,marks.subject_id,sub.name as subject,sub.subject_code, sub.subject_marks,marks.class_id,temp.optionalsubId AS optional_subject, marks.theory_marks, marks.ct_marks,
 temp.ctPMarks,temp.theoryPMarks, temp.obtainedMark,max_score.hmarks AS highest_marks, temp.result as result_status, temp.fiftyToHundredMark,

				 CASE
		         WHEN  temp.result = 'PASSED' THEN (
            CASE
               WHEN  temp.fiftyToHundredMark >= 80 THEN 'A+'
               WHEN  temp.fiftyToHundredMark >= 70 and temp.fiftyToHundredMark <= 79 THEN 'A'
               WHEN  temp.fiftyToHundredMark >= 60 and temp.fiftyToHundredMark <= 69 THEN 'A-'
               WHEN  temp.fiftyToHundredMark >= 50 and temp.fiftyToHundredMark <= 59 THEN 'B'
               WHEN  temp.fiftyToHundredMark >= 40 and temp.fiftyToHundredMark <= 49 THEN 'C'
               WHEN  temp.fiftyToHundredMark >= 33 and temp.fiftyToHundredMark <= 39 THEN 'D'
               ELSE   'F' END ) ELSE 'F' END AS grade,
				CASE
               WHEN temp.result = 'PASSED' THEN (
               CASE
               WHEN  temp.fiftyToHundredMark >= 80 THEN '5.00'
               WHEN  temp.fiftyToHundredMark >= 70 and temp.fiftyToHundredMark <= 79 THEN '4.00'
               WHEN  temp.fiftyToHundredMark >= 60 and temp.fiftyToHundredMark <= 69 THEN '3.50'
               WHEN  temp.fiftyToHundredMark >= 50 and temp.fiftyToHundredMark <= 59 THEN '3.00'
               WHEN  temp.fiftyToHundredMark >= 40 and temp.fiftyToHundredMark <= 49 THEN '2.00'
               WHEN  temp.fiftyToHundredMark >= 33 and temp.fiftyToHundredMark <= 39 THEN '1.00'
               ELSE   '0.00' END ) ELSE '0.00' END AS CGPA
               FROM marks
               LEFT JOIN
               (
               SELECT marks.subject_id,
					MAX(CEIL(

                        CASE
                            when exams.ct_marks_percentage != '0' THEN (
                                CASE
                                    WHEN sub.subject_marks = 100 THEN CEIL(marks.theory_marks * 0.8)
                                    ELSE CEIL(marks.theory_marks*0.6)
                                END
                            )
                            ELSE CEIL(marks.theory_marks)
                        END + CEIL(marks.ct_marks)
			    	))
					as hmarks FROM marks
					JOIN exams  on exams.id = marks.exam_id
					JOIN subjects AS sub on sub.id = marks.subject_id
               WHERE marks.exam_id =@exam_id and marks.class_id = @class_id
               GROUP BY marks.exam_id,marks.class_id,marks.subject_id
               )
               as max_score
               on marks.subject_id = max_score.subject_id

               LEFT JOIN students as std on std.std_code = marks.student_code
               LEFT JOIN exams  on exams.id = marks.exam_id
               LEFT JOIN subjects as sub on sub.id = marks.subject_id
               LEFT JOIN temp_marksheet_result as temp on temp.subId = marks.subject_id
               WHERE marks.exam_id =@exam_id and marks.class_id = @class_id and marks.section_id = COALESCE(@section_id, marks.section_id) and
					marks.student_code = @std_code and marks.year = @year
               order by marks.student_code,sub.subject_order ASC");

         DB::unprepared(DB::raw(" DROP TABLE IF EXISTS temp_marksheet_result"));

         return $result;
      }
   }

   public static function jrfinalSummeryResult($exam_id_half, $exam_id_final, $class_id, $section_id, $year)
   {
      DB::statement(DB::raw("set @rownum=0,
            @exam_id_half = '$exam_id_half', @exam_id_final ='$exam_id_final',  @class_id = '$class_id',@section_id = '$section_id', @year = '$year',
            @halfTheoryPMarks=0,@halfCTPmarks=0,@halfObtainedMarks=0,@halfConvertedMarks=0, @halfResultStatus='PASSED',
            @finalTheoryPMarks=0,@finalCTPmarks=0,@finalObtainedMarks=0,@finalConvertedMarks=0, @finalResultStatus='PASSED',
            @avgMarks=0,@avgConvertedMarks=0,@avgGrade='',@avgCGPA='0.00' "));

      $result = DB::select("SELECT halfExam.student_code,students.name AS std_name,enrolls.roll
         FROM marks AS halfExam
         INNER JOIN students on students.std_code = halfExam.student_code
         INNER JOIN enrolls  on enrolls.student_id = students.id AND enrolls.class_id = @class_id  and enrolls.year= @year
         LEFT JOIN marks AS finalExam ON halfExam.student_code = finalExam.student_code AND halfExam.subject_id = finalExam.subject_id AND halfExam.student_code = finalExam.student_code
         WHERE halfExam.class_id = @class_id AND halfExam.section_id= @section_id AND halfExam.exam_id= @exam_id_half AND halfExam.year = @year AND
         finalExam.class_id = @class_id AND finalExam.section_id= @section_id AND finalExam.exam_id= @exam_id_final AND finalExam.year = @year
         group by enrolls.student_id order by enrolls.roll asc");

      return $result;

   }



   public static function jrfinalMarksheet($exam_id_half, $exam_id_final, $class_id, $section_id, $student_code, $year)
   {
      DB::statement(DB::raw("set @rownum=0, @std_code ='$student_code',
            @exam_id_half = '$exam_id_half', @exam_id_final ='$exam_id_final',  @class_id = '$class_id',@section_id = '$section_id', @year = '$year',
            @halfTheoryPMarks=0,@halfCTPmarks=0,@halfObtainedMarks=0,@halfConvertedMarks=0, @halfResultStatus='PASSED',
            @finalTheoryPMarks=0,@finalCTPmarks=0,@finalObtainedMarks=0,@finalConvertedMarks=0, @finalResultStatus='PASSED',
            @avgMarks=0,@avgConvertedMarks=0,@avgGrade='',@avgCGPA='0.00', @avgResultStatus='' "));

      $result = DB::select("SELECT @rownum  := @rownum  + 1 AS rownum, halfExam.exam_id, halfExam.student_code,students.name AS std_name,enrolls.roll, halfExam.subject_id,enrolls.subject_id AS optional_subject,sub.name AS subject_name, sub.subject_marks, sub.pass_marks,

         -- @halfTheoryPMarks := ceil(IF(sub.subject_marks > 50 , halfExam.theory_marks, halfExam.theory_marks)) AS halfTheoryPMarks,
-- @halfCTPmarks :=  ceil(IF(sub.subject_marks > 50 , halfExam.ct_marks, halfExam.ct_marks*.67)) AS halfCTPmarks,

            @halfTheoryPMarks := ceil(IF(sub.subject_marks > 50 , halfExam.theory_marks, halfExam.theory_marks)) AS halfTheoryPMarks,
@halfCTPmarks :=  ceil(IF(sub.subject_marks > 50 , halfExam.ct_marks, halfExam.ct_marks)) AS halfCTPmarks,
@halfObtainedMarks := ceil(@halfTheoryPMarks + @halfCTPmarks) AS halfObtainedMarks,
@halfConvertedMarks := CEIL((@halfObtainedMarks/sub.subject_marks)*100) AS halfConvertedMarks,
@halfResultStatus :=  IF(@halfObtainedMarks >= sub.pass_marks , 'PASSED', 'FAILED') AS halfResultStatus,
                     CASE
                      WHEN  @halfResultStatus = 'PASSED' THEN (
                     CASE
                        WHEN  @halfConvertedMarks >= 80 THEN 'A+'
                        WHEN  @halfConvertedMarks >= 70 and @halfConvertedMarks <= 79 THEN 'A'
                        WHEN  @halfConvertedMarks >= 60 and @halfConvertedMarks <= 69 THEN 'A-'
                        WHEN  @halfConvertedMarks >= 50 and @halfConvertedMarks <= 59 THEN 'B'
                        WHEN  @halfConvertedMarks >= 40 and @halfConvertedMarks <= 49 THEN 'C'
                        WHEN  @halfConvertedMarks >= 33 and @halfConvertedMarks <= 39 THEN 'D'
                        ELSE   'F' END ) ELSE 'F' END AS halfGrade,
                    CASE
                           WHEN @halfResultStatus = 'PASSED' THEN (
                           CASE
                           WHEN  @halfConvertedMarks >= 80 THEN '5.00'
                           WHEN  @halfConvertedMarks >= 70 and @halfConvertedMarks <= 79 THEN '4.00'
                           WHEN  @halfConvertedMarks >= 60 and @halfConvertedMarks <= 69 THEN '3.50'
                           WHEN  @halfConvertedMarks >= 50 and @halfConvertedMarks <= 59 THEN '3.00'
                           WHEN  @halfConvertedMarks >= 40 and @halfConvertedMarks <= 49 THEN '2.00'
                           WHEN  @halfConvertedMarks >= 33 and @halfConvertedMarks <= 39 THEN '1.00'
                           ELSE   '0.00' END ) ELSE '0.00' END AS halfCGPA,



         @finalTheoryPMarks := ceil(IF(sub.subject_marks > 50 , finalExam.theory_marks, finalExam.theory_marks)) AS finalTheoryPMarks,
         @finalCTPmarks :=  ceil(IF(sub.subject_marks > 50 , finalExam.ct_marks, finalExam.ct_marks*.67)) AS finalCTPmarks,
         @finalObtainedMarks := ceil(@finalTheoryPMarks + @finalCTPmarks) AS finalObtainedMarks,
         @finalConvertedMarks := CEIL((@finalObtainedMarks/sub.subject_marks)*100) AS finalConvertedMarks,
         @finalResultStatus :=  IF(@finalObtainedMarks >= sub.pass_marks , 'PASSED', 'FAIL') AS finalResultStatus,

                     CASE
                      WHEN  @finalResultStatus = 'PASSED' THEN (
                     CASE
                        WHEN  @finalConvertedMarks >= 80 THEN 'A+'
                        WHEN  @finalConvertedMarks >= 70 and @finalConvertedMarks <= 79 THEN 'A'
                        WHEN  @finalConvertedMarks >= 60 and @finalConvertedMarks <= 69 THEN 'A-'
                        WHEN  @finalConvertedMarks >= 50 and @finalConvertedMarks <= 59 THEN 'B'
                        WHEN  @finalConvertedMarks >= 40 and @finalConvertedMarks <= 49 THEN 'C'
                        WHEN  @finalConvertedMarks >= 33 and @finalConvertedMarks <= 39 THEN 'D'
                        ELSE   'F' END ) ELSE 'F' END AS finalGrade,
                 CASE
                        WHEN @finalResultStatus = 'PASSED' THEN (
                        CASE
                        WHEN  @finalConvertedMarks >= 80 THEN '5.00'
                        WHEN  @finalConvertedMarks >= 70 and @finalConvertedMarks <= 79 THEN '4.00'
                        WHEN  @finalConvertedMarks >= 60 and @finalConvertedMarks <= 69 THEN '3.50'
                        WHEN  @finalConvertedMarks >= 50 and @finalConvertedMarks <= 59 THEN '3.00'
                        WHEN  @finalConvertedMarks >= 40 and @finalConvertedMarks <= 49 THEN '2.00'
                        WHEN  @finalConvertedMarks >= 33 and @finalConvertedMarks <= 39 THEN '1.00'
                        ELSE   '0.00' END ) ELSE '0.00' END AS finalCGPA,

         @avgMarks := CEIL((@halfObtainedMarks + @finalObtainedMarks)/2) AS avgMarks,
         @avgConvertedMarks := CEIL((@avgMarks/sub.subject_marks)*100) AS avgConvertedMarks,
         @avgResultStatus :=  IF(@avgMarks >= sub.pass_marks , 'PASSED', 'FAILED') AS avgResultStatus,


        CASE
		         WHEN  @avgResultStatus = 'PASSED' THEN (
        CASE
               WHEN  @avgConvertedMarks >= 80 THEN 'A+'
               WHEN  @avgConvertedMarks >= 70 and @avgConvertedMarks <= 79 THEN 'A'
               WHEN  @avgConvertedMarks >= 60 and @avgConvertedMarks <= 69 THEN 'A-'
               WHEN  @avgConvertedMarks >= 50 and @avgConvertedMarks <= 59 THEN 'B'
               WHEN  @avgConvertedMarks >= 40 and @avgConvertedMarks <= 49 THEN 'C'
               WHEN  @avgConvertedMarks >= 33 and @avgConvertedMarks <= 39 THEN 'D'
               ELSE   'F' END ) ELSE '0.00' END AS avgGrade,
				CASE
		         WHEN  @avgResultStatus = 'PASSED' THEN (
            CASE
               WHEN  @avgConvertedMarks >= 80 THEN '5.00'
               WHEN  @avgConvertedMarks >= 70 and @avgConvertedMarks <= 79 THEN '4.00'
               WHEN  @avgConvertedMarks >= 60 and @avgConvertedMarks <= 69 THEN '3.50'
               WHEN  @avgConvertedMarks >= 50 and @avgConvertedMarks <= 59 THEN '3.00'
               WHEN  @avgConvertedMarks >= 40 and @avgConvertedMarks <= 49 THEN '2.00'
               WHEN  @avgConvertedMarks >= 33 and @avgConvertedMarks <= 39 THEN '1.00'
               ELSE   '0.00'  END ) ELSE '0.00' END AS avgCGPA


         FROM marks AS halfExam
         INNER JOIN subjects AS sub ON sub.id = halfExam.subject_id
         INNER JOIN students on students.std_code = halfExam.student_code
         INNER JOIN enrolls  on enrolls.student_id = students.id AND enrolls.class_id = @class_id  and enrolls.year= @year
         LEFT JOIN marks AS finalExam ON halfExam.student_code = finalExam.student_code AND halfExam.subject_id = finalExam.subject_id AND halfExam.student_code = finalExam.student_code

         WHERE halfExam.student_code= @std_code AND halfExam.class_id = @class_id AND halfExam.section_id= @section_id AND halfExam.exam_id= @exam_id_half AND halfExam.year = @year AND
         finalExam.student_code= @std_code AND finalExam.class_id = @class_id AND halfExam.section_id= @section_id AND finalExam.exam_id= @exam_id_final AND finalExam.year = @year order by sub.subject_order asc");

      return $result;

   }

   public static function jrhalfExamMarksheetSpecial($exam_id, $class_id, $section_id, $student_code, $year)
   {
      DB::statement(DB::raw("set @class_id='$class_id', @section_id='$section_id', @exam_id='$exam_id', @std_code='$student_code', @year='$year',
      @obtained_marks=0, @resultStatus='PASSED',@writtenMarks=0,@assignmentMarks=0,@otherMarks=0"));

      $createMarksheetTempTables = DB::unprepared(
        DB::raw("
            CREATE TEMPORARY TABLE temp_marksheet_result(stdCode VARCHAR(50),stdRoll VARCHAR(50), subId INT, subName VARCHAR(200), optionalsubId INT,  writtenMarks INT, assignmentMarks INT, otherMarks INT, obtainedMark INT,
            result VARCHAR(50))"
        ));

      if ($createMarksheetTempTables) {
         DB::insert("insert into temp_marksheet_result select marks.student_code, enrolls.roll ,marks.subject_id, sub.name, enrolls.subject_id,
            @writtenMarks := CEIL(marks.written_marks) AS writtenMarks,
            @assignmentMarks := CEIL(marks.assignment_marks) AS assignmentMarks,
            @otherMarks := CEIL(marks.other_marks) AS otherMarks,
            @obtained_marks := (@writtenMarks + @assignmentMarks + @otherMarks) AS obtained_nmarks,



            @resultStatus :=   case when marks.subject_id != enrolls.subject_id then (
                                    case when @obtained_marks>=33
                                    then 'PASSED' else 'FAILED' END) else 'PASSED' end as result
             FROM special_marks as marks
             LEFT JOIN exams on exams.id = marks.exam_id
             LEFT JOIN special_session_subjects as sub on sub.id = marks.subject_id
             LEFT JOIN students as std on std.std_code = marks.student_code
             LEFT JOIN enrolls  on enrolls.student_id = std.id AND enrolls.class_id = @class_id  and enrolls.year= @year
             WHERE marks.exam_id =@exam_id and marks.class_id = @class_id AND marks.section_id =@section_id and marks.student_code = @std_code and marks.year = @year");

         $result = DB::select("SELECT marks.exam_id,std.name,std.std_code,temp.stdRoll,exams.name AS exam_name,marks.subject_id,sub.name as subject,sub.subject_code, sub.subject_marks,marks.class_id,temp.optionalsubId AS optional_subject, marks.written_marks, marks.assignment_marks,  marks.other_marks,
            temp.writtenMarks,temp.assignmentMarks,temp.otherMarks, temp.obtainedMark,max_score.hmarks AS highest_marks, temp.result as result_status,

				 CASE
		         WHEN  temp.result = 'PASSED' THEN (
            CASE
               WHEN  temp.obtainedMark >= 80 THEN 'A+'
               WHEN  temp.obtainedMark >= 70 and temp.obtainedMark <= 79 THEN 'A'
               WHEN  temp.obtainedMark >= 60 and temp.obtainedMark <= 69 THEN 'A-'
               WHEN  temp.obtainedMark >= 50 and temp.obtainedMark <= 59 THEN 'B'
               WHEN  temp.obtainedMark >= 40 and temp.obtainedMark <= 49 THEN 'C'
               WHEN  temp.obtainedMark >= 33 and temp.obtainedMark <= 39 THEN 'D'
               ELSE   'F' END ) ELSE 'F' END AS grade,
				CASE
               WHEN temp.result = 'PASSED' THEN (
               CASE
               WHEN  temp.obtainedMark >= 80 THEN '5.00'
               WHEN  temp.obtainedMark >= 70 and temp.obtainedMark <= 79 THEN '4.00'
               WHEN  temp.obtainedMark >= 60 and temp.obtainedMark <= 69 THEN '3.50'
               WHEN  temp.obtainedMark >= 50 and temp.obtainedMark <= 59 THEN '3.00'
               WHEN  temp.obtainedMark >= 40 and temp.obtainedMark <= 49 THEN '2.00'
               WHEN  temp.obtainedMark >= 33 and temp.obtainedMark <= 39 THEN '1.00'
               ELSE   '0.00' END ) ELSE '0.00' END AS CGPA
               FROM special_marks as marks
               LEFT JOIN
               (
               SELECT marks.subject_id,
					MAX(CEIL(marks.written_marks) + CEIL(marks.assignment_marks) + CEIL(marks.other_marks))
					as hmarks FROM special_marks as marks
					JOIN exams  on exams.id = marks.exam_id
					JOIN special_session_subjects AS sub on sub.id = marks.subject_id
               WHERE marks.exam_id =@exam_id and marks.class_id = @class_id
               GROUP BY marks.exam_id,marks.class_id,marks.subject_id
               )
               as max_score
               on marks.subject_id = max_score.subject_id

               LEFT JOIN students as std on std.std_code = marks.student_code
               LEFT JOIN exams  on exams.id = marks.exam_id
               LEFT JOIN special_session_subjects as sub on sub.id = marks.subject_id
               LEFT JOIN temp_marksheet_result as temp on temp.subId = marks.subject_id
               WHERE marks.exam_id =@exam_id and marks.class_id = @class_id and marks.section_id =@section_id and
					marks.student_code = @std_code and marks.year = @year
               order by marks.student_code,sub.subject_order ASC");

         DB::unprepared(DB::raw(" DROP TABLE IF EXISTS temp_marksheet_result"));

         return $result;
      }
   }


   public static function srSummeryResult($exam_id, $class_id, $section_id, $year)
   {
      DB::statement(DB::raw("set @rownum=0, @class_id='$class_id', @resultStatus='PASSED', @obtained_marks=0,@thPmarks=0,@mcqPmarks=0,@practPmarks=0,@ctPmarks=0, @section_id=$section_id, @exam_id='$exam_id', @year='$year'"));

      $createSummeryTempTables = DB::unprepared(
        DB::raw("
            CREATE TEMPORARY TABLE summery_result( exmId INT, examTitle VARCHAR(100), stdCode VARCHAR(50), stdName VARCHAR(100), stdClass VARCHAR(50),
            stdRoll VARCHAR(50), stdSection INT, subId INT, subName VARCHAR(200),optionalsubId INT, subCode int, subMarks int, theoryPMarks INT,mcqPMarks INT,practPmarks INT, ctPMarks INT, obtainedMark INT,
            fiftyToHundredMark INT,status VARCHAR(50), grade VARCHAR(5), gpa DECIMAL(10,2))"
        )
      );

      if ($createSummeryTempTables) {
         DB::insert("insert into summery_result select exams.id,exams.name, std.std_code, std.name,marks.class_id,enrolls.roll, marks.section_id,
         marks.subject_id, sub.name, enrolls.subject_id, sub.subject_code, sub.subject_marks,

         @thPmarks := CASE
                     when exams.ct_marks_percentage != '0' THEN (
                        CASE
                           WHEN sub.theory_marks = 100 then ceil((marks.theory_marks * 0.8)+ (marks.ct_marks))
                           WHEN sub.theory_marks = 70 then ceil((marks.theory_marks * (60/70))+ (marks.ct_marks * 0.5))
                           WHEN sub.theory_marks = 50 then ceil((marks.theory_marks * 0.8)+ (marks.ct_marks * 0.5))
                           ELSE CEIL(marks.theory_marks)
                        END
                     )
                     ELSE CEIL(marks.theory_marks)
                  END AS theoryPMarks,

         @mcqPmarks := CASE
                     when exams.ct_marks_percentage != '0' THEN (
                        CASE
                           WHEN sub.mcq_marks = 30 then ceil((marks.mcq_marks * (20/30))+ (marks.ct_marks * 0.5))
                           WHEN sub.mcq_marks = 25 then ceil((marks.mcq_marks * 0.6)+ (marks.ct_marks * 0.5))
                           ELSE CEIL(marks.mcq_marks)
                        END
                     )
                     ELSE CEIL(marks.mcq_marks)
                  END AS mcqPMarks,

         @practPmarks := CASE
                     when exams.ct_marks_percentage != '0' THEN (
                        CASE
                           WHEN sub.theory_marks = 0 then ceil((marks.practical_marks * 0.6)+ (marks.ct_marks * 0.5))
                           ELSE CEIL(marks.practical_marks)
                        END
                     )
                     ELSE CEIL(marks.practical_marks)
                  END AS practPmarks,

         @ctPmarks := 0 AS ctPercentMarks,

         @obtained_marks := (@thPmarks +  @mcqPmarks + @ctPmarks + @practPmarks) AS obtained_nmarks,

           IF(sub.subject_marks = 50 ,@obtained_marks := CEIL(@obtained_marks*100/50), @obtained_marks := @obtained_marks) AS fiftyToHundredMark,

                     @resultStatus :=   case when marks.subject_id != -1 then (
                        case when  @thPmarks>=sub.theory_pass_marks
                        then (case when @mcqPmarks>=sub.mcq_pass_marks
								then (case when @practPmarks>=sub.practical_pass_marks
                        then (case when @obtained_marks >= 33 then 'PASSED' else 'FAILED' end)
								else 'FAILED' END) else 'FAILED' END) else 'FAILED' END) else 'PASSED' end as result,
				CASE
		         WHEN  @resultStatus = 'PASSED' THEN (
            CASE
               WHEN  @obtained_marks >= 80 THEN 'A+'
               WHEN  @obtained_marks >= 70 and @obtained_marks <= 79 THEN 'A'
               WHEN  @obtained_marks >= 60 and @obtained_marks <= 69 THEN 'A-'
               WHEN  @obtained_marks >= 50 and @obtained_marks <= 59 THEN 'B'
               WHEN  @obtained_marks >= 40 and @obtained_marks <= 49 THEN 'C'
               WHEN  @obtained_marks >= 33 and @obtained_marks <= 39 THEN 'D'
               ELSE   'F' END ) ELSE 'F' END AS grade,
				CASE
               WHEN @resultStatus = 'PASSED' THEN (
               CASE
               WHEN  @obtained_marks >= 80 THEN '5.00'
               WHEN  @obtained_marks >= 70 and @obtained_marks <= 79 THEN '4.00'
               WHEN  @obtained_marks >= 60 and @obtained_marks <= 69 THEN '3.50'
               WHEN  @obtained_marks >= 50 and @obtained_marks <= 59 THEN '3.00'
               WHEN  @obtained_marks >= 40 and @obtained_marks <= 49 THEN '2.00'
               WHEN  @obtained_marks >= 33 and @obtained_marks <= 39 THEN '1.00'
               ELSE   '0.00' END ) ELSE '0.00' END AS gpa

								from  marks
                        inner JOIN students as std on std.std_code = marks.student_code
                        LEFT JOIN enrolls  on enrolls.student_id = std.id AND enrolls.class_id = @class_id  and enrolls.YEAR= @year
                        LEFT JOIN exams on exams.id = marks.exam_id
                        LEFT JOIN subjects as sub on sub.id = marks.subject_id
                        where marks.class_id =@class_id and marks.exam_id=@exam_id and marks.section_id=COALESCE(@section_id, marks.section_id) AND marks.YEAR =@year
                        order by marks.student_code,sub.subject_order ASC");

         $result = DB::select("SELECT rownum,exmId,examTitle,stdCode,stdName,stdClass,stdRoll,stdSection,subId,totalSubject,hasOptional,mainSubPoint,optionalSubPoint,failedSubject,totalMarks,CASE  WHEN T2.CNT = 0  THEN 'PASSED'  ELSE 'FAILED'  END result
            FROM (
            SELECT @rownum  := @rownum  + 1 AS rownum, exmId,examTitle,stdCode,stdName,stdClass,stdRoll,stdSection,subId,
            sum(case when subId != optionalsubId then 1 END) AS totalSubject,
            @cgpaPoint := sum(case when subId != optionalsubId then gpa END) AS mainSubPoint,
            count(case when subId = optionalsubId then 1 END) AS hasOptional,
            @optionalSubPoint := sum(case when subId = optionalsubId then (case when gpa>2 then gpa-2 END) ELSE 0 END) AS optionalSubPoint,
            sum(CASE WHEN status = 'FAILED' THEN 1 ELSE 0 END) AS failedSubject,
            SUM(obtainedMark) AS totalMarks, COUNT(CASE WHEN status = 'FAILED' THEN 1 END) CNT
            FROM summery_result GROUP BY stdCode order by stdRoll asc) T2");

         DB::unprepared(DB::raw("DROP TABLE IF EXISTS summery_result"));

         return $result;
      }
   }
   public static function srSummeryResultSpecial($exam_id, $class_id, $section_id, $year)
   {
      DB::statement(DB::raw("set @rownum=0, @class_id='$class_id', @resultStatus='PASSED', @obtained_marks=0,@writtenMarks=0,@assignmentMarks=0,@otherMarks=0, @section_id='$section_id', @exam_id='$exam_id', @year='$year'"));

      $createSummeryTempTables = DB::unprepared(
        DB::raw("
            CREATE TEMPORARY TABLE summery_result( exmId INT, examTitle VARCHAR(100), stdCode VARCHAR(50), stdName VARCHAR(100), stdClass VARCHAR(50),
            stdRoll VARCHAR(50), stdSection INT, subId INT, subName VARCHAR(200),optionalsubId INT, subCode int, subMarks int, writtenMarks INT,assignmentMarks INT,otherMarks INT, obtainedMark INT,
            status VARCHAR(50), grade VARCHAR(5), gpa DECIMAL(10,2))"
        )
      );

      if ($createSummeryTempTables) {
         DB::insert("insert into summery_result select exams.id,exams.name, std.std_code, std.name,marks.class_id,enrolls.roll, marks.section_id,
         marks.subject_id, sub.name, enrolls.subject_id, sub.subject_code, sub.subject_marks,

         @writtenMarks := CEIL(marks.written_marks) AS writtenMarks,
         @assignmentMarks := CEIL(marks.assignment_marks) AS assignmentMarks,
         @otherMarks := CEIL(marks.other_marks) AS otherMarks,


         @obtained_marks := (@writtenMarks +  @assignmentMarks + @otherMarks) AS obtained_nmarks,


                     @resultStatus :=   case when marks.subject_id != enrolls.subject_id then (
                        case when  @obtained_marks>=33
                        then 'PASSED' else 'FAILED' END) else 'PASSED' end as result,
				CASE
		         WHEN  @resultStatus = 'PASSED' THEN (
            CASE
               WHEN  @obtained_marks >= 80 THEN 'A+'
               WHEN  @obtained_marks >= 70 and @obtained_marks <= 79 THEN 'A'
               WHEN  @obtained_marks >= 60 and @obtained_marks <= 69 THEN 'A-'
               WHEN  @obtained_marks >= 50 and @obtained_marks <= 59 THEN 'B'
               WHEN  @obtained_marks >= 40 and @obtained_marks <= 49 THEN 'C'
               WHEN  @obtained_marks >= 33 and @obtained_marks <= 39 THEN 'D'
               ELSE   'F' END ) ELSE 'F' END AS grade,
				CASE
               WHEN @resultStatus = 'PASSED' THEN (
               CASE
               WHEN  @obtained_marks >= 80 THEN '5.00'
               WHEN  @obtained_marks >= 70 and @obtained_marks <= 79 THEN '4.00'
               WHEN  @obtained_marks >= 60 and @obtained_marks <= 69 THEN '3.50'
               WHEN  @obtained_marks >= 50 and @obtained_marks <= 59 THEN '3.00'
               WHEN  @obtained_marks >= 40 and @obtained_marks <= 49 THEN '2.00'
               WHEN  @obtained_marks >= 33 and @obtained_marks <= 39 THEN '1.00'
               ELSE   '0.00' END ) ELSE '0.00' END AS gpa

								from special_marks as marks
                        inner JOIN students as std on std.std_code = marks.student_code
                        LEFT JOIN enrolls  on enrolls.student_id = std.id AND enrolls.class_id = @class_id  and enrolls.YEAR= @year
                        LEFT JOIN exams on exams.id = marks.exam_id
                        LEFT JOIN special_session_subjects as sub on sub.id = marks.subject_id
                        where marks.class_id =@class_id and marks.exam_id=@exam_id and marks.section_id=@section_id AND marks.YEAR =@year
                        order by marks.student_code,sub.subject_order ASC");

         $result = DB::select("SELECT rownum,exmId,examTitle,stdCode,stdName,stdClass,stdRoll,stdSection,subId,totalSubject,hasOptional,mainSubPoint,optionalSubPoint,failedSubject,totalMarks,CASE  WHEN T2.CNT = 0  THEN 'PASSED'  ELSE 'FAILED'  END result
            FROM (
            SELECT @rownum  := @rownum  + 1 AS rownum, exmId,examTitle,stdCode,stdName,stdClass,stdRoll,stdSection,subId,
            sum(1) AS totalSubject,
            @cgpaPoint := sum( gpa) AS mainSubPoint,
            count(case when subId = optionalsubId then 1 END) AS hasOptional,
            @optionalSubPoint := sum(0) AS optionalSubPoint,
            sum(CASE WHEN status = 'FAILED' THEN 1 ELSE 0 END) AS failedSubject,
            SUM(obtainedMark) AS totalMarks, COUNT(CASE WHEN status = 'FAILED' THEN 1 END) CNT
            FROM summery_result GROUP BY stdCode order by stdRoll asc) T2");

         DB::unprepared(DB::raw("DROP TABLE IF EXISTS summery_result"));

         return $result;
      }
   }


   public static function srExamMarksheet($exam_id, $class_id, $section_id, $student_code, $year)
   {
      DB::statement(DB::raw("set @class_id='$class_id', @section_id=$section_id, @exam_id='$exam_id', @std_code='$student_code', @year='$year',
      @obtained_marks=0, @resultStatus='PASSED',@thPmarks=0,@mcqPmarks=0,@practPmarks=0,@ctPmarks=0"));

      $createMarksheetTempTables = DB::unprepared(
        DB::raw("
            CREATE TEMPORARY TABLE temp_marksheet_result(stdCode VARCHAR(50),stdRoll VARCHAR(50), subId INT, subName VARCHAR(200), optionalsubId INT, theoryPMarks INT, mcqPMarks INT,practPmarks INT, ctPMarks INT, obtainedMark INT,
            fiftyToHundredMark INT, result VARCHAR(50))"
        ));

      if ($createMarksheetTempTables) {
         DB::insert("insert into temp_marksheet_result select marks.student_code, enrolls.roll ,marks.subject_id, sub.name, enrolls.subject_id,


            @thPmarks := CASE
                        when exams.ct_marks_percentage != '0' THEN (
                           CASE
                              WHEN sub.theory_marks = 100 then ceil((marks.theory_marks * 0.8)+ (marks.ct_marks))
                              WHEN sub.theory_marks = 70 then ceil((marks.theory_marks * (60/70))+ (marks.ct_marks * 0.5))
                              WHEN sub.theory_marks = 50 then ceil((marks.theory_marks * 0.8)+ (marks.ct_marks * 0.5))
                              ELSE CEIL(marks.theory_marks)
                           END
                        )
                        ELSE CEIL(marks.theory_marks)
                     END AS theoryPMarks,

            @mcqPmarks := CASE
                        when exams.ct_marks_percentage != '0' THEN (
                           CASE
                              WHEN sub.mcq_marks = 30 then ceil((marks.mcq_marks * (20/30))+ (marks.ct_marks * 0.5))
                              WHEN sub.mcq_marks = 25 then ceil((marks.mcq_marks * 0.6)+ (marks.ct_marks * 0.5))
                              ELSE CEIL(marks.mcq_marks)
                           END
                        )
                        ELSE CEIL(marks.mcq_marks)
                     END AS mcqPMarks,

            @practPmarks := CASE
                        when exams.ct_marks_percentage != '0' THEN (
                           CASE
                              WHEN sub.theory_marks = 0 then ceil((marks.practical_marks * 0.6)+ (marks.ct_marks * 0.5))
                              ELSE CEIL(marks.practical_marks)
                           END
                        )
                        ELSE CEIL(marks.practical_marks)
                     END AS practPmarks,

            @ctPmarks := 0 AS ctPercentMarks,

            @obtained_marks := (@thPmarks +  @mcqPmarks + @ctPmarks + @practPmarks) AS obtained_nmarks,

            IF(sub.subject_marks = 50 ,@obtained_marks := CEIL(@obtained_marks*100/50), @obtained_marks := @obtained_marks) AS fiftyToHundredMark,

                                 @resultStatus :=   case when marks.subject_id != -1 then (
                                    case when  @thPmarks>=sub.theory_pass_marks
                                    then (case when @mcqPmarks>=sub.mcq_pass_marks
                            then (case when @practPmarks>=sub.practical_pass_marks
                                    then (case when @obtained_marks >= 33 then 'PASSED' else 'FAILED' end)
                            else 'FAILED' END) else 'FAILED' END) else 'FAILED' END) else 'PASSED' end as result
             FROM marks
             LEFT JOIN exams on exams.id = marks.exam_id
             LEFT JOIN subjects as sub on sub.id = marks.subject_id
             LEFT JOIN students as std on std.std_code = marks.student_code
             LEFT JOIN enrolls  on enrolls.student_id = std.id AND enrolls.class_id = @class_id  and enrolls.year= @year
             WHERE marks.exam_id =@exam_id and marks.class_id = @class_id AND marks.section_id = COALESCE(@section_id, marks.section_id) and marks.student_code = @std_code and marks.year = @year");

         $result = DB::select("SELECT marks.exam_id,std.name,std.std_code,temp.stdRoll,exams.name AS exam_name,marks.subject_id,sub.name as subject,sub.subject_code, sub.subject_marks,marks.class_id,temp.optionalsubId AS optional_subject,
  marks.theory_marks,marks.ct_marks, marks.mcq_marks,marks.practical_marks,
 temp.ctPMarks,temp.theoryPMarks,temp.mcqPMarks,temp.practPmarks, temp.obtainedMark,max_score.hmarks AS highest_marks, temp.fiftyToHundredMark, temp.result as result_status,

				 CASE
		         WHEN  temp.result = 'PASSED' THEN (
            CASE
               WHEN  temp.fiftyToHundredMark >= 80 THEN 'A+'
               WHEN  temp.fiftyToHundredMark >= 70 and temp.fiftyToHundredMark <= 79 THEN 'A'
               WHEN  temp.fiftyToHundredMark >= 60 and temp.fiftyToHundredMark <= 69 THEN 'A-'
               WHEN  temp.fiftyToHundredMark >= 50 and temp.fiftyToHundredMark <= 59 THEN 'B'
               WHEN  temp.fiftyToHundredMark >= 40 and temp.fiftyToHundredMark <= 49 THEN 'C'
               WHEN  temp.fiftyToHundredMark >= 33 and temp.fiftyToHundredMark <= 39 THEN 'D'
               ELSE   'F' END ) ELSE 'F' END AS grade,
				CASE
               WHEN temp.result = 'PASSED' THEN (
               CASE
               WHEN  temp.fiftyToHundredMark >= 80 THEN '5.00'
               WHEN  temp.fiftyToHundredMark >= 70 and temp.fiftyToHundredMark <= 79 THEN '4.00'
               WHEN  temp.fiftyToHundredMark >= 60 and temp.fiftyToHundredMark <= 69 THEN '3.50'
               WHEN  temp.fiftyToHundredMark >= 50 and temp.fiftyToHundredMark <= 59 THEN '3.00'
               WHEN  temp.fiftyToHundredMark >= 40 and temp.fiftyToHundredMark <= 49 THEN '2.00'
               WHEN  temp.fiftyToHundredMark >= 33 and temp.fiftyToHundredMark <= 39 THEN '1.00'
               ELSE   '0.00' END ) ELSE '0.00' END AS CGPA
               FROM marks
               LEFT JOIN
               (
               SELECT marks.subject_id,
					MAX(CEIL(
                    CASE
                        when exams.ct_marks_percentage != '0' THEN (
                           CASE
                              WHEN sub.theory_marks = 100 then ceil((marks.theory_marks * 0.8)+ (marks.ct_marks))
                              WHEN sub.theory_marks = 70 then ceil((marks.theory_marks * (60/70))+ (marks.ct_marks * 0.5))
                              WHEN sub.theory_marks = 50 then ceil((marks.theory_marks * 0.8)+ (marks.ct_marks * 0.5))
                              ELSE CEIL(marks.theory_marks)
                           END
                        )
                        ELSE CEIL(marks.theory_marks)
                     END
                    +
                    CASE
                        when exams.ct_marks_percentage != '0' THEN (
                           CASE
                              WHEN sub.mcq_marks = 30 then ceil((marks.mcq_marks * (20/30))+ (marks.ct_marks * 0.5))
                              WHEN sub.mcq_marks = 25 then ceil((marks.mcq_marks * 0.6)+ (marks.ct_marks * 0.5))
                              ELSE CEIL(marks.mcq_marks)
                           END
                        )
                        ELSE CEIL(marks.mcq_marks)
                     END
                    +
                    CASE
                        when exams.ct_marks_percentage != '0' THEN (
                           CASE
                              WHEN sub.theory_marks = 0 then ceil((marks.practical_marks * 0.6)+ (marks.ct_marks * 0.5))
                              ELSE CEIL(marks.practical_marks)
                           END
                        )
                        ELSE CEIL(marks.practical_marks)
                     END


			    	))
					as hmarks FROM marks
					JOIN exams  on exams.id = marks.exam_id
					JOIN subjects AS sub on sub.id = marks.subject_id
               WHERE marks.exam_id =@exam_id and marks.class_id = @class_id
               GROUP BY marks.exam_id,marks.class_id,marks.subject_id
               )
               as max_score
               on marks.subject_id = max_score.subject_id

               LEFT JOIN students as std on std.std_code = marks.student_code
               LEFT JOIN exams  on exams.id = marks.exam_id
               LEFT JOIN subjects as sub on sub.id = marks.subject_id
               LEFT JOIN temp_marksheet_result as temp on temp.subId = marks.subject_id
               WHERE marks.exam_id =@exam_id and marks.class_id = @class_id and marks.section_id =COALESCE(@section_id, marks.section_id) and
					marks.student_code = @std_code and marks.year = @year
               order by sub.subject_code,sub.subject_order ASC");

         DB::unprepared(DB::raw(" DROP TABLE IF EXISTS temp_marksheet_result"));

         return $result;
      }
   }


   public static function srExamMarksheetSpecial($exam_id, $class_id, $section_id, $student_code, $year)
   {
      DB::statement(DB::raw("set @class_id='$class_id', @section_id='$section_id', @exam_id='$exam_id', @std_code='$student_code', @year='$year',
      @obtained_marks=0, @resultStatus='PASSED',@writtenMarks=0,@assignmentMarks=0,@otherMarks=0"));

      $createMarksheetTempTables = DB::unprepared(
        DB::raw("
            CREATE TEMPORARY TABLE temp_marksheet_result(stdCode VARCHAR(50),stdRoll VARCHAR(50), subId INT, subName VARCHAR(200), optionalsubId INT, writtenMarks INT, assignmentMarks INT,otherMarks INT, obtainedMark INT,
            result VARCHAR(50))"
        ));

      if ($createMarksheetTempTables) {
         DB::insert("insert into temp_marksheet_result select marks.student_code, enrolls.roll ,marks.subject_id, sub.name, enrolls.subject_id,

            @writtenMarks := CEIL(marks.written_marks) AS writtenMarks,
            @assignmentMarks := CEIL(marks.assignment_marks) AS assignmentMarks,
            @otherMarks := CEIL(marks.other_marks) AS otherMarks,

            @obtained_marks := ( @writtenMarks +  @assignmentMarks + @otherMarks) AS obtained_nmarks,


            @resultStatus :=   case when marks.subject_id != enrolls.subject_id then (
                                    case when  @obtained_marks>=33
                                    then 'PASSED' else 'FAILED' END) else 'PASSED' end as result
             FROM special_marks as marks
             LEFT JOIN exams on exams.id = marks.exam_id
             LEFT JOIN special_session_subjects as sub on sub.id = marks.subject_id
             LEFT JOIN students as std on std.std_code = marks.student_code
             LEFT JOIN enrolls  on enrolls.student_id = std.id AND enrolls.class_id = @class_id  and enrolls.year= @year
             WHERE marks.exam_id =@exam_id and marks.class_id = @class_id AND marks.section_id =@section_id and marks.student_code = @std_code and marks.year = @year");

         $result = DB::select("SELECT marks.exam_id,std.name,std.std_code,temp.stdRoll,exams.name AS exam_name,marks.subject_id,sub.name as subject,sub.subject_code, sub.subject_marks,marks.class_id,temp.optionalsubId AS optional_subject,
           marks.written_marks,marks.assignment_marks, marks.other_marks,
          temp.writtenMarks,temp.assignmentMarks,temp.otherMarks, temp.obtainedMark,max_score.hmarks AS highest_marks, temp.result as result_status,

				 CASE
		         WHEN  temp.result = 'PASSED' THEN (
            CASE
               WHEN  temp.obtainedMark >= 80 THEN 'A+'
               WHEN  temp.obtainedMark >= 70 and temp.obtainedMark <= 79 THEN 'A'
               WHEN  temp.obtainedMark >= 60 and temp.obtainedMark <= 69 THEN 'A-'
               WHEN  temp.obtainedMark >= 50 and temp.obtainedMark <= 59 THEN 'B'
               WHEN  temp.obtainedMark >= 40 and temp.obtainedMark <= 49 THEN 'C'
               WHEN  temp.obtainedMark >= 33 and temp.obtainedMark <= 39 THEN 'D'
               ELSE   'F' END ) ELSE 'F' END AS grade,
				CASE
               WHEN temp.result = 'PASSED' THEN (
               CASE
               WHEN  temp.obtainedMark >= 80 THEN '5.00'
               WHEN  temp.obtainedMark >= 70 and temp.obtainedMark <= 79 THEN '4.00'
               WHEN  temp.obtainedMark >= 60 and temp.obtainedMark <= 69 THEN '3.50'
               WHEN  temp.obtainedMark >= 50 and temp.obtainedMark <= 59 THEN '3.00'
               WHEN  temp.obtainedMark >= 40 and temp.obtainedMark <= 49 THEN '2.00'
               WHEN  temp.obtainedMark >= 33 and temp.obtainedMark <= 39 THEN '1.00'
               ELSE   '0.00' END ) ELSE '0.00' END AS CGPA
               FROM special_marks as marks
               LEFT JOIN
               (
               SELECT marks.subject_id,

					MAX(CEIL(marks.written_marks+marks.assignment_marks+marks.other_marks))
					as hmarks FROM special_marks as marks
					JOIN exams  on exams.id = marks.exam_id
					JOIN special_session_subjects AS sub on sub.id = marks.subject_id
               WHERE marks.exam_id =@exam_id and marks.class_id = @class_id
               GROUP BY marks.exam_id,marks.class_id,marks.subject_id
               )
               as max_score
               on marks.subject_id = max_score.subject_id

               LEFT JOIN students as std on std.std_code = marks.student_code
               LEFT JOIN exams  on exams.id = marks.exam_id
               LEFT JOIN special_session_subjects as sub on sub.id = marks.subject_id
               LEFT JOIN temp_marksheet_result as temp on temp.subId = marks.subject_id
               WHERE marks.exam_id =@exam_id and marks.class_id = @class_id and marks.section_id =@section_id and
					marks.student_code = @std_code and marks.year = @year
               order by sub.subject_code,sub.subject_order ASC");

         DB::unprepared(DB::raw(" DROP TABLE IF EXISTS temp_marksheet_result"));

         return $result;
      }
   }


   public static function generateSummeryResult($exam_id, $class_id, $section_id, $year)
   {
      DB::statement(DB::raw("set @rownum=0, @class_id='$class_id',@obtained_marks=0, @resultStatus='PASSED',@ctPmarks=0,@mPmarks=0, @section_id='$section_id', @exam_id='$exam_id', @year='$year'"));

      $createSummeryTempTables = DB::unprepared(
        DB::raw("
            CREATE TEMPORARY TABLE summery_result( exmId INT, examTitle VARCHAR(100), stdCode VARCHAR(50), stdName VARCHAR(100), stdClass VARCHAR(50),
            stdRoll VARCHAR(50), stdSection INT, subId INT, optionalsubId INT, ctPMarks INT, mainPMarks INT, obtainedMark INT,
            fiftyToHundredMark INT,status VARCHAR(50), grade VARCHAR(5), gpa DECIMAL(10,2))"
        )
      );

      if ($createSummeryTempTables) {
         DB::insert("insert into summery_result select exams.id,exams.name, std.std_code, std.name,marks.class_id,enrolls.roll, marks.section_id,
marks.subject_id, enrolls.subject_id,
   @ctPmarks := CEIL(marks.ct_marks*exams.ct_marks_percentage/if(sub.ct_marks=0,1,sub.ct_marks)) AS ctPercentMarks,
   @mPmarks := CEIL( marks.total_marks*(sub.subject_marks - exams.ct_marks_percentage)/(sub.theory_marks+sub.mcq_marks +
				sub.practical_marks)) AS mainPercentMarks,
	@mPmarks + @ctPmarks AS obtained_nmarks,

	IF(sub.subject_marks = 50 ,@obtained_marks := CEIL((@ctPmarks+@mPmarks)*100/sub.subject_marks), @obtained_marks := (@ctPmarks+@mPmarks)) AS fiftyToHundredMark,

                     @resultStatus :=   case when marks.subject_id != enrolls.subject_id then (
                        case when  marks.theory_marks>=sub.theory_pass_marks
                        then (case when marks.mcq_marks>=sub.mcq_pass_marks
								then (case when marks.practical_marks>=sub.practical_pass_marks
                        then (case when
								CEIL( marks.total_marks*(sub.subject_marks-exams.ct_marks_percentage)/(sub.theory_marks+sub.mcq_marks +
				sub.practical_marks) + marks.ct_marks*exams.ct_marks_percentage/if(sub.ct_marks=0,1,sub.ct_marks)) >=sub.pass_marks
								then 'PASSED' else 'FAILED' end) else 'FAILED' END) else 'FAILED' END)
                        else 'FAILED' END) else 'PASSED' end as result,
				CASE
		         WHEN  @resultStatus = 'PASSED' THEN (
               CASE
               WHEN  @obtained_marks >= 80 THEN 'A+'
               WHEN  @obtained_marks >= 70 and @obtained_marks <= 79 THEN 'A'
               WHEN  @obtained_marks >= 60 and @obtained_marks <= 69 THEN 'A-'
               WHEN  @obtained_marks >= 50 and @obtained_marks <= 59 THEN 'B'
               WHEN  @obtained_marks >= 40 and @obtained_marks <= 49 THEN 'C'
               WHEN  @obtained_marks >= 33 and @obtained_marks <= 39 THEN 'D'
               ELSE   'F' END ) ELSE 'F' END AS grade,
				CASE
               WHEN @resultStatus = 'PASSED' THEN (
               CASE
               WHEN  @obtained_marks >= 80 THEN '5.00'
               WHEN  @obtained_marks >= 70 and @obtained_marks <= 79 THEN '4.00'
               WHEN  @obtained_marks >= 60 and @obtained_marks <= 69 THEN '3.50'
               WHEN  @obtained_marks >= 50 and @obtained_marks <= 59 THEN '3.00'
               WHEN  @obtained_marks >= 40 and @obtained_marks <= 49 THEN '2.00'
               WHEN  @obtained_marks >= 33 and @obtained_marks <= 39 THEN '1.00'
               ELSE   '0.00' END ) ELSE '0.00' END AS gpa

								from  marks
                        inner JOIN students as std on std.std_code = marks.student_code
                        LEFT JOIN enrolls  on enrolls.student_id = std.id AND enrolls.class_id = @class_id  and enrolls.YEAR= @year
                        LEFT JOIN exams on exams.id = marks.exam_id
                        LEFT JOIN subjects as sub on sub.id = marks.subject_id
                        where marks.class_id =@class_id and marks.exam_id=@exam_id and marks.section_id=@section_id AND marks.YEAR =@year
                        order by marks.student_code,sub.subject_order ASC");

         $result = DB::select("SELECT rownum,exmId,examTitle,stdCode,stdName,stdClass,stdRoll,stdSection,subId,totalSubject,hasOptional,mainSubPoint,optionalSubPoint,totalMarks,failedSubject,CASE  WHEN T2.CNT = 0  THEN 'PASSED'  ELSE 'FAILED'  END result
               FROM (
               SELECT @rownum  := @rownum  + 1 AS rownum, exmId,examTitle,stdCode,stdName,stdClass,stdRoll,stdSection,subId,
               sum(case when subId != optionalsubId then 1 END) AS totalSubject,
               @cgpaPoint := sum(case when subId != optionalsubId then gpa END) AS mainSubPoint,
               count(case when subId = optionalsubId then 1 END) AS hasOptional,
               @optionalSubPoint := sum(case when subId = optionalsubId then (case when gpa>2 then gpa-2 END) ELSE 0 END) AS optionalSubPoint,
               sum(CASE WHEN status = 'FAILED' THEN 1 ELSE 0 END) AS failedSubject,
               SUM(obtainedMark) AS totalMarks, COUNT(CASE WHEN status = 'FAILED' THEN 1 END) CNT
                    FROM summery_result GROUP BY stdCode) T2");

         DB::unprepared(DB::raw(" DROP TABLE IF EXISTS summery_result"));

         return $result;
      }
   }


   public static function generateMarksheetResult($exam_id, $class_id, $section_id, $student_code, $year)
   {
      DB::statement(DB::raw("set @class_id='$class_id', @section_id='$section_id', @exam_id='$exam_id', @std_code='$student_code', @year='$year',
      @obtained_marks=0, @resultStatus='PASSED',@ctPmarks=0,@mPmarks=0"));

      $createMarksheetTempTables = DB::unprepared(
        DB::raw("
            CREATE TEMPORARY TABLE temp_marksheet_result(stdCode VARCHAR(50),stdRoll VARCHAR(50), subId INT, optionalsubId INT, ctPMarks INT, mainPMarks INT, obtainedMark INT, result VARCHAR(50))"
        )
      );

      if ($createMarksheetTempTables) {
         DB::insert("insert into temp_marksheet_result select marks.student_code, enrolls.roll ,marks.subject_id, enrolls.subject_id,
   @ctPmarks := CEIL(marks.ct_marks*exams.ct_marks_percentage/if(sub.ct_marks=0,1,sub.ct_marks)) AS ctPercentMarks,
   @mPmarks := CEIL( marks.total_marks*(sub.subject_marks - exams.ct_marks_percentage)/(sub.theory_marks+sub.mcq_marks +
				sub.practical_marks)) AS mainPercentMarks,
	@mPmarks + @ctPmarks AS obtained_nmarks,
	case when marks.subject_id != enrolls.subject_id then (
                        case when  marks.theory_marks>=sub.theory_pass_marks
                        then (case when marks.mcq_marks>=sub.mcq_pass_marks
								then (case when marks.practical_marks>=sub.practical_pass_marks
                        then (case when  	@mPmarks + @ctPmarks >=sub.pass_marks
								then 'P' else 'F' end) else 'F' END) else 'F' END)
                        else 'F' END) else 'P' end as result
 FROM marks
 LEFT JOIN exams on exams.id = marks.exam_id
 LEFT JOIN subjects as sub on sub.id = marks.subject_id
 LEFT JOIN students as std on std.std_code = marks.student_code
 LEFT JOIN enrolls  on enrolls.student_id = std.id AND enrolls.class_id = @class_id  and enrolls.year= @year
 WHERE marks.exam_id =@exam_id and marks.class_id = @class_id AND marks.section_id =@section_id and marks.student_code = @std_code and marks.year = @year");

         $result = DB::select("SELECT marks.exam_id,std.name,std.std_code,temp.stdRoll,exams.name AS exam_name,sub.name as subject,sub.subject_code,sub.subject_marks,marks.class_id,marks.subject_id,temp.optionalsubId AS optional_subject, marks.theory_marks, marks.mcq_marks, marks.practical_marks, marks.ct_marks,marks.total_marks,
 temp.ctPMarks,temp.mainPMarks, temp.obtainedMark,max_score.hmarks AS highest_marks, temp.result as result_status,

     IF(sub.subject_marks = 50 ,@obtained_marks := CEIL((temp.ctPMarks+temp.mainPMarks)*100/sub.subject_marks), @obtained_marks := (temp.ctPMarks+temp.mainPMarks)) AS fiftyToHundredMark,
				 CASE
		         WHEN  temp.result = 'P' THEN (
               CASE
               WHEN  @obtained_marks >= 80 THEN 'A+'
               WHEN  @obtained_marks >= 70 and @obtained_marks <= 79 THEN 'A'
               WHEN  @obtained_marks >= 60 and @obtained_marks <= 69 THEN 'A-'
               WHEN  @obtained_marks >= 50 and @obtained_marks <= 59 THEN 'B'
               WHEN  @obtained_marks >= 40 and @obtained_marks <= 49 THEN 'C'
               WHEN  @obtained_marks >= 33 and @obtained_marks <= 39 THEN 'D'
               ELSE   'F' END ) ELSE 'F' END AS grade,
				CASE
               WHEN temp.result = 'P' THEN (
               CASE
               WHEN  @obtained_marks >= 80 THEN '5.00'
               WHEN  @obtained_marks >= 70 and @obtained_marks <= 79 THEN '4.00'
               WHEN  @obtained_marks >= 60 and @obtained_marks <= 69 THEN '3.50'
               WHEN  @obtained_marks >= 50 and @obtained_marks <= 59 THEN '3.00'
               WHEN  @obtained_marks >= 40 and @obtained_marks <= 49 THEN '2.00'
               WHEN  @obtained_marks >= 33 and @obtained_marks <= 39 THEN '1.00'
               ELSE   '0.00' END ) ELSE '0.00' END AS CGPA
               FROM marks
               LEFT JOIN
               (
               SELECT marks.subject_id,
					MAX(CEIL( marks.total_marks*(sub.subject_marks - exams.ct_marks_percentage)/(sub.theory_marks+sub.mcq_marks +
				   sub.practical_marks)) + CEIL(marks.ct_marks*exams.ct_marks_percentage/if(sub.ct_marks=0,1,sub.ct_marks)))
					as hmarks FROM marks
					JOIN exams  on exams.id = marks.exam_id
					JOIN subjects AS sub on sub.id = marks.subject_id
               WHERE marks.exam_id =@exam_id and marks.class_id = @class_id
               GROUP BY marks.exam_id,marks.class_id,marks.subject_id
               )
               as max_score
               on marks.subject_id = max_score.subject_id

               LEFT JOIN students as std on std.std_code = marks.student_code
               LEFT JOIN exams  on exams.id = marks.exam_id
               LEFT JOIN subjects as sub on sub.id = marks.subject_id
               LEFT JOIN temp_marksheet_result as temp on temp.subId = marks.subject_id
               WHERE marks.exam_id =@exam_id and marks.class_id = @class_id and marks.section_id =@section_id and
					marks.student_code = @std_code and marks.year = @year
               order by marks.student_code,sub.subject_order ASC");

         DB::unprepared(DB::raw(" DROP TABLE IF EXISTS temp_marksheet_result"));

         return $result;
      }
   }


   // old

   public static function jrhalfSummeryResult_old($exam_id, $class_id, $section_id, $year)
   {
      DB::statement(DB::raw("set @rownum=0, @class_id='$class_id', @resultStatus='PASSED', @obtained_marks=0,@thPmarks=0,@mcqPmarks=0,@practPmarks=0,@ctPmarks=0, @section_id='$section_id', @exam_id='$exam_id', @year='$year'"));

      $createSummeryTempTables = DB::unprepared(
        DB::raw("
            CREATE TEMPORARY TABLE summery_result( exmId INT, examTitle VARCHAR(100), stdCode VARCHAR(50), stdName VARCHAR(100), stdClass VARCHAR(50),
            stdRoll VARCHAR(50), stdSection INT, subId INT, subName VARCHAR(200),  optionalsubId INT, theoryPMarks INT, mcqPMarks INT,practPmarks INT,ctPMarks INT, obtainedMark INT,
            fiftyToHundredMark INT,status VARCHAR(50), grade VARCHAR(5), gpa DECIMAL(10,2))"
        )
      );

      if ($createSummeryTempTables) {
         DB::insert("insert into summery_result select exams.id,exams.name, std.std_code, std.name,marks.class_id,enrolls.roll, marks.section_id,
               marks.subject_id, sub.name, enrolls.subject_id,

               case
                  when sub.subject_code = '611' OR sub.subject_code = '711' OR sub.subject_code = '811'
                  then @thPmarks := CEIL(marks.theory_marks*.5)
                  ELSE @thPmarks := IF(sub.subject_marks = 100 , CEIL(marks.theory_marks), CEIL(marks.theory_marks))
                  END AS theoryPMarks,
               case
                  when sub.subject_code = '611' OR sub.subject_code = '711' OR sub.subject_code = '811'
                  then @mcqPmarks := 0
                  ELSE @mcqPmarks :=  CEIL(marks.mcq_marks)
                  END AS mcqPercentMarks,
               case
                  when sub.subject_code = '611' OR sub.subject_code = '711' OR sub.subject_code = '811'
                  then @practPmarks := CEIL(marks.practical_marks)
                  ELSE @practPmarks :=  0
                  END AS practPercentMarks,
               case
                  when sub.subject_code = '611' OR sub.subject_code = '711' OR sub.subject_code = '811'
                  then @ctPmarks :=  CEIL(marks.ct_marks*.5)
                  ELSE @ctPmarks :=  IF(sub.subject_marks = 100 , CEIL(marks.ct_marks), CEIL(marks.ct_marks*.67))
                  END AS ctPercentMarks,

                 (@thPmarks +  @mcqPmarks + @ctPmarks + @practPmarks) AS obtained_nmarks,

	IF(sub.subject_marks = 50 ,@obtained_marks := CEIL((@thPmarks +  @mcqPmarks + @ctPmarks + @practPmarks)*100/50), @obtained_marks := (@thPmarks +  @mcqPmarks + @ctPmarks + @practPmarks)) AS fiftyToHundredMark,

                     @resultStatus :=   case when marks.subject_id != enrolls.subject_id then (
                        case when  marks.theory_marks>=sub.theory_pass_marks
                        then (case when marks.mcq_marks>=sub.mcq_pass_marks
								then (case when marks.practical_marks>=sub.practical_pass_marks
                        then (case when @obtained_marks >=sub.pass_marks then 'PASSED' else 'FAILED' end)
								else 'FAILED' END) else 'FAILED' END) else 'FAILED' END) else 'PASSED' end as result,
				CASE
		         WHEN  @resultStatus = 'PASSED' THEN (
            CASE
               WHEN  @obtained_marks >= 80 THEN 'A+'
               WHEN  @obtained_marks >= 70 and @obtained_marks <= 79 THEN 'A'
               WHEN  @obtained_marks >= 60 and @obtained_marks <= 69 THEN 'A-'
               WHEN  @obtained_marks >= 50 and @obtained_marks <= 59 THEN 'B'
               WHEN  @obtained_marks >= 40 and @obtained_marks <= 49 THEN 'C'
               WHEN  @obtained_marks >= 33 and @obtained_marks <= 39 THEN 'D'
               ELSE   'F' END ) ELSE 'F' END AS grade,
				CASE
               WHEN @resultStatus = 'PASSED' THEN (
               CASE
               WHEN  @obtained_marks >= 80 THEN '5.00'
               WHEN  @obtained_marks >= 70 and @obtained_marks <= 79 THEN '4.00'
               WHEN  @obtained_marks >= 60 and @obtained_marks <= 69 THEN '3.50'
               WHEN  @obtained_marks >= 50 and @obtained_marks <= 59 THEN '3.00'
               WHEN  @obtained_marks >= 40 and @obtained_marks <= 49 THEN '2.00'
               WHEN  @obtained_marks >= 33 and @obtained_marks <= 39 THEN '1.00'
               ELSE   '0.00' END ) ELSE '0.00' END AS gpa

								from  marks
                        inner JOIN students as std on std.std_code = marks.student_code
                        LEFT JOIN enrolls  on enrolls.student_id = std.id AND enrolls.class_id = @class_id  and enrolls.YEAR= @year
                        LEFT JOIN exams on exams.id = marks.exam_id
                        LEFT JOIN subjects as sub on sub.id = marks.subject_id
                        where marks.class_id =@class_id and marks.exam_id=@exam_id and marks.section_id=@section_id AND marks.YEAR =@year
                        order by marks.student_code,sub.subject_order ASC");

         $result = DB::select("SELECT rownum,exmId,examTitle,stdCode,stdName,stdClass,stdRoll,stdSection,subId,totalSubject,hasOptional,mainSubPoint,optionalSubPoint,totalMarks,failedSubject,CASE  WHEN T2.CNT = 0  THEN 'PASSED'  ELSE 'FAILED'  END result
               FROM (
               SELECT @rownum  := @rownum  + 1 AS rownum,exmId,examTitle,stdCode,stdName,stdClass,stdRoll,stdSection,subId,
               sum(case when subId != optionalsubId then 1 END) AS totalSubject,
               @cgpaPoint := sum(case when subId != optionalsubId then gpa END) AS mainSubPoint,
               count(case when subId = optionalsubId then 1 END) AS hasOptional,
               @optionalSubPoint := sum(case when subId = optionalsubId then (case when gpa>2 then gpa-2 END) ELSE 0 END) AS optionalSubPoint,
               sum(CASE WHEN status = 'FAILED' THEN 1 ELSE 0 END) AS failedSubject,
               SUM(obtainedMark) AS totalMarks, COUNT(CASE WHEN status = 'FAILED' THEN 1 END) CNT
                    FROM summery_result GROUP BY stdCode) T2");

         DB::unprepared(DB::raw("DROP TABLE IF EXISTS summery_result"));

         return $result;
      }
   }


   public static function jrhalfExamMarksheet_old($exam_id, $class_id, $section_id, $student_code, $year)
   {
      DB::statement(DB::raw("set @class_id='$class_id', @section_id='$section_id', @exam_id='$exam_id', @std_code='$student_code', @year='$year',
      @obtained_marks=0, @resultStatus='PASSED',@thPmarks=0,@mcqPmarks=0,@practPmarks=0,@ctPmarks=0"));

      $createMarksheetTempTables = DB::unprepared(
        DB::raw("
            CREATE TEMPORARY TABLE temp_marksheet_result(stdCode VARCHAR(50),stdRoll VARCHAR(50), subId INT, subName VARCHAR(200),  optionalsubId INT, theoryPMarks INT, mcqPMarks INT,practPmarks INT,ctPMarks INT, obtainedMark INT,
            fiftyToHundredMark INT, result VARCHAR(50))"
        )
      );

      if ($createMarksheetTempTables) {
         DB::insert("insert into temp_marksheet_result select marks.student_code, enrolls.roll ,marks.subject_id, sub.name, enrolls.subject_id,
  case
   when sub.subject_code = '611' OR sub.subject_code = '711' OR sub.subject_code = '811'
   then @thPmarks := CEIL(marks.theory_marks*.5)
   ELSE @thPmarks := IF(sub.subject_marks = 100 , CEIL(marks.theory_marks), CEIL(marks.theory_marks))
   END AS theoryPMarks,
case
   when sub.subject_code = '611' OR sub.subject_code = '711' OR sub.subject_code = '811'
   then @mcqPmarks := 0
   ELSE @mcqPmarks :=  CEIL(marks.mcq_marks)
   END AS mcqPercentMarks,
case
   when sub.subject_code = '611' OR sub.subject_code = '711' OR sub.subject_code = '811'
   then @practPmarks := CEIL(marks.practical_marks)
   ELSE @practPmarks :=  0
   END AS practPercentMarks,
case
   when sub.subject_code = '611' OR sub.subject_code = '711' OR sub.subject_code = '811'
   then @ctPmarks :=  CEIL(marks.ct_marks*.5)
   ELSE @ctPmarks :=  IF(sub.subject_marks = 100 , CEIL(marks.ct_marks), CEIL(marks.ct_marks*.67))
   END AS ctPercentMarks,

 (@thPmarks +  @mcqPmarks + @ctPmarks + @practPmarks) AS obtained_nmarks,

	IF(sub.subject_marks = 50 ,@obtained_marks := CEIL((@thPmarks +  @mcqPmarks + @ctPmarks + @practPmarks)*100/50), @obtained_marks := (@thPmarks +  @mcqPmarks + @ctPmarks + @practPmarks)) AS fiftyToHundredMark,

                     @resultStatus :=   case when marks.subject_id != enrolls.subject_id then (
                        case when  marks.theory_marks>=sub.theory_pass_marks
                        then (case when marks.mcq_marks>=sub.mcq_pass_marks
								then (case when marks.practical_marks>=sub.practical_pass_marks
                        then (case when @obtained_marks >=sub.pass_marks then 'PASSED' else 'FAILED' end)
								else 'FAILED' END) else 'FAILED' END) else 'FAILED' END) else 'PASSED' end as result
 FROM marks
 LEFT JOIN exams on exams.id = marks.exam_id
 LEFT JOIN subjects as sub on sub.id = marks.subject_id
 LEFT JOIN students as std on std.std_code = marks.student_code
 LEFT JOIN enrolls  on enrolls.student_id = std.id AND enrolls.class_id = @class_id  and enrolls.year= @year
 WHERE marks.exam_id =@exam_id and marks.class_id = @class_id AND marks.section_id =@section_id and marks.student_code = @std_code and marks.year = @year");

         $result = DB::select("SELECT   marks.exam_id,std.name,std.std_code,temp.stdRoll,exams.name AS exam_name,marks.subject_id,sub.name as subject,sub.subject_code, sub.subject_marks,marks.class_id,temp.optionalsubId AS optional_subject, marks.theory_marks, marks.mcq_marks, marks.practical_marks, marks.ct_marks,marks.total_marks,
 temp.ctPMarks,temp.theoryPMarks, temp.obtainedMark,max_score.hmarks AS highest_marks, temp.result as result_status, temp.fiftyToHundredMark,

				 CASE
		         WHEN  @resultStatus = 'PASSED' THEN (
            CASE
               WHEN  @obtained_marks >= 80 THEN 'A+'
               WHEN  @obtained_marks >= 70 and @obtained_marks <= 79 THEN 'A'
               WHEN  @obtained_marks >= 60 and @obtained_marks <= 69 THEN 'A-'
               WHEN  @obtained_marks >= 50 and @obtained_marks <= 59 THEN 'B'
               WHEN  @obtained_marks >= 40 and @obtained_marks <= 49 THEN 'C'
               WHEN  @obtained_marks >= 33 and @obtained_marks <= 39 THEN 'D'
               ELSE   'F' END ) ELSE 'F' END AS grade,
				CASE
               WHEN @resultStatus = 'PASSED' THEN (
               CASE
               WHEN  @obtained_marks >= 80 THEN '5.00'
               WHEN  @obtained_marks >= 70 and @obtained_marks <= 79 THEN '4.00'
               WHEN  @obtained_marks >= 60 and @obtained_marks <= 69 THEN '3.50'
               WHEN  @obtained_marks >= 50 and @obtained_marks <= 59 THEN '3.00'
               WHEN  @obtained_marks >= 40 and @obtained_marks <= 49 THEN '2.00'
               WHEN  @obtained_marks >= 33 and @obtained_marks <= 39 THEN '1.00'
               ELSE   '0.00' END ) ELSE '0.00' END AS CGPA
               FROM marks
               LEFT JOIN
               (
               SELECT marks.subject_id,

					MAX(CEIL(

					case
					   when sub.subject_code = '611' OR sub.subject_code = '711' OR sub.subject_code = '811'
					   then @thPmarks := CEIL(marks.theory_marks*.5)
					   ELSE @thPmarks := IF(sub.subject_marks = 100 , CEIL(marks.theory_marks), CEIL(marks.theory_marks))
					   END +
					case
					   when sub.subject_code = '611' OR sub.subject_code = '711' OR sub.subject_code = '811'
					   then @mcqPmarks := 0
					   ELSE @mcqPmarks :=  CEIL(marks.mcq_marks)
					   END +
					case
					   when sub.subject_code = '611' OR sub.subject_code = '711' OR sub.subject_code = '811'
					   then @practPmarks := CEIL(marks.practical_marks)
					   ELSE @practPmarks :=  0
					   END +
					case
					   when sub.subject_code = '611' OR sub.subject_code = '711' OR sub.subject_code = '811'
					   then @ctPmarks :=  CEIL(marks.ct_marks*.5)
					   ELSE @ctPmarks :=  IF(sub.subject_marks = 100 , CEIL(marks.ct_marks), CEIL(marks.ct_marks*.67))
					   END

					))
					as hmarks FROM marks
					JOIN exams  on exams.id = marks.exam_id
					JOIN subjects AS sub on sub.id = marks.subject_id
               WHERE marks.exam_id =@exam_id and marks.class_id = @class_id
               GROUP BY marks.exam_id,marks.class_id,marks.subject_id
               )
               as max_score
               on marks.subject_id = max_score.subject_id

               LEFT JOIN students as std on std.std_code = marks.student_code
               LEFT JOIN exams  on exams.id = marks.exam_id
               LEFT JOIN subjects as sub on sub.id = marks.subject_id
               LEFT JOIN temp_marksheet_result as temp on temp.subId = marks.subject_id
               WHERE marks.exam_id =@exam_id and marks.class_id = @class_id and marks.section_id =@section_id and
					marks.student_code = @std_code and marks.year = @year
               order by marks.student_code,sub.subject_order ASC");

         DB::unprepared(DB::raw(" DROP TABLE IF EXISTS temp_marksheet_result"));

         return $result;
      }
   }

}
