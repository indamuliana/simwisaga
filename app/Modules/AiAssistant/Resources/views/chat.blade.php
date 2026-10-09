@php
    $sekolah = \App\Modules\Sistem\Models\School::current();
    $logoUrl = $sekolah->logoAplikasiUrl();
@endphp
<x-layouts.app title="Chat AI Assistant">
    <div class="flex flex-col h-[calc(100vh-64px)] -m-4 lg:-m-6 bg-bg overflow-hidden relative">
        
        <!-- Header -->
        <div class="bg-surface/80 backdrop-blur border-b border-border text-text-main px-6 py-4 flex items-center shrink-0 z-10">
            <div class="flex-shrink-0 bg-surface rounded-full p-1.5 mr-4 border border-border">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" class="w-7 h-7 object-contain rounded-full">
                @else
                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"></path>
                    </svg>
                @endif
            </div>
            <div>
                <h2 class="text-lg font-semibold tracking-wide">SIRAGA AI Assistant</h2>
                <p class="text-xs text-text-secondary mt-0.5">Siap membantu pengelolaan data sekolah Anda</p>
            </div>
        </div>

        <!-- Chat History -->
        <div id="chat-box" class="flex-1 overflow-y-auto px-4 lg:px-8 py-6 space-y-6 pb-40 scroll-smooth">
            <!-- Pesan Pembuka dari AI -->
            <div class="flex items-start gap-4 max-w-4xl mx-auto">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-surface border border-border flex items-center justify-center mt-1 overflow-hidden">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" class="w-full h-full object-contain p-1">
                    @else
                        <span class="text-primary text-xs font-bold">AI</span>
                    @endif
                </div>
                <div class="bg-transparent text-text-main pt-2 max-w-[85%] w-full">
                    <p class="text-sm leading-relaxed">Assalamualaikum Saya Sundari AI Assistant SMK Wikrama 1 Garut. apa yang ingin anda tahu ?</p>
                </div>
            </div>
        </div>

        <!-- Form Input (Floating) -->
        <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-bg via-bg/95 to-transparent pt-12 pb-6 px-4 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <form id="chat-form" class="relative flex items-end shadow-2xl rounded-2xl bg-surface border border-border/80 focus-within:border-primary/50 focus-within:ring-4 focus-within:ring-primary/10 transition-all">
                    <textarea id="chat-input" rows="1" class="w-full bg-transparent border-0 rounded-2xl px-5 py-4 focus:outline-none focus:ring-0 text-sm text-text-main placeholder-text-muted resize-none max-h-32" placeholder="Tanyakan sesuatu... (Shift+Enter untuk baris baru)" required></textarea>
                    <button type="submit" id="btn-send" class="absolute right-2 bottom-2.5 btn btn-primary w-10 h-10 rounded-xl p-0 flex items-center justify-center transition-transform active:scale-95">
                        <svg class="w-5 h-5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
                <div class="text-center mt-3">
                    <span class="text-[10px] text-text-muted">AI dapat membuat kesalahan. Harap periksa kembali informasi penting.</span>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const AI_LOGO_URL = @json($logoUrl);

        document.addEventListener('DOMContentLoaded', function () {
            const chatForm = document.getElementById('chat-form');
            const chatInput = document.getElementById('chat-input');
            const chatBox = document.getElementById('chat-box');
            const btnSend = document.getElementById('btn-send');

            // Auto-resize textarea
            chatInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
                if(this.value === '') this.style.height = 'auto';
            });

            // Enter to submit, Shift+Enter for new line
            chatInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if(this.value.trim() !== '') {
                        chatForm.dispatchEvent(new Event('submit'));
                    }
                }
            });

            chatForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                
                const message = chatInput.value.trim();
                if (!message) return;

                // 1. Tampilkan pesan user
                appendMessage(message, 'user');
                chatInput.value = '';
                chatInput.style.height = 'auto';
                
                // 2. Tampilkan indikator loading (typing...)
                const loadingId = showLoading();
                
                // Disable input saat loading
                chatInput.disabled = true;
                btnSend.disabled = true;

                try {
                    // 3. Kirim ke API dengan Fetch
                    const response = await fetch('{{ route('ai-assistant.chat.api') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message: message })
                    });

                    const data = await response.json();
                    
                    // 4. Hapus loading
                    removeLoading(loadingId);

                    // 5. Tampilkan balasan AI dengan efek mengetik
                    if (response.ok && data.success) {
                        await appendMessageWithTypingEffect(data.message);
                    } else {
                        appendMessage(data.message || 'Maaf, terjadi kesalahan saat menghubungi server.', 'error');
                    }
                } catch (error) {
                    removeLoading(loadingId);
                    appendMessage('Maaf, koneksi terputus atau terjadi kesalahan sistem.', 'error');
                } finally {
                    // Enable input kembali
                    chatInput.disabled = false;
                    btnSend.disabled = false;
                    chatInput.focus();
                }
            });

            function getAvatarHtml() {
                if (AI_LOGO_URL) {
                    return `<img src="${AI_LOGO_URL}" class="w-full h-full object-contain p-1">`;
                }
                return `<span class="text-primary text-xs font-bold">AI</span>`;
            }

            function appendMessage(text, sender) {
                const div = document.createElement('div');
                div.className = sender === 'user' ? 'flex justify-end max-w-4xl mx-auto w-full' : 'flex items-start gap-4 max-w-4xl mx-auto w-full';
                
                let innerContent = '';
                
                if (sender === 'user') {
                    const finalHtml = text.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, '<br>');
                    innerContent = `<div class="bg-primary/20 text-text-main rounded-2xl px-5 py-3 max-w-[80%] text-sm whitespace-pre-wrap leading-relaxed shadow-sm backdrop-blur-sm border border-primary/10">${finalHtml}</div>`;
                } else if (sender === 'error') {
                    innerContent = `
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-danger/10 border border-danger/20 flex items-center justify-center mt-1">
                            <span class="text-danger text-xs font-bold">!</span>
                        </div>
                        <div class="bg-danger-soft border border-danger/30 text-danger rounded-2xl px-5 py-3 max-w-[85%] text-sm whitespace-pre-wrap leading-relaxed">${text}</div>
                    `;
                } else {
                    const finalHtml = marked.parse(text);
                    innerContent = `
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-surface border border-border flex items-center justify-center mt-1 overflow-hidden">
                            ${getAvatarHtml()}
                        </div>
                        <div class="bg-transparent text-text-main pt-1.5 max-w-[85%] text-sm markdown-body w-full overflow-hidden">${finalHtml}</div>
                    `;
                }

                div.innerHTML = innerContent;
                chatBox.appendChild(div);
                chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
            }

            async function appendMessageWithTypingEffect(markdownText) {
                const finalHtml = marked.parse(markdownText);
                
                const div = document.createElement('div');
                div.className = 'flex items-start gap-4 max-w-4xl mx-auto w-full';
                
                div.innerHTML = `
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-surface border border-border flex items-center justify-center mt-1 overflow-hidden">
                        ${getAvatarHtml()}
                    </div>
                    <div class="bg-transparent text-text-main pt-1.5 max-w-[85%] text-sm markdown-body w-full overflow-hidden typing-container"></div>
                `;
                
                chatBox.appendChild(div);
                
                const container = div.querySelector('.typing-container');
                
                // Safe DOM-based typing effect to prevent breaking HTML tags
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = finalHtml;
                
                async function processNode(node, targetParent) {
                    if (node.nodeType === Node.TEXT_NODE) {
                        const text = node.nodeValue;
                        const textNode = document.createTextNode('');
                        targetParent.appendChild(textNode);
                        
                        for (let i = 0; i < text.length; i++) {
                            textNode.nodeValue += text[i];
                            chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'auto' });
                            // Faster typing for spaces, slight random delay for realism
                            await new Promise(r => setTimeout(r, text[i] === ' ' ? 2 : 5 + Math.random() * 10));
                        }
                    } else if (node.nodeType === Node.ELEMENT_NODE) {
                        const clone = node.cloneNode(false); // shallow clone (no children yet)
                        targetParent.appendChild(clone);
                        
                        // If it's a pre/code block, it might be better to just dump it immediately, but typing looks cool.
                        for (let child of Array.from(node.childNodes)) {
                            await processNode(child, clone);
                        }
                    }
                }
                
                // Disable input until typing finishes
                chatInput.disabled = true;
                btnSend.disabled = true;
                
                await processNode(tempDiv, container);
                
                chatInput.disabled = false;
                btnSend.disabled = false;
                chatInput.focus();
            }

            function showLoading() {
                const id = 'loading-' + Date.now();
                const div = document.createElement('div');
                div.id = id;
                div.className = 'flex items-start gap-4 max-w-4xl mx-auto w-full';
                div.innerHTML = `
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-surface border border-border flex items-center justify-center mt-1 overflow-hidden">
                        ${getAvatarHtml()}
                    </div>
                    <div class="bg-transparent pt-3 max-w-[85%] flex items-center space-x-1.5 opacity-60">
                        <div class="w-1.5 h-1.5 bg-text-secondary rounded-full animate-bounce"></div>
                        <div class="w-1.5 h-1.5 bg-text-secondary rounded-full animate-bounce" style="animation-delay: 0.15s"></div>
                        <div class="w-1.5 h-1.5 bg-text-secondary rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
                    </div>
                `;
                chatBox.appendChild(div);
                chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
                return id;
            }

            function removeLoading(id) {
                const el = document.getElementById(id);
                if (el) el.remove();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    @endpush
    
    @push('styles')
    <style>
        .markdown-body {
            line-height: 1.6;
        }
        .markdown-body strong {
            font-weight: 600;
            color: var(--color-text-main);
        }
        .markdown-body p {
            margin-bottom: 0.75rem;
        }
        .markdown-body p:last-child {
            margin-bottom: 0;
        }
        .markdown-body h1, .markdown-body h2, .markdown-body h3 {
            font-weight: 600;
            color: var(--color-text-main);
            margin-top: 1.25rem;
            margin-bottom: 0.75rem;
        }
        .markdown-body h1 { font-size: 1.25rem; }
        .markdown-body h2 { font-size: 1.125rem; }
        .markdown-body h3 { font-size: 1rem; }
        .markdown-body ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin-bottom: 0.75rem;
        }
        .markdown-body ol {
            list-style-type: decimal;
            padding-left: 1.5rem;
            margin-bottom: 0.75rem;
        }
        .markdown-body li {
            margin-bottom: 0.375rem;
        }
        .markdown-body pre {
            background-color: var(--color-surface-hover);
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin-bottom: 0.75rem;
            border: 1px solid var(--color-border);
        }
        .markdown-body code {
            font-family: monospace;
            background-color: var(--color-surface-hover);
            padding: 0.125rem 0.25rem;
            border-radius: 0.25rem;
            font-size: 0.875em;
        }
        .markdown-body pre code {
            background-color: transparent;
            padding: 0;
            font-size: 0.875rem;
        }
        
        /* Custom scrollbar for chatbox */
        #chat-box::-webkit-scrollbar {
            width: 6px;
        }
        #chat-box::-webkit-scrollbar-track {
            background: transparent;
        }
        #chat-box::-webkit-scrollbar-thumb {
            background-color: var(--color-border);
            border-radius: 20px;
        }
    </style>
    @endpush
</x-layouts.app>
