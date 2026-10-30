<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\LoginActivity;
use Illuminate\Support\Facades\DB;
use App\Models\SavedAccount;
use Illuminate\Support\Facades\Cookie;

class UserControllers extends Controller
{
    private $brevoApiKey = "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";
    private $senderEmail = "tolbazar67@gmail.com";
    private $senderName = "TolBazar";

    private function redirectIfAuthenticated()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return null;
    }

    private function redirectIfNotAuthenticated()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors(['email' => 'Please login to continue.']);
        }
        return null;
    }

    public function showRegisterForm()
    {
        if ($redirect = $this->redirectIfAuthenticated()) {
            return $redirect;
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        if ($redirect = $this->redirectIfAuthenticated()) {
            return $redirect;
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $otp = rand(100000, 999999);
            $username = $this->generateUsername($request->name, $request->email);

            $user = User::create([
                'name' => $request->name,
                'username' => $username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(10),
                'active_mode' => 'customer',
            ]);

            // Public registrations are customers by default
            $user->assignRole('Customer');

            session(['otp_email' => $user->email]);
            $emailSent = $this->sendOtpEmail($user, $otp);

            if (!$emailSent['success']) {
                Log::error('Registration OTP Email Failed', ['email' => $user->email, 'error' => $emailSent['message']]);
                return redirect()->route('verify.otp')->withErrors(['email' => 'Failed to send OTP. Please click Resend OTP.']);
            }

            return redirect()->route('verify.otp')->with('success', 'Registration successful. OTP has been sent to your email.');
        } catch (\Throwable $e) {
            Log::error('Registration Error', ['message' => $e->getMessage(), 'email' => $request->email]);
            return back()->withInput()->withErrors(['email' => 'Registration failed. Please try again.']);
        }
    }

    private function sendOtpEmail(User $user, $otp)
    {
        try {
            $response = Http::timeout(30)->withHeaders([
                'Accept' => 'application/json',
                'api-key' => $this->brevoApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => ['name' => $this->senderName, 'email' => $this->senderEmail],
                'to' => [['name' => $user->name, 'email' => $user->email]],
                'subject' => 'Your TolBazar OTP Verification Code',
                'htmlContent' => "<div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;padding:30px;background:#ffffff;border:1px solid #eeeeee;border-radius:10px;'><h2>TolBazar Email Verification</h2><p>Hello {$user->name},</p><p>Thank you for registering with TolBazar. Please use the OTP below to verify your email.</p><div style='text-align:center;margin:25px 0;'><div style='font-size:32px;font-weight:bold;letter-spacing:8px;padding:15px;background:#f5f5f5;border-radius:8px;display:inline-block;'>{$otp}</div></div><p>This OTP will expire in <strong>10 minutes</strong>.</p><p>If you did not create a TolBazar account, you can safely ignore this email.</p><br><p>Best Regards,<br><strong>TolBazar Team</strong></p></div>",
            ]);

            Log::info('Brevo OTP API Response', ['status' => $response->status(), 'body' => $response->body()]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'OTP email sent successfully.'];
            }
            return ['success' => false, 'message' => $response->body()];
        } catch (\Throwable $e) {
            Log::error('Brevo OTP Exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function sendRegistrationSuccessEmail(User $user)
    {
        try {
            $response = Http::timeout(30)->withHeaders([
                'Accept' => 'application/json',
                'api-key' => $this->brevoApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => ['name' => $this->senderName, 'email' => $this->senderEmail],
                'to' => [['name' => $user->name, 'email' => $user->email]],
                'subject' => 'Welcome to TolBazar - Registration Successful 🎉',
                'htmlContent' => "<div style='font-family:Arial,sans-serif;max-width:650px;margin:auto;padding:30px;background:#ffffff;border:1px solid #eeeeee;border-radius:12px;'><h1 style='margin-bottom:20px;'>Welcome to TolBazar! 🎉</h1><p>Hello <strong>{$user->name}</strong>,</p><p>Congratulations! Your TolBazar account has been successfully created and verified.</p><p>Your registration is now complete and you can start exploring TolBazar.</p><div style='margin:25px 0;padding:20px;background:#f8f9fa;border-radius:10px;border:1px solid #eeeeee;'><h3 style='margin-top:0;'>Your Account Information</h3><p><strong>Name:</strong> {$user->name}</p><p><strong>Username:</strong> @{$user->username}</p><p><strong>Email:</strong> {$user->email}</p></div><p>You can now buy, sell and explore products on TolBazar.</p><p>Thank you for joining us!</p><br><p>Best Regards,<br><strong>TolBazar Team</strong></p></div>",
            ]);

            Log::info('Brevo Registration Success Email Response', ['email' => $user->email, 'status' => $response->status(), 'body' => $response->body()]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Registration success email sent successfully.'];
            }
            return ['success' => false, 'message' => $response->body()];
        } catch (\Throwable $e) {
            Log::error('Registration Success Email Exception', ['email' => $user->email, 'message' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

// =========================================================================
// CUSTOMER & DELIVERY PORTAL
// =========================================================================
public function showLoginForm()
{
    if (Auth::check()) {
        return $this->redirectAfterLogin(Auth::user(), request());
    }
    return view('auth.login');
}

public function login(Request $request)
{
    return $this->attemptLogin($request, 'customer', 'login');
}

// =========================================================================
// SELLER PORTAL
// =========================================================================
public function showSellerLoginForm()
{
    if (Auth::check()) {
        return $this->redirectAfterLogin(Auth::user(), request());
    }
    return view('auth.seller-login');
}

public function sellerLogin(Request $request)
{
    return $this->attemptLogin($request, 'seller', 'seller.login');
}

// =========================================================================
// ADMIN PORTAL (obscured)
// =========================================================================
public function showAdminLoginForm()
{
    if (Auth::check() && $this->canAccessPortal(Auth::user(), 'admin')) {
        return $this->redirectAfterLogin(Auth::user(), request());
    }
    return view('auth.admin-login');
}

public function adminLogin(Request $request)
{
    return $this->attemptLogin($request, 'admin', 'admin.auth.login');
}

// =========================================================================
// SHARED LOGIN ENGINE (role-aware, portal-restricted)
// =========================================================================
private function attemptLogin(Request $request, string $portal, string $loginRouteName)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::withTrashed()->where('email', $request->email)->first();

    // ---------- Failed authentication ----------
    if (!$user || !Hash::check($request->password, $user->password)) {
        if ($user) {
            $this->logActivity($user, 'failed', null, 'Invalid password');
        }
        return back()->withInput($request->only('email'))
            ->withErrors(['email' => 'Invalid email or password.']);
    }

    // ---------- PORTAL ACCESS GUARD (critical!) ----------
    // Wrong-portal attempt → same generic error (don't reveal role mismatch)
    if (!$this->canAccessPortal($user, $portal)) {
        $this->logActivity($user, 'failed', null, "Unauthorized portal access: {$portal}");
        return back()->withInput($request->only('email'))
            ->withErrors(['email' => 'Invalid email or password.']);
    }

    // ---------- Deactivated account ----------
    if ($user->isDeactivated()) {
        session(['reactivation_email' => $user->email]);
        return redirect()->route('account.reactivate.form')
            ->with('info', 'Your account is deactivated. Confirm below to reactivate and log in.');
    }

    // ---------- Pending deletion ----------
    if ($user->isPendingDeletion() || $user->trashed()) {
        session(['reactivation_email' => $user->email]);
        return redirect()->route('account.cancel-deletion.form')
            ->with('info', 'Your account is scheduled for deletion. Cancel it to log in.');
    }

    // ---------- Email not verified ----------
    if (!$user->email_verified_at) {
        session(['otp_email' => $user->email]);
        return redirect()->route('verify.otp')
            ->withErrors(['otp' => 'Your email is not verified. Please verify your OTP first.']);
    }

    // ---------- Success ----------
    Auth::login($user, $request->boolean('remember'));
    $request->session()->regenerate();

    $this->logActivity($user, 'success');
    $this->saveAccountToDevice($user);

    return $this->redirectAfterLogin($user, $request);
}

/**
 * Check whether a user is allowed into a given portal.
 * Centralises role/branch logic so all 3 login pages enforce the same rules.
 */
private function canAccessPortal(User $user, string $portal): bool
{
    return match ($portal) {
        'admin'    => $user->hasAnyRole(['Super Admin', 'Admin']),
        'seller'   => $user->isBranchStaff()
                        || $user->isSellerOwner()
                        || $user->hasRole('Seller'),
        'customer' => $user->hasAnyRole(['Customer', 'Delivery Boy']),
        default    => false,
    };
}

    /**
     * Redirect user to their role-appropriate dashboard after login.
     *
     * Priority order:
     *   1. Branch Manager / Branch Staff → seller mode (own branch)
     *   2. Seller owner                  → seller mode (default branch)
     *   3. Super Admin / Admin           → admin mode
     *   4. Delivery Boy (only)           → delivery mode
     *   5. Seller (no profile)           → seller.apply
     *   6. Default                       → customer mode
     */
    private function redirectAfterLogin(User $user, Request $request)
    {
        // ============================================================
        // 1. BRANCH MANAGER / BRANCH STAFF → seller mode, own branch
        // ============================================================
        if ($user->isBranchStaff()) {
            $branchStaff = $user->branchStaff;

            if (!$branchStaff || !$branchStaff->status) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => 'Your staff account is inactive. Please contact the store owner.']);
            }

            $user->update(['active_mode' => 'seller']);

            session([
                'active_branch_id'   => $branchStaff->branch_id,
                'active_branch_role' => $branchStaff->role,
            ]);

            $roleLabel = ucfirst($branchStaff->role);
            return redirect()->route('seller.dashboard')
                ->with('success', "Welcome back, {$roleLabel}!");
        }

        if ($user->isSellerOwner()) {
            $sellerProfile = $user->sellerProfile;

            if (!$sellerProfile) {
                return redirect()->route('seller.apply')
                    ->with('info', 'Please complete your store registration.');
            }

            $defaultBranch = $sellerProfile->branches()
                ->where('is_default', 1)
                ->where('status', 1)
                ->first()
                ?? $sellerProfile->branches()->where('status', 1)->first();

            if ($defaultBranch) {
                $user->update(['active_mode' => 'seller']);
                session([
                    'active_branch_id'   => $defaultBranch->id,
                    'active_branch_role' => 'owner',
                ]);

                return redirect()->route('seller.dashboard')
                    ->with('success', 'Welcome back to your seller account.');
            }

            return redirect()->route('seller.apply')
                ->with('error', 'Please complete your store setup.');
        }

        // ============================================================
        // 3. SUPER ADMIN / ADMIN → admin mode
        //    (BEFORE seller-apply fallback so admins never hit it)
        // ============================================================
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            $user->update(['active_mode' => 'admin']);
            session()->forget(['active_branch_id', 'active_branch_role']);

            $label = $user->hasRole('Super Admin') ? 'Super Admin' : 'Admin';
            return redirect()->route('admin.dashboard')
                ->with('success', "Welcome back, {$label}!");
        }

        // ============================================================
        // 4. DELIVERY BOY (only, not also a customer) → delivery mode
        // ============================================================
        if ($user->hasRole('Delivery Boy') && !$user->hasRole('Customer')) {
            $user->update(['active_mode' => 'delivery']);
            session()->forget(['active_branch_id', 'active_branch_role']);

            return redirect()->route('delivery.dashboard')
                ->with('success', 'Welcome back to the delivery panel.');
        }

        // ============================================================
        // 5. SELLER role without profile → apply form
        // ============================================================
        if ($user->hasRole('Seller') && !$user->sellerProfile) {
            $user->update(['active_mode' => 'seller']);
            return redirect()->route('seller.apply')
                ->with('info', 'Please complete your store registration to start selling.');
        }

        $user->update(['active_mode' => 'customer']);
        session()->forget(['active_branch_id', 'active_branch_role']);

        return redirect()->intended(route('home'))
            ->with('success', 'Logged in successfully.');
    }

    public function home(Request $request)
    {
        return view('auth.home');
    }

    public function logout(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->logActivity(Auth::user(), 'logout');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session()->forget(['active_branch_id', 'active_branch_role', 'reactivation_email']);

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    public function showVerifyForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $email = session('otp_email');

        if (!$email) {
            return redirect()->route('login')->withErrors(['email' => 'Please login or register first.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            session()->forget('otp_email');
            return redirect()->route('register')->withErrors(['email' => 'User not found. Please register again.']);
        }

        if ($user->email_verified_at) {
            return redirect()->route('login')->with('success', 'Your email is already verified. Please login.');
        }

        return view('auth.verify-otp', compact('email'));
    }

    public function verifyOtp(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $sessionEmail = session('otp_email');

        if (!$sessionEmail || $sessionEmail !== $request->email) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid verification session. Please login again.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            session()->forget('otp_email');
            return redirect()->route('register')->withErrors(['email' => 'User not found.']);
        }

        if ($user->email_verified_at) {
            session()->forget('otp_email');
            return redirect()->route('login')->with('success', 'Your email is already verified. Please login.');
        }

        if ((string) $user->otp !== (string) $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        if (!$user->otp_expires_at || Carbon::now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'OTP has expired. Please resend a new OTP.']);
        }

        $user->email_verified_at = Carbon::now();
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        $registrationEmail = $this->sendRegistrationSuccessEmail($user);

        if (!$registrationEmail['success']) {
            Log::warning('Registration Success Email Failed', ['email' => $user->email, 'error' => $registrationEmail['message']]);
        }

        Auth::login($user);
        $request->session()->regenerate();
        session()->forget('otp_email');

        $this->logActivity($user, 'success');
        $this->saveAccountToDevice($user);   // ✅ थप्नुस

        // Route through role-aware redirect (same as login)
        return $this->redirectAfterLogin($user, $request);
    }

    public function resendOtp(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $request->validate(['email' => 'required|email|exists:users,email']);

        $sessionEmail = session('otp_email');

        if (!$sessionEmail || $sessionEmail !== $request->email) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid OTP session. Please login again.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->route('register')->withErrors(['email' => 'User not found.']);
        }

        if ($user->email_verified_at) {
            return redirect()->route('login')->with('success', 'Your email is already verified. Please login.');
        }

        $otp = rand(100000, 999999);
        $emailSent = $this->sendOtpEmail($user, $otp);

        if (!$emailSent['success']) {
            Log::error('Resend OTP Email Failed', ['email' => $user->email, 'error' => $emailSent['message']]);
            return back()->withErrors(['email' => 'Failed to resend OTP. Please try again.']);
        }

        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        return back()->with('success', 'A new OTP has been sent to your email.');
    }

    public function showForgotPasswordForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.forgot-password');
    }

    public function sendPasswordResetOtp(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email address.']);
        }

        $otp = rand(100000, 999999);
        $emailSent = $this->sendPasswordResetOtpEmail($user, $otp);

        if (!$emailSent['success']) {
            Log::error('Password Reset OTP Email Failed', ['email' => $user->email, 'error' => $emailSent['message']]);
            return back()->withInput()->withErrors(['email' => 'Failed to send reset OTP. Please try again.']);
        }

        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        session(['password_reset_email' => $user->email]);

        return redirect()->route('password.reset.form')->with('success', 'A password reset OTP has been sent to your email.');
    }

    private function sendPasswordResetOtpEmail(User $user, $otp)
    {
        try {
            $response = Http::timeout(30)->withHeaders([
                'Accept' => 'application/json',
                'api-key' => $this->brevoApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => ['name' => $this->senderName, 'email' => $this->senderEmail],
                'to' => [['name' => $user->name, 'email' => $user->email]],
                'subject' => 'Reset Your TolBazar Password',
                'htmlContent' => "<div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;padding:30px;'><h2>Reset Your Password</h2><p>Hello {$user->name},</p><p>We received a request to reset your TolBazar account password.</p><p>Your password reset OTP is:</p><h1 style='font-size:32px;letter-spacing:8px;'>{$otp}</h1><p>This OTP will expire in 10 minutes.</p><p>If you did not request a password reset, you can safely ignore this email.</p></div>",
            ]);

            Log::info('Brevo Password Reset API Response', ['status' => $response->status(), 'body' => $response->body()]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Password reset OTP sent successfully.'];
            }
            return ['success' => false, 'message' => $response->body()];
        } catch (\Throwable $e) {
            Log::error('Password Reset OTP Exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function showResetPasswordForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $email = session('password_reset_email');

        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Please enter your email first.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            session()->forget('password_reset_email');
            return redirect()->route('password.request')->withErrors(['email' => 'User not found.']);
        }

        return view('auth.reset-password', compact('email'));
    }

    public function resetPassword(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $sessionEmail = session('password_reset_email');

        if (!$sessionEmail || $sessionEmail !== $request->email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Invalid password reset session.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            session()->forget('password_reset_email');
            return redirect()->route('password.request')->withErrors(['email' => 'User not found.']);
        }

        if ((string) $user->otp !== (string) $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        if (!$user->otp_expires_at || Carbon::now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'OTP has expired. Please request a new password reset OTP.']);
        }

        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        session()->forget('password_reset_email');
        Auth::logoutOtherDevices($request->password);

        return redirect()->route('login')->with('success', 'Your password has been reset successfully. Please login with your new password.');
    }

    public function googleLogin()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function googleAuthentication(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $googleId = $googleUser->getId();
            $email = $googleUser->getEmail();
            $name = $googleUser->getName() ?? 'TolBazar User';
            $avatarUrl = $googleUser->getAvatar();

            if (!$googleId || !$email) {
                return redirect()->route('login')->withErrors(['email' => 'Unable to retrieve Google account information.']);
            }

            $user = User::withTrashed()->where('google_id', $googleId)->first();

            if (!$user) {
                $user = User::withTrashed()->where('email', $email)->first();
            }

            // Block deactivated / pending-deletion Google users
            if ($user && ($user->isDeactivated() || $user->isPendingDeletion() || $user->trashed())) {
                session(['reactivation_email' => $user->email]);

                if ($user->isDeactivated()) {
                    return redirect()->route('account.reactivate.form')
                        ->with('info', 'Your account is deactivated. Please reactivate to continue.');
                }

                return redirect()->route('account.cancel-deletion.form')
                    ->with('info', 'Your account is scheduled for deletion. Cancel it to log in.');
            }

            $isNewUser = false;
            $avatarPath = null;

            if ($avatarUrl) {
                try {
                    $avatarResponse = Http::timeout(20)->get($avatarUrl);
                    if ($avatarResponse->successful()) {
                        $extension = 'jpg';
                        $contentType = $avatarResponse->header('Content-Type');
                        if ($contentType && str_contains($contentType, 'png')) {
                            $extension = 'png';
                        } elseif ($contentType && str_contains($contentType, 'webp')) {
                            $extension = 'webp';
                        }
                        $fileName = 'google_' . $googleId . '_' . time() . '.' . $extension;
                        $avatarPath = 'users/avatar/' . $fileName;
                        Storage::disk('public')->put($avatarPath, $avatarResponse->body());
                    }
                } catch (\Throwable $avatarException) {
                    Log::warning('Google Avatar Download Failed', ['email' => $email, 'error' => $avatarException->getMessage()]);
                }
            }

            if ($user) {
                if (!$user->google_id) {
                    $user->google_id = $googleId;
                }
                if (!$user->email_verified_at) {
                    $user->email_verified_at = Carbon::now();
                }
                if (!$user->name && $name) {
                    $user->name = $name;
                }
                if ($avatarPath) {
                    if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                        Storage::disk('public')->delete($user->avatar);
                    }
                    $user->avatar = $avatarPath;
                }
                if (!$user->username) {
                    $user->username = $this->generateUsername($name, $email);
                }
                $user->save();
            } else {
                $isNewUser = true;
                $user = User::create([
                    'name' => $name,
                    'username' => $this->generateUsername($name, $email),
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $avatarPath,
                    'email_verified_at' => Carbon::now(),
                    'password' => Hash::make(Str::random(40)),
                    'country' => null,
                    'mobile_number' => null,
                    'date_of_birth' => null,
                    'gender' => null,
                    'otp' => null,
                    'otp_expires_at' => null,
                    'active_mode' => 'customer',
                ]);

                $user->assignRole('Customer');
            }

            if ($isNewUser) {
                $registrationEmail = $this->sendRegistrationSuccessEmail($user);
                if (!$registrationEmail['success']) {
                    Log::warning('Google Registration Success Email Failed', ['email' => $user->email, 'error' => $registrationEmail['message']]);
                }
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            $this->logActivity($user, 'success');
            $this->saveAccountToDevice($user);   // ✅ थप्नुस

            return $this->redirectAfterLogin($user, $request);
        } catch (\Throwable $e) {
            Log::error('Google Authentication Failed', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->route('login')->withErrors(['email' => 'Google login failed. Please try again.']);
        }
    }

    private function generateUsername($name, $email)
    {
        $baseUsername = Str::slug($name, '');
        if (empty($baseUsername)) {
            $baseUsername = Str::before($email, '@');
            $baseUsername = Str::slug($baseUsername, '');
        }
        if (empty($baseUsername)) {
            $baseUsername = 'user';
        }

        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        return $username;
    }

    public function customerDashboard() {
        $user = User::where('email', Auth::user()->email)->first();
        if (!$user) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'User not found. Please login again.']);
        }
        return view('customer.dashboard');

    }

