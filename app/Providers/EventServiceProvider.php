<?php

namespace App\Providers;

use App\Models\StdParent;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Hash;

class EventServiceProvider extends ServiceProvider
{
   /**
    * The event listener mappings for the application.
    *
    * @var array
    */
   protected $listen = [
     Registered::class => [
       SendEmailVerificationNotification::class,
     ],
   ];

   /**
    * Register any events for your application.
    *
    * @return void
    */
   public function boot()
   {
      parent::boot();

//      StdParent::creating(function ($parent) {
//         if ($parent->password == "" || empty($parent->password)) {
//            $parent->password = Hash::make('1234');
//         }
//      });
   }
}
