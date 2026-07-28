<!doctype html>
<html lang="en" data-theme="{{ $config->renderer()->get('theme', 'light') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="color-scheme" content="{{ $config->renderer()->get('theme', 'light') }}">
    <title>{{ $config->get('ui.title') ?? config('app.name') . ' - API Docs' }}</title>

    <script src="https://unpkg.com/@stoplight/elements@8.4.2/web-components.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/@stoplight/elements@8.4.2/styles.min.css">

    <script>
        const originalFetch = window.fetch;

        /**
         * Token-aware fetch interceptor.
         * 1. Adds XSRF-TOKEN header for CSRF protection (Sanctum SPA).
         * 2. Adds Authorization: Bearer <token> from localStorage for every /api/* request.
         * 3. After a successful login, auto-saves the returned token.
         * 4. After a successful logout, auto-removes the stored token.
         */
        function _setHeader(headers, key, value) {
            if (headers instanceof Headers) {
                headers.set(key, value);
            } else if (Array.isArray(headers)) {
                headers.push([key, value]);
            } else if (headers && typeof headers === 'object') {
                headers[key] = value;
            }
        }

        function _getApiToken() {
            return localStorage.getItem('api_token');
        }

        function _saveApiToken(token) {
            localStorage.setItem('api_token', token);
            // Notify the status widget if available
            if (typeof checkSession === 'function') setTimeout(checkSession, 200);
        }

        function _clearApiToken() {
            localStorage.removeItem('api_token');
            if (typeof checkSession === 'function') setTimeout(checkSession, 200);
        }

        window.fetch = async (url, options = {}) => {
            // ── 1. Clone / normalise headers ──────────────────────────────────
            let headers = options.headers
                ? (options.headers instanceof Headers
                    ? new Headers(options.headers)
                    : (Array.isArray(options.headers)
                        ? new Headers(options.headers)
                        : new Headers(options.headers)))
                : new Headers();

            const urlStr = typeof url === 'string' ? url : (url?.url ?? '');

            // ── 2. CSRF token (Sanctum SPA cookie) ────────────────────────────
            const csrfCookie = document.cookie.split(';')
                .find(c => c.trim().startsWith('XSRF-TOKEN'));
            if (csrfCookie) {
                _setHeader(headers, 'X-XSRF-TOKEN', decodeURIComponent(csrfCookie.split('=')[1]));
            }

            // ── 3. Bearer token (Sanctum token auth) ──────────────────────────
            const apiToken = _getApiToken();
            if (apiToken && urlStr.includes('/api/')) {
                _setHeader(headers, 'Authorization', 'Bearer ' + apiToken);
            }

            // ── 4. Call the real fetch ─────────────────────────────────────────
            const response = await originalFetch(url, { ...options, headers });

            // ── 5. Auto-capture token after login ─────────────────────────────
            if (urlStr.includes('/api/auth/login') && response.ok) {
                try {
                    const clone = response.clone();
                    const json  = await clone.json();
                    const token = json?.data?.token ?? json?.token ?? null;
                    if (token) _saveApiToken(token);
                } catch (_) {}
            }

            // ── 6. Auto-clear token after logout ──────────────────────────────
            if (urlStr.includes('/api/auth/logout') && response.ok) {
                _clearApiToken();
            }

            return response;
        };
    </script>


    <style>
        html, body { margin:0; height:100%; }
        body { background-color: var(--color-canvas); }

        /* Language Toggle Button */
        #lang-toggle {
            position: fixed;
            top: 12px;
            right: 16px;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 0;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 8px;
            overflow: hidden;
            backdrop-filter: blur(8px);
            box-shadow: 0 2px 12px rgba(0,0,0,0.3);
        }
        /* On mobile: hide label text, only show icon + ID/EN buttons */
        @media (max-width: 768px) {
            #lang-toggle .lang-label { display: none; }
            #lang-toggle .lang-icon { padding: 0 4px 0 8px; }
        }
        .lang-icon {
            padding: 0 6px 0 10px;
            font-size: 14px;
            line-height: 1;
            opacity: 0.8;
        }
        .lang-label {
            font-size: 12px;
            font-weight: 500;
            font-family: system-ui, sans-serif;
            color: rgba(255,255,255,0.7);
            padding-right: 8px;
            white-space: nowrap;
        }
        #lang-toggle button {
            padding: 6px 11px;
            font-size: 11px;
            font-weight: 700;
            font-family: system-ui, sans-serif;
            border: none;
            background: transparent;
            cursor: pointer;
            color: rgba(255,255,255,0.35);
            transition: all 0.15s ease;
            letter-spacing: 0.05em;
        }
        #lang-toggle button.active {
            background: rgba(255,255,255,0.16);
            color: #fff;
        }
        #lang-toggle button:hover:not(.active) {
            color: rgba(255,255,255,0.75);
            background: rgba(255,255,255,0.06);
        }
        #lang-toggle .divider {
            width: 1px;
            height: 18px;
            background: rgba(255,255,255,0.12);
        }
        /* ── Session Status Widget ─────────────────── */
        #session-status {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            background: rgba(18, 18, 24, 0.85);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 12px 14px;
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 24px rgba(0,0,0,0.4);
            font-family: system-ui, sans-serif;
            min-width: 200px;
            max-width: 260px;
            transition: opacity 0.2s;
        }
        #session-status .ss-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        #session-status .ss-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
            background: #555;
            transition: background 0.3s;
        }
        #session-status .ss-dot.online  { background: #2ecc71; box-shadow: 0 0 6px #2ecc7180; }
        #session-status .ss-dot.offline { background: #e74c3c; box-shadow: 0 0 6px #e74c3c80; }
        #session-status .ss-dot.loading { background: #f39c12; animation: pulse-dot 1s infinite; }
        @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:0.3} }
        #session-status .ss-label {
            font-size: 12px;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #session-status .ss-sub {
            font-size: 11px;
            color: rgba(255,255,255,0.45);
            margin-top: 4px;
            line-height: 1.4;
        }
        #session-status .ss-hint {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(255,255,255,0.08);
            font-size: 10.5px;
            color: rgba(255,255,255,0.4);
            line-height: 1.5;
        }
        #session-status .ss-hint code {
            color: #7ec8e3;
            background: rgba(126,200,227,0.1);
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 10px;
        }
        #ss-refresh {
            margin-top: 8px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 6px;
            color: rgba(255,255,255,0.4);
            font-size: 10.5px;
            padding: 4px 8px;
            cursor: pointer;
            width: 100%;
            transition: all 0.15s;
        }
        #ss-refresh:hover { background: rgba(255,255,255,0.12); color: #fff; }
        #ss-open-panel {
            margin-top: 6px; width: 100%;
            background: linear-gradient(135deg,rgba(99,179,237,.12),rgba(118,82,252,.12));
            border: 1px solid rgba(99,179,237,.35); color: #63b3ed;
            border-radius: 6px; padding: 5px 10px; font-size: 11px; font-weight: 600;
            cursor: pointer; transition: all .15s; display: none; font-family: system-ui,sans-serif;
        }
        #ss-open-panel:hover { background: linear-gradient(135deg,rgba(99,179,237,.22),rgba(118,82,252,.22)); }

        /* ── Interactive Dashboard Panel ─────────────── */
        #ip-backdrop {
            display:none; position:fixed; inset:0; z-index:9998;
            background:rgba(0,0,0,.55); backdrop-filter:blur(3px);
        }
        #interactive-panel {
            position:fixed; top:0; right:-440px; width:420px; max-width:100vw;
            height:100vh; z-index:9999;
            background:rgba(11,11,17,.97);
            border-left:1px solid rgba(255,255,255,.08);
            box-shadow:-12px 0 60px rgba(0,0,0,.7);
            transition:right .35s cubic-bezier(.4,0,.2,1);
            display:flex; flex-direction:column; overflow:hidden;
            font-family:system-ui,sans-serif;
        }
        #interactive-panel.open { right:0; }
        .ip-header {
            display:flex; align-items:center; justify-content:space-between;
            padding:14px 16px; border-bottom:1px solid rgba(255,255,255,.07); flex-shrink:0;
        }
        .ip-title { display:flex; align-items:center; gap:8px; font-size:13px; font-weight:700; color:#fff; }
        .ip-user-chip {
            background:rgba(99,179,237,.15); color:#63b3ed;
            padding:2px 8px; border-radius:20px; font-size:10.5px; font-weight:500;
        }
        .ip-close {
            background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1);
            color:rgba(255,255,255,.5); border-radius:6px; padding:4px 9px;
            cursor:pointer; font-size:13px; transition:all .15s;
        }
        .ip-close:hover { background:rgba(255,80,80,.15); color:#ff6b6b; border-color:rgba(255,80,80,.3); }
        .ip-nav { display:flex; padding:10px 14px 0; gap:4px; border-bottom:1px solid rgba(255,255,255,.07); flex-shrink:0; }
        .ip-tab {
            background:transparent; border:none; border-bottom:2px solid transparent;
            color:rgba(255,255,255,.4); padding:6px 12px 8px; font-size:12px; font-weight:500;
            cursor:pointer; transition:all .15s; margin-bottom:-1px; font-family:system-ui,sans-serif;
        }
        .ip-tab:hover { color:rgba(255,255,255,.7); }
        .ip-tab.active { color:#63b3ed; border-bottom-color:#63b3ed; }
        .ip-content {
            flex:1; overflow-y:auto; padding:12px;
            scrollbar-width:thin; scrollbar-color:rgba(255,255,255,.1) transparent;
        }
        .ip-card {
            background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.07);
            border-radius:10px; padding:11px 13px; margin-bottom:7px; transition:border-color .15s;
        }
        .ip-card:hover { border-color:rgba(255,255,255,.14); }
        .ip-card-header { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; }
        .ip-card-title { font-size:13px; font-weight:600; color:#e2e8f0; flex:1; word-break:break-word; }
        .ip-card-sub { font-size:10.5px; color:rgba(255,255,255,.3); margin-top:3px; }
        .ip-card-actions { display:flex; gap:4px; flex-shrink:0; }
        .ip-btn {
            padding:4px 9px; border-radius:6px; border:1px solid transparent;
            font-size:11px; font-weight:500; cursor:pointer; transition:all .15s; font-family:system-ui,sans-serif;
        }
        .ip-btn-ghost { background:rgba(255,255,255,.06); border-color:rgba(255,255,255,.1); color:rgba(255,255,255,.55); }
        .ip-btn-ghost:hover { background:rgba(255,255,255,.12); color:#fff; }
        .ip-btn-danger { background:rgba(220,50,50,.1); border-color:rgba(220,50,50,.3); color:#fc8181; }
        .ip-btn-danger:hover { background:rgba(220,50,50,.2); }
        .ip-btn-primary { background:rgba(99,179,237,.12); border-color:rgba(99,179,237,.4); color:#63b3ed; }
        .ip-btn-primary:hover { background:rgba(99,179,237,.22); }
        .ip-btn-success { background:rgba(72,199,142,.12); border-color:rgba(72,199,142,.4); color:#48c78e; }
        .ip-btn-success:hover { background:rgba(72,199,142,.22); }
        .ip-btn-block { width:100%; padding:6px; font-size:12px; }
        .ip-add-btn {
            width:100%; padding:7px; margin-bottom:8px;
            background:rgba(99,179,237,.06); border:1px dashed rgba(99,179,237,.3);
            border-radius:8px; color:rgba(99,179,237,.7); font-size:11.5px;
            cursor:pointer; transition:all .15s; font-family:system-ui,sans-serif;
        }
        .ip-add-btn:hover { background:rgba(99,179,237,.12); color:#63b3ed; }
        .ip-form-box {
            background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.08);
            border-radius:8px; padding:12px; margin-bottom:8px;
        }
        .ip-field { margin-bottom:7px; }
        .ip-field label { display:block; font-size:10px; color:rgba(255,255,255,.35); margin-bottom:3px; letter-spacing:.04em; text-transform:uppercase; }
        .ip-field input,.ip-field textarea,.ip-field select {
            width:100%; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1);
            border-radius:6px; color:#e2e8f0; padding:6px 9px; font-size:12px;
            font-family:system-ui,sans-serif; box-sizing:border-box; outline:none; transition:border-color .15s;
        }
        .ip-field input:focus,.ip-field textarea:focus,.ip-field select:focus { border-color:rgba(99,179,237,.5); }
        .ip-field textarea { resize:vertical; min-height:52px; }
        .ip-field select option { background:#1a1a2e; }
        .ip-form-row { display:flex; gap:6px; }
        .ip-form-row .ip-field { flex:1; }
        .ip-form-actions { display:flex; gap:5px; margin-top:8px; }
        .ip-badge {
            display:inline-block; padding:2px 7px; border-radius:10px;
            font-size:9.5px; font-weight:700; letter-spacing:.04em;
        }
        .ip-badge-pending { background:rgba(237,137,54,.15); color:#ed8936; }
        .ip-badge-in_progress { background:rgba(99,179,237,.15); color:#63b3ed; }
        .ip-badge-completed { background:rgba(72,199,142,.15); color:#48c78e; }
        .ip-tasks { margin-top:9px; padding-top:9px; border-top:1px solid rgba(255,255,255,.05); }
        .ip-task-item { display:flex; align-items:flex-start; gap:7px; padding:6px 0; border-bottom:1px solid rgba(255,255,255,.04); }
        .ip-task-item:last-child { border-bottom:none; }
        .ip-task-title { flex:1; font-size:12px; color:#cbd5e0; word-break:break-word; }
        .ip-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; display:inline-block; border:1.5px solid rgba(255,255,255,.2); }
        .ip-user-row { display:flex; align-items:center; gap:10px; padding:9px 0; border-bottom:1px solid rgba(255,255,255,.05); }
        .ip-user-row:last-child { border-bottom:none; }
        .ip-avatar { width:28px; height:28px; border-radius:50%; background:rgba(99,179,237,.2); color:#63b3ed; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0; }
        .ip-loading { text-align:center; padding:28px 0; color:rgba(255,255,255,.2); font-size:12px; }
        .ip-empty { text-align:center; padding:24px 0; color:rgba(255,255,255,.2); font-size:12px; }
        .ip-section { font-size:10px; font-weight:700; color:rgba(255,255,255,.22); letter-spacing:.1em; text-transform:uppercase; margin:10px 0 5px; }
        #ip-toast {
            position:fixed; bottom:82px; right:24px; z-index:99999;
            padding:9px 15px; border-radius:8px; font-size:12px;
            font-family:system-ui,sans-serif; font-weight:500;
            opacity:0; transform:translateY(8px); transition:all .25s; pointer-events:none;
        }
        #ip-toast.show { opacity:1; transform:translateY(0); }
        #ip-toast.ok { background:rgba(72,199,142,.2); border:1px solid rgba(72,199,142,.4); color:#48c78e; }
        #ip-toast.err { background:rgba(220,50,50,.2); border:1px solid rgba(220,50,50,.4); color:#fc8181; }

        /* issues about the dark theme of stoplight/mosaic-code-viewer using web component:
         * https://github.com/stoplightio/elements/issues/2188#issuecomment-1485461965
         */
        [data-theme="dark"] .token.property {
            color: rgb(128, 203, 196) !important;
        }
        [data-theme="dark"] .token.operator {
            color: rgb(255, 123, 114) !important;
        }
        [data-theme="dark"] .token.number {
            color: rgb(247, 140, 108) !important;
        }
        [data-theme="dark"] .token.string {
            color: rgb(165, 214, 255) !important;
        }
        [data-theme="dark"] .token.boolean {
            color: rgb(121, 192, 255) !important;
        }
        [data-theme="dark"] .token.punctuation {
            color: #dbdbdb !important;
        }
    </style>
</head>
<body style="height: 100vh; display: flex; flex-direction: column; overflow: hidden;">
<style>
/* elements-api fills remaining space */
elements-api {
    flex: 1;
    overflow: auto;
    display: block;
    min-height: 0;
}

</style>

{{-- Language Toggle Button --}}
@php
    $currentLang = request()->query('lang') 
        ?? request()->cookie('docs_lang') 
        ?? (request()->getPreferredLanguage(['en', 'id']) === 'en' ? 'en' : 'id') 
        ?? 'id';
    $currentLang = in_array($currentLang, ['en', 'id']) ? $currentLang : 'id';
@endphp
<div id="lang-toggle">
    <span class="lang-icon">🌐</span>
    <span class="lang-label">{{ $currentLang === 'id' ? 'Bahasa Indonesia' : 'English' }}</span>
    <div class="divider"></div>
    <button id="btn-id" onclick="switchLang('id')" class="{{ $currentLang === 'id' ? 'active' : '' }}">ID</button>
    <button id="btn-en" onclick="switchLang('en')" class="{{ $currentLang === 'en' ? 'active' : '' }}">EN</button>
</div>
<script>
    function switchLang(lang) {
        const url = new URL(window.location.href);
        url.searchParams.set('lang', lang);
        window.location.href = url.toString();
    }
</script>

{{-- Session Status Widget --}}
<div id="session-status">
    <div class="ss-row">
        <span class="ss-dot loading" id="ss-dot"></span>
        <span class="ss-label" id="ss-label">{{ $currentLang === 'id' ? 'Mengecek sesi...' : 'Checking session...' }}</span>
    </div>
    <div class="ss-sub" id="ss-sub"></div>
    <div class="ss-hint" id="ss-hint" style="display:none"></div>
    <button id="ss-refresh" onclick="checkSession()">
        {{ $currentLang === 'id' ? '↻ Cek ulang' : '↻ Refresh' }}
    </button>
    <button id="ss-open-panel" onclick="openPanel()">
        {{ $currentLang === 'id' ? '🗂️ Buka Dashboard' : '🗂️ Open Dashboard' }}
    </button>
</div>

{{-- Interactive Dashboard Panel --}}
<div id="ip-backdrop" onclick="closePanel()"></div>
<div id="interactive-panel">
    <div class="ip-header">
        <div class="ip-title">
            <span>🗂️</span>
            <span>{{ $currentLang === 'id' ? 'Dashboard Interaktif' : 'Interactive Dashboard' }}</span>
            <span class="ip-user-chip auth-only" id="ip-user-name" style="display:none">—</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
            <button class="ip-btn ip-btn-ghost auth-only" style="display:none;padding:3px 8px;font-size:10px" onclick="ipLogout()">Logout</button>
            <button class="ip-close" onclick="closePanel()">✕</button>
        </div>
    </div>
    <div class="ip-nav">
        <button class="ip-tab auth-only" id="tab-btn-projects" onclick="ipShowTab('projects')">{{ $currentLang === 'id' ? 'Project' : 'Projects' }}</button>
        <button class="ip-tab auth-only" id="tab-btn-labels" onclick="ipShowTab('labels')">Labels</button>
        <button class="ip-tab auth-only" id="tab-btn-users" onclick="ipShowTab('users')">{{ $currentLang === 'id' ? 'Pengguna' : 'Users' }}</button>
        <button class="ip-tab auth-only" id="tab-btn-profile" onclick="ipShowTab('profile')">{{ $currentLang === 'id' ? 'Profil Saya' : 'My Profile' }}</button>
        
        <button class="ip-tab guest-only" id="tab-btn-login" onclick="ipShowTab('login')">Login</button>
        <button class="ip-tab guest-only" id="tab-btn-register" onclick="ipShowTab('register')">Register</button>
    </div>
    <div class="ip-content" id="ip-content"></div>
</div>
<div id="ip-toast"></div>

<script>
    const _lang = '{{ $currentLang }}';

    const t = {
        checking:   _lang === 'id' ? 'Mengecek sesi...'       : 'Checking session...',
        loggedIn:   _lang === 'id' ? '🟢 Sudah Login'          : '🟢 Logged In',
        loggedOut:  _lang === 'id' ? '🔴 Belum Login'          : '🔴 Not Logged In',
        as:         _lang === 'id' ? 'Sebagai: '               : 'As: ',
        hintTitle:  _lang === 'id' ? 'Untuk mulai eksplorasi:' : 'To start exploring:',
        hintStep1:  _lang === 'id' ? '1. Buka endpoint'        : '1. Open endpoint',
        hintStep2:  _lang === 'id' ? '2. Isi kredensial & klik Send' : '2. Fill credentials & click Send',
        hintStep3:  _lang === 'id' ? '3. Kembali ke sini & cek ulang' : '3. Come back & refresh',
        error:      _lang === 'id' ? 'Koneksi gagal'           : 'Connection failed',
    };

    async function checkSession() {
        const dot   = document.getElementById('ss-dot');
        const label = document.getElementById('ss-label');
        const sub   = document.getElementById('ss-sub');
        const hint  = document.getElementById('ss-hint');

        dot.className     = 'ss-dot loading';
        label.textContent = t.checking;
        sub.textContent   = '';

        // Fast-fail: no token stored → instantly show "not logged in"
        const storedToken = localStorage.getItem('api_token');
        if (!storedToken) {
            dot.className     = 'ss-dot offline';
            label.textContent = t.loggedOut;
            hint.innerHTML    = `<strong>${t.hintTitle}</strong><br>
                ${t.hintStep1} <code>POST /api/auth/login</code><br>
                ${t.hintStep2} (default@example.com / default123)<br>
                ${t.hintStep3}`;
            hint.style.display = 'block';
            return;
        }

        try {
            // Use Bearer token (not cookie) — matches token-based Sanctum auth
            const res = await originalFetch('/api/auth/me', {
                headers: { 'Authorization': 'Bearer ' + storedToken }
            });
            if (res.ok) {
                const json = await res.json();
                const user = json.data ?? json;
                dot.className      = 'ss-dot online';
                label.textContent  = t.loggedIn;
                sub.textContent    = t.as + (user.name ?? '—');
                hint.style.display = 'none';
            } else {
                // Token expired or invalid — clean up
                localStorage.removeItem('api_token');
                dot.className     = 'ss-dot offline';
                label.textContent = t.loggedOut;
                sub.textContent   = '';
                hint.innerHTML    = `<strong>${t.hintTitle}</strong><br>
                    ${t.hintStep1} <code>POST /api/auth/login</code><br>
                    ${t.hintStep2}<br>
                    ${t.hintStep3}`;
                hint.style.display = 'block';
            }
        } catch (e) {
            dot.className     = 'ss-dot offline';
            label.textContent = t.error;
            sub.textContent   = '';
        }
    }

    // Run on load
    checkSession();

    // Re-check when user comes back to this tab (e.g. after logging in another tab)
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') checkSession();
    });
</script>

<script>
// ════════════════════════════════════════════════════════════
//  Interactive Dashboard Panel
// ════════════════════════════════════════════════════════════
const _ipLang = '{{ $currentLang }}';
const _ipT = {
    loading:      _ipLang==='id' ? 'Memuat...'          : 'Loading...',
    empty:        _ipLang==='id' ? 'Belum ada data.'    : 'No data yet.',
    noTasks:      _ipLang==='id' ? 'Belum ada tugas.'   : 'No tasks yet.',
    saved:        _ipLang==='id' ? 'Berhasil disimpan!' : 'Saved!',
    deleted:      _ipLang==='id' ? 'Berhasil dihapus!'  : 'Deleted!',
    confirmDel:   _ipLang==='id' ? 'Yakin ingin menghapus?' : 'Sure you want to delete?',
    create:       _ipLang==='id' ? '+ Buat Baru'        : '+ Create New',
    cancel:       _ipLang==='id' ? 'Batal'              : 'Cancel',
    save:         _ipLang==='id' ? 'Simpan'             : 'Save',
    delete:       _ipLang==='id' ? 'Hapus'              : 'Delete',
    edit:         _ipLang==='id' ? 'Edit'               : 'Edit',
    tasks:        _ipLang==='id' ? 'Tugas'              : 'Tasks',
    addTask:      _ipLang==='id' ? '+ Tambah Tugas'     : '+ Add Task',
    statusLabel:  _ipLang==='id' ? 'Status'             : 'Status',
    assignLabel:  _ipLang==='id' ? 'Ditugaskan ke'      : 'Assigned to',
    noAssign:     _ipLang==='id' ? '(Tidak ada)'        : '(None)',
};

let _ipState = { tab:'projects', projects:[], expandedId:null, taskCache:{}, labels:[], users:[] };

// ── API helper ────────────────────────────────────────────
async function ipApi(method, path, body=null) {
    const token = localStorage.getItem('api_token');
    const opts = { method:method.toUpperCase(), headers:{'Content-Type':'application/json', ...(token?{'Authorization':'Bearer '+token}:{})} };
    if (body) opts.body = JSON.stringify(body);
    const res = await originalFetch('/api'+path, opts);
    let json; try { json=await res.json(); } catch(e){json={};}
    return { ok:res.ok, status:res.status, data:json.data??json, message:json.message??'' };
}

// ── Toast ─────────────────────────────────────────────────
function ipToast(msg,type='ok') {
    const el=document.getElementById('ip-toast');
    el.textContent=msg; el.className=type+' show';
    clearTimeout(el._t); el._t=setTimeout(()=>el.className='',2800);
}

// ── Panel open/close ──────────────────────────────────────
function openPanel() {
    const panel=document.getElementById('interactive-panel');
    const bd=document.getElementById('ip-backdrop');
    panel.classList.add('open'); bd.style.display='block';
    
    const token = localStorage.getItem('api_token');
    document.querySelectorAll('.auth-only').forEach(e => e.style.display = token ? 'block' : 'none');
    document.querySelectorAll('.guest-only').forEach(e => e.style.display = token ? 'none' : 'block');
    
    if(token) {
        if(['login','register'].includes(_ipState.tab)) _ipState.tab = 'projects';
    } else {
        if(!['login','register'].includes(_ipState.tab)) _ipState.tab = 'login';
    }
    ipShowTab(_ipState.tab);
}
function closePanel() {
    document.getElementById('interactive-panel').classList.remove('open');
    document.getElementById('ip-backdrop').style.display='none';
}
function ipShowTab(tab) {
    _ipState.tab=tab;
    document.querySelectorAll('.ip-tab').forEach(b=>b.classList.remove('active'));
    const btn=document.getElementById('tab-btn-'+tab);
    if(btn) btn.classList.add('active');
    
    if(tab==='projects') ipLoadProjects();
    else if(tab==='labels') ipLoadLabels();
    else if(tab==='users') ipLoadUsers();
    else if(tab==='profile') ipLoadProfile();
    else if(tab==='login') ipLoadLogin();
    else if(tab==='register') ipLoadRegister();
}

// ── AUTH TABS (LOGIN / REGISTER) ─────────────────────────
function ipLoadLogin() {
    let html = `<div class="ip-card" style="padding:16px"><form onsubmit="ipSubmitLogin(event)">`;
    html += `<div style="text-align:center;margin-bottom:16px;color:#cbd5e0;font-size:13px">Login ke akun Anda. Default: <b>default@example.com / default123</b></div>`;
    html += `<div class="ip-field"><label>Email</label><input type="email" name="email" required value="default@example.com"></div>`;
    html += `<div class="ip-field"><label>Password</label><input type="password" name="password" required value="default123"></div>`;
    html += `<div class="ip-form-actions" style="margin-top:16px"><button type="submit" class="ip-btn ip-btn-primary ip-btn-block">Login</button></div>`;
    html += `</form></div>`;
    document.getElementById('ip-content').innerHTML=html;
}

function ipLoadRegister() {
    let html = `<div class="ip-card" style="padding:16px"><form onsubmit="ipSubmitRegister(event)">`;
    html += `<div style="text-align:center;margin-bottom:16px;color:#cbd5e0;font-size:13px">Buat akun baru untuk mulai.</div>`;
    html += `<div class="ip-field"><label>Nama</label><input name="name" required placeholder="John Doe"></div>`;
    html += `<div class="ip-field"><label>Email</label><input type="email" name="email" required placeholder="john@example.com"></div>`;
    html += `<div class="ip-field"><label>Password</label><input type="password" name="password" required placeholder="***"></div>`;
    html += `<div class="ip-form-actions" style="margin-top:16px"><button type="submit" class="ip-btn ip-btn-primary ip-btn-block">Register</button></div>`;
    html += `</form></div>`;
    document.getElementById('ip-content').innerHTML=html;
}

async function ipSubmitLogin(e) {
    e.preventDefault();
    const fd = new FormData(e.target);
    const r = await ipApi('POST','/auth/login', {email:fd.get('email'), password:fd.get('password')});
    if(r.ok) {
        ipToast('Berhasil Login!');
        localStorage.setItem('api_token', r.data.token || r.data.data?.token || r.data);
        await checkSession();
        openPanel(); // refresh state
    } else ipToast(r.message||'Login gagal','err');
}

async function ipSubmitRegister(e) {
    e.preventDefault();
    const fd = new FormData(e.target);
    const r = await ipApi('POST','/auth/register', {name:fd.get('name'), email:fd.get('email'), password:fd.get('password')});
    if(r.ok) {
        ipToast('Registrasi Berhasil!');
        localStorage.setItem('api_token', r.data.token || r.data.data?.token || r.data);
        await checkSession();
        openPanel(); // refresh state
    } else ipToast(r.message||'Register gagal','err');
}

async function ipLogout() {
    if(!confirm("Yakin ingin logout?")) return;
    await ipApi('POST','/auth/logout');
    localStorage.removeItem('api_token');
    ipToast("Berhasil logout.");
    closePanel();
    checkSession();
}

// ── PROJECTS TAB ──────────────────────────────────────────
async function ipLoadProjects() {
    const c=document.getElementById('ip-content');
    c.innerHTML=`<div class="ip-loading">${_ipT.loading}</div>`;
    const r=await ipApi('GET','/projects?per_page=50');
    _ipState.projects=(r.ok&&Array.isArray(r.data?.data))?r.data.data:(r.ok&&Array.isArray(r.data))?r.data:[];
    ipRenderProjects();
}
async function ipLoadTasks(pid) {
    const r=await ipApi('GET',`/projects/${pid}/tasks?per_page=100`);
    _ipState.taskCache[pid]=(r.ok&&Array.isArray(r.data?.data))?r.data.data:(r.ok&&Array.isArray(r.data))?r.data:[];
    ipRenderProjects();
}
async function ipCreateProject(e) {
    e.preventDefault();
    const fd=new FormData(e.target);
    const r=await ipApi('POST','/projects',{name:fd.get('name'),description:fd.get('description')||null});
    if(r.ok){ipToast(_ipT.saved);_ipState.expandedId=r.data?.id??null;await ipLoadProjects();}
    else ipToast(r.message||'Error','err');
}
async function ipDeleteProject(id) {
    if(!confirm(_ipT.confirmDel))return;
    const r=await ipApi('DELETE',`/projects/${id}`);
    if(r.ok){ipToast(_ipT.deleted);if(_ipState.expandedId===id)_ipState.expandedId=null;await ipLoadProjects();}
    else ipToast(r.message||'Error','err');
}
async function ipEditProject(e, id) {
    e.preventDefault();
    const fd=new FormData(e.target);
    const r=await ipApi('PUT',`/projects/${id}`,{name:fd.get('name'),description:fd.get('description')||null});
    if(r.ok){ipToast(_ipT.saved);await ipLoadProjects();}
    else ipToast(r.message||'Error','err');
}
async function ipCreateTask(e,pid) {
    e.preventDefault();
    const fd=new FormData(e.target);
    const payload={title:fd.get('title'),description:fd.get('description')||null,status:fd.get('status')||'pending'};
    const at=fd.get('assigned_to'); if(at)payload.assigned_to=at;
    const lids=[...e.target.querySelectorAll('input[name=label_ids]:checked')].map(i=>Number(i.value));
    if(lids.length)payload.label_ids=lids;
    const r=await ipApi('POST',`/projects/${pid}/tasks`,payload);
    if(r.ok){ipToast(_ipT.saved);await ipLoadTasks(pid);}
    else ipToast(r.message||'Error','err');
}
async function ipCycleStatus(tid,pid,cur) {
    const cycle={todo:'in_progress',in_progress:'done',done:'todo'};
    const nxt=cycle[cur]||'todo';
    const r=await ipApi('PUT',`/tasks/${tid}`,{status:nxt});
    if(r.ok) await ipLoadTasks(pid); else ipToast(r.message||'Error','err');
}
async function ipDeleteTask(tid,pid) {
    if(!confirm(_ipT.confirmDel))return;
    const r=await ipApi('DELETE',`/tasks/${tid}`);
    if(r.ok){ipToast(_ipT.deleted);await ipLoadTasks(pid);}
    else ipToast(r.message||'Error','err');
}
async function ipEditTask(e, tid, pid) {
    e.preventDefault();
    const fd=new FormData(e.target);
    const payload={title:fd.get('title'),description:fd.get('description')||null,status:fd.get('status')};
    const at=fd.get('assigned_to'); payload.assigned_to=at?at:null;
    const lids=[...e.target.querySelectorAll('input[name=label_ids]:checked')].map(i=>Number(i.value));
    payload.label_ids=lids;
    const r=await ipApi('PUT',`/tasks/${tid}`,payload);
    if(r.ok){ipToast(_ipT.saved);await ipLoadTasks(pid);}
    else ipToast(r.message||'Error','err');
}
function ipRenderProjects() {
    const projs=_ipState.projects;
    let html=`<button class="ip-add-btn" onclick="ipToggleForm('pf')">${_ipT.create} Project</button>`;
    html+=`<div id="pf" style="display:none" class="ip-form-box">`;
    html+=`<form onsubmit="ipCreateProject(event)">`;
    html+=`<div class="ip-field"><label>Nama *</label><input name="name" required placeholder="Nama project"></div>`;
    html+=`<div class="ip-field"><label>Deskripsi</label><textarea name="description" placeholder="Opsional"></textarea></div>`;
    html+=`<div class="ip-form-actions"><button type="submit" class="ip-btn ip-btn-primary">${_ipT.save}</button><button type="button" class="ip-btn ip-btn-ghost" onclick="ipToggleForm('pf')">${_ipT.cancel}</button></div>`;
    html+=`</form></div>`;
    if(!projs.length){html+=`<div class="ip-empty">${_ipT.empty}</div>`;}
    projs.forEach(p=>{
        const expanded=_ipState.expandedId===p.id;
        html+=`<div class="ip-card">`;
        html+=`<div class="ip-card-header">`;
        html+=`<div style="flex:1;cursor:pointer" onclick="ipToggleProject(${p.id})">`;
        html+=`<div class="ip-card-title">${expanded?'▾':'▸'} ${escH(p.name)}</div>`;
        html+=`<div class="ip-card-sub">${p.tasks_count??0} ${_ipT.tasks}</div>`;
        html+=`</div>`;
        html+=`<div class="ip-card-actions">`;
        html+=`<button class="ip-btn ip-btn-ghost" onclick="ipToggleForm('pf-edit-${p.id}')">${_ipT.edit}</button>`;
        html+=`<button class="ip-btn ip-btn-danger" onclick="ipDeleteProject(${p.id})">${_ipT.delete}</button>`;
        html+=`</div></div>`;
        // Edit project form
        html+=`<div id="pf-edit-${p.id}" style="display:none;margin-top:8px" class="ip-form-box">`;
        html+=`<form onsubmit="ipEditProject(event, ${p.id})">`;
        html+=`<div class="ip-field"><label>Nama *</label><input name="name" required value="${escH(p.name)}"></div>`;
        html+=`<div class="ip-field"><label>Deskripsi</label><textarea name="description">${escH(p.description||'')}</textarea></div>`;
        html+=`<div class="ip-form-actions"><button type="submit" class="ip-btn ip-btn-primary">${_ipT.save}</button><button type="button" class="ip-btn ip-btn-ghost" onclick="ipToggleForm('pf-edit-${p.id}')">${_ipT.cancel}</button></div>`;
        html+=`</form></div>`;
        if(expanded){
            const tasks=_ipState.taskCache[p.id];
            if(!tasks){
                html+=`<div class="ip-tasks"><div class="ip-loading" style="padding:10px 0">${_ipT.loading}</div></div>`;
                ipLoadTasks(p.id);
            } else {
                html+=`<div class="ip-tasks">`;
                // Task add form
                html+=`<button class="ip-add-btn" style="margin-bottom:6px" onclick="ipToggleForm('tf${p.id}')">${_ipT.addTask}</button>`;
                html+=`<div id="tf${p.id}" style="display:none" class="ip-form-box">`;
                html+=`<form onsubmit="ipCreateTask(event,${p.id})">`;
                html+=`<div class="ip-field"><label>Judul *</label><input name="title" required placeholder="Judul tugas"></div>`;
                html+=`<div class="ip-field"><label>Deskripsi</label><textarea name="description" placeholder="Opsional"></textarea></div>`;
                html+=`<div class="ip-form-row">`;
                html+=`<div class="ip-field"><label>${_ipT.statusLabel}</label><select name="status"><option value="todo">Todo</option><option value="in_progress">In Progress</option><option value="done">Done</option></select></div>`;
                html+=`<div class="ip-field"><label>${_ipT.assignLabel}</label><select name="assigned_to"><option value="">${_ipT.noAssign}</option>`;
                _ipState.users.forEach(u=>{html+=`<option value="${u.id}">${escH(u.name)}</option>`;});
                html+=`</select></div></div>`;
                if(_ipState.labels.length){
                    html+=`<div class="ip-field"><label>Labels</label><div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:2px">`;
                    _ipState.labels.forEach(l=>{
                        html+=`<label style="display:flex;align-items:center;gap:4px;font-size:11px;color:rgba(255,255,255,.6);cursor:pointer">`;
                        html+=`<input type="checkbox" name="label_ids" value="${l.id}"> `;
                        html+=`<span class="ip-dot" style="background:${escH(l.color)}"></span> ${escH(l.name)}</label>`;
                    });
                    html+=`</div></div>`;
                }
                html+=`<div class="ip-form-actions"><button type="submit" class="ip-btn ip-btn-primary">${_ipT.save}</button><button type="button" class="ip-btn ip-btn-ghost" onclick="ipToggleForm('tf${p.id}')">${_ipT.cancel}</button></div>`;
                html+=`</form></div>`;
                if(!tasks.length){html+=`<div class="ip-empty" style="padding:10px 0">${_ipT.noTasks}</div>`;}
                tasks.forEach(tk=>{
                    const bc='ip-badge-'+(tk.status||'todo');
                    html+=`<div class="ip-task-item">`;
                    html+=`<span class="ip-task-title" title="${escH(tk.description||'')}">${escH(tk.title)}</span>`;
                    html+=`<span class="ip-badge ${bc}" style="cursor:pointer" onclick="ipCycleStatus(${tk.id},${p.id},'${tk.status}')">${tk.status}</span>`;
                    html+=`<button class="ip-btn ip-btn-ghost" style="padding:2px 6px" onclick="ipToggleForm('tf-edit-${tk.id}')">✎</button>`;
                    html+=`<button class="ip-btn ip-btn-danger" style="padding:2px 6px" onclick="ipDeleteTask(${tk.id},${p.id})">✕</button>`;
                    html+=`</div>`;
                    
                    // Edit task form inline
                    html+=`<div id="tf-edit-${tk.id}" style="display:none;margin:4px 0 8px" class="ip-form-box">`;
                    html+=`<form onsubmit="ipEditTask(event, ${tk.id}, ${p.id})">`;
                    html+=`<div class="ip-field"><label>Judul *</label><input name="title" required value="${escH(tk.title)}"></div>`;
                    html+=`<div class="ip-field"><label>Deskripsi</label><textarea name="description">${escH(tk.description||'')}</textarea></div>`;
                    html+=`<div class="ip-form-row">`;
                    html+=`<div class="ip-field"><label>${_ipT.statusLabel}</label><select name="status">
                        <option value="todo" ${tk.status==='todo'?'selected':''}>Todo</option>
                        <option value="in_progress" ${tk.status==='in_progress'?'selected':''}>In Progress</option>
                        <option value="done" ${tk.status==='done'?'selected':''}>Done</option>
                        </select></div>`;
                    html+=`<div class="ip-field"><label>${_ipT.assignLabel}</label><select name="assigned_to"><option value="">${_ipT.noAssign}</option>`;
                    _ipState.users.forEach(u=>{html+=`<option value="${u.id}" ${tk.assigned_to==u.id?'selected':''}>${escH(u.name)}</option>`;});
                    html+=`</select></div></div>`;
                    if(_ipState.labels.length){
                        const taskLabelIds = (tk.labels||[]).map(l=>l.id);
                        html+=`<div class="ip-field"><label>Labels</label><div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:2px">`;
                        _ipState.labels.forEach(l=>{
                            const checked = taskLabelIds.includes(l.id) ? 'checked' : '';
                            html+=`<label style="display:flex;align-items:center;gap:4px;font-size:11px;color:rgba(255,255,255,.6);cursor:pointer">`;
                            html+=`<input type="checkbox" name="label_ids" value="${l.id}" ${checked}> `;
                            html+=`<span class="ip-dot" style="background:${escH(l.color)}"></span> ${escH(l.name)}</label>`;
                        });
                        html+=`</div></div>`;
                    }
                    html+=`<div class="ip-form-actions"><button type="submit" class="ip-btn ip-btn-primary">${_ipT.save}</button><button type="button" class="ip-btn ip-btn-ghost" onclick="ipToggleForm('tf-edit-${tk.id}')">${_ipT.cancel}</button></div>`;
                    html+=`</form></div>`;
                });
                html+=`</div>`;
            }
        }
        html+=`</div>`;
    });
    document.getElementById('ip-content').innerHTML=html;
}
function ipToggleProject(id) {
    _ipState.expandedId=(_ipState.expandedId===id)?null:id;
    ipRenderProjects();
    if(_ipState.expandedId===id && !_ipState.taskCache[id]) ipLoadTasks(id);
}
function ipToggleForm(id){
    const el=document.getElementById(id);
    if(el) el.style.display=el.style.display==='none'?'block':'none';
}

// ── LABELS TAB ────────────────────────────────────────────
async function ipLoadLabels() {
    const c=document.getElementById('ip-content');
    c.innerHTML=`<div class="ip-loading">${_ipT.loading}</div>`;
    const r=await ipApi('GET','/labels?per_page=100');
    _ipState.labels=(r.ok&&Array.isArray(r.data?.data))?r.data.data:(r.ok&&Array.isArray(r.data))?r.data:[];
    ipRenderLabels();
}
async function ipCreateLabel(e){
    e.preventDefault();
    const fd=new FormData(e.target);
    const r=await ipApi('POST','/labels',{name:fd.get('name'),color:fd.get('color')});
    if(r.ok){ipToast(_ipT.saved);await ipLoadLabels();} else ipToast(r.message||'Error','err');
}
async function ipDeleteLabel(id){
    if(!confirm(_ipT.confirmDel))return;
    const r=await ipApi('DELETE',`/labels/${id}`);
    if(r.ok){ipToast(_ipT.deleted);await ipLoadLabels();} else ipToast(r.message||'Error','err');
}
function ipRenderLabels(){
    let html=`<button class="ip-add-btn" onclick="ipToggleForm('lf')">${_ipT.create} Label</button>`;
    html+=`<div id="lf" style="display:none" class="ip-form-box">`;
    html+=`<form onsubmit="ipCreateLabel(event)">`;
    html+=`<div class="ip-form-row">`;
    html+=`<div class="ip-field"><label>Nama *</label><input name="name" required placeholder="contoh: Bug"></div>`;
    html+=`<div class="ip-field" style="flex:0 0 70px"><label>Warna</label><input name="color" type="color" value="#63b3ed" style="height:34px;padding:2px 4px;cursor:pointer"></div>`;
    html+=`</div>`;
    html+=`<div class="ip-form-actions"><button type="submit" class="ip-btn ip-btn-primary">${_ipT.save}</button><button type="button" class="ip-btn ip-btn-ghost" onclick="ipToggleForm('lf')">${_ipT.cancel}</button></div>`;
    html+=`</form></div>`;
    if(!_ipState.labels.length){html+=`<div class="ip-empty">${_ipT.empty}</div>`;}
    _ipState.labels.forEach(l=>{
        html+=`<div class="ip-card"><div class="ip-card-header">`;
        html+=`<span class="ip-dot" style="background:${escH(l.color)};width:14px;height:14px"></span>`;
        html+=`<div class="ip-card-title">${escH(l.name)}</div>`;
        html+=`<div class="ip-card-actions"><button class="ip-btn ip-btn-danger" onclick="ipDeleteLabel(${l.id})">${_ipT.delete}</button></div>`;
        html+=`</div></div>`;
    });
    document.getElementById('ip-content').innerHTML=html;
}

// ── USERS TAB ─────────────────────────────────────────────
async function ipLoadUsers(){
    const c=document.getElementById('ip-content');
    c.innerHTML=`<div class="ip-loading">${_ipT.loading}</div>`;
    const r=await ipApi('GET','/users?per_page=100');
    _ipState.users=(r.ok&&Array.isArray(r.data?.data))?r.data.data:(r.ok&&Array.isArray(r.data))?r.data:[];
    ipRenderUsers();
}
function ipRenderUsers(){
    let html='';
    if(!_ipState.users.length){html=`<div class="ip-empty">${_ipT.empty}</div>`;}
    _ipState.users.forEach(u=>{
        const initials=(u.name||'?').split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase();
        html+=`<div class="ip-user-row">`;
        html+=`<div class="ip-avatar">${initials}</div>`;
        html+=`<div><div style="font-size:13px;color:#e2e8f0;font-weight:500">${escH(u.name)}</div>`;
        html+=`<div style="font-size:10.5px;color:rgba(255,255,255,.3)">ID: ${u.id}</div></div>`;
        html+=`</div>`;
    });
    document.getElementById('ip-content').innerHTML=html;
}

// ── PROFILE TAB ───────────────────────────────────────────
async function ipLoadProfile(){
    const c=document.getElementById('ip-content');
    c.innerHTML=`<div class="ip-loading">${_ipT.loading}</div>`;
    const r=await ipApi('GET','/auth/me');
    if(!r.ok) { c.innerHTML=`<div class="ip-empty">Error loading profile</div>`; return; }
    const user=r.data?.data??r.data??{};
    
    let html = `<div class="ip-card" style="padding:16px"><form onsubmit="ipUpdateProfile(event, ${user.id})">`;
    html += `<div style="text-align:center;margin-bottom:16px"><div class="ip-avatar" style="width:48px;height:48px;font-size:18px;margin:0 auto">${user.name?user.name[0]:'?'}</div></div>`;
    html += `<div class="ip-field"><label>Nama</label><input name="name" required value="${escH(user.name)}"></div>`;
    html += `<div class="ip-field"><label>Email (Read Only)</label><input value="${escH(user.email||'')}" disabled style="opacity:0.6"></div>`;
    html += `<div class="ip-field"><label>Password Baru (Kosongkan jika tidak diubah)</label><input type="password" name="password" placeholder="***"></div>`;
    html += `<div class="ip-field"><label>Konfirmasi Password</label><input type="password" name="password_confirmation" placeholder="***"></div>`;
    html += `<div class="ip-form-actions" style="margin-top:16px">`;
    html += `<button type="submit" class="ip-btn ip-btn-primary ip-btn-block" style="flex:1">Simpan Profil</button>`;
    html += `<button type="button" class="ip-btn ip-btn-danger" onclick="ipDeleteProfile(${user.id})" title="Hapus Akun">✕</button>`;
    html += `</div></form></div>`;
    c.innerHTML=html;
}

async function ipUpdateProfile(e, id) {
    e.preventDefault();
    const fd = new FormData(e.target);
    const payload = { name: fd.get('name') };
    if(fd.get('password')) {
        payload.password = fd.get('password');
        payload.password_confirmation = fd.get('password_confirmation');
    }
    const r=await ipApi('PUT',`/users/${id}`, payload);
    if(r.ok){ ipToast(_ipT.saved); await ipLoadProfile(); checkSession(); }
    else ipToast(r.message||'Error','err');
}

async function ipDeleteProfile(id) {
    if(!confirm("PERINGATAN: Akun akan dihapus permanen! Yakin?")) return;
    const r=await ipApi('DELETE',`/users/${id}`);
    if(r.ok){ 
        ipToast(_ipT.deleted); 
        localStorage.removeItem('api_token');
        closePanel();
        checkSession();
    } else ipToast(r.message||'Error','err');
}

// ── Helpers ───────────────────────────────────────────────
function escH(s){const d=document.createElement('div');d.appendChild(document.createTextNode(String(s??'')));return d.innerHTML;}

// ── Hook into checkSession to show/hide Open Dashboard btn ─
const _origCheckSession = checkSession;
checkSession = async function() {
    await _origCheckSession();
    updatePanelVisibility();
};

async function updatePanelVisibility() {
    const btn=document.getElementById('ss-open-panel');
    const token=localStorage.getItem('api_token');
    if(btn) {
        btn.style.display = 'block';
        btn.innerHTML = token 
            ? (_ipLang === 'id' ? '🗂️ Buka Dashboard' : '🗂️ Open Dashboard') 
            : (_ipLang === 'id' ? '🔑 Buka Dashboard (Login / Daftar)' : '🔑 Open Dashboard (Login / Register)');
    }
    // also update panel username chip
    if(token){
        const r=await ipApi('GET','/auth/me');
        if(r.ok){
            const name=r.data?.name??r.data?.data?.name??'';
            const el=document.getElementById('ip-user-name');
            if(el) el.textContent=name;
            // pre-load users for task assignment dropdown
            const ru=await ipApi('GET','/users?per_page=100');
            _ipState.users=(ru.ok&&Array.isArray(ru.data?.data))?ru.data.data:(ru.ok&&Array.isArray(ru.data))?ru.data:[];
        }
    }
}

// Run immediately to set correct button state on page load
updatePanelVisibility();
</script>
<elements-api
    id="docs"
    @foreach($config->renderer()->all(except: ['theme']) as $key => $value)
        @continue(! $value)
        {{ $key }}="{{ $value === true ? 'true' : ($value === false ? 'false' : $value) }}"
    @endforeach
/>
<script>
    (async () => {
        const docs = document.getElementById('docs');
        // Fetch spec with current lang preference so the middleware filters it
        const urlParams = new URLSearchParams(window.location.search);
        const lang = urlParams.get('lang') || document.cookie.match(/docs_lang=([^;]+)/)?.[1] || 'auto';
        const specUrl = lang !== 'auto'
            ? '/docs/api.json?lang=' + lang
            : '/docs/api.json';
        const res = await fetch(specUrl);
        docs.apiDescriptionDocument = await res.json();
    })();
</script>

@if($config->renderer()->get('theme', 'light') === 'system')
    <script>
        var mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

        function updateTheme(e) {
            if (e.matches) {
                window.document.documentElement.setAttribute('data-theme', 'dark');
                window.document.getElementsByName('color-scheme')[0].setAttribute('content', 'dark');
            } else {
                window.document.documentElement.setAttribute('data-theme', 'light');
                window.document.getElementsByName('color-scheme')[0].setAttribute('content', 'light');
            }
        }

        mediaQuery.addEventListener('change', updateTheme);
        updateTheme(mediaQuery);
    </script>
@endif
{{-- Attribution: replaces the removed Stoplight footer branding --}}
<div id="site-credit" style="
    position: fixed;
    bottom: 8px;
    left: 12px;
    z-index: 9000;
    font-family: system-ui, sans-serif;
    font-size: 10.5px;
    color: rgba(255,255,255,0.25);
    pointer-events: none;
    user-select: none;
    letter-spacing: 0.02em;
">NexTask API &copy; {{ date('Y') }} &bull; Created by <span style="color:rgba(255,255,255,0.4);font-weight:600;">r1lz</span></div>
</body>
</html>
