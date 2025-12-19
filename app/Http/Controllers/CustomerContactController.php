<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerContactRequest;
use App\Http\Requests\UpdateCustomerContactRequest;
use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:contacts.view')->only(['index', 'show']);
        $this->middleware('permission:contacts.create')->only(['create', 'store']);
        $this->middleware('permission:contacts.edit')->only(['edit', 'update']);
        $this->middleware('permission:contacts.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Customer $customer): View
    {
        $contacts = $customer->contacts()
            ->orderBy('name')
            ->paginate(10);

        return view('customers.contacts.index', [
            'customer' => $customer,
            'contacts' => $contacts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Customer $customer): View
    {
        return view('customers.contacts.create', [
            'customer' => $customer,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerContactRequest $request, Customer $customer): RedirectResponse|JsonResponse
    {
        $contact = $customer->contacts()->create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('customers.show', $customer),
                'message' => 'Contact created.',
            ]);
        }

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Contact created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer, Contact $contact): View
    {
        return view('customers.contacts.show', [
            'customer' => $customer,
            'contact' => $contact,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer, Contact $contact): View
    {
        return view('customers.contacts.edit', [
            'customer' => $customer,
            'contact' => $contact,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerContactRequest $request, Customer $customer, Contact $contact): RedirectResponse|JsonResponse
    {
        $contact->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('customers.show', $customer),
                'message' => 'Contact updated.',
            ]);
        }

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Contact updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer, Contact $contact): RedirectResponse|JsonResponse
    {
        $contact->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'redirect' => route('customers.show', $customer),
                'message' => 'Contact deleted.',
            ]);
        }

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Contact deleted.');
    }
}
