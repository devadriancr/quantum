<x-authentication-layout>
    <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold mb-6">¡Bienvenido de nuevo!</h1>
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif   
    <!-- Form -->
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <x-label for="email" value="Correo o Nickname" />
                <x-input id="email" type="text" name="email" :value="old('email')" required autofocus
                         placeholder="Tu correo o nickname" />
            </div>
            <div>
                <x-label for="password" value="Contraseña" />
                <x-input id="password" type="password" name="password" required autocomplete="current-password"
                         placeholder="Tu contraseña" />
            </div>
        </div>
        <div class="mt-6">
            {{-- Forgot Password (oculto temporalmente)
            @if (Route::has('password.request'))
                <div class="mr-1">
                    <a class="text-sm underline hover:no-underline" href="{{ route('password.request') }}">
                        {{ __('Forgot Password?') }}
                    </a>
                </div>
            @endif
            --}}
            <x-button class="w-full justify-center">
                Iniciar sesión
            </x-button>
        </div>
    </form>
    <x-validation-errors class="mt-4" />
    {{-- Registro (oculto temporalmente)
    <!-- Footer -->
    <div class="pt-5 mt-6 border-t border-gray-100 dark:border-gray-700/60">
        <div class="text-sm">
            {{ __('Don\'t you have an account?') }} <a class="font-medium text-violet-500 hover:text-violet-600 dark:hover:text-violet-400" href="{{ route('register') }}">{{ __('Sign Up') }}</a>
        </div>
    </div>
    --}}
</x-authentication-layout>
