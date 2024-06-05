<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Console\Contracts\NewLineAware;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
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
    public function store(StoreContactRequest $request)
    {
        $data = $request->validated();
        $user = auth()->user();

        // dd($data);
        $contact = Contact::create([
            'first_name' => $request->first_name ,
            'last_name' => $request->last_name,
            'email' => $request->email ,
            'phone' => $request->phone ,
            'user_id' => $user->id,
        ]);

        // $contact = new Contact($data);
        // $contact->user_id = $user->id;
        // $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // TODO: code refactory
        $contact = Contact::where('id' , $id)->where('user_id', Auth::user()->id)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Not Found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new ContactResource($contact);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact, int $id)
    {
        $user = Auth::user();
        $contact = Contact::where('id' , $id)->where('user_id', Auth::user()->id)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Not Found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated();
        $contact->fill($data);
        $contact->save();

        return new ContactResource($contact);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $c = Contact::where('id' , $id)->where('user_id', Auth::user()->id)->first();
        if (!$c) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Not Found'
                    ]
                ]
            ])->setStatusCode(404));
        }
        $c->delete();
        return response()->json([
            'data' => 'true'
        ])->setStatusCode(200);
    }

    function search(Request $request) {
        $user = Auth::user();
        $page = $request->input('page',1);
        $size = $request->input('size',10);

        $contacts = Contact::where('user_id',$user->id)->where(function (Builder $builder) use ($request) {

            $builder->when($request->name ?? false , function (Builder $builder) use ($request) {
                return $builder->where('first_name' ,'LIKE' ,"%$request->name%")
                ->orWhere('last_name' , 'LIKE' , "%$request->name%");
            });
            
            $builder->when($request->email ?? false , function (Builder $builder) use ($request) {
                return $builder->where('email' ,'LIKE' ,"%$request->email%");
            });
            
            $builder->when($request->phone ?? false , function (Builder $builder) use ($request) {
                return $builder->where('phone' ,'LIKE' ,"%$request->phone%");
            });
            
        });
        
        $contacts = $contacts->paginate(perPage: $size, page: $page);
        return new ContactCollection($contacts);
    }
}
