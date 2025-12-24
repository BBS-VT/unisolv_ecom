<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Contacts ({{ $contacts->count() }})</h5>
        <button wire:click="openModal" class="btn btn-sm btn-primary">
            <i class="mdi mdi-plus"></i> Add Contact
        </button>
    </div>

    @if($contacts->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light small text-uppercase">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th class="text-center">Primary</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($contacts as $contact)
                    <tr wire:key="contact-{{ $contact->id }}">
                        <td>
                            <div class="fw-bold">{{ $contact->name }}</div>
                            @if($contact->position)
                                <small class="text-muted">{{ $contact->position }}</small>
                            @endif
                            @if($contact->department)
                                <br><small class="text-muted">{{ $contact->department }}</small>
                            @endif
                        </td>
                        <td>
                            @if($contact->email)
                                <a href="mailto:{{ $contact->email }}" class="text-decoration-none">
                                    {{ $contact->email }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($contact->mobile)
                                <a href="tel:{{ $contact->mobile }}" class="text-decoration-none">
                                    <i class="mdi mdi-cellphone"></i> {{ $contact->mobile }}
                                </a>
                            @elseif($contact->phone)
                                <a href="tel:{{ $contact->phone }}" class="text-decoration-none">
                                    <i class="mdi mdi-phone"></i> {{ $contact->phone }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button wire:click="makePrimary({{ $contact->id }})"
                                    class="btn btn-sm p-0 border-0 {{ $contact->is_primary ? 'text-warning' : 'text-muted' }}"
                                    title="{{ $contact->is_primary ? 'Primary Contact' : 'Make Primary' }}">
                                <i class="mdi mdi-star{{ $contact->is_primary ? '' : '-outline' }} fs-4"></i>
                            </button>
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input"
                                       type="checkbox"
                                       wire:click="toggleStatus({{ $contact->id }})"
                                       {{ $contact->is_active ? 'checked' : '' }}
                                       title="{{ $contact->is_active ? 'Active' : 'Inactive' }}">
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <button wire:click="editContact({{ $contact->id }})"
                                        class="btn btn-outline-secondary"
                                        title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                </button>
                                <button wire:click="deleteContact({{ $contact->id }})"
                                        wire:confirm="Are you sure you want to delete {{ $contact->name }}?"
                                        class="btn btn-outline-danger"
                                        title="Delete">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-5">
            <i class="mdi mdi-account-off mdi-48px text-muted mb-3 d-block"></i>
            <p class="text-muted mb-3">No contacts added yet</p>
            <button wire:click="openModal" class="btn btn-sm btn-primary">
                <i class="mdi mdi-plus me-1"></i> Add First Contact
            </button>
        </div>
    @endif

    <!-- Contact Modal -->
    @if($showModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5)" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $editingContactId ? 'Edit Contact' : 'New Contact' }}
                        </h5>
                        <button wire:click="closeModal" type="button" class="btn-close"></button>
                    </div>
                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       wire:model="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="e.g., John Smith">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Position</label>
                                    <input type="text"
                                           wire:model="position"
                                           class="form-control @error('position') is-invalid @enderror"
                                           placeholder="e.g., Sales Manager">
                                    @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Department</label>
                                    <input type="text"
                                           wire:model="department"
                                           class="form-control @error('department') is-invalid @enderror"
                                           placeholder="e.g., Sales">
                                    @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email"
                                       wire:model="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="email@example.com">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text"
                                           wire:model="phone"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           placeholder="0123456789">
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mobile</label>
                                    <input type="text"
                                           wire:model="mobile"
                                           class="form-control @error('mobile') is-invalid @enderror"
                                           placeholder="0821234567">
                                    @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               wire:model="is_primary"
                                               class="form-check-input"
                                               id="isPrimary">
                                        <label class="form-check-label" for="isPrimary">
                                            Set as primary contact
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               wire:model="is_active"
                                               class="form-check-input"
                                               id="isActive">
                                        <label class="form-check-label" for="isActive">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button"
                                    wire:click="closeModal"
                                    class="btn btn-secondary">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove wire:target="save">
                                    <i class="mdi mdi-content-save me-1"></i>
                                    {{ $editingContactId ? 'Update Contact' : 'Save Contact' }}
                                </span>
                                <span wire:loading wire:target="save">
                                    <i class="mdi mdi-loading mdi-spin me-1"></i>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>


