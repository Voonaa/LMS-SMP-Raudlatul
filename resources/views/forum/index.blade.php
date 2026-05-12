@extends(auth()->user()->role == 'guru' ? 'layouts.guru' : 'layouts.siswa')

@section('title', 'Forum Diskusi - SMP Islam Raudlatul Hikmah')
@section('page_title', 'Forum Diskusi')

@section('content')
<div class="p-4 lg:p-margin max-w-5xl mx-auto" x-data="{ createModalOpen: false }">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Forum Diskusi</h2>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Ruang diskusi asinkron untuk siswa dan guru.</p>
        </div>
        <button @click="createModalOpen = true" class="bg-primary text-on-primary px-6 py-2 rounded-full font-label-lg text-label-lg hover:bg-primary-container transition-colors flex items-center gap-2 ambient-shadow">
            <span class="material-symbols-outlined">add</span>
            Buat Topik Baru
        </button>
    </div>

    <!-- Forum List -->
    <div class="space-y-4">
        @forelse($threads ?? [] as $thread)
        <div class="bg-surface-container-lowest rounded-xl p-6 ambient-shadow border border-surface-container flex gap-4" x-data="{ replyOpen: false }">
            <div class="flex flex-col items-center gap-1 min-w-[40px]" 
                 x-data="{ 
                     liked: {{ $thread->likes()->where('user_id', auth()->id())->exists() ? 'true' : 'false' }}, 
                     likesCount: {{ $thread->likes()->count() }},
                     async toggleLike() {
                         try {
                             const response = await fetch('{{ route('forum.like') }}', {
                                 method: 'POST',
                                 headers: {
                                     'Content-Type': 'application/json',
                                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                 },
                                 body: JSON.stringify({
                                     likeable_id: {{ $thread->id }},
                                     likeable_type: 'App\\Models\\ForumThread'
                                 })
                             });
                             const data = await response.json();
                             if(data.success) {
                                 this.liked = data.action === 'liked';
                                 this.likesCount = data.likes_count;
                             }
                         } catch (e) {
                             console.error('Error toggling like:', e);
                         }
                     }
                 }">
                <button @click="toggleLike()" :class="liked ? 'text-primary' : 'text-on-surface-variant'" class="p-1 rounded-full hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined" :style="liked ? 'font-variation-settings: \'FILL\' 1;' : 'font-variation-settings: \'FILL\' 0;'">thumb_up</span>
                </button>
                <span class="font-label-sm font-bold text-primary" x-text="likesCount"></span>
            </div>
            
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="font-label-sm text-label-sm text-on-surface font-semibold">{{ $thread->user->name }}</span>
                    <span class="text-on-surface-variant text-xs">•</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant font-normal">{{ $thread->created_at->diffForHumans() }}</span>
                    <span class="bg-primary-container/20 text-primary-container px-2 py-0.5 rounded-full text-xs font-semibold ml-2">{{ $thread->mata_pelajaran->nama_mapel ?? 'Umum' }}</span>
                </div>
                
                <h3 class="font-headline-md text-headline-md text-lg mb-2 text-on-surface">{{ $thread->judul }}</h3>
                <p class="font-body-md text-body-md text-on-surface-variant mb-4">{{ $thread->konten }}</p>
                
                <div class="flex items-center gap-4">
                    <button @click="replyOpen = !replyOpen" class="flex items-center gap-1.5 text-on-surface-variant hover:text-primary transition-colors font-label-sm text-label-sm">
                        <span class="material-symbols-outlined text-[18px]">chat_bubble</span> {{ $thread->replies->count() }} Balasan
                    </button>
                </div>
                
                <div class="mt-4 pt-4 border-t border-surface-container" x-show="replyOpen" style="display: none;">
                    @foreach($thread->replies as $reply)
                    <div class="mb-3 p-3 bg-surface-container-low rounded-lg">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-label-sm text-label-sm text-on-surface font-semibold">{{ $reply->user->name }}</span>
                            <span class="text-on-surface-variant text-xs">•</span>
                            <span class="font-label-sm text-label-sm text-on-surface-variant font-normal">{{ $reply->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="font-body-md text-body-md text-on-surface-variant text-sm">{{ $reply->konten }}</p>
                    </div>
                    @endforeach
                    
                    <form action="{{ route('forum.reply', $thread->id) }}" method="POST" class="mt-4">
                        @csrf
                        <div class="flex gap-3 mb-4">
                            <textarea name="konten" required class="w-full border border-outline-variant rounded-lg p-2 font-body-md text-body-md focus:border-primary focus:ring-1 focus:ring-primary bg-surface" placeholder="Tulis balasan Anda..." rows="2"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" @click="replyOpen = false" class="bg-surface-container text-on-surface px-4 py-1.5 rounded-full font-label-sm text-label-sm hover:bg-surface-container-high transition-colors mr-2">Batal</button>
                            <button type="submit" class="bg-primary text-on-primary px-4 py-1.5 rounded-full font-label-sm text-label-sm hover:bg-primary-container transition-colors">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-surface-container-lowest rounded-xl p-6 ambient-shadow text-center text-on-surface-variant">Belum ada diskusi</div>
        @endforelse
    </div>

    <!-- Create Topic Modal -->
    <div x-show="createModalOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div @click.outside="createModalOpen = false" class="bg-surface rounded-xl shadow-xl w-full max-w-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-headline-md text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined">forum</span> Buat Topik Baru
                </h3>
                <button @click="createModalOpen = false" class="text-on-surface-variant hover:text-error font-bold">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form action="{{ route('forum.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1">Mata Pelajaran</label>
                        <select name="mata_pelajaran_id" required class="w-full border border-outline-variant bg-surface rounded-lg p-2 focus:ring-primary focus:border-primary">
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach($mapelList ?? [] as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1">Judul Topik</label>
                        <input type="text" name="judul" required class="w-full border border-outline-variant bg-surface rounded-lg p-2 focus:ring-primary focus:border-primary" placeholder="Masukkan judul topik">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1">Konten</label>
                        <textarea name="konten" required rows="4" class="w-full border border-outline-variant bg-surface rounded-lg p-2 focus:ring-primary focus:border-primary" placeholder="Tuliskan isi topik diskusi..."></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 bg-surface-container text-on-surface rounded-lg font-medium">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg font-bold hover:bg-primary-container transition-colors">Kirim</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection