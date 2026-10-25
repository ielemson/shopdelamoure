<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class WebsiteSettingController extends Controller
{
    public function edit()
    {
        $setting = WebsiteSetting::firstOrCreate([]);

        return view('admin.settings.website', compact('setting'));
    }

    public function update(Request $request)
    {
        abort_unless($request->user(), 401);

        $validated = $request->validate([
            'website_name' => 'nullable|string|max:255',

            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg,jpeg,webp|max:1024',

            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',

            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',

            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'snapchat' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',

            'support_name' => 'nullable|string|max:255',
            'support_role' => 'nullable|string|max:255',
            'support_phone' => 'nullable|string|max:50',

            'support_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:2048',

            'support_message_title' => 'nullable|string|max:255',
            'support_message_body' => 'nullable|string',

            // Admin password
            'current_password' => [
                'nullable',
                'required_with:password,password_confirmation',
                'current_password',
            ],
            'password' => [
                'nullable',
                'required_with:current_password,password_confirmation',
                'string',
                'min:8',
                'confirmed',
            ],
            'password_confirmation' => [
                'nullable',
                'required_with:password',
                'string',
            ],

            // Checkout settings
            'enable_store_pickup' => 'required|boolean',
        ]);

        $setting = WebsiteSetting::firstOrCreate([]);

        // Password fields belong to the user, not website settings.
        $data = Arr::except($validated, [
            'current_password',
            'password',
            'password_confirmation',
        ]);

        $data['enable_store_pickup'] = $request->boolean(
            'enable_store_pickup'
        );

        $passwordChanged = $request->filled('password');

        $newPasswordHash = $passwordChanged
            ? Hash::make($validated['password'])
            : null;

        $newFiles = [];
        $oldFiles = [];

        try {
            // Upload replacements before removing existing images.
            foreach ([
                'logo' => 'settings',
                'favicon' => 'settings',
                'support_image' => 'settings/support',
            ] as $field => $directory) {
                if (! $request->hasFile($field)) {
                    continue;
                }

                $path = $request->file($field)->store(
                    $directory,
                    'public'
                );

                if (! $path) {
                    throw new \RuntimeException(
                        "Unable to upload {$field}."
                    );
                }

                $data[$field] = $path;
                $newFiles[] = $path;

                if ($setting->{$field}) {
                    $oldFiles[] = $setting->{$field};
                }
            }

            DB::transaction(function () use (
                $setting,
                $data,
                $request,
                $passwordChanged,
                $newPasswordHash
            ) {
                $setting->update($data);

                if ($passwordChanged) {
                    $request->user()->forceFill([
                        'password' => $newPasswordHash,
                    ])->save();
                }
            });
        } catch (\Throwable $exception) {
            // Remove new uploads if saving fails.
            foreach ($newFiles as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        // Remove replaced images only after saving succeeds.
        foreach ($oldFiles as $path) {
            Storage::disk('public')->delete($path);
        }

        return back()->with(
            'success',
            $passwordChanged
                ? 'Website settings and your admin password updated successfully.'
                : 'Website settings updated successfully.'
        );
    }
}
