<?php

namespace Tests\Feature;

use App\Models\Address;
use Tests\TestCase;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ContactSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressTest extends TestCase
{
    function testCreateSuccess() {
        $this->seed([UserSeeder::class,ContactSeeder::class]);
        $contact = Contact::first();

        $this->post("/api/contacts/{$contact->id}/addresses",[
            'street' => 'test',
            'city' => 'test',
            'province' => 'test',
            'country' => 'test',
            'postal_code' => '123123123'
        ],[
            'Authorization' => 'ren'
        ])
        ->assertStatus(201)
        ->assertJson([
            'data' => [
                'street' => 'test',
                'city' => 'test',
                'province' => 'test',
                'country' => 'test',
                'postal_code' => '123123123'
            ]
        ]);
    }

    function testCreateFailed() {
        $this->seed([UserSeeder::class,ContactSeeder::class]);
        $contact = Contact::first();

        $this->post("/api/contacts/{$contact->id}/addresses",[
            'street' => 'test',
            'city' => 'test',
            'province' => 'test',
            'postal_code' => '123123123'
        ],[
            'Authorization' => 'ren'
        ])
        ->assertStatus(400)
        ->assertJson([
            'errors' => [
                'country' => [
                    'The country field is required.'
                ]
            ]
        ]);
    }
    
    function testCreateContactNotFound() {
        $this->seed([UserSeeder::class,ContactSeeder::class]);
        $contact = Contact::first();

        $this->post("/api/contacts/0/addresses",[
            'street' => 'test',
            'city' => 'test',
            'country' => 'test',
            'province' => 'test',
            'postal_code' => '123123123'
        ],[
            'Authorization' => 'ren'
        ])
        ->assertStatus(404)
        ->assertJson([
            'errors' => [
                'message' => [
                    'Not Found'
                ]
            ]
        ]);
    }

    function testGetAddressSuccess() {
        $this->seed([UserSeeder::class,ContactSeeder::class,AddressSeeder::class]);
        $address = Address::first();

        $this->get("/api/contacts/{$address->contact_id}/addresses/{$address->id}",[
            'Authorization' => 'ren'
        ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'street' => 'test',
                'city' => 'test',
                'province' => 'test',
                'country' => 'test' ,
                'postal_code' => '123123445',
            ]
        ]);
    }

    function testGetAddressNotFound() {
        $this->seed([UserSeeder::class,ContactSeeder::class,AddressSeeder::class]);

        $this->get("/api/contacts/0/addresses/0",[
            'Authorization' => 'ren'
        ])
        ->assertStatus(404)
        ->assertJson([
            'errors' => [
                'message' => ['Not Found']
            ]
        ]);
    }

    function testUpdateSuccess() {
        $this->seed([UserSeeder::class,ContactSeeder::class,AddressSeeder::class]);
        $address = Address::first();

        $this->put("/api/contacts/{$address->contact_id}/addresses/{$address->id}",[
            'street' => '',
            'city' => '',
            'province' => 'test',
            'country' => 'test' ,
            'postal_code' => '123123445',
        ],
        [
            'Authorization' => 'ren'
        ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'street' => '',
                'city' => '',
                'province' => 'test',
                'country' => 'test' ,
                'postal_code' => '123123445',
            ]
        ]);
    }

    function testUpdateFailed() {
        $this->seed([UserSeeder::class,ContactSeeder::class,AddressSeeder::class]);
        $address = Address::first();

        $this->put("/api/contacts/{$address->contact_id}/addresses/{$address->id}",[
            'street' => '',
            'city' => '',
            'province' => 'test',
            'country' => '' ,
            'postal_code' => '123123445',
        ],
        [
            'Authorization' => 'ren'
        ])
        ->assertStatus(400)
        ->assertJson([
            'errors' => [
                'country' => ["The country field is required."]
            ]
        ]);
    }

    function testUpdateNotFound() {
        $this->seed([UserSeeder::class,ContactSeeder::class,AddressSeeder::class]);

        $this->put("/api/contacts/0/addresses/0",[
            'street' => '',
            'city' => '',
            'province' => 'test',
            'country' => 'qqweqwe' ,
            'postal_code' => '123123445',
        ],
        [
            'Authorization' => 'ren'
        ])
        ->assertStatus(404)
        ->assertJson([
            'errors' => [
                'message' => ["Not Found"]
            ]
        ]);
    }
}
