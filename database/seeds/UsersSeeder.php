<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;

class UsersSeeder extends Seeder
{

    public function run()
    {
        DB::table('users')->delete();

        User::create([
            'name' => 'Admin User',
            'email' => 'admin',
            'password' => bcrypt('admin'),
            'access' => 'Admin',
            'paymentTypeOverride' => 'PostPay',
            'acs_id' => '',
            'credit' => 0,
            'bill_date' => ''
        ]);
    }
}
