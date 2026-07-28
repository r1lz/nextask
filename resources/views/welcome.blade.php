<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexTask - API & Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f172a;
            --surface: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
            --primary: #3b82f6;
            --primary-hover: #60a5fa;
            --secondary: #10b981;
            --secondary-hover: #34d399;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -10%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(37,99,235,0.08) 0%, transparent 70%);
            z-index: -1;
        }

        body::after {
            content: '';
            position: absolute;
            bottom: -10%;
            right: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(16,185,129,0.08) 0%, transparent 70%);
            z-index: -1;
        }

        .container {
            max-width: 800px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .header h1 {
            font-size: 2.75rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            margin-bottom: 1rem;
            color: var(--text-main);
        }

        .header p {
            font-size: 1.125rem;
            color: var(--text-muted);
            font-weight: 300;
            max-width: 500px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .option-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: all 0.2s ease;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
        }

        .option-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        .icon-wrapper {
            width: 64px;
            height: 64px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
        }

        .api-icon {
            background-color: rgba(37, 99, 235, 0.1);
            color: var(--primary);
        }

        .app-icon {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--secondary);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-direction: column;
        }

        .badge {
            background-color: var(--primary);
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .card-desc {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 2rem;
            flex-grow: 1;
            font-weight: 400;
        }

        .btn {
            padding: 0.6rem 1.5rem;
            border-radius: 9999px;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-block;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .option-card:hover .btn-primary {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: var(--surface);
            color: var(--text-main);
            border: 1px solid var(--border);
        }

        .option-card:hover .btn-secondary {
            border-color: var(--secondary);
            color: var(--secondary);
            background-color: rgba(16, 185, 129, 0.05);
        }

        /* Mobile Optimization */
        @media (max-width: 640px) {
            body {
                padding: 1.5rem;
                align-items: flex-start;
            }
            .header {
                margin-top: 1rem;
                margin-bottom: 2.5rem;
            }
            .header h1 {
                font-size: 2rem;
            }
            .header p {
                font-size: 1rem;
            }
            .options-grid {
                grid-template-columns: 1fr;
                gap: 1.25rem;
            }
            .option-card {
                padding: 2rem 1.5rem;
                border-radius: 1rem;
            }
            .icon-wrapper {
                width: 56px;
                height: 56px;
                font-size: 1.5rem;
                margin-bottom: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>NexTask</h1>
            <p>Welcome to NexTask. Select how you want to experience the platform.</p>
        </div>

        <div class="options-grid">
            <!-- Documentation Card -->
            <a href="/docs/api" class="option-card">
                <span class="badge">Recommended</span>
                <div class="icon-wrapper api-icon">
                    <svg xmlns="http://www.w3.org/-svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                </div>
                <div class="card-title">
                    API Documentation
                </div>
                <div class="card-desc">
                    Explore the REST API endpoints interactively. See how the engine handles projects, tasks, and authentication under the hood.
                </div>
                <div class="btn btn-primary">Read Docs</div>
            </a>

            <!-- Dashboard Card -->
            <a href="/dashboard" class="option-card">
                <div class="icon-wrapper app-icon">
                    <svg xmlns="http://www.w3.org/-svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                </div>
                <div class="card-title">
                    App Dashboard
                </div>
                <div class="card-desc">
                    Experience the visual interface as a user. Manage your tasks, create projects, and see the API in action through the frontend.
                </div>
                <div class="btn btn-secondary">Open App</div>
            </a>
        </div>
    </div>
</body>
</html>
