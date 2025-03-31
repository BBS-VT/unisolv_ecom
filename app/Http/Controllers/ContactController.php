<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Customer $customer)
    {
        $contactPeople = $customer->contacts;
        return view('contacts.index', compact('contactPeople', 'customer'));
    }

    public function create(Customer $customer)
    {
        return view('contacts.create', compact('customer'));
    }

    public function store(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:255',
            'is_primary' => 'boolean',
        ]);


        if ($request->is_primary) {
            $customer->contacts()->update(['is_primary' => false]);
        }

        $customer->contacts()->create($request->all());

        return redirect()->route('customers.show', $customer);
    }

    public function edit(Customer $customer, Contact $contact)
    {
        return view('contacts.edit', compact('customer', 'contact'));
    }

    public function update(Request $request, Customer $customer, Contact $contact)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:255',
            'is_primary' => 'boolean',
        ]);

        if ($request->is_primary) {
            $customer->contacts()->update(['is_primary' => false]);
        }

        $contact->update($request->all());

        return redirect()->route('customers.show', $customer);
    }

    public function destroy(Customer $customer, Contact $contact)
    {
        $contact->delete();
        return redirect()->route('customers.show', $customer);
    }
}
