<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Status Pesanan Transfer Manual - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .status-step {
            position: relative;
        }

        .status-step::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 40px;
            bottom: -20px;
            width: 2px;
            background: #e5e7eb;
        }

        .status-step:last-child::before {
            display: none;
        }

        .status-step.active::before {
            background: linear-gradient(to bottom, #10b981, #e5e7eb);
        }

        .status-step.completed::before {
            background: #10b981;
        }

        .upload-zone {
            border: 2px dashed #d1d5db;
            transition: all 0.3s ease;
        }

        .upload-zone:hover,
        .upload-zone.dragover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }

        .pulse-dot {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-2xl mx-auto px-4 py-4">
            <div class="flex items-center gap-3">
                @if($order->vendor && $order->vendor->logo)
                    <img src="{{ asset('vendors_logo/' . $order->vendor->logo) }}" alt="{{ $order->vendor->name }}"
                        class="w-10 h-10 rounded-full object-cover border">
                @else
                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($order->vendor->name ?? 'V', 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h1 class="font-semibold text-gray-900">{{ $order->vendor->name ?? 'Vendor' }}</h1>
                    <p class="text-sm text-gray-500">Pesanan Transfer Manual</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-4 py-6 space-y-6">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 fade-in">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-green-700 text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 fade-in">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <p class="text-red-700 text-sm">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Order Info Card -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden fade-in">
            <div class="px-6 py-4 border-b bg-gray-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Detail Pesanan</h2>
                        <p class="text-sm text-gray-500 mt-1">Nomor Pesanan: <span
                                class="font-mono font-medium text-gray-900">{{ $order->order_number }}</span></p>
                    </div>
                    @php
                        $statusConfig = match($order->status) {
                            'pending' => ['label' => 'Menunggu Pembayaran', 'color' => 'yellow', 'icon' => 'clock'],
                            'paid' => ['label' => 'Menunggu Konfirmasi', 'color' => 'blue', 'icon' => 'check-circle'],
                            'completed' => ['label' => 'Selesai', 'color' => 'green', 'icon' => 'check'],
                            'rejected' => ['label' => 'Ditolak', 'color' => 'red', 'icon' => 'x-circle'],
                            default => ['label' => ucfirst($order->status), 'color' => 'gray', 'icon' => 'question'],
                        };
                    @endphp
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium
                        {{ $statusConfig['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $statusConfig['color'] === 'blue' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $statusConfig['color'] === 'green' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $statusConfig['color'] === 'red' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $statusConfig['color'] === 'gray' ? 'bg-gray-100 text-gray-800' : '' }}">
                        @if($statusConfig['color'] === 'yellow')
                            <span class="w-2 h-2 rounded-full bg-yellow-500 pulse-dot"></span>
                        @endif
                        {{ $statusConfig['label'] }}
                    </span>
                </div>
            </div>

            <div class="px-6 py-4 space-y-4">
                <!-- Customer Info -->
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Nama</span>
                        <p class="font-medium text-gray-900">{{ $order->customer_name }}</p>
                    </div>
                    @if($order->customer_phone)
                        <div>
                            <span class="text-gray-500">Telepon</span>
                            <p class="font-medium text-gray-900">{{ $order->customer_phone }}</p>
                        </div>
                    @endif
                    @if($order->customer_email)
                        <div class="col-span-2">
                            <span class="text-gray-500">Email</span>
                            <p class="font-medium text-gray-900">{{ $order->customer_email }}</p>
                        </div>
                    @endif
                </div>

                <hr class="border-gray-100">

                <!-- Items -->
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Item Pesanan</h3>
                    <div class="bg-gray-50 rounded-lg divide-y">
                        @foreach($order->items ?? [] as $item)
                            <div class="px-4 py-3 flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $item['name'] ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ number_format($item['price'] ?? 0, 0, ',', '.') }} × {{ $item['quantity'] ?? 1 }}
                                    </p>
                                </div>
                                <p class="text-sm font-medium text-gray-900">
                                    Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex items-center justify-between mt-3 px-1">
                        <span class="text-sm font-semibold text-gray-900">Total</span>
                        <span class="text-lg font-bold text-blue-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($order->notes)
                    <div>
                        <span class="text-sm text-gray-500">Catatan</span>
                        <p class="text-sm text-gray-900 mt-1">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Bank Transfer Info (shown when status is pending) -->
        @if($order->status === 'pending')
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden fade-in">
                <div class="px-6 py-4 border-b bg-blue-50">
                    <h3 class="text-lg font-semibold text-blue-900">Informasi Transfer Bank</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    @if($order->bank_name)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Bank</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $order->bank_name }}</span>
                        </div>
                    @endif
                    @if($order->account_number)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Nomor Rekening</span>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-mono font-semibold text-gray-900" id="account-number">{{ $order->account_number }}</span>
                                <button onclick="copyToClipboard('{{ $order->account_number }}')" class="text-blue-500 hover:text-blue-700 text-xs">
                                    📋 Salin
                                </button>
                            </div>
                        </div>
                    @endif
                    @if($order->account_name)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Atas Nama</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $order->account_name }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between pt-2 border-t">
                        <span class="text-sm text-gray-500">Jumlah Transfer</span>
                        <span class="text-lg font-bold text-blue-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="px-6 pb-4">
                    <p class="text-xs text-gray-500 bg-gray-50 rounded-lg p-3">
                        ⚠️ Transfer sesuai nominal yang tertera untuk mempercepat proses verifikasi.
                    </p>
                </div>
            </div>

            <!-- Upload Proof Section -->
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden fade-in">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Upload Bukti Transfer</h3>
                    <p class="text-sm text-gray-500 mt-1">Upload bukti transfer setelah melakukan pembayaran</p>
                </div>
                <div class="px-6 py-4">
                    <form action="{{ route('manual-transfer.upload-proof', $order->order_number) }}" method="POST"
                        enctype="multipart/form-data" id="upload-form">
                        @csrf
                        <div class="upload-zone rounded-xl p-8 text-center cursor-pointer" id="drop-zone"
                            onclick="document.getElementById('file-input').click()">
                            <input type="file" name="transfer_proof" id="file-input" accept="image/*" class="hidden"
                                onchange="handleFileSelect(this)">
                            <div id="upload-placeholder">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">
                                    <span class="font-semibold text-blue-600">Klik untuk upload</span> atau seret gambar ke sini
                                </p>
                                <p class="mt-1 text-xs text-gray-500">PNG, JPG, JPEG (Maks. 5MB)</p>
                            </div>
                            <div id="file-preview" class="hidden">
                                <img id="preview-image" class="max-h-48 mx-auto rounded-lg shadow-sm mb-3">
                                <p id="file-name" class="text-sm text-gray-600"></p>
                                <button type="button" onclick="resetUpload()" class="mt-2 text-sm text-red-500 hover:text-red-700">
                                    Hapus & Pilih Lain
                                </button>
                            </div>
                        </div>
                        <button type="submit" id="submit-btn"
                            class="mt-4 w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                            Upload Bukti Transfer
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Rejection Info -->
        @if($order->status === 'rejected' && $order->rejection_reason)
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden fade-in">
                <div class="px-6 py-4 border-b bg-red-50">
                    <h3 class="text-lg font-semibold text-red-900">Pesanan Ditolak</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm text-gray-700">{{ $order->rejection_reason }}</p>
                    @if($order->paid_at)
                        <p class="text-xs text-gray-500 mt-2">Ditolak pada: {{ $order->paid_at->format('d M Y H:i') }}</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Proof Preview (when paid/completed) -->
        @if(in_array($order->status, ['paid', 'completed']) && $order->transfer_proof)
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden fade-in">
                <div class="px-6 py-4 border-b bg-green-50">
                    <h3 class="text-lg font-semibold text-green-900">Bukti Transfer</h3>
                </div>
                <div class="px-6 py-4">
                    <img src="{{ asset('storage/manual_transfer_proofs/' . $order->transfer_proof) }}"
                        alt="Bukti Transfer" class="max-w-full rounded-lg shadow-sm mx-auto" style="max-height: 400px;">
                    @if($order->paid_at)
                        <p class="text-xs text-gray-500 mt-2 text-center">
                            Diunggah pada: {{ $order->paid_at->format('d M Y H:i') }}
                        </p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Status Timeline -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden fade-in">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Status Pesanan</h3>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-0">
                    @php
                        $steps = [
                            'key' => 'pending',
                            'label' => 'Pesanan Dibuat',
                            'description' => 'Pesanan berhasil dibuat, menunggu pembayaran',
                            'time' => $order->created_at,
                        ];
                        $allSteps = [
                            ['key' => 'pending', 'label' => 'Pesanan Dibuat', 'description' => 'Pesanan berhasil dibuat, menunggu pembayaran', 'time' => $order->created_at],
                            ['key' => 'paid', 'label' => 'Pembayaran Dikonfirmasi', 'description' => 'Bukti transfer berhasil diunggah', 'time' => $order->paid_at],
                            ['key' => 'completed', 'label' => 'Pesanan Selesai', 'description' => 'Pesanan telah dikonfirmasi oleh vendor', 'time' => $order->updated_at],
                        ];

                        if ($order->status === 'rejected') {
                            $allSteps[] = ['key' => 'rejected', 'label' => 'Pesanan Ditolak', 'description' => $order->rejection_reason ?? 'Ditolak oleh vendor', 'time' => $order->updated_at];
                        }
                    @endphp

                    @foreach($allSteps as $index => $step)
                        @php
                            $statusOrder = ['pending' => 0, 'paid' => 1, 'completed' => 2, 'rejected' => 3];
                            $currentOrder = $statusOrder[$order->status] ?? 0;
                            $stepOrder = $statusOrder[$step['key']] ?? 0;

                            if ($order->status === 'rejected' && $step['key'] === 'rejected') {
                                $isCompleted = true;
                                $isActive = false;
                            } elseif ($step['key'] === 'rejected') {
                                $isCompleted = false;
                                $isActive = false;
                            } else {
                                $isCompleted = $stepOrder <= $currentOrder && $order->status !== 'rejected';
                                $isActive = $stepOrder === $currentOrder && $order->status !== 'rejected';
                            }
                        @endphp
                        @if($step['key'] !== 'rejected' || $order->status === 'rejected')
                            <div
                                class="status-step pb-6 {{ $isCompleted && !$isActive ? 'completed' : '' }} {{ $isActive ? 'active' : '' }}">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5
                                        {{ $isActive ? 'bg-blue-500 text-white' : '' }}
                                        {{ $isCompleted && !$isActive ? 'bg-green-500 text-white' : '' }}
                                        {{ !$isCompleted && !$isActive ? 'bg-gray-200 text-gray-400' : '' }}">
                                        @if($isCompleted && !$isActive)
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @elseif($isActive)
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            <span class="text-xs font-medium">{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p
                                            class="text-sm font-medium {{ $isActive ? 'text-blue-600' : '' }} {{ $isCompleted ? 'text-gray-900' : 'text-gray-400' }}">
                                            {{ $step['label'] }}</p>
                                        <p class="text-xs {{ $isCompleted ? 'text-gray-500' : 'text-gray-400' }}">
                                            {{ $step['description'] }}</p>
                                        @if($step['time'] && $isCompleted)
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ $step['time']->format('d M Y H:i') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center py-4">
            <p class="text-xs text-gray-400">Dikelola oleh {{ config('app.name') }}</p>
        </div>
    </div>

    <script>
        // CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // File Upload
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const uploadForm = document.getElementById('upload-form');
        const submitBtn = document.getElementById('submit-btn');

        // Drag and Drop
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                handleFileSelect(fileInput);
            }
        });

        function handleFileSelect(input) {
            const file = input.files[0];
            if (!file) return;

            // Validate file
            if (!file.type.match('image.*')) {
                alert('Hanya file gambar yang diperbolehkan');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file maksimal 5MB');
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('preview-image').src = e.target.result;
                document.getElementById('file-name').textContent = file.name;
                document.getElementById('upload-placeholder').classList.add('hidden');
                document.getElementById('file-preview').classList.remove('hidden');
                submitBtn.disabled = false;
            };
            reader.readAsDataURL(file);
        }

        function resetUpload() {
            fileInput.value = '';
            document.getElementById('upload-placeholder').classList.remove('hidden');
            document.getElementById('file-preview').classList.add('hidden');
            submitBtn.disabled = true;
        }

        // Upload form submission
        uploadForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            submitBtn.disabled = true;
            submitBtn.textContent = 'Mengupload...';

            const formData = new FormData(this);

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                const result = await response.json();

                if (result.success) {
                    // Reload page to show updated status
                    window.location.reload();
                } else {
                    alert(result.message || 'Gagal mengupload bukti transfer');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Upload Bukti Transfer';
                }
            } catch (error) {
                console.error('Upload error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Upload Bukti Transfer';
            }
        });

        // Copy to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const btn = event.target;
                const originalText = btn.textContent;
                btn.textContent = '✓ Tersalin';
                setTimeout(() => btn.textContent = originalText, 2000);
            });
        }

        // Auto-refresh every 30 seconds for status updates
        setInterval(() => {
            if (document.hidden) return;
            fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(response => response.text()).then(html => {
                // Only reload if status changed
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newStatus = doc.querySelector('.rounded-full.text-sm.font-medium');
                const currentStatus = document.querySelector('.rounded-full.text-sm.font-medium');
                if (newStatus && currentStatus && newStatus.textContent.trim() !== currentStatus.textContent.trim()) {
                    window.location.reload();
                }
            }).catch(() => { });
        }, 30000);
    </script>
</body>

</html>
