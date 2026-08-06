@props([
    'label' => null,
    'name' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
    'inline' => false,
])

@php
    $errorClass = $errors->has($name) ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : '';
@endphp

<div {{ $attributes->merge(['class' => $inline ? 'flex items-center gap-4' : '']) }}>
    @if($label)
        <label for="{{ $name }}" class="{{ $inline ? 'text-sm font-medium text-gray-700 whitespace-nowrap min-w-[120px]' : 'block text-sm font-medium text-gray-700 mb-1' }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="{{ $inline ? 'flex-1' : '' }}">
        {{ $slot }}

        @if($hint && !$errors->has($name))
            <p class="text-xs text-gray-500 mt-1">{{ $hint }}</p>
        @endif

        @if($errors->has($name) || $error)
            <p class="text-xs text-red-600 mt-1">{{ $error ?? $errors->first($name) }}</p>
        @endif
    </div>
</div>
