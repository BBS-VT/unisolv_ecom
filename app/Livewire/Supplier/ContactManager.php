<?php

namespace App\Livewire\Supplier;

use App\Models\Contact;
use App\Models\Supplier;
use Livewire\Component;

class ContactManager extends Component
{
    public Supplier $supplier;
    public $name, $position, $email, $phone, $mobile, $department, $is_primary = false, $is_active = true;
    public $editingContactId = null;
    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'mobile' => 'nullable|string|max:20',
        'position' => 'nullable|string|max:100',
        'department' => 'nullable|string|max:100',
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'Contact name is required.',
        'email.email' => 'Please enter a valid email address.',
    ];

    /**
     * Save or update contact
     */
    public function save()
    {
        $this->validate();

        try {
            if ($this->editingContactId) {
                // Update existing contact
                $contact = Contact::find($this->editingContactId);
                $contact->update([
                    'name' => $this->name,
                    'position' => $this->position,
                    'department' => $this->department,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'mobile' => $this->mobile,
                    'is_primary' => $this->is_primary,
                    'is_active' => $this->is_active,
                ]);

                $message = 'Contact updated successfully!';
            } else {
                // Create new contact
                $this->supplier->contacts()->create([
                    'company_id' => auth()->user()->company_id,
                    'name' => $this->name,
                    'position' => $this->position,
                    'department' => $this->department,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'mobile' => $this->mobile,
                    'is_primary' => $this->is_primary,
                    'is_active' => $this->is_active,
                ]);

                $message = 'Contact added successfully!';
            }

            // If this contact is marked as primary, unset others
            if ($this->is_primary) {
                $this->supplier->contacts()
                    ->where('id', '!=', $this->editingContactId ?? 0)
                    ->update(['is_primary' => false]);
            }

            $this->resetForm();
            $this->showModal = false;
            $this->dispatch('notify', message: $message, type: 'success');

        } catch (\Exception $e) {
            \Log::error('Error saving contact: ' . $e->getMessage());
            $this->dispatch('notify', message: 'Failed to save contact. Please try again.', type: 'error');
        }
    }

    /**
     * Edit a contact
     */
    public function editContact($contactId)
    {
        $contact = Contact::find($contactId);

        if ($contact && $contact->contactable_id === $this->supplier->id) {
            $this->editingContactId = $contact->id;
            $this->name = $contact->name;
            $this->position = $contact->position;
            $this->department = $contact->department;
            $this->email = $contact->email;
            $this->phone = $contact->phone;
            $this->mobile = $contact->mobile;
            $this->is_primary = $contact->is_primary;
            $this->is_active = $contact->is_active;
            $this->showModal = true;
        }
    }

    /**
     * Toggle contact active status
     */
    public function toggleStatus($contactId)
    {
        $contact = Contact::find($contactId);

        if ($contact && $contact->contactable_id === $this->supplier->id) {
            $contact->update(['is_active' => !$contact->is_active]);
            $this->dispatch('notify', message: 'Contact status updated!', type: 'success');
        }
    }

    /**
     * Make contact primary
     */
    public function makePrimary($contactId)
    {
        // Reset all others for this supplier
        $this->supplier->contacts()->update(['is_primary' => false]);

        $contact = Contact::find($contactId);
        if ($contact && $contact->contactable_id === $this->supplier->id) {
            $contact->update(['is_primary' => true]);
            $this->dispatch('notify', message: 'Primary contact updated!', type: 'success');
        }
    }

    /**
     * Delete a contact
     */
    public function deleteContact($contactId)
    {
        $contact = Contact::find($contactId);

        if ($contact && $contact->contactable_id === $this->supplier->id) {
            $contact->delete();
            $this->dispatch('notify', message: 'Contact removed successfully.', type: 'success');
        }
    }

    /**
     * Open modal for new contact
     */
    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Close modal
     */
    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    /**
     * Reset form fields
     */
    private function resetForm()
    {
        $this->editingContactId = null;
        $this->name = '';
        $this->position = '';
        $this->department = '';
        $this->email = '';
        $this->phone = '';
        $this->mobile = '';
        $this->is_primary = false;
        $this->is_active = true;
        $this->resetValidation();
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.supplier.contact-manager', [
            'contacts' => $this->supplier->contacts()->orderBy('is_primary', 'desc')->get()
        ]);
    }
}
