@extends('layouts.festival')
@section('title', 'Dasbor Penyelenggara · AFTERGLOW')
@section('content')
<header class="bg-[#20211e] text-white"><nav class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-6 py-5">
    <a href="{{ route('home') }}" class="display text-xl font-bold">afterglow<span class="text-[#dcff69]">.</span><span class="ml-3 text-xs font-normal text-white/45">PENYELENGGARA</span></a>
    <div class="flex flex-wrap items-center gap-4 text-sm"><a href="{{ route('admin.checkin') }}">Check-in Tiket</a><a href="{{ route('admin.export') }}" class="rounded-full bg-[#dcff69] px-4 py-2 font-bold text-black">Unduh Laporan Excel ↓</a><form method="POST" action="{{ route('logout') }}">@csrf<button>Keluar</button></form></div>
</nav></header>
<main class="mx-auto max-w-7xl px-6 py-10 lg:px-10">
    @if(session('success'))<div role="status" class="mb-5 rounded-xl bg-[#eaffbb] p-4 text-sm">{{ session('success') }}</div>@endif
    @if($errors->any())<div role="alert" class="mb-5 rounded-xl bg-red-50 p-4 text-sm text-red-700">{{ $errors->first() }}</div>@endif
    <div class="mb-8"><p class="text-xs font-bold uppercase tracking-[.2em] text-[#e65b43]">Operasional festival</p><h1 class="display mt-2 text-4xl font-semibold">Dasbor Penyelenggara</h1></div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach([['Total Pendapatan Terverifikasi','Rp'.number_format($revenue,0,',','.')],['Menunggu Verifikasi',$pending],['Tiket Terjual',$sold]] as [$label,$value])
            <div class="rounded-2xl border border-black/5 bg-white p-5"><p class="text-xs font-semibold text-[#77756f]">{{ $label }}</p><p class="display mt-3 text-3xl font-semibold">{{ $value }}</p></div>
        @endforeach
    </div>
    <div class="mt-6 grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl bg-white p-5"><h2 class="font-bold">Pendapatan terverifikasi per hari</h2><canvas id="revenueChart" class="mt-4 max-h-64"></canvas></div>
        <div class="rounded-2xl bg-white p-5"><h2 class="font-bold">Jumlah tiket per hari dan zona</h2><canvas id="zoneChart" class="mt-4 max-h-64"></canvas></div>
    </div>
    <section class="mt-10">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3"><h2 class="display text-2xl font-semibold">Verifikasi Pembayaran</h2><label class="sr-only" for="statusFilter">Saring status pesanan</label><select id="statusFilter" class="rounded-xl border-black/10 bg-white px-4 py-2 text-sm"><option value="">Semua status</option><option value="waiting_verification">Menunggu Verifikasi</option><option value="verified">Terverifikasi</option><option value="rejected">Ditolak</option><option value="pending">Menunggu Pembayaran</option></select></div>
        <div class="overflow-x-auto rounded-2xl border border-black/5 bg-white">
            <table class="w-full min-w-[850px] text-left text-sm">
                <thead class="bg-[#f0efe9] text-xs uppercase text-[#77756f]"><tr><th class="p-4">Pesanan</th><th class="p-4">Pemesan</th><th class="p-4">Tiket</th><th class="p-4">Total</th><th class="p-4">Status</th><th class="p-4">Bukti / Tindakan</th></tr></thead>
                <tbody class="divide-y divide-black/5">
                    @forelse($orders as $order)
                        @php($statusLabel = ['pending' => 'Menunggu Pembayaran', 'waiting_verification' => 'Menunggu Verifikasi', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak', 'cancelled' => 'Kedaluwarsa'][$order->status] ?? ucfirst($order->status))
                        <tr data-status="{{ $order->status }}">
                            <td class="p-4 font-semibold">{{ $order->order_code }}<p class="mt-1 text-xs font-normal text-[#888]">{{ $order->created_at->locale('id')->translatedFormat('j M, H:i') }} WIB</p></td>
                            <td class="p-4">{{ $order->user->name }}<p class="text-xs text-[#888]">{{ $order->user->email }}</p></td>
                            <td class="p-4">{{ $order->day_pass ? 'Pass 3 Hari' : 'DAY '.str_pad($order->ticketTier->festivalDay->day_number, 2, '0', STR_PAD_LEFT) }}<p class="text-xs text-[#888]">{{ $order->ticketTier->zone_name }} × {{ $order->quantity }}</p></td>
                            <td class="p-4 font-semibold">Rp{{ number_format($order->total_amount,0,',','.') }}</td>
                            <td class="p-4"><span class="rounded-full bg-[#f3f2ed] px-3 py-1 text-xs">{{ $statusLabel }}</span></td>
                            <td class="p-4">
                                @if($order->payment_proof_path)<a target="_blank" rel="noopener" class="mr-2 underline" href="{{ route('admin.orders.proof',$order) }}">Lihat bukti ↗</a>@endif
                                @if($order->status === 'waiting_verification')
                                    <div class="mt-2 flex gap-2"><form method="POST" action="{{ route('admin.orders.approve',$order) }}">@csrf<button class="rounded-full bg-[#dcff69] px-3 py-1.5 text-xs font-bold">Setujui</button></form><form method="POST" action="{{ route('admin.orders.reject',$order) }}" class="flex gap-1">@csrf<input name="admin_note" required aria-label="Alasan penolakan" placeholder="Alasan penolakan" class="w-36 rounded-lg border-black/10 px-2 text-xs"><button class="rounded-full bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700">Tolak</button></form></div>
                                @endif
                            </td>
                        </tr>
                    @empty<tr><td colspan="6" class="p-8 text-center text-[#77756f]">Belum ada pesanan.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </section>
    <section class="mt-10">
        <h2 class="display mb-4 text-2xl font-semibold">Kuota dan Harga per Hari & Zona</h2>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($tiers as $tier)
                <article class="rounded-2xl bg-white p-4">
                    <div class="flex justify-between gap-2 text-sm font-semibold"><span>DAY {{ str_pad($tier->festivalDay->day_number,2,'0',STR_PAD_LEFT) }}</span><span>{{ $tier->zone_name }}</span></div>
                    <p class="mt-2 text-xs text-[#77756f]">{{ $tier->festivalDay->event_date->locale('id')->translatedFormat('j F Y') }}</p>
                    <p class="mt-3 text-xs text-[#77756f]">{{ max(0,$tier->quota-$tier->sold_count) }} tiket tersisa · {{ $tier->sold_count }} dipesan / {{ $tier->quota }}</p>
                    <div class="mt-3 h-1.5 rounded bg-[#efeee8]"><div class="h-1.5 rounded bg-[#e65b43]" style="width:{{ min(100,($tier->sold_count/max($tier->quota,1))*100) }}%"></div></div>
                    <form method="POST" action="{{ route('admin.tiers.update',$tier) }}" class="mt-4 grid grid-cols-[1fr_1fr_auto] gap-2">@csrf @method('PATCH')
                        <label class="text-[10px] text-[#77756f]">Harga (Rp)<input type="number" min="1" name="price" value="{{ $tier->price }}" class="mt-1 w-full rounded-lg border-black/10 px-2 py-1 text-xs text-black"></label>
                        <label class="text-[10px] text-[#77756f]">Kuota<input type="number" min="{{ $tier->sold_count }}" name="quota" value="{{ $tier->quota }}" class="mt-1 w-full rounded-lg border-black/10 px-2 py-1 text-xs text-black"></label>
                        <button class="self-end rounded-full bg-[#20211e] px-3 py-2 text-[10px] font-bold text-white">Simpan</button>
                    </form>
                </article>
            @endforeach
        </div>
    </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script><script>
const revenueData = @json($revenueChartData);
const ticketData = @json($ticketChartData);
new Chart(document.getElementById('revenueChart'), {type:'bar',data:{labels:revenueData.map(x=>x.day),datasets:[{label:'Pendapatan (Rp)',data:revenueData.map(x=>x.revenue),backgroundColor:'#dcff69',borderRadius:8}]},options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
new Chart(document.getElementById('zoneChart'), {type:'bar',data:{labels:['DAY 01','DAY 02','DAY 03'],datasets:['Regular','Premium','Platinum'].map((zone,index)=>({label:zone,data:[1,2,3].map(day=>ticketData.find(row=>row.day===day&&row.zone===zone)?.quantity||0),backgroundColor:['#20211e','#ff765c','#dcff69'][index]}))},options:{scales:{x:{stacked:true},y:{stacked:true,beginAtZero:true}},plugins:{legend:{position:'bottom'}}}});
document.getElementById('statusFilter').addEventListener('change', event => document.querySelectorAll('tbody tr[data-status]').forEach(row => row.hidden = !!event.target.value && row.dataset.status !== event.target.value));
</script>
@endsection
