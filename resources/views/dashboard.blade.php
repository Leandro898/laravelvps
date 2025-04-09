<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
            <!-- PRUEBA PARA VER -->

                        @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (Auth::user()->mp_access_token)
                <p>✅ Cuenta de Mercado Pago vinculada</p>
                <form method="POST" action="{{ route('mercadopago.disconnect') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">Desvincular mi cuenta de Mercado Pago</button>
                </form>
            @else
                <a href="{{ route('mercadopago.connect') }}" class="btn btn-primary">
                    Vincular mi cuenta de Mercado Pago
                </a>
            @endif
            <!-- PRUEBA PARA VER -->
            
        </div>
    </div>
</x-app-layout>
