<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Leitor de Boleto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Tab Navigation --}}
                    <div class="flex gap-2 mb-6" id="tabs">
                        <button type="button" data-tab="file"
                            class="tab-btn flex-1 py-2 px-3 rounded-lg border-2 border-indigo-500 bg-indigo-50 text-indigo-700 font-semibold text-sm transition-all">
                            📁 Arquivo
                        </button>
                        <button type="button" data-tab="camera"
                            class="tab-btn flex-1 py-2 px-3 rounded-lg border-2 border-gray-200 bg-gray-50 text-gray-500 font-semibold text-sm transition-all hover:border-gray-400">
                            📷 Câmera
                        </button>
                    </div>

                    {{-- FILE TAB --}}
                    <div id="tab-file">
                        <label for="fileInput"
                            class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                            <span class="text-3xl mb-2">📂</span>
                            <span class="text-sm font-medium text-gray-600">Clique para selecionar</span>
                            <span class="text-xs text-gray-400 mt-1">JPG, PNG ou PDF — máx. 10 MB</span>
                        </label>
                        <input type="file" id="fileInput" accept="image/jpeg,image/png,application/pdf"
                            class="hidden" />
                        <p id="fileLabel" class="mt-2 text-sm text-gray-500 text-center hidden"></p>
                    </div>

                    {{-- CAMERA TAB --}}
                    <div id="tab-camera" class="hidden">
                        <div class="relative w-full rounded-lg overflow-hidden bg-black" style="aspect-ratio:4/3">
                            <video id="video" autoplay playsinline muted
                                class="w-full h-full object-cover"></video>
                            <canvas id="canvas" class="hidden absolute inset-0 w-full h-full"></canvas>
                        </div>
                        <div class="flex gap-3 mt-3">
                            <button type="button" id="btnCapture"
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition-colors">
                                📸 Capturar
                            </button>
                            <button type="button" id="btnRetake"
                                class="hidden flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm transition-colors">
                                🔄 Nova foto
                            </button>
                        </div>
                    </div>

                    {{-- Preview --}}
                    <div id="preview" class="mt-4 hidden">
                        <p class="text-xs text-gray-400 mb-1">Pré-visualização:</p>
                        <img id="previewImg" src="" alt="Preview"
                            class="max-h-48 rounded-lg border border-gray-200 object-contain w-full" />
                    </div>

                    {{-- Validation error --}}
                    <p id="validationError" class="mt-3 text-sm text-red-600 hidden"></p>

                    {{-- Send button --}}
                    <button type="button" id="btnSend"
                        class="mt-5 w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                        <span id="btnSendText">Enviar para análise</span>
                        <svg id="spinner" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </button>

                    {{-- Result --}}
                    <div id="result" class="mt-6 hidden">

                        {{-- Success --}}
                        <div id="resultSuccess" class="hidden rounded-lg border border-green-200 bg-green-50 p-5">
                            <h3 class="text-green-800 font-semibold mb-4 flex items-center gap-2">
                                ✅ Boleto lido com sucesso
                            </h3>
                            <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div class="bg-white rounded-lg p-3 border border-green-100">
                                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Valor</dt>
                                    <dd id="resAmount" class="mt-1 text-lg font-bold text-gray-900">—</dd>
                                </div>
                                <div class="bg-white rounded-lg p-3 border border-green-100">
                                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Vencimento</dt>
                                    <dd id="resDueDate" class="mt-1 text-lg font-bold text-gray-900">—</dd>
                                </div>
                                <div class="bg-white rounded-lg p-3 border border-green-100">
                                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Banco</dt>
                                    <dd id="resBank" class="mt-1 text-lg font-bold text-gray-900">—</dd>
                                </div>
                                <div class="bg-white rounded-lg p-3 border border-green-100 sm:col-span-2">
                                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Linha Digitável</dt>
                                    <dd id="resLinha" class="mt-1 font-mono text-sm text-gray-900 break-all">—</dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Error --}}
                        <div id="resultError" class="hidden rounded-lg border border-red-200 bg-red-50 p-5">
                            <h3 class="text-red-800 font-semibold flex items-center gap-2">⚠️ Não foi possível processar</h3>
                            <p id="resultErrorMsg" class="mt-2 text-sm text-red-700"></p>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('modals')
    <script>
    (() => {
        'use strict';

        /* ── Config ────────────────────────────────────────────── */
        const MAX_BYTES   = 10 * 1024 * 1024;           // 10 MB
        const ALLOWED     = ['image/jpeg', 'image/png', 'application/pdf'];
        const API_ENDPOINT = '/api/boleto/read';
        const TOKEN_KEY    = 'api_token';

        /* ── State ─────────────────────────────────────────────── */
        let activeTab    = 'file';
        let pendingFile  = null;   // File | Blob ready to send
        let stream       = null;   // MediaStream (camera)
        let captured     = false;  // whether camera photo was taken

        /* ── DOM refs ──────────────────────────────────────────── */
        const tabBtns        = document.querySelectorAll('.tab-btn');
        const tabFile        = document.getElementById('tab-file');
        const tabCamera      = document.getElementById('tab-camera');
        const fileInput      = document.getElementById('fileInput');
        const fileLabel      = document.getElementById('fileLabel');
        const video          = document.getElementById('video');
        const canvas         = document.getElementById('canvas');
        const btnCapture     = document.getElementById('btnCapture');
        const btnRetake      = document.getElementById('btnRetake');
        const previewWrap    = document.getElementById('preview');
        const previewImg     = document.getElementById('previewImg');
        const validationError= document.getElementById('validationError');
        const btnSend        = document.getElementById('btnSend');
        const btnSendText    = document.getElementById('btnSendText');
        const spinner        = document.getElementById('spinner');
        const result         = document.getElementById('result');
        const resultSuccess  = document.getElementById('resultSuccess');
        const resultError    = document.getElementById('resultError');
        const resultErrorMsg = document.getElementById('resultErrorMsg');

        /* ── Helpers ───────────────────────────────────────────── */
        function showValidation(msg) {
            validationError.textContent = msg;
            validationError.classList.toggle('hidden', !msg);
        }

        function setLoading(on) {
            btnSend.disabled = on;
            btnSendText.textContent = on ? 'Processando...' : 'Enviar para análise';
            spinner.classList.toggle('hidden', !on);
        }

        function showPreview(src) {
            previewImg.src = src;
            previewWrap.classList.remove('hidden');
        }

        function clearResult() {
            result.classList.add('hidden');
            resultSuccess.classList.add('hidden');
            resultError.classList.add('hidden');
        }

        function validateFile(file) {
            if (!file) return 'Nenhum arquivo selecionado.';
            if (!ALLOWED.includes(file.type)) return 'Formato inválido. Use JPG, PNG ou PDF.';
            if (file.size > MAX_BYTES) return 'Arquivo muito grande. Máximo 10 MB.';
            return null;
        }

        /* ── Tabs ──────────────────────────────────────────────── */
        function switchTab(tab) {
            activeTab = tab;
            pendingFile = null;
            clearResult();
            showValidation('');
            previewWrap.classList.add('hidden');

            tabBtns.forEach(btn => {
                const active = btn.dataset.tab === tab;
                btn.classList.toggle('border-indigo-500', active);
                btn.classList.toggle('bg-indigo-50', active);
                btn.classList.toggle('text-indigo-700', active);
                btn.classList.toggle('border-gray-200', !active);
                btn.classList.toggle('bg-gray-50', !active);
                btn.classList.toggle('text-gray-500', !active);
            });

            tabFile.classList.toggle('hidden', tab !== 'file');
            tabCamera.classList.toggle('hidden', tab !== 'camera');

            if (tab === 'camera') {
                startCamera();
            } else {
                stopCamera();
                resetCapture();
            }
        }

        tabBtns.forEach(btn => btn.addEventListener('click', () => switchTab(btn.dataset.tab)));

        /* ── File input ────────────────────────────────────────── */
        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (!file) return;

            const err = validateFile(file);
            if (err) { showValidation(err); pendingFile = null; return; }

            showValidation('');
            pendingFile = file;
            fileLabel.textContent = `${file.name} (${(file.size / 1024).toFixed(0)} KB)`;
            fileLabel.classList.remove('hidden');

            if (file.type !== 'application/pdf') {
                showPreview(URL.createObjectURL(file));
            } else {
                previewImg.src = '';
                previewWrap.classList.add('hidden');
            }
        });

        /* ── Camera ────────────────────────────────────────────── */
        async function startCamera() {
            captured = false;
            canvas.classList.add('hidden');
            video.classList.remove('hidden');
            btnCapture.classList.remove('hidden');
            btnRetake.classList.add('hidden');

            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: { ideal: 'environment' } }
                });
                video.srcObject = stream;
            } catch (e) {
                showValidation('Câmera não acessível: ' + (e.message || e));
            }
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(t => t.stop());
                stream = null;
            }
            video.srcObject = null;
        }

        function resetCapture() {
            captured = false;
            canvas.classList.add('hidden');
            video.classList.remove('hidden');
            btnCapture.classList.remove('hidden');
            btnRetake.classList.add('hidden');
        }

        btnCapture.addEventListener('click', () => {
            if (!stream) return;
            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            video.classList.add('hidden');
            canvas.classList.remove('hidden');
            btnCapture.classList.add('hidden');
            btnRetake.classList.remove('hidden');
            captured = true;

            canvas.toBlob(blob => {
                if (!blob) return;
                pendingFile = new File([blob], 'captura.jpg', { type: 'image/jpeg' });
                showPreview(canvas.toDataURL('image/jpeg'));
                showValidation('');
            }, 'image/jpeg', 0.92);
        });

        btnRetake.addEventListener('click', () => {
            pendingFile = null;
            previewWrap.classList.add('hidden');
            resetCapture();
            startCamera();
        });

        /* ── Send ──────────────────────────────────────────────── */
        btnSend.addEventListener('click', async () => {
            showValidation('');
            clearResult();

            const err = validateFile(pendingFile);
            if (err) { showValidation(err); return; }

            const formData = new FormData();
            formData.append('file', pendingFile);

            const headers = { 'X-Requested-With': 'XMLHttpRequest' };
            const token   = localStorage.getItem(TOKEN_KEY);
            if (token) headers['Authorization'] = `Bearer ${token}`;

            setLoading(true);
            try {
                const resp = await fetch(API_ENDPOINT, {
                    method: 'POST',
                    headers,
                    body: formData,
                    credentials: 'same-origin',   // sends Laravel session cookie
                });

                let data;
                try { data = await resp.json(); } catch { data = {}; }

                result.classList.remove('hidden');

                if (resp.ok) {
                    document.getElementById('resAmount').textContent =
                        data.amount   ?? data.valor    ?? '—';
                    document.getElementById('resDueDate').textContent =
                        data.due_date ?? data.vencimento ?? '—';
                    document.getElementById('resBank').textContent =
                        data.bank     ?? data.banco    ?? '—';
                    document.getElementById('resLinha').textContent =
                        data.linha_digitavel ?? '—';

                    resultSuccess.classList.remove('hidden');
                } else {
                    const msg = data.message ?? data.error ?? `Erro ${resp.status}. Tente novamente.`;
                    resultErrorMsg.textContent = msg;
                    resultError.classList.remove('hidden');
                }

            } catch (e) {
                result.classList.remove('hidden');
                resultErrorMsg.textContent = 'Falha de rede. Verifique sua conexão e tente novamente.';
                resultError.classList.remove('hidden');
            } finally {
                setLoading(false);
            }
        });

    })();
    </script>
    @endpush

</x-app-layout>