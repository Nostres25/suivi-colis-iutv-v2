@extends('base')

@section('alert')
    @if (session('success'))
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="profileToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <strong>{{ session('success') }}</strong>
                    @if (session('mail_sent'))
                        <div>Un mail de confirmation vous a été envoyé.</div>
                    @endif
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endif


    @if ($errors->any())
        <div class="container mt-3">
            <div class="alert alert-danger mb-0">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if (session('login_alert'))
        <x-base.alert :alertMessage="session('login_alert')"></x-base.alert>
    @endif
@endsection

@section('header')
    <div class="container d-block">
        <h1 class="h1">Mon Profil</h1>
        <p class="mb-0 opacity-75">Modifier email, téléphone et campus</p>
    </div>
@endsection

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Identifiant (CAS)</label>
                    <input class="form-control" value="{{ $user->login }}" disabled>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prénom</label>
                        <input class="form-control" value="{{ $user->first_name }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom</label>
                        <input class="form-control" value="{{ $user->last_name }}" disabled>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Téléphone</label>
                    <input name="phone_number" type="text" class="form-control" value="{{ old('phone_number', $user->phone_number) }}">
                </div>

                <button class="btn btn-primary">Enregistrer</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('profileToast');
            if (!el) return;
            const toast = new bootstrap.Toast(el, { delay: 5000 }); // 5 sec
            toast.show();
        });
    </script>
@endsection
