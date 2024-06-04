<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $u = User::where('username','ren')->first();
        for ($i= 0; $i < 20 ; $i++) { 
            Contact::create([
                'first_name' => 'first '. $i,
                'last_name' => 'last '. $i,
                'email' => 'test'. $i . '@ren.com',
                'phone' => '123123' . $i,
                'user_id' => $u->id
            ]);
        }
    }
}
