@if (Auth::user()->usertype == 'dev')
    @extends('dev.layouts.app')
@else
    @extends('layouts.layouts_dashboard')
@endif

@section('title', 'Edit Profile')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="mb-4">
            @include('profile.partials.update-profile-information-form')
        </div>
        
        <div class="mb-4">
            @include('profile.partials.update-password-form')
        </div>
        
        <div class="mb-4">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
