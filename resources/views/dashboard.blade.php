<!doctype html>
<html lang="{{ $lang }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>NexTask Dashboard</title>
    <meta name="description" content="Dashboard interaktif NexTask API — kelola project, tugas, dan label.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #0b0b11; --bg2: #13131e; --border: rgba(255,255,255,0.08);
            --text: #e2e8f0; --sub: rgba(255,255,255,0.4); --accent: #63b3ed;
            --green: #48c78e; --orange: #ed8936; --red: #fc8181;
        }
        html, body { height: 100%; background: var(--bg); color: var(--text); font-family: 'Inter', system-ui, sans-serif; }
        body { display: flex; flex-direction: column; min-height: 100vh; }

        /* Topbar */
        .topbar { display: flex; align-items: center; justify-content: space-between; padding: 0 24px; height: 56px; flex-shrink: 0; background: rgba(11,11,17,0.98); border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100; backdrop-filter: blur(12px); }
        .topbar-brand { display: flex; align-items: center; gap: 10px; }
        .brand-name { font-size: 15px; font-weight: 700; }
        .brand-badge { font-size: 9px; font-weight: 700; padding: 2px 7px; border-radius: 10px; background: rgba(99,179,237,.15); color: var(--accent); letter-spacing: .05em; text-transform: uppercase; }
        .topbar-right { display: flex; align-items: center; gap: 8px; }
        .lang-switch { display: flex; background: rgba(255,255,255,0.06); border: 1px solid var(--border); border-radius: 6px; overflow: hidden; }
        .lang-switch button { padding: 4px 10px; font-size: 11px; font-weight: 700; border: none; background: transparent; color: var(--sub); cursor: pointer; font-family: inherit; }
        .lang-switch button.active { background: rgba(255,255,255,0.14); color: #fff; }
        .docs-link { font-size: 11px; color: var(--accent); text-decoration: none; padding: 5px 10px; border: 1px solid rgba(99,179,237,.3); border-radius: 6px; font-weight: 500; }
        .docs-link:hover { background: rgba(99,179,237,.08); }
        .user-chip { font-size: 11px; background: rgba(99,179,237,.12); color: var(--accent); padding: 4px 10px; border-radius: 20px; font-weight: 600; }
        .btn-topbar { font-size: 11px; padding: 5px 10px; border-radius: 6px; cursor: pointer; font-family: inherit; }
        .btn-topbar.danger { border: 1px solid rgba(255,80,80,.3); background: rgba(255,80,80,.08); color: var(--red); }

        /* Layout */
        .main { flex: 1; display: flex; overflow: hidden; }

        /* Sidebar */
        .sidebar { width: 220px; flex-shrink: 0; display: flex; flex-direction: column; background: var(--bg2); border-right: 1px solid var(--border); overflow-y: auto; padding: 16px 0; }
        .sidebar-label { font-size: 9.5px; font-weight: 700; color: var(--sub); letter-spacing: .1em; text-transform: uppercase; padding: 4px 20px 6px; }
        .sidebar-item { display: flex; align-items: center; gap: 9px; padding: 8px 12px; margin: 0 8px; border-radius: 7px; cursor: pointer; font-size: 13px; color: rgba(255,255,255,.6); border: none; background: transparent; width: calc(100% - 16px); text-align: left; font-family: inherit; font-weight: 500; }
        .sidebar-item:hover { background: rgba(255,255,255,.05); color: #fff; }
        .sidebar-item.active { background: rgba(99,179,237,.12); color: var(--accent); }
        .sidebar-item .icon { font-size: 14px; width: 18px; text-align: center; }
        .sidebar-sep { border: none; border-top: 1px solid var(--border); margin: 8px 16px; }

        /* Content */
        .content { flex: 1; overflow-y: auto; padding: 28px 32px; }
        .page-header { margin-bottom: 24px; }
        .page-title { font-size: 22px; font-weight: 800; letter-spacing: -.02em; margin-bottom: 4px; }
        .page-sub { font-size: 13px; color: var(--sub); }

        /* Cards */
        .card { background: var(--bg2); border: 1px solid var(--border); border-radius: 12px; padding: 16px; margin-bottom: 10px; }
        .card:hover { border-color: rgba(255,255,255,.14); }
        .card-header { display: flex; align-items: flex-start; gap: 10px; }
        .card-title { font-size: 14px; font-weight: 600; word-break: break-word; }
        .card-sub { font-size: 11.5px; color: var(--sub); margin-top: 3px; }
        .card-actions { display: flex; gap: 6px; flex-shrink: 0; margin-left: auto; padding-left: 8px; align-self: flex-start; }

        /* Buttons */
        .btn { padding: 6px 12px; border-radius: 7px; border: 1px solid transparent; font-size: 12px; font-weight: 500; cursor: pointer; font-family: inherit; }
        .btn-ghost { background: rgba(255,255,255,.06); border-color: var(--border); color: rgba(255,255,255,.6); }
        .btn-ghost:hover { background: rgba(255,255,255,.12); color: #fff; }
        .btn-primary { background: rgba(99,179,237,.12); border-color: rgba(99,179,237,.4); color: var(--accent); }
        .btn-primary:hover { background: rgba(99,179,237,.22); }
        .btn-danger { background: rgba(220,50,50,.1); border-color: rgba(220,50,50,.3); color: var(--red); }
        .btn-danger:hover { background: rgba(220,50,50,.2); }
        .btn-add { width: 100%; padding: 9px; margin-bottom: 10px; background: rgba(99,179,237,.05); border: 1px dashed rgba(99,179,237,.3); border-radius: 8px; color: rgba(99,179,237,.7); font-size: 12px; cursor: pointer; font-family: inherit; }
        .btn-add:hover { background: rgba(99,179,237,.1); color: var(--accent); }

        /* Form */
        .form-box { background: rgba(255,255,255,.03); border: 1px solid var(--border); border-radius: 10px; padding: 16px; margin-bottom: 10px; }
        .field { margin-bottom: 10px; }
        .field label { display: block; font-size: 10px; font-weight: 700; color: var(--sub); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 4px; }
        .field input, .field textarea, .field select { width: 100%; background: rgba(255,255,255,.05); border: 1px solid var(--border); border-radius: 7px; color: var(--text); padding: 8px 11px; font-size: 13px; font-family: inherit; outline: none; resize: vertical; }
        .field input:focus, .field textarea:focus, .field select:focus { border-color: rgba(99,179,237,.5); }
        .field select option { background: #1a1a2e; }
        .field textarea { min-height: 64px; }
        .field input[type="color"] { height: 38px; padding: 3px 6px; cursor: pointer; }
        .form-row { display: flex; gap: 10px; }
        .form-row .field { flex: 1; }
        .form-actions { display: flex; gap: 8px; margin-top: 8px; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 700; cursor: pointer; }
        .badge-todo { background: rgba(255,255,255,.08); color: rgba(255,255,255,.5); }
        .badge-in_progress { background: rgba(99,179,237,.15); color: var(--accent); }
        .badge-done { background: rgba(72,199,142,.15); color: var(--green); }

        /* Tasks */
        .tasks-section { margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,.05); }
        .task-row { display: flex; align-items: flex-start; gap: 8px; padding: 7px 0; border-bottom: 1px solid rgba(255,255,255,.04); }
        .task-row:last-of-type { border-bottom: none; }
        .task-title { flex: 1; font-size: 13px; color: #cbd5e0; word-break: break-word; }

        /* Labels/dot */
        .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; flex-shrink: 0; border: 1.5px solid rgba(255,255,255,.2); }

        /* Users */
        .user-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,.05); }
        .user-row:last-child { border-bottom: none; }
        .avatar { width: 34px; height: 34px; border-radius: 50%; background: rgba(99,179,237,.2); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0; }

        /* Auth */
        .auth-center { max-width: 400px; margin: 60px auto; }
        .auth-card { background: var(--bg2); border: 1px solid var(--border); border-radius: 16px; padding: 32px; }
        .auth-title { font-size: 20px; font-weight: 800; margin-bottom: 4px; text-align: center; }
        .auth-sub { font-size: 13px; color: var(--sub); text-align: center; margin-bottom: 24px; line-height: 1.5; }
        .auth-switch { text-align: center; margin-top: 20px; font-size: 12px; color: var(--sub); }
        .auth-switch a { color: var(--accent); cursor: pointer; font-weight: 500; }
        .btn-full { width: 100%; padding: 10px; font-size: 13px; justify-content: center; }

        /* Profile */
        .profile-avatar-big { width: 64px; height: 64px; border-radius: 50%; background: rgba(99,179,237,.2); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 26px; font-weight: 700; margin: 0 auto 20px; }

        /* Misc */
        .loading, .empty { text-align: center; padding: 40px 0; color: var(--sub); font-size: 13px; }

        /* Toast */
        #toast { position: fixed; bottom: 24px; right: 24px; z-index: 9999; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 500; opacity: 0; transform: translateY(10px); transition: all .25s; pointer-events: none; }
        #toast.show { opacity: 1; transform: translateY(0); }
        #toast.ok { background: rgba(72,199,142,.2); border: 1px solid rgba(72,199,142,.4); color: var(--green); }
        #toast.err { background: rgba(220,50,50,.2); border: 1px solid rgba(220,50,50,.4); color: var(--red); }

        /* Footer */
        footer { flex-shrink: 0; text-align: center; padding: 10px; font-size: 11.5px; color: var(--sub); border-top: 1px solid var(--border); background: rgba(11,11,17,.98); }
        footer a { color: var(--accent); text-decoration: none; font-weight: 600; }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { width: 52px; }
            .sidebar-item span:not(.icon) { display: none; }
            .sidebar-label, .sidebar-sep { display: none; }
            .content { padding: 16px 12px; }
            .topbar { padding: 0 12px; }
            .brand-badge, .docs-link { display: none; }
            /* Hide user chip and logout from topbar on mobile */
            #topbar-user, #btn-logout { display: none !important; }
            /* Show mobile user banner */
            #mobile-user-banner { display: flex !important; }
        }
    </style>
