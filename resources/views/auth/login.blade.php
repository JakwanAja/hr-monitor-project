<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — HR-DWMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-md p-8">

        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">HR-DWMS</h1>
            <p class="text-sm text-gray-500 mt-1">HR Daily Work Monitoring System</p>
        </div>

        {{-- Error --}}
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                @foreach ($errors->all() as $error)
                    <p class="text-sm text-red-600">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    placeholder="nama@email.com"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500
                           @error('email') border-red-400 @enderror"
                >
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    placeholder="••••••••"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center">
                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                    class="h-4 w-4 text-blue-600 border-gray-300 rounded"
                >
                <label for="remember" class="ml-2 text-sm text-gray-600">
                    Ingat saya
                </label>
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium
                       py-2.5 px-4 rounded-lg text-sm transition duration-150"
            >
                Masuk
            </button>

        </form>
    </div>

</body>
</html>