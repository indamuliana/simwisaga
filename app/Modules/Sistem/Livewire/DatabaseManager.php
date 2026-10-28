<?php

namespace App\Modules\Sistem\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Sistem\Models\DatabaseBackup;
use App\Modules\Sistem\Services\DatabaseBackupService;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Backup & Restore Database')]
class DatabaseManager extends Component
{
    use GuardsPermission, WithFileUploads, WithPagination;

    // State Form Backup
    public string $backupType = 'full'; // 'full' atau 'partial'
    public array $selectedModules = [];
    public bool $includeFiles = true;
    public bool $isProcessing = false;

    // State Upload & Restore
    public $uploadedFile;
    public string $confirmText = '';
    public ?int $selectedBackupIdForRestore = null;
    public bool $isRestoreFromUpload = false;
    public bool $showRestoreModal = false;

    // Available modules
    public array $availableModules = [];

    public function mount(DatabaseBackupService $backupService): void
    {
        $this->availableModules = $backupService->getAvailableModules();
        $this->selectedModules = $this->availableModules;
    }

    public function selectAllModules(): void
    {
        $this->selectedModules = $this->availableModules;
    }

    public function unselectAllModules(): void
    {
        $this->selectedModules = [];
    }

    public function runBackup(DatabaseBackupService $backupService): void
    {
        $this->guard('sistem.database.backup');

        if ($this->backupType === 'partial' && empty($this->selectedModules)) {
            session()->flash('error', 'Pilih minimal satu modul untuk melakukan backup sebagian.');
            return;
        }

        try {
            $data = $backupService->createBackup(
                type: $this->backupType,
                selectedModules: $this->selectedModules,
                includeFiles: $this->backupType === 'full' ? $this->includeFiles : false,
                userId: auth()->id()
            );

            DatabaseBackup::create($data);

            session()->flash('success', "Backup berhasil dibuat: {$data['filename']}");
            $this->resetPage();
        } catch (Exception $e) {
            session()->flash('error', "Gagal melakukan backup: " . $e->getMessage());
        }
    }

    public function download(int $id)
    {
        $this->guard('sistem.database.lihat');

        $backup = DatabaseBackup::findOrFail($id);
        $fullPath = storage_path('app/' . $backup->filepath);

        if (!File::exists($fullPath)) {
            session()->flash('error', "Berkas fisik backup tidak ditemukan di server.");
            return;
        }

        return response()->download($fullPath, $backup->filename);
    }

    public function delete(int $id): void
    {
        $this->guard('sistem.database.hapus');

        $backup = DatabaseBackup::findOrFail($id);
        $fullPath = storage_path('app/' . $backup->filepath);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }

        $backup->delete();
        session()->flash('success', "Berkas backup '{$backup->filename}' berhasil dihapus.");
    }

    public function promptRestore(int $id): void
    {
        $this->guard('sistem.database.restore');
        $this->selectedBackupIdForRestore = $id;
        $this->isRestoreFromUpload = false;
        $this->confirmText = '';
        $this->showRestoreModal = true;
    }

    public function promptRestoreUpload(): void
    {
        $this->guard('sistem.database.restore');
        $this->validate([
            'uploadedFile' => 'required|file|max:512000', // maks 500MB
        ], [
            'uploadedFile.required' => 'Pilih file backup (.zip / .sql) terlebih dahulu.',
        ]);

        $this->selectedBackupIdForRestore = null;
        $this->isRestoreFromUpload = true;
        $this->confirmText = '';
        $this->showRestoreModal = true;
    }

    public function cancelRestore(): void
    {
        $this->showRestoreModal = false;
        $this->confirmText = '';
        $this->selectedBackupIdForRestore = null;
        $this->isRestoreFromUpload = false;
    }

    public function executeRestore(DatabaseBackupService $backupService): void
    {
        $this->guard('sistem.database.restore');

        if (trim(strtoupper($this->confirmText)) !== 'RESTORE') {
            session()->flash('modal_error', 'Kata kunci konfirmasi salah. Harap ketik RESTORE dengan tepat.');
            return;
        }

        try {
            $filePath = null;

            if ($this->isRestoreFromUpload) {
                if (!$this->uploadedFile) {
                    throw new Exception("Berkas unggahan tidak ditemukan.");
                }
                $filePath = $this->uploadedFile->getRealPath();
            } else {
                $backup = DatabaseBackup::findOrFail($this->selectedBackupIdForRestore);
                $filePath = storage_path('app/' . $backup->filepath);
            }

            $result = $backupService->restoreBackup($filePath);

            $this->showRestoreModal = false;
            $this->uploadedFile = null;
            $this->confirmText = '';

            session()->flash('success', $result['message'] ?? 'Database berhasil dipulihkan!');
        } catch (Exception $e) {
            session()->flash('modal_error', 'Restore gagal: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $this->guard('sistem.database.lihat');

        $backups = DatabaseBackup::with('user')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('sistem::livewire.database-manager', [
            'backups' => $backups,
        ]);
    }
}