</head>
<body>

<header class="topbar">
    <div class="topbar-brand">
        <span style="font-size:20px">🗂️</span>
        <span class="brand-name">NexTask</span>
        <span class="brand-badge">Dashboard</span>
    </div>
    <div class="topbar-right">
        <div class="lang-switch">
            <button onclick="switchLang('id')" class="{{ $lang === 'id' ? 'active' : '' }}">ID</button>
            <button onclick="switchLang('en')" class="{{ $lang === 'en' ? 'active' : '' }}">EN</button>
        </div>
        <a href="/docs/api?lang={{ $lang }}" class="docs-link">API Docs ↗</a>
        <span class="user-chip" id="topbar-user" style="display:none">—</span>
        <button class="btn-topbar danger" id="btn-logout" onclick="doLogout()" style="display:none">Logout</button>
    </div>
</header>

<div class="main">
    <nav class="sidebar">
        {{-- Guest --}}
        <div id="nav-guest">
            <div class="sidebar-label">{{ $lang === 'id' ? 'Masuk' : 'Account' }}</div>
            <button class="sidebar-item active" id="nav-login" onclick="showTab('login')">
                <span class="icon">🔑</span><span>Login</span>
            </button>
            <button class="sidebar-item" id="nav-register" onclick="showTab('register')">
                <span class="icon">✨</span><span>Register</span>
            </button>
        </div>
        {{-- Auth --}}
        <div id="nav-auth" style="display:none">
            <div class="sidebar-label">{{ $lang === 'id' ? 'Kelola' : 'Manage' }}</div>
            <button class="sidebar-item" id="nav-projects" onclick="showTab('projects')">
                <span class="icon">📁</span><span>{{ $lang === 'id' ? 'Project' : 'Projects' }}</span>
            </button>
            <button class="sidebar-item" id="nav-labels" onclick="showTab('labels')">
                <span class="icon">🏷️</span><span>Labels</span>
            </button>
            <button class="sidebar-item" id="nav-users" onclick="showTab('users')">
                <span class="icon">👥</span><span>{{ $lang === 'id' ? 'Pengguna' : 'Users' }}</span>
            </button>
            <hr class="sidebar-sep">
            <div class="sidebar-label">{{ $lang === 'id' ? 'Akun' : 'Account' }}</div>
            <button class="sidebar-item" id="nav-profile" onclick="showTab('profile')">
                <span class="icon">👤</span><span>{{ $lang === 'id' ? 'Profil' : 'Profile' }}</span>
            </button>
        </div>
    </nav>

    <div style="flex:1; display:flex; flex-direction:column; overflow:hidden;">
        {{-- Mobile user banner: hidden on desktop, shown on mobile when logged in --}}
        <div id="mobile-user-banner" style="display:none; align-items:center; justify-content:space-between; background:rgba(99,179,237,.06); border-bottom:1px solid rgba(99,179,237,.15); padding:10px 16px; font-size:12px; flex-shrink:0;">
            <span style="color:var(--accent); font-weight:600">👤 <span id="mobile-user-name">—</span></span>
            <button onclick="doLogout()" style="font-size:11px;padding:4px 10px;border:1px solid rgba(255,80,80,.3);border-radius:6px;background:rgba(255,80,80,.08);color:var(--red);cursor:pointer;font-family:inherit;font-weight:600;">Logout</button>
        </div>
        <main class="content" id="content">
            <div class="loading">{{ $lang === 'id' ? 'Memuat...' : 'Loading...' }}</div>
        </main>
    </div>
