<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management API</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #1e293b;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        header h1 { font-size: 1rem; font-weight: 600; color: #f1f5f9; }
        header span { font-size: 0.7rem; color: #64748b; margin-left: 0.4rem; }

        .badge {
            margin-left: auto;
            background: #16a34a22;
            color: #4ade80;
            border: 1px solid #16a34a44;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.35rem;
            flex-shrink: 0;
        }

        .badge::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #4ade80;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        main {
            flex: 1;
            max-width: 860px;
            margin: 0 auto;
            padding: 2.5rem 1rem;
            width: 100%;
        }

        .hero {
            text-align: center;
            margin-bottom: 2.5rem;
            padding: 0 0.5rem;
        }

        .hero h2 {
            font-size: clamp(1.75rem, 5vw, 2.5rem);
            font-weight: 700;
            background: linear-gradient(135deg, #f1f5f9, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.75rem;
            line-height: 1.2;
        }

        .hero p {
            color: #64748b;
            font-size: clamp(0.9rem, 2.5vw, 1.05rem);
            max-width: 480px;
            margin: 0 auto 1.5rem;
            line-height: 1.6;
        }

        .cta-group {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.65rem 1.25rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
        }

        .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }

        .btn-outline {
            border: 1px solid #334155;
            color: #94a3b8;
            background: transparent;
        }

        .btn-outline:hover { border-color: #6366f1; color: #f1f5f9; transform: translateY(-1px); }

        .base-url {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 10px;
            padding: 0.85rem 1rem;
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-family: 'Courier New', monospace;
            overflow: hidden;
        }

        .base-url label {
            color: #64748b;
            font-size: 0.7rem;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .base-url span {
            color: #a5f3fc;
            font-size: clamp(0.65rem, 2vw, 0.85rem);
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .copy-btn {
            background: #334155;
            border: none;
            color: #94a3b8;
            padding: 0.3rem 0.65rem;
            border-radius: 6px;
            font-size: 0.72rem;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .copy-btn:hover { background: #475569; color: #f1f5f9; }

        .section-title {
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.75rem;
        }

        .endpoints { margin-bottom: 2.5rem; }

        .endpoint-group { margin-bottom: 1.25rem; }

        .group-label {
            font-size: 0.78rem;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 0.4rem;
            padding-left: 0.5rem;
        }

        .endpoint {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.55rem 0.75rem;
            border-radius: 8px;
            margin-bottom: 0.3rem;
            background: #1e293b;
            border: 1px solid #1e293b;
            transition: border-color 0.2s;
            overflow: hidden;
        }

        .endpoint:hover { border-color: #334155; }

        .method {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.18rem 0.4rem;
            border-radius: 4px;
            width: 46px;
            text-align: center;
            flex-shrink: 0;
            font-family: monospace;
        }

        .get    { background: #0d9488; color: #fff; }
        .post   { background: #2563eb; color: #fff; }
        .put    { background: #d97706; color: #fff; }
        .delete { background: #dc2626; color: #fff; }

        .path {
            font-family: 'Courier New', monospace;
            font-size: clamp(0.65rem, 2vw, 0.82rem);
            color: #cbd5e1;
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .path .param { color: #f59e0b; }

        .desc {
            font-size: 0.72rem;
            color: #64748b;
            white-space: nowrap;
            flex-shrink: 0;
            display: none;
        }

        .auth-badge {
            font-size: 0.6rem;
            background: #0f172a;
            border: 1px solid #334155;
            color: #64748b;
            padding: 0.12rem 0.35rem;
            border-radius: 4px;
            flex-shrink: 0;
        }

        @media (min-width: 600px) {
            .desc { display: block; }
            header { padding: 1.25rem 2rem; }
            header h1 { font-size: 1.15rem; }
            main { padding: 3.5rem 2rem; }
            .hero { margin-bottom: 3rem; }
            .base-url { padding: 1rem 1.5rem; }
            .endpoint { padding: 0.6rem 1rem; gap: 0.75rem; }
            .method { width: 52px; font-size: 0.7rem; }
        }

        .stack-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-bottom: 2.5rem;
        }

        @media (min-width: 480px) {
            .stack-grid { grid-template-columns: repeat(3, 1fr); }
        }

        @media (min-width: 700px) {
            .stack-grid { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); }
        }

        .stack-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 10px;
            padding: 0.85rem;
        }

        .stack-card h4 { font-size: 0.82rem; font-weight: 600; color: #f1f5f9; margin-bottom: 0.2rem; }
        .stack-card p  { font-size: 0.72rem; color: #64748b; }

        footer {
            padding: 1.25rem 1rem;
            border-top: 1px solid #1e293b;
            text-align: center;
            color: #475569;
            font-size: 0.78rem;
            line-height: 1.8;
        }

        footer a { color: #6366f1; text-decoration: none; }
        footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<header>
    <div class="logo">⚡</div>
    <div>
        <h1>Task Management API <span>v1.0</span></h1>
    </div>
    <div class="badge">Live</div>
</header>

<main>
    <div class="hero">
        <h2>Task Management<br>REST API</h2>
        <p>A clean, structured API for managing projects and tasks. Built with Laravel 13 and Sanctum authentication.</p>
        <div class="cta-group">
            <a href="/api/documentation" class="btn btn-primary">Swagger Docs</a>
            <a href="https://github.com/mohamed-hamdy1997/electro-pi" target="_blank" class="btn btn-outline">GitHub</a>
        </div>
    </div>

    <div class="base-url">
        <label>BASE URL</label>
        <span id="base-url">https://electro-pi-management.up.railway.app/api/v1</span>
        <button class="copy-btn" onclick="copyUrl()">Copy</button>
    </div>

    <div class="endpoints">
        <div class="section-title">Endpoints</div>

        <div class="endpoint-group">
            <div class="group-label">Auth</div>
            <div class="endpoint">
                <span class="method post">POST</span>
                <span class="path">/auth/register</span>
                <span class="desc">Register</span>
            </div>
            <div class="endpoint">
                <span class="method post">POST</span>
                <span class="path">/auth/login</span>
                <span class="desc">Login</span>
            </div>
            <div class="endpoint">
                <span class="method post">POST</span>
                <span class="path">/auth/logout</span>
                <span class="desc">Logout</span>
                <span class="auth-badge">Auth</span>
            </div>
        </div>

        <div class="endpoint-group">
            <div class="group-label">Dashboard</div>
            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="path">/dashboard</span>
                <span class="desc">Statistics</span>
                <span class="auth-badge">Auth</span>
            </div>
        </div>

        <div class="endpoint-group">
            <div class="group-label">Projects</div>
            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="path">/projects</span>
                <span class="desc">List</span>
                <span class="auth-badge">Auth</span>
            </div>
            <div class="endpoint">
                <span class="method post">POST</span>
                <span class="path">/projects</span>
                <span class="desc">Create</span>
                <span class="auth-badge">Auth</span>
            </div>
            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="path">/projects/<span class="param">{id}</span></span>
                <span class="desc">View</span>
                <span class="auth-badge">Auth</span>
            </div>
            <div class="endpoint">
                <span class="method put">PUT</span>
                <span class="path">/projects/<span class="param">{id}</span></span>
                <span class="desc">Update</span>
                <span class="auth-badge">Auth</span>
            </div>
            <div class="endpoint">
                <span class="method delete">DEL</span>
                <span class="path">/projects/<span class="param">{id}</span></span>
                <span class="desc">Delete</span>
                <span class="auth-badge">Auth</span>
            </div>
        </div>

        <div class="endpoint-group">
            <div class="group-label">Tasks</div>
            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="path">/projects/<span class="param">{project}</span>/tasks</span>
                <span class="desc">List · filter · search</span>
                <span class="auth-badge">Auth</span>
            </div>
            <div class="endpoint">
                <span class="method post">POST</span>
                <span class="path">/projects/<span class="param">{project}</span>/tasks</span>
                <span class="desc">Create</span>
                <span class="auth-badge">Auth</span>
            </div>
            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="path">/projects/<span class="param">{project}</span>/tasks/<span class="param">{task}</span></span>
                <span class="desc">View</span>
                <span class="auth-badge">Auth</span>
            </div>
            <div class="endpoint">
                <span class="method put">PUT</span>
                <span class="path">/projects/<span class="param">{project}</span>/tasks/<span class="param">{task}</span></span>
                <span class="desc">Update</span>
                <span class="auth-badge">Auth</span>
            </div>
            <div class="endpoint">
                <span class="method delete">DEL</span>
                <span class="path">/projects/<span class="param">{project}</span>/tasks/<span class="param">{task}</span></span>
                <span class="desc">Delete</span>
                <span class="auth-badge">Auth</span>
            </div>
        </div>
    </div>

    <div class="section-title">Tech Stack</div>
    <div class="stack-grid">
        <div class="stack-card">
            <h4>Laravel 13</h4>
            <p>PHP 8.4 framework</p>
        </div>
        <div class="stack-card">
            <h4>Sanctum</h4>
            <p>API token auth</p>
        </div>
        <div class="stack-card">
            <h4>MySQL</h4>
            <p>Relational database</p>
        </div>
        <div class="stack-card">
            <h4>Repository Pattern</h4>
            <p>+ Service Layer</p>
        </div>
        <div class="stack-card">
            <h4>Swagger UI</h4>
            <p>OpenAPI 3.0 docs</p>
        </div>
        <div class="stack-card">
            <h4>Docker</h4>
            <p>Containerised setup</p>
        </div>
    </div>
</main>

<footer>
    Built with Laravel 13 By Mohamed Hamdy &nbsp;·&nbsp;
    <a href="/api/documentation">API Docs</a> &nbsp;·&nbsp;
    <a href="https://github.com/mohamed-hamdy1997/electro-pi" target="_blank">GitHub</a>
</footer>

<script>
    function copyUrl() {
        const url = document.getElementById('base-url').textContent;
        navigator.clipboard.writeText(url).then(() => {
            const btn = document.querySelector('.copy-btn');
            btn.textContent = 'Copied!';
            setTimeout(() => btn.textContent = 'Copy', 2000);
        });
    }
</script>

</body>
</html>
