@extends('layouts.festival')
@section('title', 'Validasi Tiket · AFTERGLOW')
@section('content')
<main class="mx-auto max-w-3xl px-6 py-12"><a class="text-sm text-[#77756f]" href="{{ route('admin.dashboard') }}">← Kembali ke Dasbor</a>
    <div class="mt-10 rounded-3xl bg-[#20211e] p-8 text-white">
        <p class="text-xs font-bold uppercase tracking-[.2em] text-[#dcff69]">Hari acara · {{ today()->locale('id')->translatedFormat('j F Y') }} WIB</p>
        <h1 class="display mt-2 text-4xl font-semibold">Check-in Tiket</h1>
        <p class="mt-3 text-sm text-white/65">Pindai QR dengan aplikasi kamera, lalu masukkan UUID tiket. Petugas juga dapat mengetik kode secara manual. Tiket hanya berlaku pada hari yang tertera.</p>
        @if(session('success'))<p role="status" class="mt-6 rounded-xl bg-[#eaffbb] p-4 text-sm text-black">{{ session('success') }}</p>@endif
        @if($errors->any())<p role="alert" class="mt-6 rounded-xl bg-red-100 p-4 text-sm text-red-800">{{ $errors->first() }}</p>@endif
        <form method="POST" action="{{ route('admin.checkin.store') }}" class="mt-8 grid gap-4">@csrf
            <label class="grid gap-2 text-xs font-bold text-white/70" for="ticket_code">UUID Tiket
                <input id="ticket_code" autofocus required name="ticket_code" value="{{ old('ticket_code') }}" class="rounded-xl border-white/10 bg-white/10 px-4 py-4 font-mono text-base text-white" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
            </label>
            <button class="rounded-full bg-[#dcff69] px-5 py-4 font-bold text-black">Validasi & Check-in</button>
        </form>
    </div>
</main>
@endsection