</div>

<footer>
    NexTask API &copy; {{ date('Y') }} &bull; Created by <a href="https://github.com/r1lz" target="_blank">r1lz</a>
</footer>

<div id="toast"></div>

<script>
const _lang = '{{ $lang }}';
const _t = {
    loading:     _lang==='id'?'Memuat...':'Loading...',
    empty:       _lang==='id'?'Belum ada data.':'No data yet.',
    noTasks:     _lang==='id'?'Belum ada tugas.':'No tasks yet.',
    saved:       _lang==='id'?'Berhasil disimpan!':'Saved!',
    deleted:     _lang==='id'?'Berhasil dihapus!':'Deleted!',
    confirmDel:  _lang==='id'?'Yakin ingin menghapus?':'Sure you want to delete?',
    create:      _lang==='id'?'+ Buat Baru':'+ Create New',
    cancel:      _lang==='id'?'Batal':'Cancel',
    save:        _lang==='id'?'Simpan':'Save',
    delete:      _lang==='id'?'Hapus':'Delete',
    edit:        _lang==='id'?'Edit':'Edit',
    tasks:       _lang==='id'?'Tugas':'Tasks',
    addTask:     _lang==='id'?'+ Tambah Tugas':'+ Add Task',
    statusLbl:   _lang==='id'?'Status':'Status',
    assignLbl:   _lang==='id'?'Ditugaskan ke':'Assigned to',
    noAssign:    _lang==='id'?'(Tidak ada)':'(None)',
};
let _s = { tab:null, projects:[], expandedId:null, taskCache:{}, labels:[], users:[] };

