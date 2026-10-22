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

    public function showLoginForm()
    {
        if ($redirect = $this->redirectIfAuthenticated()) {
            return $redirect;
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only('email'))->withErrors(['email' => 'Invalid email or password.']);
        }

        if (!$user->email_verified_at) {
            session(['otp_email' => $user->email]);
            return redirect()->route('verify.otp')->withErrors(['otp' => 'Your email is not verified. Please verify your OTP first.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectAfterLogin($user, $request);
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

        // ============================================================
        // 2. SELLER OWNER → seller mode, default branch
        // ============================================================
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

        // ============================================================
        // 6. DEFAULT → customer mode
        // ============================================================
        $user->update(['active_mode' => 'customer']);
        session()->forget(['active_branch_id', 'active_branch_role']);

        return redirect()->intended(route('home'))
            ->with('success', 'Logged in successfully.');
    }

    public function home(Request $request)
    {
        if ($redirect = $this->redirectIfNotAuthenticated()) {
            return $redirect;
        }
        return view('auth.home');
    }

    public function logout(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session()->forget(['active_branch_id', 'active_branch_role']);

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

            $user = User::where('google_id', $googleId)->first();

            if (!$user) {
                $user = User::where('email', $email)->first();
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
}