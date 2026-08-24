@extends('layouts.pos')

@section('title', 'Pengaturan Printer')

@section('content')
<div class="px-4 py-4">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">
                <i class="fas fa-print mr-2 text-primary"></i>Pengaturan Printer Thermal
            </h2>
            <p class="text-sm text-gray-500 mt-1">Pengaturan cetak struk/invoice</p>
        </div>
        <div class="mt-3 sm:mt-0">
            <a href="{{ route('vendor.pos.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke POS
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Config --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-800">
                        <i class="fas fa-cog mr-2 text-gray-500"></i>Konfigurasi Printer
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('vendor.pos.printer.settings.save') }}" method="POST" id="printerForm">
                        @csrf

                        {{-- Paper Width --}}
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Ukuran Kertas</label>
                            <div class="flex gap-4">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="paper_width" value="58mm"
                                        class="peer sr-only" {{ $printerSettings->paper_width === '58mm' ? 'checked' : '' }}>
                                    <div class="border-2 border-gray-200 peer-checked:border-primary peer-checked:bg-primary/5 rounded-lg p-4 text-center transition-all hover:border-gray-300 min-w-[140px]">
                                        <strong class="block text-gray-800">58mm</strong>
                                        <small class="text-xs text-gray-500">Kertas thermal kecil</small>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="paper_width" value="80mm"
                                        class="peer sr-only" {{ $printerSettings->paper_width === '80mm' ? 'checked' : '' }}>
                                    <div class="border-2 border-gray-200 peer-checked:border-primary peer-checked:bg-primary/5 rounded-lg p-4 text-center transition-all hover:border-gray-300 min-w-[140px]">
                                        <strong class="block text-gray-800">80mm</strong>
                                        <small class="text-xs text-gray-500">Kertas thermal standar (default)</small>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Font Size --}}
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Ukuran Font</label>
                            <div class="flex items-center gap-3">
                                <input type="range" name="font_size" id="fontSize" min="8" max="20"
                                    value="{{ $printerSettings->font_size }}"
                                    class="w-48 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary">
                                <span id="fontSizeValue" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary text-white">{{ $printerSettings->font_size }}px</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Disarankan: 10-14px untuk kertas 80mm, 8-12px untuk 58mm</p>
                        </div>

                        {{-- Margin --}}
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Margin</label>
                            <select name="margin"
                                class="w-48 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                                <option value="0mm" {{ $printerSettings->margin === '0mm' ? 'selected' : '' }}>Tanpa Margin (0mm)</option>
                                <option value="1mm" {{ $printerSettings->margin === '1mm' ? 'selected' : '' }}>Sedikit (1mm)</option>
                                <option value="2mm" {{ $printerSettings->margin === '2mm' ? 'selected' : '' }}>Normal (2mm)</option>
                                <option value="3mm" {{ $printerSettings->margin === '3mm' ? 'selected' : '' }}>Lebar (3mm)</option>
                                <option value="5mm" {{ $printerSettings->margin === '5mm' ? 'selected' : '' }}>Sangat Lebar (5mm)</option>
                            </select>
                        </div>

                        <hr class="my-6 border-gray-200">

                        {{-- Auto Options --}}
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Otomatis</label>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="auto_print" id="autoPrint" value="1"
                                        class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary"
                                        {{ $printerSettings->auto_print ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-700">Auto-print saat halaman dimuat</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="auto_close_window" id="autoClose" value="1"
                                        class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary"
                                        {{ $printerSettings->auto_close_window ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-700">Tutup jendela setelah cetak</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="auto_cut" id="autoCut" value="1"
                                        class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary"
                                        {{ $printerSettings->auto_cut ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-700">Auto-cut kertas (ESC/POS command)</span>
                                </label>
                            </div>
                        </div>

                        {{-- Print Delay --}}
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jeda Cetak (ms)</label>
                            <input type="number" name="print_delay"
                                class="w-48 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                                value="{{ $printerSettings->print_delay }}" min="0" max="5000">
                            <p class="text-xs text-gray-500 mt-1">Delay sebelum print dimulai (ms). Default: 500ms</p>
                        </div>

                        {{-- Printer Name --}}
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Printer (Opsional)</label>
                            <input type="text" name="printer_name"
                                class="w-72 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                                value="{{ $printerSettings->printer_name ?? '' }}" placeholder="Contoh: EPSON TM-T82">
                            <p class="text-xs text-gray-500 mt-1">Catatan referensi untuk mengenali printer ini</p>
                        </div>

                        <hr class="my-6 border-gray-200">

                        {{-- Action Buttons --}}
                        <div class="flex gap-3">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
                                <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                            </button>
                            <button type="button"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors"
                                onclick="resetDefaults()">
                                <i class="fas fa-undo mr-2"></i>Reset Default
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Preview & Test --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-800">
                        <i class="fas fa-eye mr-2 text-gray-500"></i>Preview & Test
                    </h3>
                </div>
                <div class="p-6">
                    {{-- Preview --}}
                    <div id="preview"
                        class="border border-gray-300 rounded-lg p-3 font-mono bg-white mb-4 text-xs">
                        <div class="text-center mb-2">
                            <strong style="font-size: 12px;">TOKO GRAFIKA</strong><br>
                            <span class="text-gray-500" style="font-size: 8px;">Jl. Contoh No. 123<br>081-515-876-755</span>
                        </div>
                        <div class="border-t border-dashed border-black my-1"></div>
                        <div style="font-size: 8px;">
                            <p>No: INV-2025-001</p>
                            <p>Tgl: 04/08/2026 10:30</p>
                        </div>
                        <div class="border-t border-dashed border-black my-1"></div>
                        <div style="font-size: 8px;">
                            <p><strong>Spanduk Roll</strong></p>
                            <p>Qty: 2 pcs</p>
                            <div class="flex justify-between mt-1">
                                <span>Subtotal:</span>
                                <span>Rp 100.000</span>
                            </div>
                        </div>
                        <div class="border-t border-dashed border-black my-1"></div>
                        <div style="font-size: 9px;">
                            <div class="flex justify-between font-bold">
                                <span>TOTAL:</span>
                                <span>Rp 100.000</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Dibayar:</span>
                                <span>Rp 100.000</span>
                            </div>
                        </div>
                        <div class="border-t border-dashed border-black my-1"></div>
                        <div class="text-center mt-1" style="font-size: 7px;">
                            Terima kasih!
                        </div>
                    </div>

                    <div class="space-y-2">
                        <button type="button"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-green-500 text-green-600 rounded-lg text-sm font-medium hover:bg-green-50 transition-colors"
                            onclick="testWebUSB()">
                            <i class="fas fa-usb mr-2"></i>Test Koneksi WebUSB
                        </button>
                        <button type="button"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-primary text-primary rounded-lg text-sm font-medium hover:bg-primary/5 transition-colors"
                            onclick="window.print()">
                            <i class="fas fa-print mr-2"></i>Test Print (Browser)
                        </button>
                    </div>

                    {{-- WebUSB Status --}}
                    <div id="webusb-status" class="mt-3" x-data="{ webusbVisible: false, webusbMessage: '', webusbColor: 'text-blue-700' }" x-show="webusbVisible" x-cloak>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm">
                            <span :class="webusbColor" x-html="webusbMessage"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tips --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-800">
                        <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>Tips
                    </h3>
                </div>
                <div class="p-6">
                    <ul class="space-y-2 text-xs">
                        <li><strong>58mm:</strong> Gunakan font size 8-10px</li>
                        <li><strong>80mm:</strong> Gunakan font size 10-14px</li>
                        <li><strong>WebUSB:</strong> Hanya tersedia di Chrome/Edge</li>
                        <li><strong>Auto-print:</strong> Matikan jika ingin preview dulu</li>
                        <li><strong>Margin:</strong> Set 0mm untuk thermal printer</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Font size slider
    const fontSizeSlider = document.getElementById('fontSize');
    const fontSizeValue = document.getElementById('fontSizeValue');
    const preview = document.getElementById('preview');

    fontSizeSlider.addEventListener('input', function() {
        fontSizeValue.textContent = this.value + 'px';
        preview.style.fontSize = this.value + 'px';
    });

    // Paper width change
    document.querySelectorAll('input[name="paper_width"]').forEach(radio => {
        radio.addEventListener('change', function() {
            preview.style.width = this.value;
        });
    });

    // Reset defaults
    function resetDefaults() {
        document.querySelector('input[name="paper_width"][value="80mm"]').checked = true;
        document.getElementById('fontSize').value = 12;
        document.getElementById('fontSizeValue').textContent = '12px';
        document.querySelector('select[name="margin"]').value = '0mm';
        document.getElementById('autoPrint').checked = true;
        document.getElementById('autoClose').checked = true;
        document.getElementById('autoCut').checked = true;
        document.querySelector('input[name="print_delay"]').value = 500;
        document.querySelector('input[name="printer_name"]').value = '';
    }

    // Test WebUSB
    async function testWebUSB() {
        const statusEl = document.getElementById('webusb-status');
        const webusbData = statusEl._x_dataStack ? statusEl._x_dataStack[0] : null;

        if (webusbData) {
            webusbData.webusbVisible = true;
            webusbData.webusbMessage = 'Mencari printer USB...';
            webusbData.webusbColor = 'text-blue-700';
        }

        if ('usb' in navigator) {
            try {
                const device = await navigator.usb.requestDevice({ filters: [] });
                await device.open();

                if (webusbData) {
                    webusbData.webusbMessage = `<i class="fas fa-check-circle text-green-600 mr-1"></i> Printer ditemukan: <strong>${device.productName || 'USB Device'}</strong><br>Vendor ID: ${device.vendorId}, Product ID: ${device.productId}`;
                    webusbData.webusbColor = 'text-green-700';
                }

                await device.close();
            } catch (error) {
                if (webusbData) {
                    if (error.name === 'NotFoundError') {
                        webusbData.webusbMessage = '<i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i> Tidak ada printer yang dipilih. User membatalkan pemilihan.';
                        webusbData.webusbColor = 'text-yellow-700';
                    } else {
                        webusbData.webusbMessage = `<i class="fas fa-times-circle text-red-500 mr-1"></i> Error: ${error.message}`;
                        webusbData.webusbColor = 'text-red-700';
                    }
                }
            }
        } else {
            if (webusbData) {
                webusbData.webusbMessage = '<i class="fas fa-times-circle text-red-500 mr-1"></i> WebUSB tidak didukung di browser ini. Gunakan Chrome atau Edge.';
                webusbData.webusbColor = 'text-red-700';
            }
        }
    }
</script>
@endsection