// ── Helpers ────────────────────────────────────────────────────
function escH(s){const d=document.createElement('div');d.appendChild(document.createTextNode(String(s??'')));return d.innerHTML;}
function toast(msg,type='ok'){const el=document.getElementById('toast');el.textContent=msg;el.className=type+' show';clearTimeout(el._t);el._t=setTimeout(()=>el.className='',2800);}
function switchLang(l){const u=new URL(window.location.href);u.searchParams.set('lang',l);window.location.href=u.toString();}
function setContent(html){document.getElementById('content').innerHTML=html;}
function toggleForm(id){const el=document.getElementById(id);if(el)el.style.display=el.style.display==='none'?'block':'none';}

// ── API ────────────────────────────────────────────────────────
async function api(method,path,body=null){
    const token=localStorage.getItem('api_token');
    const opts={method:method.toUpperCase(),headers:{'Content-Type':'application/json',...(token?{'Authorization':'Bearer '+token}:{})}};
    if(body)opts.body=JSON.stringify(body);
    const res=await fetch('/api'+path,opts);
    let json;try{json=await res.json();}catch(e){json={};}
    return{ok:res.ok,status:res.status,data:json.data??json,message:json.message??''};
}

// ── Session ────────────────────────────────────────────────────
async function initSession(){
    const token=localStorage.getItem('api_token');
    if(token){
        const r=await api('GET','/auth/me');
        if(r.ok){
            const name=r.data?.name??r.data?.data?.name??'';
            document.getElementById('topbar-user').textContent=name;
            const mun = document.getElementById('mobile-user-name');
            if(mun) mun.textContent=name;
            document.getElementById('topbar-user').style.display='inline-flex';
            document.getElementById('btn-logout').style.display='inline-flex';
            document.getElementById('nav-auth').style.display='block';
            document.getElementById('nav-guest').style.display='none';
            const ru=await api('GET','/users?per_page=100');
            _s.users=(ru.ok&&Array.isArray(ru.data?.data))?ru.data.data:(ru.ok&&Array.isArray(ru.data))?ru.data:[];
            if(!_s.tab||['login','register'].includes(_s.tab)) showTab('projects');
            else showTab(_s.tab);
            return;
        }
        localStorage.removeItem('api_token');
    }
    document.getElementById('topbar-user').style.display='none';
    document.getElementById('btn-logout').style.display='none';
    document.getElementById('nav-auth').style.display='none';
    document.getElementById('nav-guest').style.display='block';
    showTab('login');
}

async function doLogout(){
    if(!confirm(_lang==='id'?'Yakin ingin logout?':'Sure you want to logout?'))return;
    await api('POST','/auth/logout');
    localStorage.removeItem('api_token');
    toast(_lang==='id'?'Berhasil logout.':'Logged out.');
    initSession();
}

// ── Nav ────────────────────────────────────────────────────────
function showTab(tab){
    _s.tab=tab;
    document.querySelectorAll('.sidebar-item').forEach(b=>b.classList.remove('active'));
    const btn=document.getElementById('nav-'+tab);
    if(btn)btn.classList.add('active');
    if(tab==='login')renderLogin();
    else if(tab==='register')renderRegister();
    else if(tab==='projects')loadProjects();
    else if(tab==='labels')loadLabels();
    else if(tab==='users')loadUsers();
    else if(tab==='profile')loadProfile();
}

