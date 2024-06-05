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
    private function getContact(int $idUser, int $idContact) {
        $contact = Contact::where('user_id',$idUser)->where('id',$idContact)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [  
                    'message' => [
                        'Not Found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $contact;
    }
    
    private function getAddress(int $idContact, int $idAddress)  {
        $address = Address::where('contact_id',$idContact)->where('id',$idAddress)->first();
        if (!$address) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Not Found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $address;
    }
    
    function index(int $idContact) {
        $contact = $this->getContact(Auth::user()->id,$idContact);

        $addresses = Address::where('contact_id',$contact->id)->get();
        return AddressResource::collection($addresses);
    }
    public function store(StoreAddressRequest $request, int $idContact)
    {
        $user = Auth::user();
        $contact = $this->getContact($user->id,$idContact);
        $request->validated();   
        
        $address = Address::create([
            'street' => $request->street,
            'city' => $request->city,
            'province' => $request->province,
            'country' => $request->country,
            'postal_code' =>$request->postal_code,
            'contact_id' => $contact->id
        ]);

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */

    // urutan parameter harus sesuai dengan urutan variable pada path api
    public function show(int $idContact, int $idAddress)
    {
        $user = Auth::user();
        $contact = $this->getContact($user->id,$idContact);
        $address = $this->getAddress($contact->id,$idAddress);

        return new AddressResource($address);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAddressRequest $request, int $idContact, int $idAddress)
    {
        $user = Auth::user();
        $contact = $this->getContact($user->id,$idContact);
        $address = $this->getAddress($contact->id,$idAddress);

        $address->fill($request->validated());
        $address->save();

        return new AddressResource($address);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $idContact, int $idAddress)
    {
        $user = Auth::user();
        $contact = $this->getContact($user->id,$idContact);
        $address = $this->getAddress($contact->id,$idAddress);

        $address->delete();
        return response()->json([
            'data' => 'true'
        ])->setStatusCode(200);
    }
}
