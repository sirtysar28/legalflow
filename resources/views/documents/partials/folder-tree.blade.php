<ul class="list-unstyled mb-0 @if($level > 0) ps-4 border-start @endif">
    @foreach ($folders as $folder)
        <li class="mb-1">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if ($level > 0)<i class="bi bi-arrow-return-right text-muted small"></i>@endif
                <i class="bi {{ $folder->children->isNotEmpty() ? 'bi-folder-fill text-warning' : 'bi-folder text-secondary' }}"></i>
                <span class="fw-semibold small">{{ $folder->name }}</span>
                <span class="text-muted" style="font-size:.72rem">
                    dibuat {{ $folder->created_at->format('d M Y') }} · {{ $folder->creator?->name ?? 'sistem' }}
                </span>
                <button class="btn btn-sm btn-outline-secondary py-0 px-2" type="button"
                        data-bs-toggle="collapse" data-bs-target="#renameFolder{{ $folder->id }}">
                    <i class="bi bi-pencil small"></i>
                </button>
                <form method="POST" action="{{ route('documents.folders.destroy', $folder) }}" class="d-inline"
                      onsubmit="return confirm('Hapus folder &quot;{{ $folder->name }}&quot;? Sub-folder akan naik menjadi folder utama.')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger py-0 px-2"><i class="bi bi-trash small"></i></button>
                </form>
            </div>

            {{-- Form rename --}}
            <div class="collapse mt-1" id="renameFolder{{ $folder->id }}">
                <form method="POST" action="{{ route('documents.folders.update', $folder) }}" class="input-group input-group-sm" style="max-width: 380px;">
                    @csrf @method('PUT')
                    <input type="text" name="name" value="{{ $folder->name }}" class="form-control" required>
                    <button class="btn btn-outline-primary">Simpan Nama</button>
                </form>
            </div>

            @if ($folder->children->isNotEmpty())
                @include('documents.partials.folder-tree', ['folders' => $folder->children, 'level' => $level + 1])
            @endif
        </li>
    @endforeach
</ul>
