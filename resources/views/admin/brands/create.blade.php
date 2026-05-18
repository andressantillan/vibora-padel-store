@extends('layouts.app')

@section('title', 'Nueva marca')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Nueva marca</h1>
    <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">
        ← Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.brands._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar marca</button>
            </div>
        </form>
    </div>
</div>
@endsection

