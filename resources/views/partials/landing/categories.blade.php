<section class="categories">
    <h2 class="section-title"><span>Kategori Populer</span></h2>
    <div class="category-grid">
        @forelse($categories ?? [] as $cat)
            <a href="{{ url('pages/produk?kategori=' . $cat->id) }}" class="category-item">
                <div class="category-icon">
                    <i class="{{ $cat->icon_class ?? 'fas fa-tools' }}"></i>
                </div>
                <p>{{ $cat->nama_kategori }}</p>
            </a>
        @empty
            <p>Kategori kosong.</p>
        @endforelse
    </div>
</section