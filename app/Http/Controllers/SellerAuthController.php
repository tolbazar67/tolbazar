<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SellerAuthController extends Controller
{
    /**
     * Seller Register Page
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Branch staff → seller dashboard
            if ($user->isBranchStaff() && $user->branchStaff?->status) {
                session([
                    'active_branch_id'   => $user->branchStaff->branch_id,
                    'active_branch_role' => $user->branchStaff->role,
                ]);
                $user->update(['active_mode' => 'seller']);
                return redirect()->route('seller.dashboard');
            }

            // Owner → seller dashboard
            if ($user->isSellerOwner()) {
                $user->update(['active_mode' => 'seller']);
                return redirect()->route('seller.dashboard');
            }

            // Seller role but no profile → apply
            if ($user->hasRole('Seller') && !$user->sellerProfile) {
                return redirect()->route('seller.apply');
            }

            return redirect()->route('home');
        }

        return view('seller.auth.register');
    }

    /**
     * Send Seller Registration OTP
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
        ]);

        $otp = rand(100000, 999999);

        session([
            'seller_register_email' => $request->email,
            'seller_register_otp' => $otp,
            'seller_register_otp_expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::raw(
                "Your TolBazar Seller Verification Code is: {$otp}. This code will expire in 10 minutes.",
                function ($message) use ($request) {
                    $message->to($request->email)
                        ->subject('Seller Registration Verification Code');
                }
            );

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to send verification code.'
            ], 500);
        }
    }

    /**
     * Seller Registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'otp' => 'required|digits:6',
        ]);

        // Check OTP Email
        $sessionEmail = session('seller_register_email');
        if (!$sessionEmail || $sessionEmail !== $request->email) {
            return back()->withInput()->withErrors([
                'email' => 'Please request a verification code for this email.'
            ]);
        }

        // Check OTP
        if (session('seller_register_otp') != $request->otp) {
            return back()->withInput()->withErrors([
                'otp' => 'Invalid verification code.'
            ]);
        }

        // Check OTP Expiry
        $expiresAt = session('seller_register_otp_expires_at');
        if (!$expiresAt || now()->greaterThan($expiresAt)) {
            return back()->withInput()->withErrors([
                'otp' => 'Verification code has expired. Please request a new code.'
            ]);
        }

        // Get Seller Role
        $sellerRole = Role::where('name', 'Seller')->first();
        if (!$sellerRole) {
            return back()->withErrors([
                'email' => 'Seller role does not exist. Please contact administrator.'
            ]);
        }

        // Create Seller User
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp' => null,
            'otp_expires_at' => null,
            'email_verified_at' => now(),
            'active_mode' => 'seller',
        ]);

        // Attach Seller Role
        $user->roles()->attach($sellerRole->id);

        // Clear OTP Session
        session()->forget([
            'seller_register_email',
            'seller_register_otp',
            'seller_register_otp_expires_at',
        ]);

        // Login Seller Automatically
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('seller.dashboard')
            ->with('success', 'Seller account registered and verified successfully.');
    }

    /**
     * Seller Login Page
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Branch staff → seller dashboard
            if ($user->isBranchStaff() && $user->branchStaff?->status) {
                session([
                    'active_branch_id'   => $user->branchStaff->branch_id,
                    'active_branch_role' => $user->branchStaff->role,
                ]);
                $user->update(['active_mode' => 'seller']);
                return redirect()->route('seller.dashboard');
            }

            // Owner → seller dashboard
            if ($user->isSellerOwner()) {
                $user->update(['active_mode' => 'seller']);
                return redirect()->route('seller.dashboard');
            }

            // Seller role, no profile → apply
            if ($user->hasRole('Seller') && !$user->sellerProfile) {
                return redirect()->route('seller.apply');
            }

            return redirect()->route('home');
        }

        return view('seller.auth.login');
    }

    /**
     * Seller Login — Owner + Manager + Staff सबैको लागि
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->with('roles')
            ->first();

        // Email exists?
        if (!$user) {
            return back()->withInput()->withErrors([
                'email' => 'Invalid seller email or password.'
            ]);
        }

        // Must have Seller Role
        if (!$user->hasRole('Seller')) {
            return back()->withInput()->withErrors([
                'email' => 'This account is not registered as a seller.'
            ]);
        }

        // Verify Password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withInput()->withErrors([
                'password' => 'Invalid seller email or password.'
            ]);
        }

        // Check Email Verification
        if (!$user->email_verified_at) {
            return back()->withErrors([
                'email' => 'Please verify your email before logging in.'
            ]);
        }

        // Login
        Auth::login($user);
        $request->session()->regenerate();

        // ============================================================
        // BRANCH STAFF (Manager / Staff) FIRST CHECK
        // ============================================================
        if ($user->isBranchStaff()) {
            $branchStaff = $user->branchStaff;

            if (!$branchStaff || !$branchStaff->status) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->with('error', 'Your staff account is inactive. Contact store owner.');
            }

            if (!$branchStaff->branch_id || !$branchStaff->branch) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->with('error', 'Branch assignment missing. Contact store owner.');
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
        // SELLER OWNER
        // ============================================================
        if ($user->isSellerOwner()) {
            $sellerProfile = $user->sellerProfile;

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
        // Seller role, no profile
        // ============================================================
        $user->update(['active_mode' => 'seller']);
        return redirect()->route('seller.apply')
            ->with('info', 'Please complete your store registration.');
    }

    /**
     * Seller Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Clear seller-specific session
        session()->forget(['active_branch_id', 'active_branch_role']);

        return redirect()
            ->route('seller.login')
            ->with('success', 'Seller logged out successfully.');
    }
}