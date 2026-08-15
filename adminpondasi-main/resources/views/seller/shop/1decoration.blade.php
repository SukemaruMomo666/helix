<script>
// HARDCODE DATA JAVASCRIPT
    const TEMPLATES_DATA = [
        {id:1, name:'Oceanic Premium', hc:'bg-gradient-to-r from-blue-600 to-indigo-800', ac:'bg-gradient-to-br from-blue-500 to-indigo-600', layout:['banner', 'kategori', 'produk']},
        {id:2, name:'Eco Harvest', hc:'bg-gradient-to-r from-emerald-600 to-teal-800', ac:'bg-gradient-to-br from-emerald-500 to-green-600', layout:['kategori', 'carousel', 'produk']},
        {id:3, name:'Sunset Flash', hc:'bg-gradient-to-r from-orange-500 to-red-600', ac:'bg-gradient-to-br from-orange-500 to-red-500', layout:['video', 'produk', 'banner']},
        {id:4, name:'Midnight Luxury', hc:'bg-slate-900', ac:'bg-gradient-to-br from-slate-700 to-slate-900', layout:['carousel', 'kategori', 'produk']},
        {id:5, name:'Pink Blossom', hc:'bg-gradient-to-r from-pink-500 to-rose-600', ac:'bg-gradient-to-br from-pink-400 to-rose-500', layout:['banner', 'produk', 'kategori']},
        {id:6, name:'Neon Cyber', hc:'bg-gradient-to-r from-purple-700 to-indigo-800', ac:'bg-gradient-to-br from-purple-600 to-cyan-500', layout:['video', 'carousel', 'produk']},
        {id:7, name:'Minimalist Clean', hc:'bg-slate-800', ac:'bg-slate-800', layout:['kategori', 'produk', 'produk']},
        {id:8, name:'Pastel Dream', hc:'bg-gradient-to-r from-fuchsia-500 to-purple-600', ac:'bg-gradient-to-br from-violet-400 to-fuchsia-400', layout:['carousel', 'banner', 'produk']},
        {id:9, name:'Earthy Warm', hc:'bg-gradient-to-r from-amber-700 to-orange-800', ac:'bg-gradient-to-br from-amber-600 to-orange-700', layout:['banner', 'kategori', 'produk']},
        {id:10, name:'Royal Gold', hc:'bg-gradient-to-r from-yellow-600 to-amber-700', ac:'bg-gradient-to-br from-yellow-500 to-amber-500', layout:['carousel', 'produk', 'kategori']},
        {id:11, name:'Ruby Red', hc:'bg-gradient-to-r from-red-600 to-rose-700', ac:'bg-gradient-to-br from-red-500 to-rose-500', layout:['video', 'kategori', 'produk']},
        {id:12, name:'Sky Blue', hc:'bg-gradient-to-r from-sky-400 to-blue-500', ac:'bg-gradient-to-br from-sky-300 to-blue-400', layout:['banner', 'carousel', 'produk']},
        {id:13, name:'Vintage Retro', hc:'bg-gradient-to-r from-stone-600 to-orange-800', ac:'bg-gradient-to-br from-stone-400 to-orange-300', layout:['kategori', 'produk', 'banner']},
        {id:14, name:'Sporty Active', hc:'bg-gradient-to-r from-yellow-400 to-yellow-500 text-slate-900', ac:'bg-gradient-to-br from-slate-800 to-black', layout:['video', 'produk', 'produk']},
        {id:15, name:'Lavender Magic', hc:'bg-gradient-to-r from-indigo-500 to-purple-600', ac:'bg-gradient-to-br from-indigo-400 to-purple-400', layout:['banner', 'kategori', 'produk']},
        {id:16, name:'Mint Fresh', hc:'bg-gradient-to-r from-teal-400 to-emerald-400', ac:'bg-gradient-to-br from-teal-300 to-emerald-300', layout:['carousel', 'kategori', 'produk']},
        {id:17, name:'Dark Maroon', hc:'bg-gradient-to-r from-rose-900 to-red-950', ac:'bg-gradient-to-br from-rose-800 to-red-800', layout:['video', 'banner', 'produk']},
        {id:18, name:'Silver Steel', hc:'bg-gradient-to-r from-slate-400 to-slate-600', ac:'bg-gradient-to-br from-slate-300 to-slate-400 text-slate-800', layout:['kategori', 'carousel', 'produk']},
        {id:19, name:'Peach Perfect', hc:'bg-gradient-to-r from-rose-400 to-orange-400', ac:'bg-gradient-to-br from-rose-300 to-orange-300', layout:['banner', 'produk', 'kategori']},
        {id:20, name:'Galaxy Night', hc:'bg-gradient-to-r from-indigo-900 to-fuchsia-900', ac:'bg-gradient-to-br from-indigo-500 to-fuchsia-500', layout:['carousel', 'video', 'produk']}
    ];