public function myProfile(Request $request)
{
    if ($redirect = $this->redirectIfNotAuthenticated()) {
        return $redirect;
    }

    $user = Auth::user()->load(['roles', 'sellerProfile', 'websites', 'works', 'educations']);
    $isOwnProfile = true;

    return view('profile.show', compact('user', 'isOwnProfile'));
}

public function showProfile(string $username)
{
    $user = User::with(['roles', 'sellerProfile', 'websites', 'works', 'educations'])
        ->where('username', $username)
        ->first();

    if (!$user) {
        abort(404, 'User not found.');
    }

    $isOwnProfile = Auth::check() && Auth::id() === $user->id;

    return view('profile.show', compact('user', 'isOwnProfile'));
}

    public function editProfile(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

public function updateProfile(Request $request)
{
    if ($redirect = $this->redirectIfNotAuthenticated()) {
        return $redirect;
    }

    $user = Auth::user();

    $validated = $request->validate([
        'bio'                 => 'nullable|string|max:2000',
        'location'            => 'nullable|string|max:150',
        'country'             => 'nullable|string|max:100',
        'mobile_number'       => 'nullable|string|max:30',
        'date_of_birth'       => 'nullable|date|before:today',
        'gender'              => 'nullable|in:male,female,other,prefer_not_to_say',
        'relationship_status' => 'nullable|in:single,married,divorced,widowed,separated',
        'categories'          => 'nullable|array|max:2',
        'categories.*'        => 'string|max:60',
        'languages'           => 'nullable|array|max:20',
        'languages.*'         => 'string|max:60',
        'hobbies'             => 'nullable',
        'address'             => 'nullable|string|max:500',
        'latitude'            => 'nullable|numeric|between:-90,90',
        'longitude'           => 'nullable|numeric|between:-180,180',

        // Dynamic collections
        'websites'                 => 'nullable|array|max:10',
        'websites.*.icon'          => 'nullable|string|max:50',
        'websites.*.url'           => 'nullable|url|max:500',

        'works'                    => 'nullable|array|max:15',
        'works.*.title'            => 'nullable|string|max:200',
        'works.*.company'          => 'nullable|string|max:200',
        'works.*.description'      => 'nullable|string|max:1000',
        'works.*.start_date'       => 'nullable|date',
        'works.*.end_date'         => 'nullable|date',
        'works.*.is_current'       => 'nullable',

        'educations'               => 'nullable|array|max:15',
        'educations.*.school'      => 'nullable|string|max:200',
        'educations.*.degree'      => 'nullable|string|max:200',
        'educations.*.field_of_study' => 'nullable|string|max:200',
        'educations.*.description' => 'nullable|string|max:1000',
        'educations.*.start_date'  => 'nullable|date',
        'educations.*.end_date'    => 'nullable|date',
        'educations.*.is_current'  => 'nullable',
    ]);

    // ---------- Bio word count (70 max) ----------
    if (!empty($validated['bio'])) {
        $wordCount = count(preg_split('/\s+/u', trim(strip_tags($validated['bio']))));
        if ($wordCount > 70) {
            return back()->withInput()
                ->withErrors(['bio' => "Bio cannot exceed 70 words. You wrote {$wordCount}."]);
        }
    }

    // ---------- Hobbies (Tagify may send JSON string) ----------
    if (!empty($validated['hobbies'])) {
        if (is_string($validated['hobbies'])) {
            $decoded = json_decode($validated['hobbies'], true);
            $validated['hobbies'] = is_array($decoded) ? array_values($decoded) : [];
        }
    } else {
        $validated['hobbies'] = [];
    }

    // ---------- Mobile — readonly if already set ----------
    if (!empty($user->mobile_number)) {
        unset($validated['mobile_number']);
    } elseif (array_key_exists('mobile_number', $validated)
        && $validated['mobile_number'] !== $user->mobile_number) {
        // If new mobile added, reset verification
        $validated['mobile_verified_at']    = null;
        $validated['mobile_otp']            = null;
        $validated['mobile_otp_expires_at'] = null;
    }

    // ---------- Latitude/longitude cast ----------
    foreach (['latitude', 'longitude'] as $coord) {
        if (isset($validated[$coord]) && $validated[$coord] === '') {
            $validated[$coord] = null;
        }
    }

    // ---------- Update user ----------
    $user->update($validated);

    // ---------- Sync collections ----------
    $this->syncUserWebsites($user, $request->input('websites', []));
    $this->syncUserWorks($user, $request->input('works', []));
    $this->syncUserEducations($user, $request->input('educations', []));

    // ---------- Sync legacy `website` column from first website URL ----------
    $firstWebsite = $user->websites()->first();
    $user->update(['website' => $firstWebsite?->url]);

    return redirect()->route('profile.me')
        ->with('success', 'Profile updated successfully.');
}

private function syncUserWebsites(User $user, array $websites): void
{
    $user->websites()->delete();

    $sort = 0;
    foreach ($websites as $w) {
        if (empty($w['url'])) continue;

        $user->websites()->create([
            'icon'       => $w['icon'] ?? 'globe',
            'url'        => $w['url'],
            'sort_order' => $sort++,
        ]);
    }
}

private function syncUserWorks(User $user, array $works): void
{
    $user->works()->delete();

    $sort = 0;
    foreach ($works as $w) {
        if (empty($w['title']) || empty($w['company'])) continue;

        $isCurrent = !empty($w['is_current']);

        $user->works()->create([
            'title'       => $w['title'],
            'company'     => $w['company'],
            'description' => $w['description'] ?? null,
            'start_date'  => $w['start_date'] ?? null,
            'end_date'    => $isCurrent ? null : ($w['end_date'] ?? null),
            'is_current'  => $isCurrent,
            'sort_order'  => $sort++,
        ]);
    }
}

private function syncUserEducations(User $user, array $educations): void
{
    $user->educations()->delete();

    $sort = 0;
    foreach ($educations as $e) {
        if (empty($e['school'])) continue;

        $isCurrent = !empty($e['is_current']);

        $user->educations()->create([
            'school'         => $e['school'],
            'degree'         => $e['degree'] ?? null,
            'field_of_study' => $e['field_of_study'] ?? null,
            'description'    => $e['description'] ?? null,
            'start_date'     => $e['start_date'] ?? null,
            'end_date'       => $isCurrent ? null : ($e['end_date'] ?? null),
            'is_current'     => $isCurrent,
            'sort_order'     => $sort++,
        ]);
    }
}

    public function updateProfilePicture(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // 2MB
        ]);

        $user = Auth::user();

        try {
            $file = $request->file('avatar');
            $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('users/avatar', $fileName, 'public');

            // delete old avatar
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->update(['avatar' => $path]);

            return back()->with('success', 'Profile picture updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Profile picture upload failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['avatar' => 'Failed to upload profile picture. Please try again.']);
        }
    }

    public function removeProfilePicture(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return back()->with('success', 'Profile picture removed.');
    }

    public function updateCoverPhoto(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $request->validate([
            'cover_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB
        ]);

        $user = Auth::user();

        try {
            $file = $request->file('cover_photo');
            $fileName = 'cover_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('users/cover', $fileName, 'public');

            if ($user->cover_photo && Storage::disk('public')->exists($user->cover_photo)) {
                Storage::disk('public')->delete($user->cover_photo);
            }

            $user->update(['cover_photo' => $path]);

            return back()->with('success', 'Cover photo updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Cover photo upload failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['cover_photo' => 'Failed to upload cover photo. Please try again.']);
        }
    }

    public function removeCoverPhoto(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();

        if ($user->cover_photo && Storage::disk('public')->exists($user->cover_photo)) {
            Storage::disk('public')->delete($user->cover_photo);
        }

        $user->update(['cover_photo' => null]);

        return back()->with('success', 'Cover photo removed.');
    }

    public function verificationCenter(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();
        return view('verification.index', compact('user'));
    }

    public function resendEmailVerification(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();

        if ($user->isEmailVerified()) {
            return back()->with('info', 'Your email is already verified.');
        }

        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        $sent = $this->sendOtpEmail($user, $otp);
        if (!$sent['success']) {
            Log::error('Resend verification email failed', ['user_id' => $user->id, 'error' => $sent['message']]);
            return back()->withErrors(['email' => 'Failed to send verification email. Please try again.']);
        }

        return back()->with('success', 'Verification OTP has been sent to your email.');
    }

    public function sendMobileOtp(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $request->validate([
            'mobile_number' => 'required|string|max:30',
        ]);

        $user = Auth::user();

        // Save/reset mobile number
        $user->mobile_number = $request->mobile_number;
        $otp = rand(100000, 999999);
        $user->mobile_otp = $otp;
        $user->mobile_otp_expires_at = Carbon::now()->addMinutes(10);
        $user->mobile_verified_at = null;
        $user->save();

        $sent = $this->sendMobileOtpSms($user, $otp);

        if (!$sent['success']) {
            Log::error('Mobile OTP send failed', ['user_id' => $user->id, 'error' => $sent['message'] ?? null]);
            return back()->withErrors(['mobile_number' => 'Failed to send OTP. Please try again.']);
        }

        return back()->with('success', 'OTP has been sent to your mobile number.');
    }

    public function verifyMobileOtp(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $request->validate([
            'mobile_otp' => 'required|digits:6',
        ]);

        $user = Auth::user();

        if ((string) $user->mobile_otp !== (string) $request->mobile_otp) {
            return back()->withErrors(['mobile_otp' => 'Invalid OTP.']);
        }

        if (!$user->mobile_otp_expires_at || Carbon::now()->isAfter($user->mobile_otp_expires_at)) {
            return back()->withErrors(['mobile_otp' => 'OTP has expired. Please request a new one.']);
        }

        $user->mobile_verified_at = Carbon::now();
        $user->mobile_otp = null;
        $user->mobile_otp_expires_at = null;
        $user->save();

        return back()->with('success', 'Mobile number verified successfully.');
    }

    // ============================================================
    // HELPER: SEND MOBILE OTP (SMS)
    // ============================================================
    /**
     * ⚠️ Placeholder — plug in your SMS provider here.
     * Examples: Twilio, Nexmo, Sparrow SMS (Nepal), Fast2SMS, etc.
     *
     * For development, the OTP is logged to storage/logs/laravel.log.
     */
    private function sendMobileOtpSms(User $user, string $otp): array
    {
        try {
            $mobile = $user->mobile_number;

            if (empty($mobile)) {
                return ['success' => false, 'message' => 'No mobile number on account.'];
            }

            // ---------------------------
            // TODO: Replace with real SMS provider.
            // ---------------------------
            // Example (Sparrow SMS for Nepal):
            // Http::get('http://api.sparrowsms.com/v2/sms/', [
            //     'token'  => config('services.sparrow.token'),
            //     'from'   => config('services.sparrow.from'),
            //     'to'     => $mobile,
            //     'text'   => "Your TolBazar OTP is {$otp}. Valid for 10 minutes.",
            // ]);
            //
            // Example (Twilio):
            // Http::withBasicAuth(config('services.twilio.sid'), config('services.twilio.token'))
            //     ->asForm()
            //     ->post('https://api.twilio.com/2010-04-01/Accounts/'.config('services.twilio.sid').'/Messages.json', [
            //         'From' => config('services.twilio.from'),
            //         'To'   => $mobile,
            //         'Body' => "Your TolBazar OTP is {$otp}.",
            //     ]);

            Log::info('MOBILE OTP (DEV MODE)', ['user_id' => $user->id, 'mobile' => $mobile, 'otp' => $otp]);

            return ['success' => true, 'message' => 'OTP sent (dev mode — check logs).'];
        } catch (\Throwable $e) {
            Log::error('Mobile OTP exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function accountSettings()
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();
        $sessionsCount = DB::table('sessions')->where('user_id', $user->id)->count();
        $recentActivities = $user->loginActivities()->recent(5)->get();

        return view('account.settings', compact('user', 'sessionsCount', 'recentActivities'));
    }

    public function loginActivity(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();

        $filter = $request->get('filter', 'all');
        $query = $user->loginActivities();

        if ($filter === 'success') $query->where('status', 'success');
        elseif ($filter === 'failed') $query->where('status', 'failed');

        $activities = $query->paginate(15)->withQueryString();

        return view('account.login-activity', compact('user', 'activities', 'filter'));
    }

    private function logActivity(User $user, string $status = 'success', ?string $sessionId = null, ?string $reason = null): void
    {
        try {
            $ua = request()->userAgent();
            $parsed = \App\Support\UserAgentParser::parse($ua);

            LoginActivity::create([
                'user_id'        => $user->id,
                'ip_address'     => request()->ip(),
                'user_agent'     => $ua,
                'device_type'    => $parsed['device_type'],
                'browser'        => $parsed['browser'],
                'platform'       => $parsed['platform'],
                'status'         => $status,
                'session_id'     => $sessionId ?? session()->getId(),
                'failure_reason' => $reason,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Login activity log failed', ['error' => $e->getMessage()]);
        }
    }

    public function sessions()
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();
        $currentSessionId = session()->getId();

        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($session) use ($currentSessionId) {
                $parsed = \App\Support\UserAgentParser::parse($session->user_agent);
                $session->is_current   = $session->id === $currentSessionId;
                $session->device_type  = $parsed['device_type'];
                $session->browser      = $parsed['browser'];
                $session->platform     = $parsed['platform'];
                $session->last_seen    = \Carbon\Carbon::createFromTimestamp($session->last_activity);
                return $session;
            });

        return view('account.sessions', compact('user', 'sessions'));
    }

    public function destroySession(Request $request, string $sessionId)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();

        if ($sessionId === session()->getId()) {
            return back()->withErrors(['session' => 'You cannot logout your current session from here. Use Logout instead.']);
        }

        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', $sessionId)
            ->delete();

        return back()->with('success', 'Device logged out successfully.');
    }

    public function logoutAllDevices(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();
        $currentSessionId = session()->getId();

        // Delete all sessions except current
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        // Also invalidate remember token
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Log the action
        $this->logActivity($user, 'logout');

        return back()->with('success', 'All other devices have been logged out successfully.');
    }

    public function showDeactivateForm()
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        return view('account.deactivate', ['user' => Auth::user()]);
    }

    public function deactivateAccount(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $request->validate([
            'password' => 'required|string',
            'confirm'  => 'accepted',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        if ($user->hasAnyRole(['Super Admin'])) {
            return back()->withErrors(['password' => 'Super Admin account cannot be deactivated.']);
        }

        $user->update([
            'account_status' => 'deactivated',
            'deactivated_at' => Carbon::now(),
        ]);

        // Logout all sessions (including current)
        DB::table('sessions')->where('user_id', $user->id)->delete();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Your account has been deactivated. You can reactivate it by logging in again.');
    }

    public function showReactivateForm(Request $request)
    {
        $email = session('reactivation_email');
        if (!$email) {
            return redirect()->route('login')->withErrors(['email' => 'Please login first.']);
        }

        $user = User::withTrashed()->where('email', $email)->first();
        if (!$user || !$user->isDeactivated()) {
            session()->forget('reactivation_email');
            return redirect()->route('login');
        }

        return view('account.reactivate', compact('user', 'email'));
    }

    public function showCancelDeletionForm(Request $request)
    {
        $email = session('reactivation_email');
        if (!$email) {
            return redirect()->route('login')->withErrors(['email' => 'Please login first.']);
        }

        $user = User::withTrashed()->where('email', $email)->first();
        if (!$user || (!$user->isPendingDeletion() && !$user->trashed())) {
            session()->forget('reactivation_email');
            return redirect()->route('login');
        }

        return view('account.cancel-deletion', compact('user', 'email'));
    }


    public function reactivateAccount(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::withTrashed()->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        if (!$user->isDeactivated()) {
            return redirect()->route('login')->with('info', 'Your account is already active. Please login.');
        }

        $user->update([
            'account_status' => 'active',
            'deactivated_at' => null,
            'deleted_at'     => null,
        ]);

        session()->forget('reactivation_email');
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $this->logActivity($user, 'success');
        $this->saveAccountToDevice($user);   // ✅ थप्नुस

        return $this->redirectAfterLogin($user, $request);
    }

    public function showDeleteForm()
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        return view('account.delete', ['user' => Auth::user()]);
    }

    public function deleteAccount(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $request->validate([
            'password'         => 'required|string',
            'confirmation'     => 'required|in:DELETE',
            'understand'       => 'accepted',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        if ($user->hasRole('Super Admin')) {
            return back()->withErrors(['password' => 'Super Admin account cannot be deleted.']);
        }

        // 30-day grace period
        $user->update([
            'account_status'        => 'deletion_pending',
            'deletion_requested_at' => Carbon::now(),
            'deletion_scheduled_at' => Carbon::now()->addDays(30),
        ]);

        // Logout all sessions
        DB::table('sessions')->where('user_id', $user->id)->delete();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Your account deletion has been scheduled. You have 30 days to cancel by logging in again.');
    }

    public function cancelDeletion(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::withTrashed()->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        if (!$user->isPendingDeletion() && !$user->trashed()) {
            return redirect()->route('login')->with('info', 'No deletion pending for this account.');
        }

        $user->update([
            'account_status'        => 'active',
            'deletion_requested_at' => null,
            'deletion_scheduled_at' => null,
            'deleted_at'            => null,
        ]);

        session()->forget('reactivation_email');
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $this->logActivity($user, 'success');
        $this->saveAccountToDevice($user);   // ✅ थप्नुस

        return $this->redirectAfterLogin($user, $request);
    }


public function accountSwitcher()
{
    if ($redirect = $this->redirectIfNotAuthenticated()) {
        return $redirect;
    }

    $user = Auth::user();
    $modes = $user->getAvailableModes();

    // Load saved accounts on this device
    $token = Cookie::get('tb_device_token');
    $savedAccounts = collect();

    if ($token) {
        $savedAccounts = SavedAccount::where('device_token', $token)
            ->with('user')
            ->orderByDesc('last_used_at')
            ->get()
            ->filter(function ($sa) {
                return $sa->user
                    && !$sa->user->isDeactivated()
                    && !$sa->user->isPendingDeletion()
                    && !$sa->user->trashed();
            });
    }

    return view('account.switcher', compact('user', 'modes', 'savedAccounts'));
}

private function saveAccountToDevice(User $user): void
{
    try {
        $token = Cookie::get('tb_device_token');

        if (!$token) {
            $token = Str::random(64);
            Cookie::queue('tb_device_token', $token, 60 * 24 * 365); // 1 year
        }

        SavedAccount::updateOrCreate(
            ['device_token' => $token, 'user_id' => $user->id],
            ['last_used_at' => now()]
        );
    } catch (\Throwable $e) {
        Log::warning('Failed to save account to device', [
            'user_id' => $user->id,
            'error'   => $e->getMessage(),
        ]);
    }
}

public function switchAccount(Request $request, int $userId)
{
    if ($redirect = $this->redirectIfNotAuthenticated()) {
        return $redirect;
    }

    $token = Cookie::get('tb_device_token');

    if (!$token) {
        return back()->withErrors(['account' => 'No saved accounts found on this device.']);
    }

    $saved = SavedAccount::where('device_token', $token)
        ->where('user_id', $userId)
        ->first();

    if (!$saved) {
        return back()->withErrors(['account' => 'This account is not saved on this device.']);
    }

    $targetUser = User::withTrashed()->find($userId);

    if (!$targetUser) {
        $saved->delete();
        return back()->withErrors(['account' => 'This account no longer exists.']);
    }

    if ($targetUser->isDeactivated()) {
        return back()->withErrors([
            'account' => 'This account is deactivated. Log in with password to reactivate it.',
        ]);
    }

    if ($targetUser->isPendingDeletion() || $targetUser->trashed()) {
        return back()->withErrors([
            'account' => 'This account is scheduled for deletion. Log in with password to restore it.',
        ]);
    }

    // Same user? just refresh
    if (Auth::id() === $userId) {
        return back()->with('info', 'You are already using this account.');
    }

    // Logout current account
    if (Auth::check()) {
        $this->logActivity(Auth::user(), 'logout');
        Auth::logout();
    }

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Login target account
    Auth::login($targetUser, true);
    $request->session()->regenerate();

    $saved->update(['last_used_at' => now()]);
    $this->logActivity($targetUser, 'success');

    return $this->redirectAfterLogin($targetUser, $request)
        ->with('success', 'Switched to ' . ($targetUser->name ?: $targetUser->email) . '.');
}

public function removeSavedAccount(Request $request, int $userId)
{
    if ($redirect = $this->redirectIfNotAuthenticated()) {
        return $redirect;
    }

    $token = Cookie::get('tb_device_token');

    if ($token) {
        SavedAccount::where('device_token', $token)
            ->where('user_id', $userId)
            ->delete();
    }

    // If removing current user, logout
    if (Auth::id() === $userId) {
        $this->logActivity(Auth::user(), 'logout');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Account removed from this device. Please log in again.');
    }

    return back()->with('success', 'Account removed from this device.');
}

public function addAnotherAccount(Request $request)
{
    if ($redirect = $this->redirectIfNotAuthenticated()) {
        return $redirect;
    }

    $this->logActivity(Auth::user(), 'logout');
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    session()->forget(['active_branch_id', 'active_branch_role', 'reactivation_email']);

    return redirect()->route('login')
        ->with('info', 'Log in to add another account. Your previous account is saved on this device.');
}

    public function switchMode(Request $request, string $mode)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();

        if (!$user->canSwitchTo($mode)) {
            return back()->withErrors(['mode' => 'You do not have access to this mode.']);
        }

        $user->update(['active_mode' => $mode]);

        // Set session context based on mode
        match ($mode) {
            'seller'   => $this->setupSellerSession($user),
            'admin'    => session()->forget(['active_branch_id', 'active_branch_role']),
            'delivery' => session()->forget(['active_branch_id', 'active_branch_role']),
            default    => session()->forget(['active_branch_id', 'active_branch_role']),
        };

        $route = match ($mode) {
            'seller'   => 'seller.dashboard',
            'admin'    => 'admin.dashboard',
            'delivery' => 'delivery.dashboard',
            default    => 'home',
        };

        return redirect()->route($route)
            ->with('success', "Switched to {$mode} mode.");
    }

    private function setupSellerSession(User $user): void
    {
        if ($user->isBranchStaff() && $user->branchStaff) {
            session([
                'active_branch_id'   => $user->branchStaff->branch_id,
                'active_branch_role' => $user->branchStaff->role,
            ]);
            return;
        }

        if ($user->sellerProfile) {
            $default = $user->sellerProfile->branches()
                ->where('is_default', 1)->where('status', 1)->first()
                ?? $user->sellerProfile->branches()->where('status', 1)->first();

            if ($default) {
                session([
                    'active_branch_id'   => $default->id,
                    'active_branch_role' => 'owner',
                ]);
            }
        }
    }

    public function showChangeUsernameForm()
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        return view('account.change-username', ['user' => Auth::user()]);
    }

    public function updateUsername(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();

        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'alpha_dash',
                'unique:users,username,' . $user->id,
            ],
            'password' => 'required|string',
        ], [
            'username.alpha_dash' => 'Username can only contain letters, numbers, hyphens and underscores.',
            'username.unique'     => 'This username is already taken.',
        ]);

        // Password check — required for security
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        // Same username check
        if (strtolower($validated['username']) === strtolower($user->username ?? '')) {
            return back()->withErrors(['username' => 'This is already your current username.']);
        }

        $oldUsername = $user->username;
        $user->update(['username' => $validated['username']]);

        Log::info('Username changed', [
            'user_id'      => $user->id,
            'old_username' => $oldUsername,
            'new_username' => $validated['username'],
        ]);

        return redirect()->route('profile.me')
            ->with('success', 'Your username has been updated to @' . $validated['username']);
    }

    public function showChangeEmailForm()
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        return view('account.change-email', ['user' => Auth::user()]);
    }

    public function sendEmailChangeOtp(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();

        $request->validate([
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        if (strtolower($request->email) === strtolower($user->email ?? '')) {
            return back()->withErrors(['email' => 'This is already your current email.']);
        }

        $otp = rand(100000, 999999);

        // Save pending change
        $user->update([
            'pending_email'                => $request->email,
            'email_change_otp'             => $otp,
            'email_change_otp_expires_at'  => Carbon::now()->addMinutes(10),
        ]);

        $sent = $this->sendEmailChangeOtpEmail($user, $request->email, $otp);

        if (!$sent['success']) {
            // Roll back if email send fails
            $user->update([
                'pending_email'               => null,
                'email_change_otp'            => null,
                'email_change_otp_expires_at' => null,
            ]);

            Log::error('Email change OTP failed', [
                'user_id' => $user->id,
                'error'   => $sent['message'] ?? null,
            ]);

            return back()->withErrors(['email' => 'Failed to send OTP to the new email. Please try again.']);
        }

        return back()->with('success', 'OTP sent to ' . $request->email . '. Check your inbox.');
    }

    public function verifyEmailChangeOtp(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = Auth::user();

        if (!$user->pending_email || !$user->email_change_otp) {
            return back()->withErrors(['otp' => 'No pending email change. Please request a new one.']);
        }

        if ((string) $user->email_change_otp !== (string) $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        if (!$user->email_change_otp_expires_at || Carbon::now()->isAfter($user->email_change_otp_expires_at)) {
            return back()->withErrors(['otp' => 'OTP has expired. Please request a new one.']);
        }

        $newEmail = $user->pending_email;

        // Double check uniqueness (race condition guard)
        if (User::where('email', $newEmail)->where('id', '!=', $user->id)->exists()) {
            $user->update([
                'pending_email'               => null,
                'email_change_otp'            => null,
                'email_change_otp_expires_at' => null,
            ]);
            return back()->withErrors(['otp' => 'This email was just taken by another account. Please use a different email.']);
        }

        $oldEmail = $user->email;

        $user->update([
            'email'                        => $newEmail,
            'pending_email'                => null,
            'email_change_otp'             => null,
            'email_change_otp_expires_at'  => null,
            'email_verified_at'            => Carbon::now(),
        ]);

        Log::info('Email changed', [
            'user_id'   => $user->id,
            'old_email' => $oldEmail,
            'new_email' => $newEmail,
        ]);

        return redirect()->route('account.change-email')
            ->with('success', 'Your email has been successfully updated to ' . $newEmail . '.');
    }

    public function resendEmailChangeOtp(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();

        if (!$user->pending_email) {
            return back()->withErrors(['otp' => 'No pending email change to resend.']);
        }

        $otp = rand(100000, 999999);

        $user->update([
            'email_change_otp'            => $otp,
            'email_change_otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        $sent = $this->sendEmailChangeOtpEmail($user, $user->pending_email, $otp);

        if (!$sent['success']) {
            Log::error('Email change OTP resend failed', [
                'user_id' => $user->id,
                'error'   => $sent['message'] ?? null,
            ]);
            return back()->withErrors(['otp' => 'Failed to resend OTP. Please try again.']);
        }

        return back()->with('success', 'A new OTP has been sent to ' . $user->pending_email . '.');
    }

    public function cancelEmailChange(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();

        $user->update([
            'pending_email'               => null,
            'email_change_otp'            => null,
            'email_change_otp_expires_at' => null,
        ]);

        return back()->with('info', 'Pending email change cancelled.');
    }

    private function sendEmailChangeOtpEmail(User $user, string $newEmail, string $otp): array
    {
        try {
            $response = Http::timeout(30)->withHeaders([
                'Accept'       => 'application/json',
                'api-key'      => $this->brevoApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => ['name' => $this->senderName, 'email' => $this->senderEmail],
                'to'     => [['name' => $user->name, 'email' => $newEmail]],
                'subject' => 'Confirm Your New TolBazar Email Address',
                'htmlContent' => "
                    <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;padding:30px;background:#ffffff;border:1px solid #eeeeee;border-radius:10px;'>
                        <h2 style='color:#0d6efd;margin-bottom:16px;'>Confirm Your New Email</h2>
                        <p>Hello <strong>{$user->name}</strong>,</p>
                        <p>We received a request to change the email address on your TolBazar account to <strong>{$newEmail}</strong>.</p>
                        <p>Please use the OTP below to confirm this change:</p>
                        <div style='text-align:center;margin:25px 0;'>
                            <div style='font-size:32px;font-weight:bold;letter-spacing:8px;padding:15px;background:#f5f5f5;border-radius:8px;display:inline-block;'>
                                {$otp}
                            </div>
                        </div>
                        <p>This OTP will expire in <strong>10 minutes</strong>.</p>
                        <p style='color:#6c757d;font-size:13px;margin-top:24px;'>
                            If you did NOT request this change, please ignore this email and consider changing your password immediately.
                        </p>
                        <br>
                        <p>Best Regards,<br><strong>TolBazar Team</strong></p>
                    </div>
                ",
            ]);

            Log::info('Brevo Email Change OTP Response', [
                'user_id' => $user->id,
                'new_email' => $newEmail,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'OTP sent to new email.'];
            }

            return ['success' => false, 'message' => $response->body()];
        } catch (\Throwable $e) {
            Log::error('Brevo Email Change OTP Exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function showChangeNameForm()
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        return view('account.change-name', ['user' => Auth::user()]);
    }

    public function updateName(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\pL\pM\s\.\-\'\x{0900}-\x{097F}]+$/u',
            ],
            'password' => 'required|string',
        ], [
            'name.regex' => 'Name can only contain letters, spaces, dots, hyphens and apostrophes.',
        ]);

        // Password confirmation (anti-hijack protection)
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        $newName = trim($validated['name']);

        // Same name check (case-insensitive)
        if (mb_strtolower($newName) === mb_strtolower($user->name ?? '')) {
            return back()->withErrors(['name' => 'This is already your current name.']);
        }

        $oldName = $user->name;
        $user->update(['name' => $newName]);

        Log::info('Name changed', [
            'user_id'  => $user->id,
            'old_name' => $oldName,
            'new_name' => $newName,
        ]);

        return redirect()->route('profile.me')
            ->with('success', 'Your name has been updated to "' . $newName . '".');
    }

    public function showChangeMobileForm()
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        return view('account.change-mobile', ['user' => Auth::user()]);
    }

    public function sendMobileChangeOtp(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();

        $request->validate([
            'mobile_number' => [
                'required',
                'string',
                'min:7',
                'max:30',
                'regex:/^\+?[0-9\s\-]{7,20}$/',
                'unique:users,mobile_number',
            ],
            'password' => 'required|string',
        ], [
            'mobile_number.regex'  => 'Please enter a valid mobile number (digits, +, spaces, hyphens only).',
            'mobile_number.unique' => 'This mobile number is already registered to another account.',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        // Same number check
        $cleanCurrent = preg_replace('/[^0-9]/', '', $user->mobile_number ?? '');
        $cleanNew     = preg_replace('/[^0-9]/', '', $request->mobile_number);

        if ($cleanCurrent && $cleanCurrent === $cleanNew) {
            return back()->withErrors(['mobile_number' => 'This is already your current mobile number.']);
        }

        $otp = rand(100000, 999999);

        $user->update([
            'pending_mobile'                 => $request->mobile_number,
            'mobile_change_otp'              => $otp,
            'mobile_change_otp_expires_at'   => Carbon::now()->addMinutes(10),
        ]);

        $sent = $this->sendMobileChangeOtpSms($user, $request->mobile_number, $otp);

        if (!$sent['success']) {
            // Rollback on SMS failure
            $user->update([
                'pending_mobile'                => null,
                'mobile_change_otp'             => null,
                'mobile_change_otp_expires_at'  => null,
            ]);

            Log::error('Mobile change OTP failed', [
                'user_id' => $user->id,
                'error'   => $sent['message'] ?? null,
            ]);

            return back()->withErrors(['mobile_number' => 'Failed to send OTP. Please try again.']);
        }

        return back()->with('success', 'OTP sent to ' . $request->mobile_number . '.');
    }

    public function verifyMobileChangeOtp(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = Auth::user();

        if (!$user->pending_mobile || !$user->mobile_change_otp) {
            return back()->withErrors(['otp' => 'No pending mobile change. Please request a new one.']);
        }

        if ((string) $user->mobile_change_otp !== (string) $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        if (!$user->mobile_change_otp_expires_at || Carbon::now()->isAfter($user->mobile_change_otp_expires_at)) {
            return back()->withErrors(['otp' => 'OTP has expired. Please request a new one.']);
        }

        $newMobile = $user->pending_mobile;

        // Race condition guard
        if (User::where('mobile_number', $newMobile)->where('id', '!=', $user->id)->exists()) {
            $user->update([
                'pending_mobile'               => null,
                'mobile_change_otp'            => null,
                'mobile_change_otp_expires_at' => null,
            ]);
            return back()->withErrors(['otp' => 'This mobile was just registered by another account. Please try a different one.']);
        }

        $oldMobile = $user->mobile_number;

        $user->update([
            'mobile_number'                 => $newMobile,
            'pending_mobile'                => null,
            'mobile_change_otp'             => null,
            'mobile_change_otp_expires_at'  => null,
            'mobile_verified_at'            => Carbon::now(),
        ]);

        Log::info('Mobile changed', [
            'user_id'    => $user->id,
            'old_mobile' => $oldMobile,
            'new_mobile' => $newMobile,
        ]);

        return redirect()->route('account.change-mobile')
            ->with('success', 'Your mobile number has been updated to ' . $newMobile . '.');
    }

    public function resendMobileChangeOtp(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();

        if (!$user->pending_mobile) {
            return back()->withErrors(['otp' => 'No pending mobile change to resend.']);
        }

        $otp = rand(100000, 999999);

        $user->update([
            'mobile_change_otp'            => $otp,
            'mobile_change_otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        $sent = $this->sendMobileChangeOtpSms($user, $user->pending_mobile, $otp);

        if (!$sent['success']) {
            Log::error('Mobile change OTP resend failed', [
                'user_id' => $user->id,
                'error'   => $sent['message'] ?? null,
            ]);
            return back()->withErrors(['otp' => 'Failed to resend OTP. Please try again.']);
        }

        return back()->with('success', 'A new OTP has been sent to ' . $user->pending_mobile . '.');
    }

    public function cancelMobileChange(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }

        $user = Auth::user();

        $user->update([
            'pending_mobile'               => null,
            'mobile_change_otp'            => null,
            'mobile_change_otp_expires_at' => null,
        ]);

        return back()->with('info', 'Pending mobile change cancelled.');
    }

    private function sendMobileChangeOtpSms(User $user, string $mobile, string $otp): array
    {
        try {
            if (empty($mobile)) {
                return ['success' => false, 'message' => 'No mobile number provided.'];
            }

            // ---------------------------
            // TODO: Replace with real SMS provider (Sparrow, Twilio, etc.)
            // ---------------------------
            // Http::get('http://api.sparrowsms.com/v2/sms/', [
            //     'token' => config('services.sparrow.token'),
            //     'from'  => config('services.sparrow.from'),
            //     'to'    => $mobile,
            //     'text'  => "Your TolBazar mobile change OTP is {$otp}. Valid for 10 minutes.",
            // ]);

            Log::info('MOBILE CHANGE OTP (DEV MODE)', [
                'user_id' => $user->id,
                'mobile'  => $mobile,
                'otp'     => $otp,
            ]);

            return ['success' => true, 'message' => 'OTP sent (dev mode — check logs).'];
        } catch (\Throwable $e) {
            Log::error('Mobile change OTP exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

}
