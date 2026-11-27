<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><style>
body{font-family:DejaVu Sans,sans-serif;color:#20211e;padding:32px}.top{background:#20211e;color:white;padding:24px}.tag{color:#637f17;text-transform:uppercase;letter-spacing:2px;font-size:11px}.pass{border:1px solid #ddd;padding:20px;margin-top:16px;page-break-inside:avoid}.qr{width:135px;height:135px}.muted{color:#777;font-size:12px}.details{line-height:1.8}
</style></head>
<body>
<div class="top"><div class="tag">AFTERGLOW · {{ config('festival.event.city') }}</div><h1>{{ config('festival.tagline') }}</h1><div>{{ config('festival.event.venue') }} · {{ config('festival.event.city') }}</div></div>
<p>Halo {{ $order->user->name }}, pembayaranmu telah terverifikasi. Tunjukkan e-tiket ini dan identitas diri saat masuk.</p>
<div class="details"><strong>Kode pesanan:</strong> {{ $order->order_code }}<br><strong>Zona:</strong> {{ $order->ticketTier->zone_name }}<br><strong>Hari:</strong> {{ $order->day_pass ? 'Pass 3 Hari' : 'DAY '.str_pad($order->ticketTier->festivalDay->day_number, 2, '0', STR_PAD_LEFT) }} · {{ $ticketDateRange }}<br><strong>Total dibayar:</strong> Rp{{ number_format($order->total_amount, 0, ',', '.') }} (termasuk kode unik Rp{{ number_format($order->unique_code, 0, ',', '.') }})</div>
<p class="muted">Gerbang dibuka pukul {{ config('festival.event.gates_open') }} · {{ config('festival.event.age_policy') }}</p>
@foreach($order->tickets as $index => $ticket)
    <div class="pass"><div class="tag">TIKET {{ $index + 1 }} · DAY {{ str_pad($ticket->ticketTier->festivalDay->day_number, 2, '0', STR_PAD_LEFT) }} · {{ $ticket->ticketTier->zone_name }}</div><p><strong>{{ $ticket->ticketTier->festivalDay->event_date->locale('id')->translatedFormat('j F Y') }}</strong></p><p class="muted">Kode tiket: {{ $ticket->ticket_code }}</p><img class="qr" src="{{ $qrCodes[$index] }}"></div>
@endforeach
<h3>Informasi acara</h3><p class="muted">Tiket berlaku untuk satu orang pada hari dan zona yang tertera. Simpan QR dengan aman dan jangan bagikan kepada orang lain. Setiap tiket hanya dapat dipindai satu kali. Lokasi: {{ config('festival.event.location') }}.</p>
<p class="muted">Bantuan: {{ config('festival.contact.email') }} · WhatsApp {{ config('festival.contact.whatsapp') }}</p>
</body>
</html>
