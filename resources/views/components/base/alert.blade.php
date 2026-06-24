<div class="alert-container">

    {{-- Messages flash d'erreurs du projet --}}
    @if (session()->exists('error'))
        <div class="alert alert-danger mb-0">
            {{session('error')}}
        </div>
    @endif

    {{-- Messages flash de succès --}}
    @if (session()->exists('success'))
        <div class="alert alert-success mb-0">
            {{session('success')}}
        </div>
    @endif

    {{-- Erreurs par défaut de Laravel comme avec les Validator  --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-0">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>



