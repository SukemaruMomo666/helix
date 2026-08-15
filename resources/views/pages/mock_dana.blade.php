<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logohelix.png') }}" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DANA Sandbox Simulator</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-xl shadow-lg max-w-md w-full text-center">
        <img src="https://a.m.dana.id/danaweb/web/dana-logo.png" alt="DANA Logo" class="h-12 mx-auto mb-6">
        <h2 class="text-2xl font-bold mb-2">Sandbox Simulator</h2>
        <p class="text-gray-600 mb-6">Ini adalah halaman simulasi pembayaran karena API Sandbox DANA sedang bermasalah.</p>
        
        <div class="bg-blue-50 p-4 rounded-lg mb-6 text-left">
            <p class="text-sm text-gray-500">Order ID:</p>
            <p class="font-mono font-bold">{{ request('orderId') }}</p>
            <p class="text-sm text-gray-500 mt-2">Jumlah Bayar:</p>
            <p class="font-bold text-xl text-blue-600">Rp {{ number_format(request('amount'), 0, ',', '.') }}</p>
        </div>

        <form action="{{ url('/dana/sandbox/pay') }}" method="POST">
            @csrf
            <input type="hidden" name="orderId" value="{{ request('orderId') }}">
            <button type="submit" class="w-full bg-[#118EEA] hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                Simulasikan Pembayaran Berhasil
            </button>
        </form>
        
        <a href="{{ url('/pesanan') }}" class="block mt-4 text-sm text-gray-500 hover:text-gray-700">
            Batal & Kembali ke Website
        </a>
    </div>
</body>
</html>

