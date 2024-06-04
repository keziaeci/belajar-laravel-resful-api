<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAddressRequest $request, int $idContact)
    {
        $user = Auth::user();
        $contact = Contact::where('user_id',$user->id)->where('id',$idContact)->first();
        $request->validated();   
        
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Not Found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $address = Address::create([
            'street' => $request->street,
            'city' => $request->city,
            'province' => $request->province,
            'country' => $request->country,
            'postal_code' =>$request->postal_code,
            'contact_id' => $idContact
        ]);

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAddressRequest $request, Address $address)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        //
    }
}
