<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username','ren')->first();
        Contact::create([
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@gmail.com',
            'phone' => '123123',
            'user_id' => $user->id
        ]);
    }
}
