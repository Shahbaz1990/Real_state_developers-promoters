<?php
// No PHP code needed here - just the HTML/JS
?>
<!-- ============ AUTH MODAL (Login/Register Tabs) ============ -->
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="authModalLabel">Welcome</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Tabs -->
                <ul class="nav nav-tabs auth-tabs" id="authTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-panel" type="button" role="tab" aria-controls="login-panel" aria-selected="true">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-panel" type="button" role="tab" aria-controls="register-panel" aria-selected="false">
                            <i class="bi bi-person-plus me-1"></i> Register
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content pt-3" id="authTabContent">
                    <!-- LOGIN PANEL -->
                    <div class="tab-pane fade show active" id="login-panel" role="tabpanel" aria-labelledby="login-tab">
                        <div id="loginAlert" class="alert" style="display:none;"></div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email"
                                   class="form-control"
                                   id="loginEmailInput"
                                   placeholder="Enter your email"
                                   autocomplete="off"
                                   aria-autocomplete="none">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password"
                                   class="form-control"
                                   id="loginPasswordInput"
                                   placeholder="Enter your password"
                                   minlength="6"
                                   autocomplete="off"
                                   aria-autocomplete="none">
                        </div>
                        <div class="text-end mb-3">
                            <a href="#" class="text-decoration-none small" id="forgotPasswordLink">Forgot password?</a>
                        </div>
                        <button class="btn btn-primary w-100 py-2" id="loginSubmitBtn">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </button>
                        <div class="text-center mt-3">
                            <span class="text-muted small">Don't have an account? </span>
                            <a href="#" id="switchToRegisterTab" class="text-decoration-none small fw-bold">Register here</a>
                        </div>
                    </div>

                    <!-- REGISTER PANEL -->
                    <div class="tab-pane fade" id="register-panel" role="tabpanel" aria-labelledby="register-tab">
                        <div id="registerAlert" class="alert" style="display:none;"></div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text"
                                   class="form-control"
                                   id="registerFullname"
                                   placeholder="Enter your full name"
                                   autocomplete="off"
                                   aria-autocomplete="none">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email"
                                   class="form-control"
                                   id="registerEmail"
                                   placeholder="Enter your email"
                                   autocomplete="off"
                                   aria-autocomplete="none">
                            <div id="registerEmailStatus" class="small mt-1" style="display:none;"></div>
                        </div>
                        <!-- NEW: Role selector (added from step-based version) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Role</label>
                            <select class="form-select" id="registerRole">
                                <option value="buyer" selected>Buyer</option>
                                <option value="seller">Seller</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mobile Number</label>
                            <input type="tel"
                                   class="form-control"
                                   id="registerMobile"
                                   placeholder="Enter mobile number"
                                   autocomplete="off"
                                   aria-autocomplete="none">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password"
                                   class="form-control"
                                   id="registerPassword"
                                   placeholder="Create password (min 6 chars)"
                                   minlength="6"
                                   autocomplete="off"
                                   aria-autocomplete="none">
                            <small class="text-muted">Password must be at least 6 characters</small>
                        </div>
                        <div class="mb-3" id="otpSection" style="display:none;">
                            <label class="form-label fw-bold">Enter OTP</label>
                            <div class="input-group">
                                <input type="text"
                                       class="form-control text-center"
                                       id="registerOtp"
                                       placeholder="6-digit OTP"
                                       maxlength="6"
                                       style="font-size:24px;letter-spacing:10px;"
                                       autocomplete="off"
                                       aria-autocomplete="none">
                                <button class="btn btn-outline-primary" type="button" id="resendOtpBtn">Resend</button>
                            </div>
                            <div id="otpTimer" class="small text-muted mt-1"></div>
                            <!-- NEW: dev OTP hint (added from step-based version) -->
                            <div id="otpActionInfo" class="small text-primary mt-1"></div>
                        </div>
                        <button class="btn btn-success w-100 py-2" id="registerSubmitBtn">
                            <i class="bi bi-person-plus me-1"></i> Register
                        </button>
                        <div class="text-center mt-3">
                            <span class="text-muted small">Already have an account? </span>
                            <a href="#" id="switchToLoginTab" class="text-decoration-none small fw-bold">Login here</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Auth Modal Tabs Styles */
