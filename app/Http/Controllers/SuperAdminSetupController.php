<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SuperAdminSetupController extends Controller
{
    public function showSetupForm()
    {
        return view('setup.superadmin');
    }

    public function createSetup(Request $request)
    {
        // Rate limit
        $rateKey  = 'superadmin_setup:' . $request->ip();
        $attempts = Cache::get($rateKey, 0);

        if ($attempts >= 3) {
            abort(429, 'Too many attempts.');
        }

        Cache::put($rateKey, $attempts + 1, now()->addMinutes(5));

        // Token verify
        $expectedToken = config('app.super_admin.setup_token');
        $providedToken = $request->input('setup_token');

        if (!$expectedToken || !$providedToken || !hash_equals($expectedToken, $providedToken)) {
            Log::warning('SuperAdmin setup: Invalid token', [
                'ip'   => $request->ip(),
                'user' => auth()->id(),
            ]);

            return back()->withErrors([
                'setup_token' => 'Invalid setup token. Please check and try again.',
            ]);
        }

        // Double-check role + not exists
        $superAdminRole = Role::where('id', 1)->first()
            ?? Role::where('name', 'Super Admin')->first();

        if (!$superAdminRole) {
            return back()->withErrors([
                'setup_token' => 'Super Admin role (id=1) not configured.',
            ]);
        }

        $exists = User::whereHas('roles', function ($q) use ($superAdminRole) {
            $q->where('roles.id', $superAdminRole->id);
        })->exists();

        if ($exists) {
            abort(404);
        }

        // Validate
        $request->validate([
            'setup_token' => 'required|string',
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:users,email',
            'password'    => [
                'required', 'string', 'min:12', 'confirmed',
                'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/',
            ],
        ], [
            'password.regex' => 'Password must contain uppercase, lowercase, number, and special character.',
        ]);

        // Create
        DB::beginTransaction();

        try {
$user = User::create([
    'name'              => $request->name,
    'username'          => $this->generateUsername($request->name),
    'email'             => $request->email,
    'password'          => Hash::make($request->password),
    'email_verified_at' => now(),
    'active_mode'       => 'super_admin',   // ✅ Super Admin mode
    'is_branch_staff'   => 0,
]);

            $user->roles()->attach($superAdminRole->id);

            DB::commit();

            // Lock file
            file_put_contents(
                config('app.super_admin.lock_file'),
                json_encode([
                    'created_at' => now()->toIso8601String(),
                    'user_id'    => $user->id,
                    'email'      => $user->email,
                    'via'        => 'web-setup',
                    'ip'         => $request->ip(),
                ], JSON_PRETTY_PRINT)
            );

            Log::info('SuperAdmin created via setup', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'ip'      => $request->ip(),
            ]);

            Cache::forget($rateKey);

            return redirect()->route('login')
                ->with('success', 'Super Admin created! Login with your new credentials.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SuperAdmin setup failed', ['error' => $e->getMessage()]);
            return back()->withInput()
                ->withErrors(['setup_token' => 'Failed: ' . $e->getMessage()]);
        }
    }

    private function generateUsername(string $name): string
    {
        $base     = Str::slug($name, '') ?: 'superadmin';
        $username = $base;
        $i        = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $i++;
        }

        return $username;
    }
}