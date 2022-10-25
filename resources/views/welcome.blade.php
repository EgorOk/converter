@auth
    <a href="{{ url('/photo') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Конвертер</a>
    <a href="{{ route('logout') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Выход</a>
@else
    <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Вход</a>

    {{-- @if (Route::has('register'))
        <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</a>
    @endif --}}
@endauth