// ── Login ──────────────────────────────────────────────────────
function renderLogin(){
    setContent(`<div class="auth-center"><div class="auth-card">
        <div class="auth-title">🔑 Login</div>
        <div class="auth-sub">${_lang==='id'?'Akun bawaan:':'Default credentials:'}<br><b>default@example.com / default123</b></div>
        <form onsubmit="submitLogin(event)">
            <div class="field"><label>Email</label><input type="email" name="email" required value="default@example.com"></div>
            <div class="field"><label>Password</label><input type="password" name="password" required value="default123"></div>
            <div class="form-actions" style="margin-top:16px"><button type="submit" class="btn btn-primary btn-full">${_lang==='id'?'Masuk':'Login'}</button></div>
        </form>
        <div class="auth-switch">${_lang==='id'?'Belum punya akun?':"Don't have an account?"} <a onclick="showTab('register')">${_lang==='id'?'Daftar':'Register'}</a></div>
    </div></div>`);
}
async function submitLogin(e){
    e.preventDefault();const fd=new FormData(e.target);
    const r=await api('POST','/auth/login',{email:fd.get('email'),password:fd.get('password')});
    if(r.ok){localStorage.setItem('api_token',r.data?.token??r.data?.data?.token??r.data);toast(_lang==='id'?'Berhasil login!':'Login successful!');initSession();}
    else toast(r.message||'Login gagal','err');
}

// ── Register ───────────────────────────────────────────────────
function renderRegister(){
    setContent(`<div class="auth-center"><div class="auth-card">
        <div class="auth-title">✨ Register</div>
        <div class="auth-sub">${_lang==='id'?'Buat akun baru untuk memulai.':'Create a new account to get started.'}</div>
        <form onsubmit="submitRegister(event)">
            <div class="field"><label>${_lang==='id'?'Nama':'Name'}</label><input name="name" required placeholder="John Doe"></div>
            <div class="field"><label>Email</label><input type="email" name="email" required placeholder="john@example.com"></div>
            <div class="field"><label>Password</label><input type="password" name="password" required placeholder="Min. 8 karakter"></div>
            <div class="form-actions" style="margin-top:16px"><button type="submit" class="btn btn-primary btn-full">${_lang==='id'?'Daftar':'Register'}</button></div>
        </form>
        <div class="auth-switch">${_lang==='id'?'Sudah punya akun?':'Already have an account?'} <a onclick="showTab('login')">Login</a></div>
    </div></div>`);
}
async function submitRegister(e){
    e.preventDefault();const fd=new FormData(e.target);
    const r=await api('POST','/auth/register',{name:fd.get('name'),email:fd.get('email'),password:fd.get('password')});
    if(r.ok){localStorage.setItem('api_token',r.data?.token??r.data?.data?.token??r.data);toast(_lang==='id'?'Registrasi berhasil!':'Registration successful!');initSession();}
    else toast(r.message||'Register gagal','err');
}

