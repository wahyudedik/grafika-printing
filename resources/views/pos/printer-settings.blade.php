@extends('layouts.vendor')

@section('title', 'Pengaturan Printer')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">🖨️ Pengaturan Printer Thermal</h2>
                <div class="page-pretitle">Pengaturan cetak struk/invoice</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('vendor.pos.index') }}" class="btn btn-outline-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l14 0"/>
                        <path d="M5 12l6 6"/>
                        <path d="M5 12l6 -6"/>
                    </svg>
                    Kembali ke POS
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"/>
                            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"/>
                            <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"/>
                        </svg>
                        Konfigurasi Printer
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('vendor.pos.printer.settings.save') }}" method="POST" id="printerForm">
                        @csrf
                        @method('POST')

                        <!-- Paper Width -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ukuran Kertas</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paper_width" id="paper58" value="58mm" {{ $printerSettings->paper_width === '58mm' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="paper58">
                                        <strong>58mm</strong><br>
                                        <small class="text-muted">Kertas thermal kecil</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paper_width" id="paper80" value="80mm" {{ $printerSettings->paper_width === '80mm' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="paper80">
                                        <strong>80mm</strong><br>
                                        <small class="text-muted">Kertas thermal standar (default)</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Font Size -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ukuran Font</label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="range" name="font_size" id="fontSize" min="8" max="20" value="{{ $printerSettings->font_size }}" class="form-range" style="width: 200px;">
                                <span id="fontSizeValue" class="badge bg-primary">{{ $printerSettings->font_size }}px</span>
                            </div>
                            <small class="text-muted">Disarankan: 10-14px untuk kertas 80mm, 8-12px untuk 58mm</small>
                        </div>

                        <!-- Margin -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Margin</label>
                            <select name="margin" class="form-select" style="width: 200px;">
                                <option value="0mm" {{ $printerSettings->margin === '0mm' ? 'selected' : '' }}>Tanpa Margin (0mm)</option>
                                <option value="1mm" {{ $printerSettings->margin === '1mm' ? 'selected' : '' }}>Sedikit (1mm)</option>
                                <option value="2mm" {{ $printerSettings->margin === '2mm' ? 'selected' : '' }}>Normal (2mm)</option>
                                <option value="3mm" {{ $printerSettings->margin === '3mm' ? 'selected' : '' }}>Lebar (3mm)</option>
                                <option value="5mm" {{ $printerSettings->margin === '5mm' ? 'selected' : '' }}>Sangat Lebar (5mm)</option>
                            </select>
                        </div>

                        <hr>

                        <!-- Auto Options -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Otomatis</label>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="auto_print" id="autoPrint" value="1" {{ $printerSettings->auto_print ? 'checked' : '' }}>
                                <label class="form-check-label" for="autoPrint">
                                    Auto-print saat halaman dimuat
                                </label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="auto_close_window" id="autoClose" value="1" {{ $printerSettings->auto_close_window ? 'checked' : '' }}>
                                <label class="form-check-label" for="autoClose">
                                    Tutup jendela setelah cetak
                                </label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="auto_cut" id="autoCut" value="1" {{ $printerSettings->auto_cut ? 'checked' : '' }}>
                                <label class="form-check-label" for="autoCut">
                                    Auto-cut kertas (ESC/POS command)
                                </label>
                            </div>
                        </div>

                        <!-- Print Delay -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jeda Cetak (ms)</label>
                            <input type="number" name="print_delay" class="form-control" value="{{ $printerSettings->print_delay }}" min="0" max="5000" style="width: 200px;">
                            <small class="text-muted">Delay sebelum print dimulai (ms). Default: 500ms</small>
                        </div>

                        <!-- Printer Name -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Printer (Opsional)</label>
                            <input type="text" name="printer_name" class="form-control" value="{{ $printerSettings->printer_name ?? '' }}" placeholder="Contoh: EPSON TM-T82" style="width: 300px;">
                            <small class="text-muted">Catatan referensi untuk mengenali printer ini</small>
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M6 20l-2 -1l-3 -2v-4l3 -2l2 -1"/>
                                    <path d="M9 14l6 -6"/>
                                    <path d="M8 6l12 0l0 12l-12 0z"/>
                                </svg>
                                Simpan Pengaturan
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetDefaults()">
                                Reset Default
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview & Test -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                        </svg>
                        Preview & Test
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Preview -->
                    <div id="preview" style="border: 1px solid #ddd; padding: 10px; font-family: 'Courier New', monospace; background: white; margin-bottom: 15px;">
                        <div style="text-align: center; margin-bottom: 8px;">
                            <strong style="font-size: 12px;">TOKO GRAFIKA</strong><br>
                            <small style="font-size: 8px;">Jl. Contoh No. 123<br>081-515-876-755</small>
                        </div>
                        <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
                        <div style="font-size: 8px;">
                            <p>No: INV-2025-001</p>
                            <p>Tgl: 04/08/2026 10:30</p>
                        </div>
                        <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
                        <div style="font-size: 8px;">
                            <p><strong>Spanduk Roll</strong></p>
                            <p>Qty: 2 pcs</p>
                            <div style="display: flex; justify-content: space-between; margin-top: 3px;">
                                <span>Subtotal:</span>
                                <span>Rp 100.000</span>
                            </div>
                        </div>
                        <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
                        <div style="font-size: 9px;">
                            <div style="display: flex; justify-content: space-between; font-weight: bold;">
                                <span>TOTAL:</span>
                                <span>Rp 100.000</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Dibayar:</span>
                                <span>Rp 100.000</span>
                            </div>
                        </div>
                        <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
                        <div style="text-align: center; font-size: 7px; margin-top: 5px;">
                            Terima kasih!
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-success" onclick="testWebUSB()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-usb me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 2l0 6"/>
                                <path d="M12 8l4 4l-4 4"/>
                                <path d="M12 16l0 6"/>
                            </svg>
                            Test Koneksi WebUSB
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"/>
                                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"/>
                                <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"/>
                            </svg>
                            Test Print (Browser)
                        </button>
                    </div>

                    <!-- WebUSB Status -->
                    <div id="webusb-status" class="mt-3" style="display: none;">
                        <div class="alert alert-info mb-0">
                            <small id="webusb-message"></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">💡 Tips</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0" style="font-size: 13px;">
                        <li class="mb-2">
                            <strong>58mm:</strong> Gunakan font size 8-10px
                        </li>
                        <li class="mb-2">
                            <strong>80mm:</strong> Gunakan font size 10-14px
                        </li>
                        <li class="mb-2">
                            <strong>WebUSB:</strong> Hanya tersedia di Chrome/Edge
                        </li>
                        <li class="mb-2">
                            <strong>Auto-print:</strong> Matikan jika ingin preview dulu
                        </li>
                        <li class="mb-0">
                            <strong>Margin:</strong> Set 0mm untuk thermal printer
                        </li>
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
        const statusDiv = document.getElementById('webusb-status');
        const messageEl = document.getElementById('webusb-message');
        statusDiv.style.display = 'block';

        if ('usb' in navigator) {
            try {
                messageEl.textContent = 'Mencari printer USB...';
                messageEl.className = 'text-info';

                const device = await navigator.usb.requestDevice({ filters: [] });
                await device.open();

                messageEl.innerHTML = `✅ Printer ditemukan: <strong>${device.productName || 'USB Device'}</strong><br>Vendor ID: ${device.vendorId}, Product ID: ${device.productId}`;
                messageEl.className = 'text-success';

                await device.close();
            } catch (error) {
                if (error.name === 'NotFoundError') {
                    messageEl.innerHTML = '⚠️ Tidak ada printer yang dipilih. User membatalkan pemilihan.';
                    messageEl.className = 'text-warning';
                } else {
                    messageEl.innerHTML = `❌ Error: ${error.message}`;
                    messageEl.className = 'text-danger';
                }
            }
        } else {
            messageEl.innerHTML = '❌ WebUSB tidak didukung di browser ini. Gunakan Chrome atau Edge.';
            messageEl.className = 'text-danger';
        }
    }
</script>
@endsection
