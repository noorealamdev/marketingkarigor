<?php

namespace App\Http\Controllers;

use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MemberProfileController extends Controller
{
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'phone'                     => 'nullable|string|max:50',
            'date_of_birth'             => 'nullable|date',
            'address'                   => 'nullable|string|max:500',
            'nid_number'                => 'nullable|string|max:50',
            'emergency_contact_name'    => 'nullable|string|max:150',
            'emergency_contact_phone'   => 'nullable|string|max:50',
            'emergency_contact_relation'=> 'nullable|string|max:80',
            'bank_name'                 => 'nullable|string|max:150',
            'bank_account_holder'       => 'nullable|string|max:150',
            'bank_account_number'       => 'nullable|string|max:100',
            'bank_branch'               => 'nullable|string|max:150',
            'bank_routing_number'       => 'nullable|string|max:50',
            'admin_notes'               => 'nullable|string|max:2000',
        ]);

        $user->profile()->updateOrCreate(['user_id' => $user->id], $data);

        return back()->with('success', 'Personal information updated.');
    }

    public function uploadDocument(Request $request, User $user)
    {
        $request->validate([
            'document'      => 'required|file|max:20480',
            'document_label'=> 'nullable|string|max:120',
        ]);

        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);

        $file  = $request->file('document');
        $label = $request->input('document_label') ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $profile->addMediaFromRequest('document')
            ->usingName($label)
            ->usingFileName($file->getClientOriginalName())
            ->withCustomProperties(['uploaded_by' => auth()->id()])
            ->toMediaCollection('personal_documents');

        $this->cleanTemp();

        return back()->with('success', 'Document uploaded.');
    }

    public function downloadDocument(User $user, Media $media)
    {
        abort_unless(
            $media->model_type === MemberProfile::class && $media->model_id === optional($user->profile)->id,
            403
        );

        $path = $media->getPath();
        abort_if(!file_exists($path), 404, 'File not found.');

        return response()->download($path, $media->file_name, ['Content-Type' => $media->mime_type]);
    }

    public function destroyDocument(User $user, Media $media)
    {
        abort_unless(
            $media->model_type === MemberProfile::class && $media->model_id === optional($user->profile)->id,
            403
        );

        $media->delete();

        return back()->with('success', 'Document deleted.');
    }

    private function cleanTemp(): void
    {
        $path = storage_path('media-library/temp');
        if (is_dir($path)) {
            File::deleteDirectory($path);
        }
    }
}
