<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Leitor de Boleto — SweetManager</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,.1);
            padding: 2rem;
            width: 100%;
            max-width: 560px;
        }

        h1 { font-size: 1.4rem; font-weight: 700; margin-bottom: .25rem; }
        p.sub { color: #666; font-size: .9rem; margin-bottom: 1.5rem; }

        /* Tabs */
        .tabs { display: flex; gap: .5rem; margin-bottom: 1.5rem; }
        .tab {
            flex: 1;
            padding: .5rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: #fafafa;
            cursor: pointer;
            font-size: .85rem;
            font-weight: 600;
            text-align: center;
            transition: all .15s;
            color: #555;
        }
        .tab:hover { border-color: #888; }
        .tab.active { border-color: #4f46e5; background: #eef2ff; color: #4f46e5; }

        .panel { display: none; }
        .panel.active { display: block; }

        /* Camera panel */
        #videoWrapper {
            position: relative;
            background: #000;
            border-radius: 8px;
            overflow: hidden;
            aspect-ratio: 4/3;
            margin-bottom: .75rem;
        }
        video { width: 100%; height: 100%; object-fit: cover; display: block; }
        canvas#snapshot { display: none; }
        .overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }
        .overlay-box {
            width: 75%;
            height: 40%;
            border: 3px solid rgba(255,255,255,.7);
            border-radius: 8px;
        }

        /* Upload drop zone */
        .dropzone {
            border: 2px dashed #c7c7c7;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: border-color .15s, background .15s;
            margin-bottom: .75rem;
        }
        .dropzone:hover, .dropzone.drag { border-color: #4f46e5; background: #f5f3ff; }
        .dropzone input { display: none; }
        .dropzone svg { margin-bottom: .5rem; color: #9ca3af; }
        .dropzone p { font-size: .9rem; color: #555; }
        .dropzone p strong { color: #4f46e5; }
        #preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            margin: .75rem auto 0;
            display: none;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .6rem 1.2rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: .9rem;
            cursor: pointer;
            transition: opacity .15s;
            width: 100%;
            justify-content: center;
        }
        .btn:disabled { opacity: .5; cursor: not-allowed; }
        .btn-primary { background: #4f46e5; color: #fff; }
        .btn-primary:hover:not(:disabled) { background: #4338ca; }
        .btn-secondary { background: #e5e7eb; color: #374151; margin-bottom: .5rem; }
        .btn-secondary:hover:not(:disabled) { background: #d1d5db; }

        /* Result */
        #result { margin-top: 1.5rem; }
        .result-card {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 1.25rem;
        }
        .result-card.error {
            background: #fef2f2;
            border-color: #fecaca;
        }
        .result-card h2 {
            font-size: 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: .4rem;
        }
        .field { margin-bottom: .75rem; }
        .field label {
            display: block;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6b7280;
            margin-bottom: .2rem;
        }
        .field value {
            display: block;
            font-size: 1rem;
            font-weight: 600;
            word-break: break-all;
        }
        .barcode-field value { font-size: .78rem; font-family: monospace; font-weight: 500; }

        /* Spinner */
        .spinner {
            width: 18px; height: 18px;
            border: 3px solid rgba(255,255,255,.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .hidden { display: none; }
    </style>
</head>
<body>
<div class="card">
    <h1>📄 Leitor de Boleto</h1>
    <p class="sub">Escaneie ou envie um boleto para extrair os dados automaticamente.</p>

    <!-- Source tabs -->
    <div class="tabs">
        <button class="tab active" data-tab="camera">📷 Câmera</button>
        <button class="tab" data-tab="image">🖼️ Imagem</button>
        <button class="tab" data-tab="pdf">📄 PDF</button>
    </div>

    <!-- ─── Camera panel ─────────────────────────── -->
    <div id="panel-camera" class="panel active">
        <div id="videoWrapper">
            <video id="video" autoplay playsinline muted></video>
            <div class="overlay"><div class="overlay-box"></div></div>
        </div>
        <canvas id="snapshot"></canvas>
        <button class="btn btn-secondary" id="btnStartCamera">▶ Iniciar câmera</button>
        <button class="btn btn-primary" id="btnCapture" disabled>📸 Capturar e ler</button>
    </div>

    <!-- ─── Image upload panel ───────────────────── -->
    <div id="panel-image" class="panel">
        <div class="dropzone" id="dropzone-image">
            <input type="file" id="inputImage" accept="image/jpeg,image/png,image/gif,image/webp" />
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M3 16.5V19a2 2 0 002 2h14a2 2 0 002-2v-2.5M16 9l-4-4m0 0L8 9m4-4v12"/>
            </svg>
            <p><strong>Clique para selecionar</strong> ou arraste aqui</p>
            <p>JPG, PNG, WEBP — máx 10 MB</p>
        </div>
        <img id="preview" alt="Pré-visualização" />
        <button class="btn btn-primary" id="btnSendImage" disabled>🔍 Ler boleto</button>
    </div>

    <!-- ─── PDF upload panel ─────────────────────── -->
    <div id="panel-pdf" class="panel">
        <div class="dropzone" id="dropzone-pdf">
            <input type="file" id="inputPdf" accept="application/pdf" />
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 12h6m-3-3v6M4 6h16M4 18h16M4 12h1M19 12h1"/>
            </svg>
            <p><strong>Clique para selecionar</strong> um PDF</p>
            <p id="pdfName" style="color:#4f46e5;font-weight:600;margin-top:.5rem"></p>
        </div>
        <button class="btn btn-primary" id="btnSendPdf" disabled>🔍 Ler boleto</button>
    </div>

    <!-- ─── Result area ───────────────────────────── -->
    <div id="result" class="hidden"></div>
</div>

<script>
// ─── Configuration ──────────────────────────────────────────
// Replace with your actual API URL and auth token
const API_URL   = '/api/boleto/read';
const API_TOKEN = localStorage.getItem('api_token') || '';

// ─── Tabs ────────────────────────────────────────────────────
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById('panel-' + tab.dataset.tab).classList.add('active');
        hideResult();
    });
});

// ─── Camera ──────────────────────────────────────────────────
const video      = document.getElementById('video');
const snapshot   = document.getElementById('snapshot');
const btnStart   = document.getElementById('btnStartCamera');
const btnCapture = document.getElementById('btnCapture');

let stream = null;

btnStart.addEventListener('click', async () => {
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 } }
        });
        video.srcObject = stream;
        btnStart.disabled = true;
        btnCapture.disabled = false;
    } catch (err) {
        showError('Não foi possível acessar a câmera: ' + err.message);
    }
});

btnCapture.addEventListener('click', async () => {
    if (!stream) return;
    snapshot.width  = video.videoWidth;
    snapshot.height = video.videoHeight;
    snapshot.getContext('2d').drawImage(video, 0, 0);

    snapshot.toBlob(async blob => {
        await sendFile(blob, 'capture.jpg', 'image/jpeg', btnCapture);
    }, 'image/jpeg', 0.92);
});

// ─── Image upload ─────────────────────────────────────────────
const inputImage   = document.getElementById('inputImage');
const dropzoneImg  = document.getElementById('dropzone-image');
const preview      = document.getElementById('preview');
const btnSendImage = document.getElementById('btnSendImage');
let selectedImage  = null;

dropzoneImg.addEventListener('click', () => inputImage.click());
setupDrop(dropzoneImg, handleImageFile);
inputImage.addEventListener('change', () => handleImageFile(inputImage.files[0]));

['dragover','dragenter'].forEach(e => dropzoneImg.addEventListener(e, ev => {
    ev.preventDefault(); dropzoneImg.classList.add('drag');
}));
['dragleave','drop'].forEach(e => dropzoneImg.addEventListener(e, () =>
    dropzoneImg.classList.remove('drag')));

function handleImageFile(file) {
    if (!file) return;
    selectedImage = file;
    preview.src = URL.createObjectURL(file);
    preview.style.display = 'block';
    btnSendImage.disabled = false;
}

btnSendImage.addEventListener('click', async () => {
    if (!selectedImage) return;
    await sendFile(selectedImage, selectedImage.name, selectedImage.type, btnSendImage);
});

// ─── PDF upload ───────────────────────────────────────────────
const inputPdf   = document.getElementById('inputPdf');
const dropzonePdf= document.getElementById('dropzone-pdf');
const pdfName    = document.getElementById('pdfName');
const btnSendPdf = document.getElementById('btnSendPdf');
let selectedPdf  = null;

dropzonePdf.addEventListener('click', () => inputPdf.click());
setupDrop(dropzonePdf, handlePdfFile);
inputPdf.addEventListener('change', () => handlePdfFile(inputPdf.files[0]));

function handlePdfFile(file) {
    if (!file) return;
    selectedPdf = file;
    pdfName.textContent = '📎 ' + file.name;
    btnSendPdf.disabled = false;
}

btnSendPdf.addEventListener('click', async () => {
    if (!selectedPdf) return;
    await sendFile(selectedPdf, selectedPdf.name, 'application/pdf', btnSendPdf);
});

// ─── Core: send file to API ───────────────────────────────────
async function sendFile(blob, filename, mimeType, triggerBtn) {
    const form = new FormData();
    form.append('file', blob, filename);

    setLoading(triggerBtn, true);
    hideResult();

    try {
        const res = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: form,
        });

        const json = await res.json();

        if (json.success) {
            showResult(json.data);
        } else {
            showError(json.error || 'Erro desconhecido ao processar o boleto.');
        }
    } catch (err) {
        showError('Falha na requisição: ' + err.message);
    } finally {
        setLoading(triggerBtn, false);
    }
}

