@props([
    'id' => 'confirm-dialog',
    'title' => 'Konfirmasi',
    'message' => 'Apakah Anda yakin ingin melanjutkan?',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'variant' => 'danger', // danger, warning, info
    'confirmUrl' => null,
    'confirmMethod' => 'DELETE',
    'icon' => null,
])

@php
$variantClasses = match($variant) {
    'danger' => 'bg-red-50 text-red-600',
    'warning' => 'bg-amber-50 text-amber-600',
    'info' => 'bg-blue-50 text-blue-600',
    default => 'bg-red-50 text-red-600',
};

$confirmBtnClass = match($variant) {
    'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
    'warning' => 'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500',
    'info' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
    default => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
};

$defaultIcon = match($variant) {
    'danger' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>',
    'warning' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>',
    'info' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    default => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>',
};
@endphp

<div
    x-data="{
        open: false,
        confirmUrl: '{{ $confirmUrl }}',
        confirmMethod: '{{ $confirmMethod }}',
        handleConfirm() {
            if (this.confirmUrl) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = this.confirmUrl;

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_token';
                methodInput.value = '{{ csrf_token() }}';
                form.appendChild(methodInput);

                if (this.confirmMethod !== 'GET') {
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = this.confirmMethod;
                    form.appendChild(methodField);
                }

                document.body.appendChild(form);
                form.submit();
            }
            $dispatch('confirm-action');
            open = false;
        }
    }"
    @open-confirmation.window="open = true; confirmUrl = $event.detail.url || '{{ $confirmUrl }}'; confirmMethod = $event.detail.method || '{{ $confirmMethod }}'"
    id="{{ $id }}"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm"
        @click="open = false"
    ></div>

    {{-- Dialog --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden" @click.away="open = false">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center {{ $variantClasses }}">
                        {!! $icon ?? $defaultIcon !!}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ $message }}</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-100">
                <button
                    type="button"
                    @click="open = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition-colors"
                >
                    {{ $cancelText }}
                </button>
                <button
                    type="button"
                    @click="handleConfirm()"
                    class="px-4 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{ $confirmBtnClass }}"
                >
                    {{ $confirmText }}
                </button>
            </div>
        </div>
    </div>
</div>
