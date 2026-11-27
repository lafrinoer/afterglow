@extends('layouts.festival')
@section('title', 'Dasbor Saya · AFTERGLOW')
@section('content')
<div class="min-h-screen">
    <header class="border-b border-black/5 bg-white"><nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5 lg:px-10">
        <a href="{{ route('home') }}" class="display text-xl font-bold">afterglow<span class="text-[#ff5c43]">.</span></a>
        <div class="flex items-center gap-4 text-sm"><span class="hidden text-[#77756f] sm:inline">Halo, {{ auth()->user()->name }}</span><a href="{{ route('home') }}" class="text-[#77756f] hover:text-black">Info Festival</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="font-semibold">Keluar</button></form></div>
    </nav></header>
    <main class="mx-auto max-w-7xl px-6 py-10 lg:px-10 lg:py-14">
        @if(session('success'))<div role="status" class="mb-6 rounded-2xl bg-[#eaffbb] p-4 text-sm">{{ session('success') }}</div>@endif
        @if($errors->any())<div role="alert" class="mb-6 rounded-2xl bg-red-50 p-4 text-sm text-red-700">{{ $errors->first() }}</div>@endif
        <div class="mb-10 flex flex-col justify-between gap-5 sm:flex-row sm:items-end"><div><p class="mb-2 text-xs font-bold uppercase tracking-[.2em] text-[#e65b43]">AFTERGLOW · 29–31 Desember 2026</p><h1 class="display text-4xl font-semibold sm:text-5xl">Tiket untuk momen istimewa.</h1><p class="mt-3 text-sm text-[#77756f]">{{ config('festival.event.venue') }}, {{ config('festival.event.city') }}</p></div><a href="{{ route('home') }}#tickets" class="rounded-full bg-[#20211e] px-5 py-3 text-center text-sm font-bold text-white">Lihat pilihan tiket ↗</a></div>
        <div class="grid gap-8 lg:grid-cols-[.85fr_1.15fr]">
            <section class="h-fit rounded-3xl bg-[#20211e] p-6 text-white sm:p-8">
                <p class="text-xs font-bold uppercase tracking-[.18em] text-[#dcff69]">Pemesanan tiket</p><h2 class="display mt-3 text-3xl font-semibold">Pilih harimu.</h2><p class="mt-2 text-sm leading-6 text-white/60">Pesanan ditahan selama 24 jam. Selesaikan pembayaran dan unggah bukti sebelum batas waktu.</p>
                <form method="POST" action="{{ route('orders.store') }}" class="mt-7 grid gap-4">@csrf
                    <label class="grid gap-2 text-xs font-bold text-white/75">Hari & zona tiket<select name="ticket_tier_id" required class="rounded-xl border-white/10 bg-white/10 px-4 py-3 text-sm text-white">@foreach($tiers as $tier)<option class="text-black" value="{{ $tier->id }}">{{ $tier->festivalDay->title }} · {{ $tier->zone_name }} · Rp{{ number_format($tier->price, 0, ',', '.') }} ({{ max(0, $tier->quota - $tier->sold_count) }} tersedia)</option>@endforeach</select></label>
                    <label class="grid gap-2 text-xs font-bold text-white/75">Jumlah<select name="quantity" required class="rounded-xl border-white/10 bg-white/10 px-4 py-3 text-sm text-white">@for($quantity = 1; $quantity <= 6; $quantity++)<option class="text-black" value="{{ $quantity }}">{{ $quantity }} tiket</option>@endfor</select></label>
                    <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-white/5 p-4 text-sm"><input type="checkbox" name="day_pass" value="1" class="mt-1 rounded border-white/30 text-[#dcff69] focus:ring-[#dcff69]"><span><strong>Jadikan Pass 3 Hari</strong><span class="mt-1 block text-xs font-normal leading-5 text-white/60">Zona yang sama untuk 29, 30, dan 31 Desember. Tarif merupakan jumlah harga tiket zona tersebut untuk ketiga hari.</span></span></label>
                    <button class="mt-1 rounded-full bg-[#dcff69] px-5 py-3.5 text-sm font-bold text-[#20211e]">Buat Pesanan ↗</button>
                </form>
                <div class="mt-7 border-t border-white/10 pt-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-white/55">Pembayaran resmi · {{ config('festival.payment.provider') }}</p><p class="mt-3 text-sm text-white/70">Kirim pembayaran ke nomor:</p>
                    <div class="mt-2 flex items-center justify-between gap-3 rounded-xl bg-white/10 p-3"><div><p class="font-mono text-lg font-bold tracking-wide" id="gopay-number">{{ config('festival.payment.number') }}</p><p class="mt-1 text-xs text-white/60">a.n. {{ config('festival.payment.account_name') }}</p></div><button type="button" class="rounded-full bg-[#dcff69] px-3 py-2 text-xs font-bold text-[#20211e]" onclick="navigator.clipboard.writeText(document.getElementById('gopay-number').textContent.trim()).then(() => { this.textContent = 'Tersalin'; setTimeout(() => this.textContent = 'Salin', 1800); })">Salin</button></div>
                    <ol class="mt-4 list-inside list-decimal space-y-2 text-xs leading-5 text-white/65"><li>Pilih tiket dan selesaikan pemesanan.</li><li>Bayar sesuai nominal, termasuk kode pembayaran unik.</li><li>Unggah bukti pembayaran melalui halaman pesanan.</li><li>Tunggu verifikasi dari tim AFTERGLOW.</li></ol>
                </div>
            </section>
            <section>
                <div class="mb-4 flex items-baseline justify-between"><h2 class="display text-2xl font-semibold">Pesanan Saya</h2><span class="text-xs text-[#77756f]">{{ $orders->count() }} pesanan</span></div>
                <div class="space-y-4">
                    @forelse($orders as $order)
                        @php($statusLabel = ['pending' => 'Menunggu Pembayaran', 'waiting_verification' => 'Menunggu Verifikasi', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak', 'cancelled' => 'Kedaluwarsa'][$order->status] ?? ucfirst($order->status))
                        <article class="rounded-3xl border border-black/5 bg-white p-5 sm:p-6">
                            <div class="flex flex-wrap items-start justify-between gap-3"><div><p class="font-bold">{{ $order->day_pass ? 'Pass 3 Hari' : $order->ticketTier->festivalDay->title }} · {{ $order->ticketTier->zone_name }}</p><p class="mt-1 text-xs text-[#77756f]">{{ $order->order_code }} · {{ $order->quantity }} tiket</p></div><span class="rounded-full px-3 py-1 text-xs font-bold {{ $order->status === 'verified' ? 'bg-[#eaffbb] text-[#33520a]' : ($order->status === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-[#f3f2ed] text-[#77756f]') }}">{{ $statusLabel }}</span></div>
                            <div class="mt-5 flex flex-wrap items-end justify-between gap-4 border-t border-black/5 pt-4">
                                <div><p class="text-[10px] font-bold uppercase tracking-wider text-[#99968d]">Total pembayaran</p><p class="display mt-1 text-2xl font-semibold">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</p><p class="mt-1 text-xs text-[#77756f]">Termasuk kode unik Rp{{ number_format($order->unique_code, 0, ',', '.') }} @if($order->status === 'pending')· <span data-countdown="{{ $order->expires_at?->timestamp }}">{{ $order->expires_at?->diffForHumans() }}</span>@endif</p></div>
                                @if($order->status === 'verified')
                                    <a href="{{ route('orders.download', $order) }}" class="rounded-full bg-[#20211e] px-4 py-2.5 text-xs font-bold text-white">Unduh e-tiket PDF ↓</a>
                                @elseif(in_array($order->status, ['pending', 'rejected'], true))
                                    <form method="POST" action="{{ route('orders.proof', $order) }}" enctype="multipart/form-data" class="grid gap-2">@csrf<label class="text-[10px] font-bold uppercase tracking-wider text-[#77756f]" for="payment-proof-{{ $order->id }}">Bukti pembayaran (JPG, PNG, PDF · maks. 2 MB)</label><div class="flex flex-wrap items-center gap-2"><input id="payment-proof-{{ $order->id }}" required type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" class="max-w-[210px] text-xs"><button class="rounded-full bg-[#dcff69] px-4 py-2.5 text-xs font-bold">Unggah Bukti</button></div></form>
                                @else
                                    <p class="text-xs text-[#77756f]">Bukti diterima. Tim kami akan segera memeriksanya.</p>
                                @endif
                            </div>
                            @if($order->admin_note)<p class="mt-4 rounded-xl bg-red-50 p-3 text-sm text-red-700">Catatan tim AFTERGLOW: {{ $order->admin_note }}</p>@endif
                        </article>
                    @empty<div class="rounded-3xl border border-dashed border-black/15 bg-white p-10 text-center"><p class="font-semibold">Perjalanan AFTERGLOW-mu dimulai di sini.</p><p class="mt-2 text-sm text-[#77756f]">Pilih hari dan zona untuk memesan tiket pertamamu.</p></div>@endforelse
                </div>
                @if($tickets->isNotEmpty())
                    <div class="mt-10"><h2 class="display mb-4 text-2xl font-semibold">Tiket Saya</h2><div class="grid gap-3 sm:grid-cols-2">
                        @foreach($tickets as $ticket)
                            <article class="flex items-center gap-4 rounded-2xl border border-black/5 bg-white p-4"><div class="rounded-lg bg-white p-1 text-black">{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(76)->generate($ticket->qr_payload) !!}</div><div><p class="font-bold">DAY {{ str_pad($ticket->ticketTier->festivalDay->day_number, 2, '0', STR_PAD_LEFT) }}</p><p class="text-xs text-[#77756f]">{{ $ticket->ticketTier->festivalDay->event_date->locale('id')->translatedFormat('j F Y') }} · {{ $ticket->ticketTier->zone_name }}</p><p class="mt-2 text-[10px] font-bold uppercase {{ $ticket->is_checked_in ? 'text-[#e65b43]' : 'text-green-700' }}">{{ $ticket->is_checked_in ? 'Sudah check-in' : 'Siap dipindai' }}</p></div></article>
                        @endforeach
                    </div></div>
                @endif
            </section>
        </div>
        <p class="mt-10 text-center text-xs text-[#99968d]">Butuh bantuan? <a href="{{ route('home') }}#contact" class="underline">Hubungi AFTERGLOW melalui WhatsApp atau email</a></p>
    </main>
</div>
@endsection
@push('scripts')
<script>
document.querySelectorAll('[data-countdown]').forEach((element) => {
    const expiresAt = Number(element.dataset.countdown) * 1000;
    const updateCountdown = () => {
        const secondsLeft = Math.max(0, Math.floor((expiresAt - Date.now()) / 1000));
        const hours = Math.floor(secondsLeft / 3600);
        const minutes = Math.floor((secondsLeft % 3600) / 60);
        const seconds = secondsLeft % 60;
        element.textContent = secondsLeft ? 'Sisa ' + hours + 'j ' + minutes + 'm ' + seconds + 'd' : 'Batas pembayaran berakhir';
    };
    updateCountdown();
    window.setInterval(updateCountdown, 1000);
});
</script>
@endpush
