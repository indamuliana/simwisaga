<x-layouts.app title="Pengaturan Persona AI">
 <div class="max-w-3xl mx-auto">
 <div class="bg-surface rounded-lg shadow-sm border border-border overflow-hidden">
 <div class="px-6 py-4 border-b border-border bg-surface-hover">
 <h3 class="text-lg font-medium text-text-main">Pengaturan Persona AI</h3>
 <p class="text-sm text-text-secondary mt-1">Konfigurasi nama, gaya bahasa, dan instruksi dasar untuk AI Assistant.</p>
 </div>

 <div class="p-6">
 @if (session('success'))
 <div class="mb-4 bg-success-soft text-green-700 p-3 rounded-md border border-success text-sm">
 {{ session('success') }}
 </div>
 @endif

 <form action="{{ route('ai-assistant.settings.update') }}" method="POST">
 @csrf

 <div class="space-y-5">
 <!-- Nama AI -->
 <div>
 <label for="name" class="block text-sm font-medium text-text-main mb-1">Nama AI</label>
 <input type="text" name="name" id="name" value="{{ old('name', $setting->name) }}" class="w-full border-border rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Misal: SIRAGA Assistant" required>
 @error('name')
 <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
 @enderror
 </div>

 <!-- Gaya Bahasa -->
 <div>
 <label for="style" class="block text-sm font-medium text-text-main mb-1">Gaya Bahasa (Persona)</label>
 <textarea name="style" id="style" rows="3" class="w-full border-border rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Misal: Formal, ramah, gunakan bahasa gaul, dll.">{{ old('style', $setting->style) }}</textarea>
 <p class="text-xs text-text-secondary mt-1">AI akan merespons pertanyaan dengan mengikuti gaya bahasa ini.</p>
 @error('style')
 <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
 @enderror
 </div>

 <!-- Prompt Dasar -->
 <div>
 <label for="base_prompt" class="block text-sm font-medium text-text-main mb-1">Instruksi Dasar (Base Prompt)</label>
 <textarea name="base_prompt" id="base_prompt" rows="4" class="w-full border-border rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Instruksi sistem utama untuk AI">{{ old('base_prompt', $setting->base_prompt) }}</textarea>
 <p class="text-xs text-text-secondary mt-1">Instruksi fundamental tentang peran AI dalam sistem ini.</p>
 @error('base_prompt')
 <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
 @enderror
 </div>
 </div>

 <div class="mt-6 flex justify-end">
 <button type="submit" class="btn btn-primary">
 Simpan Pengaturan
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
</x-layouts.app>
