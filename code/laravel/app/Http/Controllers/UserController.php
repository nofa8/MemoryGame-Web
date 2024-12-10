<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function indexAll(Request $request)
    {
        // Check if the user is an Admin
        $userType = $request->user()->type;

        if ($userType === 'A') {
            // Get search, type filter, and include_deleted filter from the request
            $searchQuery = $request->input('search', '');
            $filterType = $request->input('type', ''); // 'A' for Admin, 'P' for Player, '' for all
            $includeDeleted = $request->input('include_deleted') === 'true'; // Check if it's 'true'

            // Build the query
            $query = User::select('id', 'name', 'nickname', 'email', 'photo_filename', 'brain_coins_balance', 'type', 'blocked', 'deleted_at');

            // Apply search filter
            if ($searchQuery) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('name', 'like', "%$searchQuery%")
                        ->orWhere('nickname', 'like', "%$searchQuery%")
                        ->orWhere('email', 'like', "%$searchQuery%");
                });
            }

            // Apply type filter
            if ($filterType) {
                $query->where('type', $filterType);
            }

            // Apply include_deleted filter
            if ($includeDeleted) {
                $query->withTrashed();
            }

            // Paginate the results
            $users = $query->paginate(10);

            return response()->json([
                'data' => $users->items(),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'from' => $users->firstItem(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'to' => $users->lastItem(),
                    'total' => $users->total(),
                    'trashed' => $includeDeleted,
                ],
            ]);
        } else {
            return response()->json(['message' => 'Not Admin'], 403);
        }
    }


    // Method to delete a user as an admin by their nickname
    public function deleteUserAsAdmin(Request $request, $nickname)
    {
        // Ensure the logged-in user is an admin
        $user = $request->user();
        if ($user->type !== 'A') {
            return response()->json(['message' => 'Unauthorized, Admins only'], 403);
        }

        // Find the user by their nickname
        $userToDelete = User::where('nickname', $nickname)->first();

        if (!$userToDelete) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Optionally, check if the admin is confirming with their password
        if ($request->has('password')) {
            $adminPassword = $request->input('password');
            if (!Hash::check($adminPassword, $user->password)) {
                return response()->json(['message' => 'Invalid password'], 403);
            }
        }

        // Soft delete the user (you can use forceDelete() for permanent deletion)
        $userToDelete->delete();

        // Optionally, you can also remove the user's associated data like photos, etc.
        // $userToDelete->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }


    public function restore(Request $request, $nickname)
    {
        $user = $request->user();
        // Check if the current user is an Admin
        if ($user->type != 'A') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Find the user by nickname including soft-deleted users
        $user = User::withTrashed()->where('nickname', $nickname)->first();

        // Check if user exists and is deleted
        if (!$user || !$user->deleted_at) {
            return response()->json(['message' => 'User not found or not deleted'], 404);
        }

        // Restore the user (set deleted_at to null)
        $user->restore();

        return response()->json(['message' => 'User restored successfully', 'user' => $user]);
    }

    public function showMe(Request $request)
    {
        return new UserResource($request->user());
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // Validate the incoming request data
        try {
            // Only validate fields that are being sent
            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'nickname' => 'nullable|string|max:255|unique:users,nickname,' . $user->id,
                'password' => 'nullable|string|min:3|confirmed',
            ]);
        } catch (ValidationException $e) {
            // Return validation errors
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }

        // Update the user's name if provided
        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }

        // Check and update the user's email if provided
        if (isset($validated['email'])) {
            $existingEmail = User::where('email', $validated['email'])->where('id', '!=', $user->id)->first();
            if ($existingEmail) {
                return response()->json([
                    'message' => 'The email address is already in use.',
                    'errors' => [
                        'email' => 'This email address is already associated with another account.'
                    ]
                ], 422);
            }
            $user->email = $validated['email'];
        }

        // Check and update the user's nickname if provided
        if (isset($validated['nickname'])) {
            $existingNickname = User::where('nickname', $validated['nickname'])->where('id', '!=', $user->id)->first();
            if ($existingNickname) {
                return response()->json([
                    'message' => 'The nickname is already in use.',
                    'errors' => [
                        'nickname' => 'This nickname is already taken by another user.'
                    ]
                ], 422);
            }
            $user->nickname = $validated['nickname'];
        }

        // Update password if provided
        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }



        // Save the updated user data

        $user->save();


        $user->refresh();
        // Return the updated user object in the response
        return response()->json([
            'message' => 'Profile updated successfully!',
            'user' => $user,
        ]);
    }

    public function deleteProfile(Request $request)
    {
        $user = $request->user(); // Get the authenticated user

        // Validate the password
        $request->validate([
            'password' => 'required|string',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided password is incorrect.',
            ], 403);
        }

        // Delete the user's profile photo if it exists
        if ($user->photo) {
            $photoPath = str_replace('/storage/', '', $user->photo); // Remove the '/storage/' prefix
            if (Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
        }

        $user->brain_coins_balance = 0;
        $user->save();

        // Delete the user's account
        $user->delete();

        return response()->json([
            'message' => 'Profile deleted successfully.',
        ], 200);
    }

    public function blockOrUnblockAccount(Request $request, $nickname)
    {
        // Ensure the user is an admin
        $userType = $request->user()->type;
        if ($userType != 'A') {
            return response()->json([
                'message' => 'Not Admin',
            ], 403);  // Return a 403 Forbidden if the user is not an admin
        }

        // Find the user by ID
        $user = User::where('nickname', $nickname)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);  // Return a 404 if the user does not exist
        }

        // Toggle the blocked status
        $user->blocked = !$user->blocked;  // Toggle the blocked status (if 1, set to 0; if 0, set to 1)
        $user->save();  // Save the updated user record

        $message = $user->blocked ? 'Account has been blocked' : 'Account has been unblocked';

        return response()->json([
            'message' => $message,
            'user' => $user,
        ], 200);  // Return a success message with the updated user
    }
    public function updateProfilePicture(Request $request)
    {
        Log::info('Incoming request', [
            'files' => $request->allFiles(),
            'all' => $request->all()
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'photo' => 'required|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'debug' => [
                    'files' => $request->allFiles(),
                    'all_data' => $request->all()
                ]
            ], 422);
        }

        $photoPath = $request->file('photo')->store('photos', 'public');

        $user->photo_filename = basename($photoPath);
        $user->save();

        $user = new UserResource($user);

        return response()->json([
            'message' => 'Profile picture updated successfully',
            'user' => $user
        ], 200);
    }
}
