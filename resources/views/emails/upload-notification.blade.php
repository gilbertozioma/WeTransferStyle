@component('mail::message')
# Files Have Been Shared With You

Someone has shared files with you through our file sharing system.

@component('mail::panel')
The link will expire on {{ $expiresAt }}
@endcomponent

@if($hasPassword)
@component('mail::panel')
**⚠️ Password Protected Files**
To download these files, append `?password=YOUR_PASSWORD` to any download link.
Example: `link?password=your-shared-password`
@endcomponent
@endif

@if($session->files->count() === 1)
@php $file = $session->files->first() @endphp
@component('mail::panel')
**File Details:**
- Name: {{ $file->original_filename }}
- Size: {{ \App\Helpers\FormatHelper::formatBytes($file->file_size) }}

@component('mail::button', ['url' => $downloadUrl])
Download File
@endcomponent
@endcomponent

@else
**Files Shared ({{ $session->files->count() }} files):**

@component('mail::table')
| File Name | Size | Action |
|:----------|:-----|:-------|
@foreach($session->files as $file)
| {{ $file->original_filename }} | {{ \App\Helpers\FormatHelper::formatBytes($file->file_size) }} | [Download File]({{ $downloadUrl . '?file_id=' . $file->id }}) |
@endforeach
@endcomponent
@endif

@if($hasPassword)
**Note:** Remember to add the password parameter to any download link you use.
@endif

Thanks,  
{{ config('app.name') }}
@endcomponent