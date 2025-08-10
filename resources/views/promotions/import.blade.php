@extends('layouts.master')

@section('title', 'Import Promotions')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Import Promotions</h1>
            <p class="mb-0 text-muted">Import promotions from Excel/CSV files exported from your POS system</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('promotions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Promotions
            </a>
            <a href="{{ route('promotions.download-template') }}" class="btn btn-outline-primary">
                <i class="fas fa-download me-1"></i> Download Template
            </a>
        </div>
    </div>
</div>
@endsection
