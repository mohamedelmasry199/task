<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Mail\BirthDayWish;
use Mail;

class AutoBirthDayWish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:birthdaywith';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::whereMonth('birthdate', date('m'))
                    ->whereDay('birthdate', date('d'))
                    ->get();

        if ($users->count() > 0) {
            foreach ($users as $user) {
                Mail::to($user)->send(new BirthDayWish($user));
            }
        }

        return 0;
    }
}
