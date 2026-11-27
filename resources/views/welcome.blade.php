@extends('layouts.festival')
@section('title', 'AFTERGLOW — '.config('festival.tagline'))
@section('content')
<header class="absolute inset-x-0 top-0 z-20 text-white">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-10">
        <a href="{{ route('home') }}" class="display text-xl font-bold">afterglow<span class="text-[#dcff69]">.</span></a>
        <div class="hidden items-center gap-8 text-sm text-white/75 md:flex">
            <a href="#lineup" class="hover:text-white">Jadwal</a>
            <a href="#grounds" class="hover:text-white">Area Festival</a>
            <a href="#faq" class="hover:text-white">FAQ</a>
            <a href="#contact" class="hover:text-white">Hubungi Kami</a>
        </div>
        <div class="flex items-center gap-4 text-sm">
            @auth
                <a href="{{ route('dashboard') }}" class="hidden text-white/80 hover:text-white sm:inline">Dasbor Saya</a>
            @else
                <a href="{{ route('login') }}" class="hidden text-white/80 hover:text-white sm:inline">Masuk</a>
            @endauth
            <a href="#tickets" class="rounded-full bg-[#dcff69] px-5 py-2.5 font-bold text-[#171916] transition hover:bg-white">Beli Tiket ↗</a>
        </div>
    </nav>
</header>