// ── Projects ───────────────────────────────────────────────────
async function loadProjects(){
    setContent(`<div class="loading">${_t.loading}</div>`);
    const r=await api('GET','/projects?per_page=50');
    _s.projects=(r.ok&&Array.isArray(r.data?.data))?r.data.data:(r.ok&&Array.isArray(r.data))?r.data:[];
    const rl=await api('GET','/labels?per_page=100');
    _s.labels=(rl.ok&&Array.isArray(rl.data?.data))?rl.data.data:(rl.ok&&Array.isArray(rl.data))?rl.data:[];
    renderProjects();
}
async function loadTasksFor(pid){
    const r=await api('GET',`/projects/${pid}/tasks?per_page=100`);
    _s.taskCache[pid]=(r.ok&&Array.isArray(r.data?.data))?r.data.data:(r.ok&&Array.isArray(r.data))?r.data:[];
    renderProjects();
}
function renderProjects(){
    let html=`<div class="page-header"><div class="page-title">📁 ${_lang==='id'?'Project':'Projects'}</div><div class="page-sub">${_lang==='id'?'Kelola semua project kamu.':'Manage all your projects.'}</div></div>`;
    html+=`<button class="btn-add" onclick="toggleForm('pf-c')">${_t.create} Project</button>
    <div id="pf-c" style="display:none" class="form-box"><form onsubmit="createProject(event)">
        <div class="field"><label>${_lang==='id'?'Nama':'Name'} *</label><input name="name" required placeholder="${_lang==='id'?'Nama project':'Project name'}"></div>
        <div class="field"><label>${_lang==='id'?'Deskripsi':'Description'}</label><textarea name="description"></textarea></div>
        <div class="form-actions"><button type="submit" class="btn btn-primary">${_t.save}</button><button type="button" class="btn btn-ghost" onclick="toggleForm('pf-c')">${_t.cancel}</button></div>
    </form></div>`;
    if(!_s.projects.length){html+=`<div class="empty">${_t.empty}</div>`;}
    _s.projects.forEach(p=>{
        const exp=_s.expandedId===p.id;
        html+=`<div class="card">
        <div class="card-header">
            <div style="flex:1;cursor:pointer;min-width:0" onclick="toggleProject(${p.id})">
                <div class="card-title">${exp?'▾':'▸'} ${escH(p.name)}</div>
                <div class="card-sub">${p.tasks_count??0} ${_t.tasks}</div>
            </div>
            <div class="card-actions">
                <button class="btn btn-ghost" onclick="toggleForm('pe-${p.id}')">${_t.edit}</button>
                <button class="btn btn-danger" onclick="deleteProject(${p.id})">${_t.delete}</button>
            </div>
        </div>
        <div id="pe-${p.id}" style="display:none;margin-top:12px" class="form-box"><form onsubmit="editProject(event,${p.id})">
            <div class="field"><label>${_lang==='id'?'Nama':'Name'} *</label><input name="name" required value="${escH(p.name)}"></div>
            <div class="field"><label>${_lang==='id'?'Deskripsi':'Description'}</label><textarea name="description">${escH(p.description||'')}</textarea></div>
            <div class="form-actions"><button type="submit" class="btn btn-primary">${_t.save}</button><button type="button" class="btn btn-ghost" onclick="toggleForm('pe-${p.id}')">${_t.cancel}</button></div>
        </form></div>`;
        if(exp){
            html+=`<div class="tasks-section">`;
            const tasks=_s.taskCache[p.id];
            if(!tasks){html+=`<div class="loading" style="padding:10px 0">${_t.loading}</div>`;loadTasksFor(p.id);}
            else{
                html+=`<button class="btn-add" onclick="toggleForm('tc-${p.id}')">${_t.addTask}</button>
                <div id="tc-${p.id}" style="display:none" class="form-box"><form onsubmit="createTask(event,${p.id})">
                    <div class="field"><label>${_lang==='id'?'Judul':'Title'} *</label><input name="title" required></div>
                    <div class="field"><label>${_lang==='id'?'Deskripsi':'Description'}</label><textarea name="description"></textarea></div>
                    <div class="form-row">
                        <div class="field"><label>${_t.statusLbl}</label><select name="status"><option value="todo">Todo</option><option value="in_progress">In Progress</option><option value="done">Done</option></select></div>
                        <div class="field"><label>${_t.assignLbl}</label><select name="assigned_to"><option value="">${_t.noAssign}</option>${_s.users.map(u=>`<option value="${u.id}">${escH(u.name)}</option>`).join('')}</select></div>
                    </div>`;
                if(_s.labels.length)html+=`<div class="field"><label>Labels</label><div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:2px">${_s.labels.map(l=>`<label style="display:flex;align-items:center;gap:5px;font-size:12px;color:rgba(255,255,255,.6);cursor:pointer"><input type="checkbox" name="label_ids" value="${l.id}"> <span class="dot" style="background:${escH(l.color)}"></span>${escH(l.name)}</label>`).join('')}</div></div>`;
                html+=`<div class="form-actions"><button type="submit" class="btn btn-primary">${_t.save}</button><button type="button" class="btn btn-ghost" onclick="toggleForm('tc-${p.id}')">${_t.cancel}</button></div></form></div>`;
                if(!tasks.length)html+=`<div class="empty" style="padding:10px 0">${_t.noTasks}</div>`;
                tasks.forEach(tk=>{
                    const bc='badge-'+(tk.status||'todo');
                    html+=`<div class="task-row">
                        <span class="task-title" title="${escH(tk.description||'')}">${escH(tk.title)}</span>
                        <span class="badge ${bc}" onclick="cycleStatus(${tk.id},${p.id},'${tk.status}')">${tk.status}</span>
                        <button class="btn btn-ghost" style="padding:3px 8px" onclick="toggleForm('te-${tk.id}')">✎</button>
                        <button class="btn btn-danger" style="padding:3px 8px" onclick="deleteTask(${tk.id},${p.id})">✕</button>
                    </div>
                    <div id="te-${tk.id}" style="display:none;margin:6px 0 8px" class="form-box"><form onsubmit="editTask(event,${tk.id},${p.id})">
                        <div class="field"><label>${_lang==='id'?'Judul':'Title'} *</label><input name="title" required value="${escH(tk.title)}"></div>
                        <div class="field"><label>${_lang==='id'?'Deskripsi':'Description'}</label><textarea name="description">${escH(tk.description||'')}</textarea></div>
                        <div class="form-row">
                            <div class="field"><label>${_t.statusLbl}</label><select name="status">
                                <option value="todo" ${tk.status==='todo'?'selected':''}>Todo</option>
                                <option value="in_progress" ${tk.status==='in_progress'?'selected':''}>In Progress</option>
                                <option value="done" ${tk.status==='done'?'selected':''}>Done</option>
                            </select></div>
                            <div class="field"><label>${_t.assignLbl}</label><select name="assigned_to"><option value="">${_t.noAssign}</option>${_s.users.map(u=>`<option value="${u.id}" ${tk.assigned_to==u.id?'selected':''}>${escH(u.name)}</option>`).join('')}</select></div>
                        </div>`;
                    if(_s.labels.length){const tli=(tk.labels||[]).map(l=>l.id);html+=`<div class="field"><label>Labels</label><div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:2px">${_s.labels.map(l=>`<label style="display:flex;align-items:center;gap:5px;font-size:12px;color:rgba(255,255,255,.6);cursor:pointer"><input type="checkbox" name="label_ids" value="${l.id}" ${tli.includes(l.id)?'checked':''}> <span class="dot" style="background:${escH(l.color)}"></span>${escH(l.name)}</label>`).join('')}</div></div>`;}
                    html+=`<div class="form-actions"><button type="submit" class="btn btn-primary">${_t.save}</button><button type="button" class="btn btn-ghost" onclick="toggleForm('te-${tk.id}')">${_t.cancel}</button></div></form></div>`;
                });
            }
            html+=`</div>`;
        }
        html+=`</div>`;
    });
    setContent(html);
}

