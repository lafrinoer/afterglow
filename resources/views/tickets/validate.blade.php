@extends('layouts.festival')
@section('title', 'Validasi E-Tiket · AFTERGLOW')
@section('content')
<main class="mx-auto max-w-xl px-6 py-20">
    <section class="rounded-3xl bg-white p-8 text-center shadow-sm">
        <p class="text-xs font-bold uppercase tracking-[.2em] text-green-700">Tanda tangan tiket valid</p>
        <h1 class="display mt-3 text-3xl font-semibold">AFTERGLOW · DAY {{ str_pad($ticket->ticketTier->festivalDay->day_number, 2, '0', STR_PAD_LEFT) }}</h1>
        <p class="mt-3">{{ $ticket->ticketTier->festivalDay->event_date->locale('id')->translatedFormat('j F Y') }} · {{ $ticket->ticketTier->zone_name }}</p>
        <p class="mt-6 font-mono text-sm">{{ $ticket->ticket_code }}</p>
        <p class="mt-6 text-xs text-[#77756f]">Petugas akan menyelesaikan check-in pada hari acara.</p>
    </section>
</main>
@endsection
