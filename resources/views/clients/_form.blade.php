@php $client = $client ?? null; @endphp
<form method="POST" action="{{ $client ? route('clients.update', $client) : route('clients.store') }}">
    @csrf
    @if ($client) @method('PUT') @endif
    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Nombre</label><input class="form-control" name="name" value="{{ old('name', $client->name ?? '') }}" required></div>
                <div class="col-md-6"><label class="form-label">Razón social</label><input class="form-control" name="legal_name" value="{{ old('legal_name', $client->legal_name ?? '') }}"></div>
                <div class="col-md-4"><label class="form-label">Contacto</label><input class="form-control" name="contact_name" value="{{ old('contact_name', $client->contact_name ?? '') }}"></div>
                <div class="col-md-4"><label class="form-label">Correo</label><input class="form-control" type="email" name="email" value="{{ old('email', $client->email ?? '') }}"></div>
                <div class="col-md-4"><label class="form-label">Teléfono</label><input class="form-control" name="phone" value="{{ old('phone', $client->phone ?? '') }}"></div>
            </div>
        </div>
        <div class="card-footer"><button class="btn btn-primary" type="submit">Guardar</button></div>
    </div>
</form>