#authModal .modal-content {
    border-radius: 1rem;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    overflow: hidden;
}

#authModal .auth-tabs {
    border-bottom: 2px solid #e7e9ee;
    margin-bottom: 0;
}

#authModal .auth-tabs .nav-link {
    border: none;
    background: transparent;
    color: #6b7686;
    font-weight: 600;
    font-size: 0.95rem;
    padding: 0.75rem 1.5rem;
    border-radius: 0;
    position: relative;
    transition: all 0.2s ease;
}

#authModal .auth-tabs .nav-link:hover {
    color: #12213a;
    background: transparent;
}

#authModal .auth-tabs .nav-link.active {
    color: #12213a;
    background: transparent;
    border-bottom: 3px solid #f0b429;
}

#authModal .auth-tabs .nav-link i {
    font-size: 1.1rem;
}

#authModal .form-control,
#authModal .form-select {
    border-radius: 0.5rem;
    padding: 0.7rem 1rem;
    border: 1px solid #e7e9ee;
    background: #fff;
}

#authModal .form-control:focus,
#authModal .form-select:focus {
    border-color: #f0b429;
    box-shadow: 0 0 0 0.2rem rgba(240, 180, 41, 0.15);
}

/* NEW: Prevent autofill background color (added from step-based version) */
#authModal .form-control:-webkit-autofill,
#authModal .form-control:-webkit-autofill:hover,
#authModal .form-control:-webkit-autofill:focus,
#authModal .form-control:-webkit-autofill:active {
    -webkit-box-shadow: 0 0 0 1000px #ffffff inset !important;
    box-shadow: 0 0 0 1000px #ffffff inset !important;
    -webkit-text-fill-color: #12213a !important;
    background-clip: content-box !important;
    transition: background-color 5000s ease-in-out 0s;
}

#authModal input:-webkit-autofill {
    background-color: #fff !important;
    background-image: none !important;
    color: #12213a !important;
}

#authModal .btn-primary {
    background: #1f6feb;
    border: none;
    border-radius: 0.5rem;
    padding: 0.7rem;
    font-weight: 600;
}

#authModal .btn-primary:hover {
    background: #175bc4;
}

#authModal .btn-success {
    background: #f0b429;
    border: none;
    border-radius: 0.5rem;
    padding: 0.7rem;
    font-weight: 600;
    color: #12213a;
}

#authModal .btn-success:hover {
    background: #d99b00;
    color: #12213a;
}

#authModal .btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

#authModal .alert {
    border-radius: 0.5rem;
    padding: 0.7rem 1rem;
    margin-bottom: 1rem;
}

#registerEmailStatus.success { color: #1a9c5c; }
#registerEmailStatus.error { color: #e0245e; }
#registerEmailStatus.info { color: #1f6feb; }

/* Dark mode support */
body.dark-mode #authModal .modal-content {
    background: var(--dm-bg-secondary, #161b22);
}

body.dark-mode #authModal .auth-tabs .nav-link {
    color: var(--dm-text-secondary, #8b949e);
}

body.dark-mode #authModal .auth-tabs .nav-link:hover {
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode #authModal .auth-tabs .nav-link.active {
    color: var(--dm-text-primary, #f0f6fc);
    border-bottom-color: #f0b429;
}

body.dark-mode #authModal .form-control,
body.dark-mode #authModal .form-select {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode #authModal .form-control:focus,
body.dark-mode #authModal .form-select:focus {
    border-color: #f0b429;
    box-shadow: 0 0 0 0.2rem rgba(240, 180, 41, 0.15);
}

body.dark-mode #authModal .form-control::placeholder {
    color: var(--dm-text-secondary, #8b949e);
}

body.dark-mode #authModal .auth-tabs {
    border-bottom-color: var(--dm-border, #30363d);
}

body.dark-mode #authModal .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}
</style>

