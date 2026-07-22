<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - RSUD Landak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>body { font-family: 'Source Sans 3', sans-serif; }</style>
</head>
<body class="min-h-screen flex items-center justify-center" style="background: linear-gradient(135deg, #1E3A8A, #2563EB)">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md mx-4">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background: linear-gradient(135deg, #2563EB, #60A5FA)">
                <i class="fas fa-hospital text-white text-2xl"></i>
            </div>
            <h1 class="font-playfair text-2xl font-bold text-gray-800">RSUD Landak</h1>
            <p class="text-gray-500 text-sm mt-1">Panel Administrasi</p>
        </div>

        @if($errors->any())
        <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="/admin/login">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-user text-sm"></i></span>
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                        placeholder="Masukkan username">
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-lock text-sm"></i></span>
                    <input type="password" name="password" required
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                        placeholder="••••••••">
                </div>
            </div>
            <button type="submit" class="w-full py-3 rounded-lg text-white font-semibold transition-colors" style="background: #2563EB" onmouseover="this.style.background='#1E3A8A'" onmouseout="this.style.background='#2563EB'">
                <i class="fas fa-sign-in-alt mr-2"></i> Masuk
            </button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-6">
            <a href="{{ route('home') }}" class="hover:text-green-600"><i class="fas fa-arrow-left mr-1"></i> Kembali ke Website</a>
        </p>
    </div>
</body>
</html>