async function createProject(e){e.preventDefault();const fd=new FormData(e.target);const r=await api('POST','/projects',{name:fd.get('name'),description:fd.get('description')||null});if(r.ok){toast(_t.saved);_s.expandedId=r.data?.id??null;await loadProjects();}else toast(r.message||'Error','err');}
async function editProject(e,id){e.preventDefault();const fd=new FormData(e.target);const r=await api('PUT',`/projects/${id}`,{name:fd.get('name'),description:fd.get('description')||null});if(r.ok){toast(_t.saved);await loadProjects();}else toast(r.message||'Error','err');}
async function deleteProject(id){if(!confirm(_t.confirmDel))return;const r=await api('DELETE',`/projects/${id}`);if(r.ok){toast(_t.deleted);if(_s.expandedId===id)_s.expandedId=null;await loadProjects();}else toast(r.message||'Error','err');}
function toggleProject(id){_s.expandedId=(_s.expandedId===id)?null:id;renderProjects();if(_s.expandedId===id&&!_s.taskCache[id])loadTasksFor(id);}
async function createTask(e,pid){e.preventDefault();const fd=new FormData(e.target);const payload={title:fd.get('title'),description:fd.get('description')||null,status:fd.get('status')||'todo'};const at=fd.get('assigned_to');if(at)payload.assigned_to=at;const lids=[...e.target.querySelectorAll('input[name=label_ids]:checked')].map(i=>Number(i.value));if(lids.length)payload.label_ids=lids;const r=await api('POST',`/projects/${pid}/tasks`,payload);if(r.ok){toast(_t.saved);await loadTasksFor(pid);}else toast(r.message||'Error','err');}
async function editTask(e,tid,pid){e.preventDefault();const fd=new FormData(e.target);const payload={title:fd.get('title'),description:fd.get('description')||null,status:fd.get('status')};const at=fd.get('assigned_to');payload.assigned_to=at?at:null;const lids=[...e.target.querySelectorAll('input[name=label_ids]:checked')].map(i=>Number(i.value));payload.label_ids=lids;const r=await api('PUT',`/tasks/${tid}`,payload);if(r.ok){toast(_t.saved);await loadTasksFor(pid);}else toast(r.message||'Error','err');}
async function cycleStatus(tid,pid,cur){const c={todo:'in_progress',in_progress:'done',done:'todo'};const r=await api('PUT',`/tasks/${tid}`,{status:c[cur]||'todo'});if(r.ok)await loadTasksFor(pid);else toast(r.message||'Error','err');}
async function deleteTask(tid,pid){if(!confirm(_t.confirmDel))return;const r=await api('DELETE',`/tasks/${tid}`);if(r.ok){toast(_t.deleted);await loadTasksFor(pid);}else toast(r.message||'Error','err');}

