@php($thumbnail = $article->getFirstMediaUrl($article::THUMBNAIL_COLLECTION) ?: $article->thumbnail_path)
<div>
    <section class="border-b border-[#e2e8f0] bg-white py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="text-xs text-slate-500"><a href="{{ route('public.home') }}">Beranda</a> / <a href="{{ route('public.news.index') }}">Kabar Desa</a> / {{ $article->category->label() }}</nav>
            <span class="mt-7 inline-flex rounded-full bg-[#f1f5f9] px-3 py-1 text-xs font-bold text-[#2d6a4f]">{{ $article->category->label() }}</span>
            <h1 class="mt-4 max-w-4xl text-4xl font-extrabold">{{ $article->title }}</h1>
            <p class="mt-5 text-sm text-slate-500">{{ $article->author->name }} • {{ $article->published_at->translatedFormat('d F Y') }}</p>
        </div>
    </section>

    <section class="py-12">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[1fr_320px] lg:px-8">
            <article>
                @if ($thumbnail)
                    <img src="{{ $thumbnail }}" alt="{{ $article->title }}" class="h-[420px] w-full rounded-xl object-cover">
                @endif
                <div class="mt-8 space-y-5 text-base leading-8 text-slate-600">{!! $article->content !!}</div>
            </article>
            <aside>
                <h2 class="border-l-4 border-[#2d6a4f] pl-3 text-lg font-extrabold">Artikel Terkait</h2>
                <div class="mt-5 grid gap-4">
                    @forelse ($related as $item)
                        <a href="{{ route('public.news.show', $item->slug) }}" class="rounded-xl border border-[#e2e8f0] bg-white p-4">
                            <p class="text-xs font-bold text-[#2d6a4f]">{{ $item->category->label() }}</p>
                            <h3 class="mt-2 text-sm font-extrabold">{{ $item->title }}</h3>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada artikel terkait.</p>
                    @endforelse
                </div>
            </aside>
        </div>
    </section>
</div>