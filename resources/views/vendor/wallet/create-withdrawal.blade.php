@extends('layouts.vendor')

@section('title', 'Tarik Dana')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Permintaan Penarikan Dana</h3>
            <p class="text-sm text-gray-500 mt-1">Ajukan penarikan dana dari saldo wallet Anda</p>
        </div>
        <div class="p-5">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                    <div class="flex items-center gap-2 text-green-800"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
                    <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between">
                    <div class="flex items-center gap-2 text-red-800"><i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span></div>
                    <button @click="show = false" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
                </div>
            @endif

            {{-- Wallet Info --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-50 rounded-xl p-5">
                    <h6 class="text-sm font-semibold text-gray-700">Saldo Tersedia</h6>
                    <div class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($wallet->available_balance) }}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-5">
                    <h6 class="text-sm font-semibold text-gray-700">Total Pendapatan</h6>
                    <div class="text-2xl font-bold text-primary-600 mt-1">Rp {{ number_format($wallet->total_earned) }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('vendor.wallet.store-withdrawal') }}" data-loading>
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Penarikan <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Rp</span>
                                <input type="number" class="flex-1 rounded-r-lg border {{ $errors->has('amount') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" id="amount" name="amount" value="{{ old('amount') }}" min="10000" max="{{ $wallet->available_balance }}" step="1000" required>
                            </div>
                            @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1">Minimum: Rp 10,000 | Maksimum: Rp {{ number_format($wallet->available_balance) }}</p>
                        </div>

                        <div>
                            <label for="method" class="block text-sm font-medium text-gray-700 mb-1">Metode Penarikan <span class="text-red-500">*</span></label>
                            <select class="w-full rounded-lg border {{ $errors->has('method') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" id="method" name="method" required>
                                <option value="">Pilih Metode</option>
                                <option value="bank_transfer" {{ old('method') === 'bank_transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="e_wallet" {{ old('method') === 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                                <option value="cash" {{ old('method') === 'cash' ? 'selected' : '' }}>Tunai</option>
                            </select>
                            @error('method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening/E-Wallet <span class="text-red-500">*</span></label>
                            <input type="text" class="w-full rounded-lg border {{ $errors->has('account_number') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" id="account_number" name="account_number" value="{{ old('account_number') }}" placeholder="Masukkan nomor rekening atau e-wallet" required>
                            @error('account_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="account_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik <span class="text-red-500">*</span></label>
                            <input type="text" class="w-full rounded-lg border {{ $errors->has('account_name') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" id="account_name" name="account_name" value="{{ old('account_name') }}" placeholder="Nama pemilik rekening/e-wallet" required>
                            @error('account_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div id="bank_name_field" style="display: none;">
                            <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                            <input type="text" class="w-full rounded-lg border {{ $errors->has('bank_name') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" id="bank_name" name="bank_name" value="{{ old('bank_name') }}" placeholder="Nama bank">
                            @error('bank_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea class="w-full rounded-lg border {{ $errors->has('notes') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" id="notes" name="notes" rows="4" placeholder="Catatan tambahan untuk penarikan">{{ old('notes') }}</textarea>
                            @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Fee Information --}}
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h6 class="text-sm font-semibold text-gray-700 mb-2">Informasi Biaya</h6>
                            <div class="text-sm space-y-1">
                                <div class="flex justify-between"><span class="text-gray-600">Transfer Bank:</span><span>1% (min Rp 5,000)</span></div>
                                <div class="flex justify-between"><span class="text-gray-600">E-Wallet:</span><span>0.5% (min Rp 3,000)</span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Tunai:</span><span>Gratis</span></div>
                            </div>
                        </div>

                        {{-- Preview --}}
                        <div class="bg-white border border-gray-200 rounded-xl p-4" id="preview_card" style="display: none;">
                            <h6 class="text-sm font-semibold text-gray-700 mb-2">Preview Penarikan</h6>
                            <div class="text-sm space-y-1">
                                <div class="flex justify-between"><span class="text-gray-600">Jumlah:</span><span id="preview_amount">Rp 0</span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Biaya:</span><span id="preview_fee">Rp 0</span></div>
                                <hr class="my-2 border-gray-200">
                                <div class="flex justify-between font-bold"><span>Diterima:</span><span id="preview_net">Rp 0</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <x.ui.button href="{{ route('vendor.wallet.index') }}" variant="outline">Batal</x.ui.button>
                    <x.ui.button type="submit" variant="primary">Ajukan Penarikan</x.ui.button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const methodSelect = document.getElementById('method');
        const bankNameField = document.getElementById('bank_name_field');
        const previewCard = document.getElementById('preview_card');
        const amountInput = document.getElementById('amount');

        methodSelect.addEventListener('change', function() {
            bankNameField.style.display = this.value === 'bank_transfer' ? 'block' : 'none';
            updatePreview();
        });

        amountInput.addEventListener('input', updatePreview);

        function updatePreview() {
            const amount = parseFloat(amountInput.value) || 0;
            const method = methodSelect.value;

            if (amount > 0 && method) {
                let fee = 0;
                switch (method) {
                    case 'bank_transfer': fee = Math.min(5000, amount * 0.01); break;
                    case 'e_wallet': fee = Math.min(3000, amount * 0.005); break;
                    case 'cash': fee = 0; break;
                }
                const netAmount = amount - fee;
                document.getElementById('preview_amount').textContent = 'Rp ' + amount.toLocaleString();
                document.getElementById('preview_fee').textContent = 'Rp ' + fee.toLocaleString();
                document.getElementById('preview_net').textContent = 'Rp ' + netAmount.toLocaleString();
                previewCard.style.display = 'block';
            } else {
                previewCard.style.display = 'none';
            }
        }
    });
</script>
@endsection
