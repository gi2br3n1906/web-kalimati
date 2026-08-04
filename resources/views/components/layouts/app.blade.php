@props(['title' => 'Portal Resmi Desa Kalimati'])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - Desa Kalimati</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-[#fafafa] font-sans text-[#0f172a] antialiased">
    <div x-data="{ mobileMenuOpen: false }" class="min-h-screen">
        <header class="sticky top-0 z-50 border-b border-[#e2e8f0] bg-white/85 backdrop-blur-md">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <a href="{{ route('public.home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-boyolali.svg') }}" alt="Logo Kabupaten Boyolali" class="h-11 w-9">
                    <span><span class="block text-sm font-extrabold">Desa Kalimati</span><span class="block text-[11px] font-medium text-slate-500">Kab. Boyolali</span></span>
                </a>

                @php
                    $navigation = [
                        ['label' => 'Beranda', 'route' => 'public.home'],
                        ['label' => 'Profil & Sejarah', 'route' => 'public.profile'],
                        ['label' => 'Smart Agriculture', 'route' => 'public.agriculture'],
                        ['label' => 'Peta Spasial', 'route' => 'public.gis.map'],
                        ['label' => 'Kabar Desa', 'route' => 'public.news.index'],
                    ];
                @endphp

                <nav class="hidden items-center gap-1 lg:flex" aria-label="Navigasi publik">
                    @foreach ($navigation as $item)
                        <a href="{{ route($item['route']) }}" class="relative rounded-lg px-3 py-2 text-xs font-semibold transition {{ request()->routeIs($item['route'] === 'public.news.index' ? 'public.news.*' : $item['route']) ? 'bg-[rgba(45,106,79,0.08)] text-[#2d6a4f]' : 'text-slate-600 hover:bg-slate-100 hover:text-[#0f172a]' }}">
                            {{ $item['label'] }}
                            @if (request()->routeIs($item['route'] === 'public.news.index' ? 'public.news.*' : $item['route']))
                                <span class="absolute inset-x-3 -bottom-[13px] h-0.5 rounded-full bg-[#2d6a4f]"></span>
                            @endif
                        </a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-2"><a href="{{ url('/admin') }}" class="hidden rounded-full bg-[#2d6a4f] px-4 py-2.5 text-xs font-bold text-white hover:bg-[#1b4332] sm:inline-flex">Panel Admin →</a><button type="button" class="grid h-10 w-10 place-items-center rounded-xl border border-[#e2e8f0] text-slate-700 lg:hidden" @click="mobileMenuOpen = ! mobileMenuOpen" :aria-expanded="mobileMenuOpen" aria-label="Buka menu navigasi">
                    <svg x-show="! mobileMenuOpen" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
                </button></div>
            </div>

            <nav x-show="mobileMenuOpen" x-collapse x-cloak class="border-t border-[#e2e8f0] bg-white px-4 py-3 lg:hidden" aria-label="Navigasi seluler">
                <div class="mx-auto grid max-w-7xl gap-1">
                    @foreach ($navigation as $item)
                        <a href="{{ route($item['route']) }}" class="rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs($item['route'] === 'public.news.index' ? 'public.news.*' : $item['route']) ? 'bg-[rgba(45,106,79,0.08)] text-[#2d6a4f]' : 'text-slate-600 hover:bg-slate-100' }}">{{ $item['label'] }}</a>
                    @endforeach
                    <a href="{{ url('/admin') }}" class="rounded-xl bg-[#2d6a4f] px-4 py-3 text-center text-sm font-bold text-white sm:hidden">Panel Admin →</a>
                </div>
            </nav>
        </header>

        <main>{{ $slot }}</main>

        <footer class="bg-gradient-to-r from-[#0f172a] to-[#1e293b] text-slate-300">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-8">
                <div>
                    <div class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-[#2d6a4f] font-extrabold text-white">DK</span><div><p class="font-bold text-white">Desa Kalimati</p><p class="text-xs text-slate-400">Kabupaten Boyolali</p></div></div>
                    <p class="mt-4 text-sm leading-6 text-slate-400">Portal resmi informasi, potensi, dan pelayanan publik Desa Kalimati.</p>
                </div>
                <div><h2 class="font-bold text-white">Akses Cepat</h2><div class="mt-4 grid gap-2 text-sm text-slate-400"><a href="{{ route('public.home') }}">Beranda</a><a href="{{ route('public.profile') }}">Profil & Sejarah</a><a href="{{ route('public.agriculture') }}">Smart Agriculture</a></div></div>
                <div><h2 class="font-bold text-white">Layanan</h2><div class="mt-4 grid gap-2 text-sm text-slate-400"><a href="{{ route('public.gis.map') }}">Peta Spasial</a><a href="{{ route('public.news.index') }}">Kabar Desa</a><a href="{{ url('/admin') }}">Panel Admin</a></div></div>
                <div><h2 class="font-bold text-white">Kontak Balai Desa</h2><div class="mt-4 grid gap-2 text-sm text-slate-400"><span>pemdes@kalimati.desa.id</span><span>+62 812-3456-7890</span><span>Senin–Jumat, 08.00–15.00 WIB</span></div></div>
            </div>
            <div class="border-t border-white/10 px-4 py-5 text-center text-xs text-slate-500">&copy; 2026 Pemerintah Desa Kalimati. Seluruh hak dilindungi.</div>
        </footer>
    </div>
    @livewireScripts
</body>
</html>