<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contact = Contact::first();
        for ($i=0; $i < 10; $i++) { 
            Address::create([
                'street' => "test $i",
                'city' => "test $i",
                'province' => "test $i",
                'country' => "test $i" ,
                'postal_code' => "00$i",
                'contact_id' => $contact->id
            ]);
        }
    }
}
