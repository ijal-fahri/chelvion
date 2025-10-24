<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | TOKO GADGET</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>

<body class="bg-black text-white min-h-screen flex items-center justify-center">

  <div class="w-full max-w-sm mx-auto p-8 rounded-2xl border border-gray-800 shadow-2xl bg-gradient-to-br from-gray-900 to-black space-y-8">

    <!-- Brand -->
    <div class="text-center">
      <h1 class="text-5xl font-extrabold tracking-widest text-white">TOKO GADGET</h1>
      <p class="text-gray-400 text-sm mt-2">Gadget & iPhone Marketplace</p>
    </div>

    @if (session('status'))
      <div class="p-3 bg-green-500 text-white rounded text-sm">
        {{ session('status') }}
      </div>
    @endif

    <!-- Form Login -->
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
      @csrf

      <div>
        <label for="email" class="block text-xs uppercase font-bold tracking-wide text-gray-400">Email</label>
        <input id="email" name="email" type="email" required autofocus autocomplete="username"
          class="mt-2 w-full p-3 bg-black border border-gray-600 rounded-lg focus:ring-2 focus:ring-white focus:border-white transition"
          placeholder="your@email.com" value="{{ old('email') }}">
        @error('email')
          <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="password" class="block text-xs uppercase font-bold tracking-wide text-gray-400">Password</label>
        <input id="password" name="password" type="password" required autocomplete="current-password"
          class="mt-2 w-full p-3 bg-black border border-gray-600 rounded-lg focus:ring-2 focus:ring-white focus:border-white transition"
          placeholder="••••••••">
        @error('password')
          <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div class="flex items-center justify-between">
        <label class="inline-flex items-center text-gray-400 text-sm">
          <input type="checkbox" name="remember" class="rounded border-gray-600 text-white bg-black focus:ring-white">
          <span class="ml-2">Remember me</span>
        </label>

        @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}" class="text-sm text-gray-400 hover:text-white">Forgot?</a>
        @endif
      </div>

      <button type="submit"
        class="w-full py-3 px-4 bg-white text-black font-extrabold rounded-lg hover:bg-gray-300 transition text-lg tracking-wide">
        LOGIN
      </button>
    </form>

    <!-- Register Link -->
    <p class="text-center text-sm text-gray-400">Belum punya akun?
      <a href="{{ route('register') }}" class="text-white font-medium hover:underline">Daftar Sekarang</a>
    </p>
  </div>

  <!-- Footer -->
  <div class="absolute bottom-4 text-center text-gray-600 text-xs w-full">
    &copy; {{ date('Y') }} TOKO GADGET. All rights reserved.
  </div>

</body>
</html>