// ─── UI helpers ───────────────────────────────────────────────
function showResult(data) {
    const result = document.getElementById('result');
    const amount   = data.amount   != null ? 'R$ ' + data.amount.toFixed(2).replace('.', ',') : '—';
    const dueDate  = data.due_date != null ? formatDate(data.due_date) : '—';
    const bank     = data.bank     || '—';

    result.className = '';
    result.innerHTML = `
        <div class="result-card">
            <h2>✅ Boleto lido com sucesso</h2>
            <div class="field">
                <label>Valor</label>
                <value>${amount}</value>
            </div>
            <div class="field">
                <label>Vencimento</label>
                <value>${dueDate}</value>
            </div>
            <div class="field">
                <label>Banco emissor</label>
                <value>${bank}</value>
            </div>
            <div class="field barcode-field">
                <label>Linha Digitável</label>
                <value>${data.linha_digitavel || '—'}</value>
            </div>
            <div class="field barcode-field">
                <label>Código de barras</label>
                <value>${data.barcode || '—'}</value>
            </div>
        </div>`;
}

function showError(msg) {
    const result = document.getElementById('result');
    result.className = '';
    result.innerHTML = `
        <div class="result-card error">
            <h2>❌ Falha ao ler o boleto</h2>
            <p>${msg}</p>
        </div>`;
}

function hideResult() {
    const result = document.getElementById('result');
    result.className = 'hidden';
    result.innerHTML = '';
}

function setLoading(btn, loading) {
    if (loading) {
        btn.dataset.origText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner"></span> Processando…';
        btn.disabled = true;
    } else {
        btn.innerHTML = btn.dataset.origText || btn.innerHTML;
        btn.disabled = false;
    }
}

function formatDate(iso) {
    const [y, m, d] = iso.split('-');
    return `${d}/${m}/${y}`;
}

function setupDrop(zone, handler) {
    zone.addEventListener('drop', ev => {
        ev.preventDefault();
        const file = ev.dataTransfer.files[0];
        if (file) handler(file);
    });
}
</script>
</body>
</html>
