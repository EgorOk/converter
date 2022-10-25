<!-- CSS only -->
<!-- JavaScript Bundle with Popper -->
<div class="container">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous">
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <br>
    @auth
        <a href="{{ url('/photo') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Конвертер</a>
        <a href="{{ route('logout') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Выход</a>
    @else
        <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Вход</a>

        {{-- @if (Route::has('register'))
        <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</a>
    @endif --}}
    @endauth
    <br>

    <form action="/api/jpg" enctype="multipart/form-data" method="post">
        @csrf
        <div class="mb-3">
            <label for="formFile" class="form-label">Загрузите файл</label>
            <input class="form-control" type="file" id="formFile" name="formFile">
        </div>
        <select class="form-select" aria-label="Default select example" id="type" name="type">
            <option selected>Выберите формат преобразования</option>
            <option value="gif">gif</option>
            <option value="jpeg">jpeg</option>
            <option value="jpg">jpg</option>
            <option value="png">png</option>
            <option value="webp">webp</option>
        </select>
        <br>
        <button type="submit" class="btn btn-primary">Конвертировать</button>
    </form>
</div>
