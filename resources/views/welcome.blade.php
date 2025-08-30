<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome — Cyber Travel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
  <main class="max-w-2xl mx-auto px-4 py-20 text-center">
    <h1 class="text-4xl font-extrabold">Cyber Travel</h1>
    <p class="mt-3 text-slate-600"> Personalized travel plans for every client</p>

    <div class="mt-8 flex items-center justify-center gap-4">
      @if (Route::has('login'))
        <a href="{{ route('login') }}" class="px-5 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700">Login</a>
      @endif
      @if (Route::has('register'))
        <a href="{{ route('register') }}" class="px-5 py-3 rounded-xl bg-white text-emerald-700 font-semibold shadow hover:shadow-md">Register</a>
      @endif
    </div>
  </main>
</body>
</html>