// ── Labels ─────────────────────────────────────────────────────
async function loadLabels(){
    setContent(`<div class="loading">${_t.loading}</div>`);
    const r=await api('GET','/labels?per_page=100');
    _s.labels=(r.ok&&Array.isArray(r.data?.data))?r.data.data:(r.ok&&Array.isArray(r.data))?r.data:[];
    let html=`<div class="page-header"><div class="page-title">🏷️ Labels</div><div class="page-sub">${_lang==='id'?'Label berwarna untuk organisasi tugas.':'Color tags to organize your tasks.'}</div></div>`;
    html+=`<button class="btn-add" onclick="toggleForm('lf-c')">${_t.create} Label</button>
    <div id="lf-c" style="display:none" class="form-box"><form onsubmit="createLabel(event)">
        <div class="form-row">
            <div class="field"><label>${_lang==='id'?'Nama':'Name'} *</label><input name="name" required placeholder="Bug"></div>
            <div class="field" style="flex:0 0 90px"><label>${_lang==='id'?'Warna':'Color'}</label><input name="color" type="color" value="#63b3ed"></div>
        </div>
        <div class="form-actions"><button type="submit" class="btn btn-primary">${_t.save}</button><button type="button" class="btn btn-ghost" onclick="toggleForm('lf-c')">${_t.cancel}</button></div>
    </form></div>`;
    if(!_s.labels.length){html+=`<div class="empty">${_t.empty}</div>`;}
    _s.labels.forEach(l=>{html+=`<div class="card"><div class="card-header"><span class="dot" style="background:${escH(l.color)};width:14px;height:14px;margin-top:2px"></span><div class="card-title">${escH(l.name)}</div><div class="card-actions"><button class="btn btn-danger" onclick="deleteLabel(${l.id})">${_t.delete}</button></div></div></div>`;});
    setContent(html);
}
async function createLabel(e){e.preventDefault();const fd=new FormData(e.target);const r=await api('POST','/labels',{name:fd.get('name'),color:fd.get('color')});if(r.ok){toast(_t.saved);await loadLabels();}else toast(r.message||'Error','err');}
async function deleteLabel(id){if(!confirm(_t.confirmDel))return;const r=await api('DELETE',`/labels/${id}`);if(r.ok){toast(_t.deleted);await loadLabels();}else toast(r.message||'Error','err');}

// ── Users ──────────────────────────────────────────────────────
async function loadUsers(){
    setContent(`<div class="loading">${_t.loading}</div>`);
    const r=await api('GET','/users?per_page=100');
    _s.users=(r.ok&&Array.isArray(r.data?.data))?r.data.data:(r.ok&&Array.isArray(r.data))?r.data:[];
    let html=`<div class="page-header"><div class="page-title">👥 ${_lang==='id'?'Pengguna':'Users'}</div><div class="page-sub">${_lang==='id'?'Semua pengguna terdaftar.':'All registered users.'}</div></div><div class="card">`;
    if(!_s.users.length)html+=`<div class="empty">${_t.empty}</div>`;
    _s.users.forEach(u=>{const i=(u.name||'?').split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase();html+=`<div class="user-row"><div class="avatar">${i}</div><div><div style="font-size:14px;font-weight:600">${escH(u.name)}</div><div style="font-size:11px;color:var(--sub)">ID: ${u.id}</div></div></div>`;});
    html+=`</div>`;
    setContent(html);
}

// ── Profile ────────────────────────────────────────────────────
async function loadProfile(){
    setContent(`<div class="loading">${_t.loading}</div>`);
    const r=await api('GET','/auth/me');
    if(!r.ok){setContent(`<div class="empty">Error.</div>`);return;}
    const user=r.data?.data??r.data??{};
    setContent(`<div class="page-header"><div class="page-title">👤 ${_lang==='id'?'Profil Saya':'My Profile'}</div></div>
    <div class="card" style="max-width:480px"><form onsubmit="updateProfile(event,${user.id})">
        <div class="profile-avatar-big">${(user.name||'?')[0]}</div>
        <div class="field"><label>${_lang==='id'?'Nama':'Name'}</label><input name="name" required value="${escH(user.name)}"></div>
        <div class="field"><label>Email</label><input value="${escH(user.email||'')}" disabled style="opacity:0.5"></div>
        <div class="field"><label>${_lang==='id'?'Password Baru (kosongkan jika tidak diubah)':'New Password (leave blank to keep current)'}</label><input type="password" name="password" placeholder="•••••••"></div>
        <div class="field"><label>${_lang==='id'?'Konfirmasi Password':'Confirm Password'}</label><input type="password" name="password_confirmation" placeholder="•••••••"></div>
        <div class="form-actions" style="margin-top:16px">
            <button type="submit" class="btn btn-primary" style="flex:1;padding:9px">${_lang==='id'?'Simpan Profil':'Save Profile'}</button>
            <button type="button" class="btn btn-danger" onclick="deleteProfile(${user.id})">${_lang==='id'?'Hapus Akun':'Delete Account'}</button>
        </div>
    </form></div>`);
}
async function updateProfile(e,id){e.preventDefault();const fd=new FormData(e.target);const payload={name:fd.get('name')};if(fd.get('password')){payload.password=fd.get('password');payload.password_confirmation=fd.get('password_confirmation');}const r=await api('PUT',`/users/${id}`,payload);if(r.ok){toast(_t.saved);await loadProfile();initSession();}else toast(r.message||'Error','err');}
async function deleteProfile(id){if(!confirm(_lang==='id'?'PERINGATAN: Akun akan dihapus permanen! Yakin?':'WARNING: Account will be permanently deleted! Sure?'))return;const r=await api('DELETE',`/users/${id}`);if(r.ok){toast(_t.deleted);localStorage.removeItem('api_token');initSession();}else toast(r.message||'Error','err');}

// ── Boot ───────────────────────────────────────────────────────
initSession();
</script>
</body>
</html>