<script>
// ============ AUTH SYSTEM WITH TABS ============
(function() {
    'use strict';

    // DOM Elements
    const authModal = document.getElementById('authModal');

    // Login elements
    const loginEmail = document.getElementById('loginEmailInput');
    const loginPassword = document.getElementById('loginPasswordInput');
    const loginBtn = document.getElementById('loginSubmitBtn');
    const loginAlert = document.getElementById('loginAlert');

    // Register elements
    const regFullname = document.getElementById('registerFullname');
    const regEmail = document.getElementById('registerEmail');
    const regRole = document.getElementById('registerRole'); // NEW
    const regMobile = document.getElementById('registerMobile');
    const regPassword = document.getElementById('registerPassword');
    const regOtp = document.getElementById('registerOtp');
    const regSubmitBtn = document.getElementById('registerSubmitBtn');
    const regAlert = document.getElementById('registerAlert');
    const regEmailStatus = document.getElementById('registerEmailStatus');
    const otpSection = document.getElementById('otpSection');
    const otpActionInfo = document.getElementById('otpActionInfo'); // NEW
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    const otpTimer = document.getElementById('otpTimer');

    // Tab switches
    const switchToRegister = document.getElementById('switchToRegisterTab');
    const switchToLogin = document.getElementById('switchToLoginTab');
    const loginTab = document.getElementById('login-tab');
    const registerTab = document.getElementById('register-tab');

    // State
    let currentEmail = '';
    let otpTimerInterval = null;
    let isRegisterFlow = false;

    // NEW: smarter API base URL resolution (probes + caches, from step-based version)
    let resolvedApiBase = null;
    async function getApiBaseUrl() {
        if (resolvedApiBase) return resolvedApiBase;
        const candidates = ['/api/auth.php', '/front/api/auth.php'];
        for (let i = 0; i < candidates.length; i++) {
            try {
                const probe = await fetch(candidates[i] + '?action=check-email', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: 'probe@example.com' })
                });
                const text = await probe.text();
                if (text.trim().charAt(0) === '{') {
                    const parsed = JSON.parse(text);
                    if (parsed && parsed.success === true) {
                        resolvedApiBase = candidates[i];
                        return resolvedApiBase;
                    }
                }
            } catch (e) { /* try next candidate */ }
        }
        // Fallback to path-based guess if probing fails
        const currentPath = window.location.pathname;
        if (currentPath.includes('/front/')) {
            resolvedApiBase = '/front/api/auth.php';
        } else if (currentPath === '/' || currentPath === '/index.php') {
            resolvedApiBase = '/front/api/auth.php';
        } else {
            resolvedApiBase = 'api/auth.php';
        }
        return resolvedApiBase;
    }

    // API call wrapper
    async function apiCall(action, data) {
        const baseUrl = await getApiBaseUrl();
        const url = baseUrl + '?action=' + action;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }

            const text = await response.text();

            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('Server returned invalid response.');
            }
        } catch (error) {
            throw error;
        }
    }

    // Show alert in specific panel
    function showAlert(panel, type, message) {
        const alertEl = panel === 'login' ? loginAlert : regAlert;
        alertEl.className = 'alert alert-' + type;
        alertEl.textContent = message;
        alertEl.style.display = 'block';

        clearTimeout(window.alertTimeout);
        window.alertTimeout = setTimeout(() => {
            alertEl.style.display = 'none';
        }, 8000);
    }

    // Switch tabs
    function switchTab(tabId) {
        const tab = document.querySelector('#authTabs [data-bs-target="' + tabId + '"]');
        if (tab) {
            const tabInstance = new bootstrap.Tab(tab);
            tabInstance.show();
        }
        // Clear alerts when switching tabs
        loginAlert.style.display = 'none';
        regAlert.style.display = 'none';
    }

    // Switch to register tab
    switchToRegister.addEventListener('click', function(e) {
        e.preventDefault();
        switchTab('#register-panel');
        // Pre-fill email if available
        if (loginEmail.value.trim()) {
            regEmail.value = loginEmail.value.trim();
            checkEmailExists(regEmail.value.trim());
        }
    });

    // Switch to login tab
    switchToLogin.addEventListener('click', function(e) {
        e.preventDefault();
        switchTab('#login-panel');
        // Pre-fill email if available
        if (regEmail.value.trim()) {
            loginEmail.value = regEmail.value.trim();
        }
    });

    // Check email existence on register
    let emailCheckTimeout = null;
    regEmail.addEventListener('input', function() {
        const email = this.value.trim();

        if (emailCheckTimeout) {
            clearTimeout(emailCheckTimeout);
        }

        if (!email || !email.includes('@')) {
            regEmailStatus.style.display = 'none';
            return;
        }

        emailCheckTimeout = setTimeout(function() {
            checkEmailExists(email);
        }, 500);
    });

    async function checkEmailExists(email) {
        try {
            const data = await apiCall('check-email', { email: email });
            if (!data || !data.success) return;

            // NEW: richer auto-switch behavior (from step-based version)
            if (data.exists && data.verified) {
                regEmailStatus.className = 'small mt-1 warning';
                regEmailStatus.textContent = '⚠️ This email is already registered. Please login.';
                regEmailStatus.style.display = 'block';
                regEmailStatus.style.color = '#f0b429';

                // Show suggestion to switch to login
                document.querySelector('#register-panel .text-center .small').innerHTML =
                    'Already have an account? <a href="#" id="switchToLoginTab2" class="text-decoration-none small fw-bold">Login here</a>';
                document.getElementById('switchToLoginTab2')?.addEventListener('click', function(e) {
                    e.preventDefault();
                    loginEmail.value = email;
                    switchTab('#login-panel');
                });
            } else if (data.exists && !data.verified) {
                regEmailStatus.className = 'small mt-1 info';
                regEmailStatus.textContent = '⚠️ Email not verified yet. Please complete registration below.';
                regEmailStatus.style.display = 'block';
                regEmailStatus.style.color = '#1f6feb';
            } else {
                regEmailStatus.className = 'small mt-1 success';
                regEmailStatus.textContent = '✓ Email available';
                regEmailStatus.style.display = 'block';
                regEmailStatus.style.color = '#1a9c5c';
            }
        } catch (error) {
            // Silent fail
        }
    }

    // ===== LOGIN =====
    loginBtn.addEventListener('click', async function() {
        const email = loginEmail.value.trim();
        const password = loginPassword.value.trim();

        if (!email || !email.includes('@')) {
            showAlert('login', 'warning', 'Please enter a valid email address');
            return;
        }

        if (!password || password.length < 6) {
            showAlert('login', 'warning', 'Please enter your password (min 6 chars)');
            return;
        }

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Logging in...';

        try {
            const data = await apiCall('login', {
                email: email,
                password: password
            });

            if (data.success) {
                showAlert('login', 'success', 'Welcome back, ' + data.user.fullname + '! 👋');
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(authModal);
                    if (modal) modal.hide();
                    location.reload();
                }, 1500);
            } else {
                if (data.code === 'NOT_VERIFIED') {
                    showAlert('login', 'warning', 'Please verify your email first. Switch to Register tab.');
                    switchTab('#register-panel');
                    regEmail.value = email;
                } else if (data.code === 'PASSWORD_MISSING') {
                    showAlert('login', 'warning', 'Account setup incomplete. Please register again.');
                    switchTab('#register-panel');
                    regEmail.value = email;
                } else {
                    showAlert('login', 'danger', data.message || 'Login failed');
                }
            }
        } catch (error) {
            showAlert('login', 'danger', 'Error: ' + error.message);
        } finally {
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-box-arrow-in-right me-1"></i> Login';
        }
    });

    // ===== REGISTER =====
    regSubmitBtn.addEventListener('click', async function() {
        const fullname = regFullname.value.trim();
        const email = regEmail.value.trim();
        const role = regRole ? regRole.value : 'buyer'; // NEW
        const mobile = regMobile.value.trim();
        const password = regPassword.value.trim();
        const otp = regOtp.value.trim();

        if (!isRegisterFlow) {
            // Step 1: Send OTP
            if (!fullname) {
                showAlert('register', 'warning', 'Please enter your full name');
                return;
            }
            if (!email || !email.includes('@')) {
                showAlert('register', 'warning', 'Please enter a valid email address');
                return;
            }
            if (!mobile) {
                showAlert('register', 'warning', 'Please enter your mobile number');
                return;
            }
            if (!password || password.length < 6) {
                showAlert('register', 'warning', 'Password must be at least 6 characters');
                return;
            }

            currentEmail = email;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending OTP...';

            try {
                const data = await apiCall('send-otp', {
                    email: email,
                    is_register: true,
                    fullname: fullname,
                    mobile: mobile,
                    password: password,
                    account_type: role // NEW: send role/account_type
                });

                if (data.success) {
                    isRegisterFlow = true;
                    otpSection.style.display = 'block';
                    regSubmitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Verify OTP & Register';
                    showAlert('register', 'success', data.message);
                    // NEW: show dev OTP if the backend returns it (no email on local server)
                    if (otpActionInfo) {
                        otpActionInfo.textContent = data.otp
                            ? ('Dev OTP: ' + data.otp + ' (no email on local server - copy it above)')
                            : '';
                    }
                    startOtpTimer();
                } else {
                    if (data.code === 'ALREADY_VERIFIED') {
                        showAlert('register', 'info', 'Email already verified. Please login.');
                        loginEmail.value = email;
                        switchTab('#login-panel');
                    } else {
                        showAlert('register', 'danger', data.message || 'Failed to send OTP');
                    }
                }
            } catch (error) {
                showAlert('register', 'danger', 'Error: ' + error.message);
            } finally {
                this.disabled = false;
                if (!isRegisterFlow) {
                    this.innerHTML = '<i class="bi bi-person-plus me-1"></i> Register';
                }
            }
        } else {
            // Step 2: Verify OTP and complete registration
            if (!otp || otp.length !== 6) {
                showAlert('register', 'warning', 'Please enter the 6-digit OTP');
                return;
            }

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...';

            try {
                const data = await apiCall('verify-otp', {
                    email: currentEmail,
                    otp: otp
                });

                if (data.success) {
                    // Complete registration
                    const regData = await apiCall('register', {
                        fullname: regFullname.value.trim(),
                        email: currentEmail,
                        mobile: regMobile.value.trim(),
                        password: regPassword.value.trim(),
                        account_type: role // NEW: send role/account_type
                    });

                    if (regData.success) {
                        showAlert('register', 'success', 'Registration complete! Welcome to Tatito Developers! 🎉');
                        setTimeout(() => {
                            const modal = bootstrap.Modal.getInstance(authModal);
                            if (modal) modal.hide();
                            location.reload();
                        }, 2000);
                    } else {
                        showAlert('register', 'danger', regData.message || 'Registration failed');
                        isRegisterFlow = false;
                        otpSection.style.display = 'none';
                        this.innerHTML = '<i class="bi bi-person-plus me-1"></i> Register';
                    }
                } else {
                    showAlert('register', 'danger', data.message || 'Invalid OTP');
                }
            } catch (error) {
                showAlert('register', 'danger', 'Error: ' + error.message);
            } finally {
                this.disabled = false;
            }
        }
    });

    // Resend OTP
    resendOtpBtn.addEventListener('click', async function() {
        if (!currentEmail) return;

        this.disabled = true;
        this.innerHTML = 'Sending...';

        try {
            const data = await apiCall('send-otp', {
                email: currentEmail,
                is_register: true
            });

            if (data.success) {
                showAlert('register', 'success', 'New OTP sent to your email');
                // NEW: refresh dev OTP hint on resend
                if (otpActionInfo) {
                    otpActionInfo.textContent = data.otp
                        ? ('Dev OTP: ' + data.otp + ' (no email on local server - copy it above)')
                        : '';
                }
                startOtpTimer();
            } else {
                showAlert('register', 'danger', data.message || 'Failed to resend OTP');
            }
        } catch (error) {
            showAlert('register', 'danger', 'Error: ' + error.message);
        } finally {
            this.disabled = false;
            this.innerHTML = 'Resend';
        }
    });

    // OTP Timer
    function startOtpTimer() {
        if (otpTimerInterval) clearInterval(otpTimerInterval);
        let seconds = 60;
        otpTimer.textContent = 'OTP expires in ' + seconds + 's';
        resendOtpBtn.disabled = true;

        otpTimerInterval = setInterval(() => {
            seconds--;
            if (seconds <= 0) {
                clearInterval(otpTimerInterval);
                otpTimer.textContent = 'OTP expired. Request a new one.';
                resendOtpBtn.disabled = false;
            } else {
                otpTimer.textContent = 'OTP expires in ' + seconds + 's';
            }
        }, 1000);
    }

    // Enter key support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const modal = document.getElementById('authModal');
            if (!modal || !modal.classList.contains('show')) return;

            const activeTab = document.querySelector('#authTabs .nav-link.active');
            if (activeTab && activeTab.id === 'login-tab') {
                e.preventDefault();
                loginBtn.click();
            } else if (activeTab && activeTab.id === 'register-tab') {
                e.preventDefault();
                regSubmitBtn.click();
            }
        }
    });

    // NEW: force remove autofill on input focus (from step-based version)
    document.querySelectorAll('#authModal .form-control').forEach(function(input) {
        input.addEventListener('focus', function() {
            if (this.type === 'password' || this.type === 'email') {
                this.setAttribute('autocomplete', 'off');
                this.setAttribute('aria-autocomplete', 'none');
            }
        });
    });

    // Reset form when modal is closed
    authModal.addEventListener('hidden.bs.modal', function() {
        loginEmail.value = '';
        loginPassword.value = '';
        regFullname.value = '';
        regEmail.value = '';
        if (regRole) regRole.value = 'buyer'; // NEW
        regMobile.value = '';
        regPassword.value = '';
        regOtp.value = '';
        loginAlert.style.display = 'none';
        regAlert.style.display = 'none';
        regEmailStatus.style.display = 'none';
        otpSection.style.display = 'none';
        if (otpActionInfo) otpActionInfo.textContent = ''; // NEW
        isRegisterFlow = false;
        currentEmail = '';

        if (otpTimerInterval) {
            clearInterval(otpTimerInterval);
            otpTimerInterval = null;
        }
        otpTimer.textContent = '';
        resendOtpBtn.disabled = false;
        resendOtpBtn.innerHTML = 'Resend';

        // Reset register button text
        regSubmitBtn.innerHTML = '<i class="bi bi-person-plus me-1"></i> Register';
        regSubmitBtn.disabled = false;
    });

    // Handle modal show event - set default tab
    authModal.addEventListener('show.bs.modal', function(event) {
        loginAlert.style.display = 'none';
        regAlert.style.display = 'none';
        regEmailStatus.style.display = 'none';
        otpSection.style.display = 'none';
        if (otpActionInfo) otpActionInfo.textContent = ''; // NEW
        isRegisterFlow = false;
        regSubmitBtn.innerHTML = '<i class="bi bi-person-plus me-1"></i> Register';
        regSubmitBtn.disabled = false;

        const triggerButton = event.relatedTarget;
        if (triggerButton) {
            const mode = triggerButton.getAttribute('data-mode');
            if (mode === 'register') {
                switchTab('#register-panel');
            } else {
                switchTab('#login-panel');
            }
        }
    });

    // Check login status on page load
    async function checkAuthStatus() {
        try {
            const baseUrl = await getApiBaseUrl(); // NEW: now async
            const response = await fetch(baseUrl + '?action=get-user');

            if (!response.ok) {
                throw new Error('HTTP error ' + response.status);
            }

            const data = await response.json();

            if (data.success) {
                const loginBtn = document.querySelector('.btn-login');
                if (loginBtn) {
                    loginBtn.innerHTML = '<i class="bi bi-person-fill"></i>';
                    loginBtn.title = 'Logged in as ' + data.user.fullname;
                    loginBtn.href = '/front/profile.php';
                    loginBtn.setAttribute('data-bs-toggle', '');
                    loginBtn.removeAttribute('data-bs-target');
                }
            }
        } catch (error) {
            // Silent fail - user is not logged in
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkAuthStatus);
    } else {
        checkAuthStatus();
    }
})();
</script>