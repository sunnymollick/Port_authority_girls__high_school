<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmissionApplicationsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('admission_applications', function (Blueprint $table) {
         $table->increments('id');
         $table->string('applicant_id')->nullable();
         $table->string('applicant_form_no')->nullable();
         $table->string('applicant_name_en')->nullable();
         $table->string('applicant_name_bn')->nullable();
         $table->string('dob')->nullable();
         $table->string('mobile')->nullable();
         $table->string('email')->nullable();
         $table->string('blood_group')->nullable();
         $table->string('father_name_en')->nullable();
         $table->string('father_name_bn')->nullable();
         $table->string('mother_name_en')->nullable();
         $table->string('mother_name_bn')->nullable();
         $table->string('father_qualification')->nullable();
         $table->string('mother_qualification')->nullable();
         $table->string('father_occupation')->nullable();
         $table->string('yearly_income')->nullable();
         $table->string('mother_occupation')->nullable();
         $table->string('father_occupation_post_name')->nullable();
         $table->string('father_occupation_org_name')->nullable();
         $table->string('father_occupation_business_type')->nullable();
         $table->string('alternet_gurdian_name')->nullable();
         $table->string('alternet_gurdian_phone')->nullable();
         $table->string('alternet_gurdian_address')->nullable();
         $table->string('present_village')->nullable();
         $table->string('present_post_office')->nullable();
         $table->string('present_thana')->nullable();
         $table->string('present_district')->nullable();
         $table->string('parmanent_village')->nullable();
         $table->string('parmanent_post_office')->nullable();
         $table->string('parmanent_thana')->nullable();
         $table->string('parmanent_district')->nullable();
         $table->string('nationality')->nullable();
         $table->string('religion')->nullable();
         $table->string('children_in_school')->nullable();
         $table->string('children_name')->nullable();
         $table->string('children_class')->nullable();
         $table->string('children_section')->nullable();
         $table->string('admitted_class')->nullable();
         $table->string('admitted_section')->nullable();
         $table->string('old_school_name')->nullable();
         $table->string('old_class')->nullable();
         $table->tinyInteger('status')->nullable()->default(2);
         $table->string('applied_year')->nullable();
         $table->string('file_path')->nullable();
         $table->string('aggrement')->nullable();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('admission_applications');
   }
}
