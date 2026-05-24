<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Login - Shiloh Learning Center</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --login-bg: #f5f5f7;
            --login-card: rgba(255, 255, 255, 0.82);
            --login-card-border: rgba(0, 0, 0, 0.06);
            --login-card-shadow: 0 8px 40px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04);
            --login-input-bg: #f9fafb;
            --login-input-border: #e2e8f0;
            --login-input-focus: #0d9488;
            --login-input-focus-ring: rgba(13, 148, 136, 0.18);
            --login-text: #1e293b;
            --login-text-secondary: #64748b;
            --login-text-muted: #94a3b8;
            --login-accent: #0d9488;
            --login-accent-hover: #0f766e;
            --login-accent-active: #115e59;
            --login-error-bg: #fef2f2;
            --login-error-border: #fecaca;
            --login-error-text: #991b1b;
            --login-error-icon: #ef4444;
        }

        html, body {
            height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* === BACKGROUND === */
        body {
            background: var(--login-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Subtle premium gradient orbs */
        body::before,
        body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(80px);
            opacity: 0.5;
        }
        body::before {
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(13, 148, 136, 0.12) 0%, transparent 70%);
            top: -15%; right: -10%;
        }
        body::after {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, transparent 70%);
            bottom: -10%; left: -8%;
        }

        /* === WRAPPER === */
        .login-wrapper {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
            animation: pageEnter 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes pageEnter {
            from { opacity: 0; transform: translateY(24px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* === HEADER === */
        .login-header {
            text-align: center;
            margin-bottom: 2.25rem;
        }

        .login-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .login-logo img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 6px;
        }

        .login-title {
            font-size: clamp(1.5rem, 4vw, 1.75rem);
            font-weight: 700;
            color: var(--login-text);
            letter-spacing: -0.025em;
            line-height: 1.2;
        }

        .login-subtitle {
            font-size: 0.9375rem;
            color: var(--login-text-secondary);
            margin-top: 0.5rem;
            font-weight: 400;
        }

        /* === CARD === */
        .login-card {
            background: var(--login-card);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--login-card-border);
            border-radius: 20px;
            box-shadow: var(--login-card-shadow);
            padding: 2.25rem 2rem;
        }

        @media (min-width: 480px) {
            .login-card { padding: 2.5rem; }
        }

        /* === ERROR ALERT === */
        .login-error {
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            padding: 0.875rem 1rem;
            background: var(--login-error-bg);
            border: 1px solid var(--login-error-border);
            border-radius: 12px;
            margin-bottom: 1.5rem;
            animation: shakeIn 0.4s ease-out;
        }

        @keyframes shakeIn {
            0%   { transform: translateX(0); }
            25%  { transform: translateX(-6px); }
            50%  { transform: translateX(4px); }
            75%  { transform: translateX(-2px); }
            100% { transform: translateX(0); }
        }

        .login-error-icon {
            flex-shrink: 0;
            width: 18px; height: 18px;
            color: var(--login-error-icon);
            margin-top: 1px;
        }

        .login-error-text {
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--login-error-text);
            line-height: 1.45;
        }

        /* === FORM === */
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        /* === FIELD GROUP === */
        .field-group { display: flex; flex-direction: column; gap: 0.4375rem; }

        .field-label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--login-text);
            letter-spacing: 0.01em;
        }

        .field-input {
            width: 100%;
            height: 48px;
            padding: 0 1rem;
            font-size: 0.9375rem;
            font-family: inherit;
            color: var(--login-text);
            background: var(--login-input-bg);
            border: 1.5px solid var(--login-input-border);
            border-radius: 12px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .field-input::placeholder {
            color: var(--login-text-muted);
        }

        .field-input:hover {
            border-color: #cbd5e1;
        }

        .field-input:focus {
            border-color: var(--login-input-focus);
            box-shadow: 0 0 0 3px var(--login-input-focus-ring);
            background: #fff;
        }

        /* === PASSWORD WRAPPER === */
        .password-wrapper {
            position: relative;
        }

        .password-wrapper .field-input {
            padding-right: 3rem;
        }

        .password-toggle {
            position: absolute;
            top: 50%; right: 0.75rem;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px; height: 32px;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--login-text-muted);
            border-radius: 8px;
            transition: color 0.15s ease, background 0.15s ease;
            padding: 0;
        }

        .password-toggle:hover {
            color: var(--login-text-secondary);
            background: rgba(0,0,0,0.04);
        }

        .password-toggle svg {
            width: 18px; height: 18px;
        }

        /* === REMEMBER + OPTIONS ROW === */
        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .remember-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .remember-checkbox {
            width: 18px; height: 18px;
            accent-color: var(--login-accent);
            border-radius: 4px;
            cursor: pointer;
        }

        .remember-label {
            font-size: 0.8125rem;
            color: var(--login-text-secondary);
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }

        /* === SUBMIT BUTTON === */
        .login-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 50px;
            padding: 0 1.5rem;
            font-size: 0.9375rem;
            font-weight: 600;
            font-family: inherit;
            letter-spacing: 0.01em;
            color: #fff;
            background: linear-gradient(180deg, var(--login-accent) 0%, var(--login-accent-hover) 100%);
            border: none;
            border-radius: 14px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(13, 148, 136, 0.25), inset 0 1px 0 rgba(255,255,255,0.15);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            margin-top: 0.25rem;
        }

        .login-btn:hover {
            background: linear-gradient(180deg, var(--login-accent-hover) 0%, var(--login-accent-active) 100%);
            box-shadow: 0 4px 16px rgba(13, 148, 136, 0.35), inset 0 1px 0 rgba(255,255,255,0.15);
            transform: translateY(-1px);
        }

        .login-btn:active {
            transform: translateY(0);
            box-shadow: 0 1px 4px rgba(13, 148, 136, 0.2);
        }

        .login-btn:focus-visible {
            outline: 2px solid var(--login-accent);
            outline-offset: 2px;
        }

        /* Loading state */
        .login-btn.is-loading {
            pointer-events: none;
            opacity: 0.85;
        }

        .login-btn.is-loading .btn-text { opacity: 0; }

        .login-btn.is-loading .btn-spinner {
            display: block;
        }

        .btn-spinner {
            display: none;
            position: absolute;
            width: 20px; height: 20px;
            border: 2.5px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* === FOOTER === */
        .login-footer {
            text-align: center;
            margin-top: 2rem;
        }

        .login-footer-divider {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .login-footer-divider::before {
            content: '';
            position: absolute;
            top: 50%; left: 0; right: 0;
            height: 1px;
            background: #e2e8f0;
        }

        .login-footer-divider span {
            position: relative;
            background: var(--login-bg);
            padding: 0 1rem;
            font-size: 0.75rem;
            color: var(--login-text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .login-copyright {
            font-size: 0.75rem;
            color: var(--login-text-muted);
            line-height: 1.5;
        }

        /* === RESPONSIVE === */
        @media (max-width: 380px) {
            body { padding: 1rem; }
            .login-card { padding: 1.5rem 1.25rem; border-radius: 16px; }
            .login-logo img { width: 80px; height: 80px; }
            .field-input, .login-btn { height: 46px; }
        }

        @media (min-width: 1200px) {
            .login-wrapper { max-width: 460px; }
            .login-card { padding: 3rem; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- Header -->
        <div class="login-header">
            <div class="login-logo">
                <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Shiloh Learning Center Logo">
            </div>
            <h1 class="login-title">Shiloh Learning Center</h1>
            <p class="login-subtitle">Sign in to your account</p>
        </div>

        <!-- Card -->
        <div class="login-card">
            <!-- Error Alert -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                <div class="login-error" role="alert">
                    <svg class="login-error-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span class="login-error-text"><?php echo e($errors->first()); ?></span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Form — all fields, action, CSRF, method preserved -->
            <form class="login-form" action="<?php echo e(route('unified.login')); ?>" method="POST" id="loginForm">
                <?php echo csrf_field(); ?>

                <div class="field-group">
                    <label for="email" class="field-label">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           value="<?php echo e(old('email')); ?>"
                           placeholder="you@example.com"
                           class="field-input">
                </div>

                <div class="field-group">
                    <label for="password" class="field-label">Password</label>
                    <div class="password-wrapper">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               placeholder="Enter your password"
                               class="field-input">
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Show password">
                            <svg id="eyeOff" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            <svg id="eyeOn" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <div class="options-row">
                    <label class="remember-group">
                        <input id="remember" name="remember" type="checkbox" class="remember-checkbox">
                        <span class="remember-label">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="login-btn" id="loginBtn">
                    <span class="btn-text">Sign in</span>
                    <span class="btn-spinner"></span>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            <div class="login-footer-divider">
                <span>Secure login</span>
            </div>
            <p class="login-copyright">&copy; <?php echo e(date('Y')); ?> Shiloh Learning Center</p>
        </div>
    </div>

    <script>
        // Loading spinner on submit
        document.getElementById('loginForm').addEventListener('submit', function () {
            var btn = document.getElementById('loginBtn');
            btn.classList.add('is-loading');
        });

        // Show/hide password toggle
        document.getElementById('togglePassword').addEventListener('click', function () {
            var pw = document.getElementById('password');
            var isHidden = pw.type === 'password';
            pw.type = isHidden ? 'text' : 'password';
            document.getElementById('eyeOff').style.display = isHidden ? 'none' : 'block';
            document.getElementById('eyeOn').style.display = isHidden ? 'block' : 'none';
            this.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            pw.focus();
        });
    </script>
</body>
</html>
<?php /**PATH C:\Users\pc1\Downloads\Attendance-Payment\resources\views/auth/login.blade.php ENDPATH**/ ?>