@use(Database\Seeders\Status)

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Suivi IUT Villetaneuse') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    {{-- Bootstrap --}}
    <link href="{{asset('bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <script type="text/javascript" src="{{asset('bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    {{-- Custom CSS --}}
    <link href="{{asset('css/style.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/base/header.css')}}" rel="stylesheet" type="text/css">

    @yield('head')
</head>
<body>
<header class="mb-5">
    <div id="navbar-container">
        <x-base.nav></x-base.nav>
        <x-base.alert></x-base.alert>
        @yield('alert')
    </div>

    {{--Bannière bleue--}}
<div class="page-header">
    <div class="container d-flex flex-row-reverse align-items-center justify-content-between">
        <img src="{{ asset('217.png') }}" alt="Logo Sorbonne" style="height: 70px; width: auto; margin-left: 20px;">
        <div>
            @yield('header')
        </div>
    </div>
</div>
</header>
<main>
    @yield('content')
    <div id="modal-container"></div>
</main>
<footer>
    @yield('footer')
    <div class="bg-gray-50 px-8 py-5 text-center border-t">
        <p class="text-sm font-semibold text-gray-700">BUT2 Informatique - IUT de Villetaneuse</p>
        <p class="text-xs text-gray-500 mt-1">Projet SAE - Suivi de Colis • 2024-2025</p>
    </div>
{{--    <div class="min-h-screen bg-gray-50 py-8">--}}
{{--        <div class="mx-auto max-w-5xl">--}}

{{--            <div class="bg-white shadow-lg rounded-xl overflow-hidden">--}}

{{--                <Ancien contrenu footer>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</footer>
</body>
<script>
    {{--let modalToOpenId = '{{$modalToOpen}}';--}}
    {{--if (modalToOpenId) {--}}
    {{--    console.debug(modalToOpenId);--}}
    {{--    let modalToOpen = new bootstrap.Modal(document.getElementById(modalToOpenId));--}}
    {{--    modalToOpen.show();--}}
    {{--}--}}

    // --------------------------------------------------------------------------------
    // CONFIGURATION : Mapping des Enums PHP vers JS avec synchronisation automatique
    // --------------------------------------------------------------------------------
    const STATUS = {
        @php
            foreach (STATUS::cases() as $status) { echo $status->name.': "'.$status->value.'",'; }
        @endphp
    };
</script>
<script src="{{asset('js/global_functions.js')}}"></script>
<script src="{{asset('js/base.js')}}"></script>
@yield('javascript')
</html>
