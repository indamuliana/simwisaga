<div class="mx-auto max-w-6xl space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold">Backup & Restore Database</h1>
            <p class="text-sm text-text-secondary">Kelola pencadangan dan pemulihan data sistem SIRAGA secara penuh atau per modul.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-success bg-success-soft px-4 py-3 text-sm text-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-lg border border-danger bg-danger-soft px-4 py-3 text-sm text-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- Grid 2 Kolom: Form Buat Backup & Upload Restore --}}
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Card Buat Backup --}}
        <div class="lg:col-span-2 card space-y-5">
            <h2 class="text-base font-semibold border-b border-border-base pb-3">Buat Cadangan Baru (Backup)</h2>

            <div class="space-y-4">
                <div>
                    <label class="label font-medium mb-1">Tipe Backup</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" value="full" wire:model.live="backupType" class="radio">
                            <span class="text-sm font-medium">Full Backup (Seluruh Database)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" value="partial" wire:model.live="backupType" class="radio">
                            <span class="text-sm font-medium">Backup Sebagian (Pilih Modul)</span>
                        </label>
                    </div>
                </div>

                @if ($backupType === 'full')
                    <div class="p-3 bg-surface-raised rounded-lg border border-border-base">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="includeFiles" class="checkbox">
                            <span class="text-sm">Sertakan berkas lampiran/upload (<code class="text-xs">storage/app/public</code>)</span>
                        </label>
                        <p class="text-xs text-text-muted mt-1 ml-6">Foto profil, template dokumen, dan berkas bukti fisik akan disertakan dalam arsip ZIP.</p>
                    </div>
                @else
                    <div class="space-y-2 p-3 bg-surface-raised rounded-lg border border-border-base">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Pilih Modul yang Dicadangkan:</span>
                            <div class="flex gap-2">
                                <button type="button" wire:click="selectAllModules" class="text-xs text-primary hover:underline">Pilih Semua</button>
                                <span class="text-xs text-text-muted">•</span>
                                <button type="button" wire:click="unselectAllModules" class="text-xs text-text-muted hover:underline">Batal Semua</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                            @foreach ($availableModules as $mod)
                                <label class="flex items-center gap-2 p-2 rounded hover:bg-surface-base cursor-pointer text-sm">
                                    <input type="checkbox" value="{{ $mod }}" wire:model="selectedModules" class="checkbox">
                                    <span>{{ $mod }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="pt-2">
                    <button type="button" wire:click="runBackup" wire:loading.attr="disabled" class="btn btn-primary">
                        <span wire:loading.remove wire:target="runBackup">Mulai Proses Backup</span>
                        <span wire:loading wire:target="runBackup">Sedang Memproses Backup...</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Card Upload File Restore --}}
        <div class="card space-y-4">
            <h2 class="text-base font-semibold border-b border-border-base pb-3">Pulihkan dari Berkas Komputer</h2>
            <p class="text-xs text-text-secondary">Punya file cadangan (.zip atau .sql) dari komputer atau server lain? Unggah berkas untuk dipulihkan ke sistem.</p>

            <form wire:submit="promptRestoreUpload" class="space-y-4">
                <div>
                    <label class="label text-xs">Pilih File (.zip / .sql)</label>
                    <input type="file" wire:model="uploadedFile" accept=".zip,.sql" class="file-input w-full text-xs">
                    @error('uploadedFile') <p class="error text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div wire:loading wire:target="uploadedFile" class="text-xs text-info">
                    Sedang mengunggah berkas...
                </div>

                <button type="submit" wire:loading.attr="disabled" class="btn btn-secondary w-full text-sm">
                    Upload & Restore
                </button>
            </form>
        </div>
    </div>

    {{-- Tabel Riwayat File Backup di Server --}}
    <div class="card space-y-4">
        <h2 class="text-base font-semibold">Riwayat Berkas Cadangan di Server</h2>

        <div class="overflow-x-auto">
            <table class="table w-full text-sm">
                <thead>
                    <tr class="border-b border-border-base text-left text-text-secondary text-xs uppercase">
                        <th class="py-3 px-4">Nama Berkas</th>
                        <th class="py-3 px-4">Tipe & Cakupan</th>
                        <th class="py-3 px-4">Ukuran</th>
                        <th class="py-3 px-4">Dibuat Oleh</th>
                        <th class="py-3 px-4">Waktu Dibuat</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-base">
                    @forelse ($backups as $item)
                        <tr class="hover:bg-surface-raised">
                            <td class="py-3 px-4 font-mono text-xs">
                                {{ $item->filename }}
                            </td>
                            <td class="py-3 px-4">
                                @if ($item->type === 'full')
                                    <span class="badge badge-primary text-xs">Full Backup</span>
                                    @if ($item->has_files)
                                        <span class="badge badge-info text-xs ml-1">+ Files</span>
                                    @endif
                                @else
                                    <span class="badge badge-warning text-xs">Sebagian</span>
                                    <div class="text-[11px] text-text-muted mt-1">
                                        {{ count($item->modules ?? []) }} modul
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-xs font-mono">
                                {{ $item->formatted_size }}
                            </td>
                            <td class="py-3 px-4 text-xs">
                                {{ $item->user?->name ?? 'Sistem' }}
                            </td>
                            <td class="py-3 px-4 text-xs text-text-secondary">
                                {{ $item->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <button type="button" wire:click="download({{ $item->id }})" class="btn btn-xs btn-ghost text-primary" title="Unduh Berkas">
                                    Unduh
                                </button>
                                <button type="button" wire:click="promptRestore({{ $item->id }})" class="btn btn-xs btn-ghost text-warning" title="Restore Data">
                                    Restore
                                </button>
                                <button type="button" wire:click="delete({{ $item->id }})" wire:confirm="Hapus file cadangan ini secara permanen?" class="btn btn-xs btn-ghost text-danger" title="Hapus">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-text-muted text-sm">
                                Belum ada riwayat berkas cadangan database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($backups->hasPages())
            <div class="pt-2">
                {{ $backups->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Konfirmasi Ketat Restore --}}
    @if ($showRestoreModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="card max-w-lg w-full bg-surface-base border border-danger/40 shadow-2xl space-y-4">
                <div class="flex items-center gap-3 text-danger border-b border-border-base pb-3">
                    <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <h3 class="text-lg font-bold">Konfirmasi Pemulihan (Restore)</h3>
                </div>

                <div class="text-sm space-y-2 text-text-secondary">
                    <p class="font-semibold text-danger">PERINGATAN KRUSIAL:</p>
                    <p>Proses pemulihan akan menimpa (overwrite) data database yang ada saat ini dengan data yang ada di dalam berkas cadangan. Data yang tidak ada dalam cadangan dapat hilang atau tertimpa.</p>
                    @if ($isRestoreFromUpload)
                        <p class="text-xs bg-surface-raised p-2 rounded">Sumber: <strong>Berkas Unggahan (Upload Komputer)</strong></p>
                    @else
                        <p class="text-xs bg-surface-raised p-2 rounded">Sumber: <strong>Berkas Cadangan di Server</strong></p>
                    @endif
                </div>

                @if (session('modal_error'))
                    <div class="p-3 bg-danger-soft border border-danger rounded text-xs text-danger">
                        {{ session('modal_error') }}
                    </div>
                @endif

                <div class="space-y-2">
                    <label class="label text-xs">Untuk melanjutkan, ketik kata sandi konfirmasi <span class="font-bold text-danger">RESTORE</span> di bawah ini:</label>
                    <input type="text" wire:model="confirmText" placeholder="Ketik RESTORE" class="input font-mono uppercase text-center font-bold tracking-widest">
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-border-base">
                    <button type="button" wire:click="cancelRestore" class="btn btn-ghost">
                        Batal
                    </button>
                    <button type="button" wire:click="executeRestore" wire:loading.attr="disabled" class="btn btn-danger">
                        <span wire:loading.remove wire:target="executeRestore">Lanjutkan Restore</span>
                        <span wire:loading wire:target="executeRestore">Sedang Me-restore...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
