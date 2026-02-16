<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Handles customer account management actions.
 *
 * This controller manages customer-specific account operations such as
 * account deletion. It is separate from the LoginController to maintain
 * single responsibility principle.
 */
class CustomerAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Handle account deletion request.
     *
     * This permanently deletes the user account and all associated data.
     * Requires password confirmation for security.
     *
     * @throws ValidationException If password confirmation fails
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Ensure user is authenticated
        if (!$user) {
            return redirect()->route('user.login');
        }

        // Require password confirmation for destructive action
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => [translate('The provided password does not match our records.')],
            ]);
        }

        // Store user info for logging before deletion
        $userInfo = [
            'user_id' => $user->id,
            'email' => $user->email,
            'user_type' => $user->user_type,
            'deleted_at' => now()->toDateTimeString(),
            'ip_address' => $request->ip(),
        ];

        try {
            DB::transaction(function () use ($user) {
                // Delete cart items
                Cart::where('user_id', $user->id)->delete();

                // Delete user uploads (files and database records)
                $this->deleteUserUploads($user);

                // Delete customer products (if any)
                $user->customer_products()->delete();

                // Delete the user record
                $user->delete();
            });

            // Log the account deletion for audit purposes
            Log::channel('single')->info('Account deleted', $userInfo);

            // Logout and invalidate session
            auth()->guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            flash(translate('Your account has been permanently deleted.'))->success();

            return redirect()->route('home');
        } catch (\Throwable $e) {
            Log::error('Account deletion failed', [
                'user_id' => $userInfo['user_id'],
                'error' => $e->getMessage(),
            ]);

            flash(translate('Account deletion failed. Please try again or contact support.'))->error();

            return back();
        }
    }

    /**
     * Delete all uploaded files for a user.
     */
    private function deleteUserUploads(User $user): void
    {
        $uploads = $user->uploads;

        if (!$uploads || $uploads->isEmpty()) {
            return;
        }

        $isS3 = config('filesystems.default') === 's3';

        foreach ($uploads as $upload) {
            try {
                if ($isS3) {
                    Storage::disk('s3')->delete($upload->file_name);
                }

                // Also delete local copy if exists
                $localPath = public_path($upload->file_name);
                if (file_exists($localPath)) {
                    unlink($localPath);
                }

                $upload->delete();
            } catch (\Throwable $e) {
                // Log but don't fail the whole operation for file cleanup issues
                Log::warning('Failed to delete upload file', [
                    'upload_id' => $upload->id,
                    'file_name' => $upload->file_name,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
