<?php

namespace App\Imports;

use App\Models\StdParent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;


class ParentsImport implements ToModel, WithHeadingRow, WithValidation
{
   /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
   use Importable;

   public function model(array $row)
   {
      if (!empty($row['name'])) {
         return new StdParent([
           'name' => $row['name'],
           'email' => $row['email'],
           'gender' => $row['gender'],
           'phone' => $row['phone'],
           'blood_group' => $row['blood_group'],
           'address' => $row['address'],
           'profession' => $row['profession'],
           'password' => Hash::make(1234),
         ]);
      }
   }

   public function rules(): array
   {

      return [
        'name' => ['required'],
        'email' => ['required', 'unique:parents,email'],
        'phone' => ['required', 'unique:parents,phone'],
      ];

   }
}


class test implements ToCollection, WithHeadingRow
{
   public function collection(Collection $rows)
   {

      foreach ($rows as $row) {
         if ($row->filter()->isNotEmpty()) {

            Validator::make($rows->toArray(), [
              '*.0' => 'required',
            ])->validate();

            StdParent::create([
              'name' => $row['name'],
              'email' => $row['email'],
              'gender' => $row['gender'],
              'phone' => $row['phone'],
              'blood_group' => $row['blood_group'],
              'address' => $row['address'],
              'profession' => $row['profession'],
              'password' => Hash::make(1234),
            ]);
         }
      }
   }

}