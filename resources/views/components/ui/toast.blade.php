@props([
    'position' => 'top-right', // top-right, top-left, bottom-right, bottom-left, top-center, bottom-center
    'duration' => 5000,
])

@php
$positionClasses = match($position) {
    'top-right' => 'top-4 right-4',
    'top-left' => 'top-4 left-4',
    'bottom-right' => 'bottom-4 right-4',
    'bottom-left' => 'bottom-4 left-4',
    'top-center' => 'top-4 left-1/2 -translate-x-1/2',
    'bottom-center' => 'bottom-4 left-1/2 -translate-x-1/2',
    default => 'top-4 right-4',
};
@endphp

<div
    x-data="toastManager()"
    x-on:show-toast.window="addToast($event.detail)"
    class="fixed {{ $positionClasses }} z-[9999] flex flex-col gap-3 pointer-events-none"
    style="max-width: 420px;"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            class="pointer-events-auto bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden"
        >
            <div class="flex items-start gap-3 p-4">
                {{-- Icon --}}
                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center" :class="getToastIconClass(toast.type)">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p x-show="toast.title" class="text-sm font-semibold text-gray-900" x-text="toast.title"></p>
                    <p class="text-sm text-gray-600" x-text="toast.message"></p>
                </div>

                {{-- Close Button --}}
                <button
                    @click="removeToast(toast.id)"
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Progress Bar --}}
            <div class="h-1 bg-gray-100">
                <div
                    class="h-full transition-all ease-linear"
                    :class="getProgressBarClass(toast.type)"
                    :style="`width: ${toast.progress}%`"
                ></div>
            </div>
        </div>
    </template>
</div>

<script>
function toastManager() {
    return {
        toasts: [],
        counter: 0,
        addToast(detail) {
            const id = ++this.counter;
            const toast = {
                id,
                type: detail.type || 'info',
                title: detail.title || '',
                message: detail.message || '',
                show: true,
                progress: 100,
            };
            this.toasts.push(toast);

            const duration = detail.duration || {{ $duration }};
            const interval = duration / 100;
            const timer = setInterval(() => {
                toast.progress -= 1;
                if (toast.progress <= 0) {
                    clearInterval(timer);
                    this.removeToast(id);
                }
            }, interval);
        },
        removeToast(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index > -1) {
                this.toasts[index].show = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        },
        getToastIconClass(type) {
            return {
                'success': 'bg-green-100',
                'error': 'bg-red-100',
                'warning': 'bg-amber-100',
                'info': 'bg-blue-100',
            }[type] || 'bg-gray-100';
        },
        getProgressBarClass(type) {
            return {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'warning': 'bg-amber-500',
                'info': 'bg-blue-500',
            }[type] || 'bg-gray-500';
        },
    };
}
</script>