<main>
    <section class="relative min-h-[700px] overflow-hidden bg-[#171916] text-white">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_72%_42%,#ef76574d,transparent_34%),radial-gradient(ellipse_at_27%_75%,#b7d75230,transparent_35%),linear-gradient(118deg,#171916,#272521_60%,#171916)]"></div>
        <div class="grain absolute inset-0 opacity-30"></div>
        <div class="absolute right-[12%] top-28 hidden h-[440px] w-[440px] rounded-full border border-white/10 lg:block"><div class="absolute inset-8 rounded-full border border-white/10"></div><div class="absolute inset-20 rounded-full border border-white/10"></div></div>
        <div class="relative mx-auto grid min-h-[700px] max-w-7xl content-center px-6 pb-20 pt-32 lg:px-10">
            <div class="max-w-4xl">
                <p class="mb-7 flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[.2em] text-[#dcff69] sm:tracking-[.25em]"><span class="h-px w-9 bg-[#dcff69]"></span>Bogor · {{ $days->first()?->event_date->locale('id')->translatedFormat('j') }}–{{ $days->last()?->event_date->locale('id')->translatedFormat('j F Y') }}</p>
                <h1 class="display max-w-4xl text-5xl font-semibold leading-[.98] sm:text-7xl lg:text-[92px]">{{ config('festival.tagline') }}</h1>
                <p class="mt-7 max-w-2xl text-lg leading-8 text-white/70 sm:text-xl">{{ config('festival.subtext') }}</p>
                <div class="mt-9 flex flex-wrap items-center gap-5">
                    <a href="#tickets" class="rounded-full bg-[#dcff69] px-7 py-4 font-bold text-[#171916] hover:bg-white">Beli Tiket Sekarang ↗</a>
                    <a href="#lineup" class="text-sm text-white/75 hover:text-white">Lihat jadwal festival ↓</a>
                </div>
            </div>
            <div class="mt-14 flex flex-wrap gap-x-8 gap-y-3 border-t border-white/15 pt-5 text-sm text-white/65">
                <span>📍 {{ config('festival.event.location') }}</span>
                <span>☀ Gerbang dibuka {{ config('festival.event.gates_open') }}</span>
                <span>✳ {{ config('festival.event.age_policy') }}</span>
            </div>
        </div>
        <div class="absolute bottom-12 right-[10%] hidden rotate-[-8deg] rounded-2xl border border-white/15 bg-white/5 p-5 backdrop-blur-md lg:block">
            <p class="text-[10px] uppercase tracking-[.2em] text-white/55">Rayakan pergantian tahun bersama</p>
            <p class="display mt-3 text-3xl font-semibold text-[#dcff69]">29 · 30 · 31</p>
            <p class="mt-1 text-xs text-white/55">DESEMBER 2026 · BOGOR</p>
        </div>
    </section>

    <section id="lineup" class="mx-auto max-w-7xl px-6 py-20 lg:px-10 lg:py-28">
        <div class="mb-11">
            <p class="mb-3 text-xs font-bold uppercase tracking-[.2em] text-[#e65b43]">Jadwal festival</p>
            <h2 class="display text-4xl font-semibold sm:text-6xl">Tiga hari, tiga suasana.</h2>
            <p class="mt-4 max-w-2xl text-sm leading-6 text-[#6c6a65]">Bersiap untuk tiga hari penuh musik, momen bersama, dan perayaan pergantian tahun di Kebun Raya Bogor.</p>
        </div>
        <div class="grid gap-4 lg:grid-cols-3">
            @foreach($days as $day)
                @php($dayInfo = $lineup[$day->day_number])
                <article class="rounded-3xl border border-black/5 bg-white p-6 transition hover:-translate-y-1 hover:shadow-xl sm:p-7">
                    <p class="text-xs font-bold uppercase tracking-[.18em] text-[#77756f]">DAY {{ str_pad($day->day_number, 2, '0', STR_PAD_LEFT) }} · {{ $day->event_date->locale('id')->translatedFormat('j F Y') }}</p>
                    <h3 class="display mt-4 text-2xl font-semibold leading-tight">{{ $dayInfo['title'] }}</h3>
                    @if(!empty($dayInfo['notes']))<p class="mt-3 rounded-xl bg-[#f3f2ed] p-3 text-xs leading-5 text-[#6c6a65]">{{ $dayInfo['notes'] }}</p>@endif
                    <div class="my-6 h-px bg-black/8"></div>
                    <p class="mb-4 text-[10px] font-bold uppercase tracking-[.18em] text-[#99968d]">Waktu Indonesia Barat (WIB)</p>
                    <ol class="space-y-4">
                        @foreach($dayInfo['acts'] as $act)
                            <li class="flex items-start justify-between gap-3 text-sm">
                                <span class="shrink-0 font-medium text-[#99968d]">{{ $act['time'] }} WIB @if(!empty($act['date_note']))<span class="mt-1 block text-[10px]">{{ $act['date_note'] }}</span>@endif</span>
                                <span class="text-right font-semibold">{{ $act['artist'] }}</span>
                            </li>
                        @endforeach
                    </ol>
                    <a href="#tickets" class="mt-7 inline-flex gap-2 text-sm font-bold">Lihat tiket Day {{ str_pad($day->day_number, 2, '0', STR_PAD_LEFT) }} ↗</a>
                </article>
            @endforeach
        </div>
    </section>

    <section id="grounds" x-data="{ selectedDay: 1, prices: @js($groundsPrices), formatPrice(price) { return 'Rp' + new Intl.NumberFormat('id-ID').format(price || 0) } }" class="bg-[#e9e8df]">
        <div class="mx-auto grid max-w-7xl gap-12 px-6 py-20 lg:grid-cols-[.85fr_1.15fr] lg:px-10 lg:py-28">
            <div>
                <p class="mb-3 text-xs font-bold uppercase tracking-[.2em] text-[#e65b43]">Area festival</p>
                <h2 class="display text-4xl font-semibold sm:text-5xl">Pilih tempat<br>favoritmu.</h2>
                <p class="mt-5 text-sm leading-6 text-[#6c6a65]">Semua zona memberimu akses ke festival. Pilih pengalaman yang paling cocok untuk merayakan akhir tahun.</p>
                <div class="mt-7 flex flex-wrap gap-2" role="tablist" aria-label="Pilih hari festival">
                    @foreach($days as $day)
                        <button type="button" role="tab" x-on:click="selectedDay = {{ $day->day_number }}" x-bind:aria-selected="selectedDay === {{ $day->day_number }}" class="rounded-full border border-black/10 px-4 py-2 text-xs font-bold aria-selected:bg-[#20211e] aria-selected:text-white" :class="selectedDay === {{ $day->day_number }} ? 'bg-[#20211e] text-white' : 'bg-white text-[#55534e]'">DAY {{ str_pad($day->day_number, 2, '0', STR_PAD_LEFT) }}</button>
                    @endforeach
                </div>
                <div class="mt-5 space-y-3">
                    @foreach(['Regular', 'Premium', 'Platinum'] as $zoneName)
                        @php($tier = $zoneBenefits->get($zoneName))
                        <article class="rounded-2xl bg-white p-5">
                            <div class="flex items-center justify-between gap-3"><h3 class="font-bold">{{ $zoneName }}</h3><span class="font-bold" x-text="formatPrice(prices[selectedDay] && prices[selectedDay]['{{ $zoneName }}'])"></span></div>
                            <ul class="mt-3 flex flex-wrap gap-x-4 gap-y-2 text-xs text-[#77756f]">
                                @foreach($tier?->benefits ?? [] as $benefit)<li><span class="mr-1 text-[#e65b43]">✓</span>{{ $benefit }}</li>@endforeach
                            </ul>
                        </article>
                    @endforeach
                </div>
                <p class="mt-4 text-xs text-[#77756f]">Harga mengikuti hari yang dipilih dan bersumber langsung dari daftar tiket.</p>
            </div>
            <div class="relative flex min-h-[430px] items-center justify-center overflow-hidden rounded-[2rem] bg-[#20211e] p-8 text-white">
                <div class="w-full max-w-lg">
                    <div class="mb-5 rounded-xl border border-white/10 bg-white/5 py-3 text-center text-xs font-bold uppercase tracking-[.2em]">Panggung utama · AFTERGLOW</div>
                    <div class="mx-auto mb-5 flex h-16 w-[70%] items-center justify-center rounded-xl border border-[#dcff69]/40 bg-[#dcff69]/10 text-[10px] uppercase tracking-[.2em] text-[#dcff69]">Platinum · depan panggung</div>
                    <div class="mx-auto mb-5 flex h-24 w-[88%] items-center justify-center rounded-xl border border-[#ff8b72]/40 bg-[#ff8b72]/10 text-[10px] uppercase tracking-[.2em] text-[#ff9d88]">Premium · area menonton khusus</div>
                    <div class="flex h-32 items-center justify-center rounded-xl border border-white/15 bg-white/5 text-[10px] uppercase tracking-[.2em] text-white/60">Regular · area festival</div>
                    <div class="mt-6 flex justify-between text-[10px] uppercase tracking-wider text-white/40"><span>← Kuliner & bazar</span><span>Pintu masuk →</span></div>
                </div>
            </div>
        </div>
    </section>

    <section id="tickets" class="mx-auto max-w-7xl px-6 py-20 lg:px-10 lg:py-28">
        <div class="mb-11 text-center">
            <p class="mb-3 text-xs font-bold uppercase tracking-[.2em] text-[#e65b43]">Tiket AFTERGLOW</p>
            <h2 class="display text-4xl font-semibold sm:text-6xl">Sampai jumpa di Bogor.</h2>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-6 text-[#77756f]">Pilih hari dan zona. Setelah membayar melalui GoPay, unggah bukti pembayaran agar tiket digitalmu segera diverifikasi.</p>
        </div>
        <div class="mx-auto grid max-w-6xl gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($days as $day)
                @foreach($day->ticketTiers as $tier)
                    <article class="rounded-2xl border border-black/5 bg-white p-5">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#77756f]">DAY {{ str_pad($day->day_number, 2, '0', STR_PAD_LEFT) }} · {{ $day->event_date->locale('id')->translatedFormat('j M') }}</span>
                            <span class="rounded-full bg-[#f3f2ed] px-3 py-1 text-[10px] font-bold uppercase">{{ $tier->zone_name }}</span>
                        </div>
                        <p class="display mt-5 text-3xl font-semibold">Rp{{ number_format($tier->price, 0, ',', '.') }}</p>
                        <p class="mt-2 text-xs text-[#77756f]">{{ max(0, $tier->quota - $tier->sold_count) }} tiket tersedia</p>
                        <a href="{{ auth()->check() ? route('dashboard') : route('register') }}" class="mt-5 block rounded-full {{ $tier->zone_name === 'Platinum' ? 'bg-[#dcff69]' : 'bg-[#20211e] text-white' }} px-4 py-3 text-center text-sm font-bold">Pilih {{ $tier->zone_name }}</a>
                    </article>
                @endforeach
            @endforeach
        </div>
        <p class="mt-6 text-center text-xs text-[#77756f]">Ingin hadir selama tiga hari? Pilih opsi Pass 3 Hari di dasbor pemesanan. Kode pembayaran unik sudah termasuk dalam total tagihan. Pesanan yang belum dibayar kedaluwarsa setelah 24 jam.</p>
    </section>

    <section id="faq" class="border-y border-black/5 bg-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-6 py-20 lg:grid-cols-[.7fr_1.3fr] lg:px-10">
            <div><p class="mb-3 text-xs font-bold uppercase tracking-[.2em] text-[#e65b43]">Informasi festival</p><h2 class="display text-4xl font-semibold">Yang perlu kamu tahu.</h2><p class="mt-4 text-sm text-[#77756f]">Biar pengalaman festivalmu lebih nyaman.</p></div>
            <div class="divide-y divide-black/10">
                @foreach([
                    ['Bagaimana cara mendapatkan tiket?', 'Pilih tiket dan selesaikan pesanan. Bayar sesuai nominal melalui GoPay, lalu unggah bukti pembayaran. Setelah diverifikasi, QR e-tiket tersedia di dasbor dan dapat diunduh sebagai PDF.'],
                    ['Kapan gerbang dibuka dan siapa yang boleh hadir?', 'Gerbang dibuka pukul 14:00 WIB. Festival ini terbuka untuk semua usia. Bawa e-tiket dan identitas diri yang berlaku.'],
                    ['Apakah tiket bisa dikembalikan?', 'Tiket yang sudah dibeli tidak dapat dikembalikan. Jika acara dibatalkan oleh penyelenggara, tim AFTERGLOW akan menghubungi pemegang tiket mengenai langkah selanjutnya.'],
                    ['Bagaimana cara menuju lokasi?', 'Festival berlangsung di Kebun Raya Bogor, Bogor, Jawa Barat. Rencanakan perjalanan lebih awal dan ikuti informasi transportasi resmi menjelang acara.'],
                ] as [$question, $answer])
                    <details class="group py-5"><summary class="flex cursor-pointer list-none justify-between gap-4 font-semibold">{{ $question }}<span class="text-xl text-[#e65b43] group-open:rotate-45">+</span></summary><p class="mt-3 max-w-2xl text-sm leading-6 text-[#77756f]">{{ $answer }}</p></details>
                @endforeach
            </div>
        </div>
    </section>

    <section id="terms" class="mx-auto max-w-7xl px-6 py-16 lg:px-10">
        <p class="mb-3 text-xs font-bold uppercase tracking-[.2em] text-[#e65b43]">Syarat & ketentuan</p>
        <div class="grid gap-4 text-sm leading-6 text-[#6c6a65] sm:grid-cols-3">
            <p>Setiap QR e-tiket berlaku untuk satu orang, satu hari, dan zona yang tertera. Pass 3 Hari berisi tiket terpisah untuk setiap hari.</p>
            <p>Tiket bersifat pribadi. Jangan membagikan QR e-tiket kepada orang lain. Tiket yang sudah digunakan tidak dapat digunakan kembali.</p>
            <p>Pengunjung wajib mengikuti arahan petugas dan kebijakan lokasi Kebun Raya Bogor. Jadwal dapat berubah mengikuti keputusan penyelenggara.</p>
        </div>
    </section>

    <section id="contact" class="mx-auto max-w-7xl px-6 py-20 lg:px-10 lg:py-24">
        <div class="mx-auto max-w-3xl rounded-[2rem] bg-white px-6 py-10 text-center shadow-sm sm:px-12 sm:py-14">
            <p class="mb-3 text-xs font-bold uppercase tracking-[.2em] text-[#e65b43]">Kontak AFTERGLOW</p>
            <h2 class="display text-4xl font-semibold sm:text-5xl">Kami siap menyambutmu.</h2>
            <p class="mx-auto mt-5 max-w-xl text-sm leading-6 text-[#77756f]">Hubungi tim kami langsung melalui WhatsApp atau email untuk pertanyaan seputar festival dan pemesanan tiket.</p>
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a class="inline-flex justify-center rounded-full bg-[#20211e] px-6 py-3.5 text-sm font-bold text-white" href="https://wa.me/{{ config('festival.contact.whatsapp') }}?text={{ rawurlencode(config('festival.contact.whatsapp_message')) }}" target="_blank" rel="noopener noreferrer">WhatsApp · 0821 2894 3420 ↗</a>
                <a class="inline-flex items-center justify-center rounded-full border border-black/10 bg-white px-6 py-3.5 text-sm font-bold" href="mailto:{{ config('festival.contact.email') }}">Email · {{ config('festival.contact.email') }}</a>
            </div>
        </div>
    </section>
</main>

<footer class="bg-[#20211e] px-6 py-8 text-white">
    <div class="mx-auto flex max-w-7xl flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <a href="{{ route('home') }}" class="display text-xl font-bold">afterglow<span class="text-[#dcff69]">.</span></a>
        <p class="text-xs text-white/45">© 2026 AFTERGLOW Festival · Bogor, Indonesia</p>
        <div class="flex flex-wrap gap-5 text-xs text-white/60"><a href="#terms">Syarat & ketentuan</a><a href="mailto:{{ config('festival.contact.email') }}">{{ config('festival.contact.email') }}</a></div>
    </div>
</footer>
<a aria-label="Hubungi AFTERGLOW melalui WhatsApp" href="https://wa.me/{{ config('festival.contact.whatsapp') }}?text={{ rawurlencode(config('festival.contact.whatsapp_message')) }}" target="_blank" rel="noopener noreferrer" class="fixed bottom-5 right-5 z-30 flex h-14 w-14 items-center justify-center rounded-full bg-[#dcff69] text-xl font-bold text-[#20211e] shadow-lg shadow-black/15 transition hover:scale-105">WA</a>
@endsection
