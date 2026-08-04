<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\Vendor\Linktree;
use App\Models\Vendor\LinktreeLink;
use App\Models\Vendor\LinktreeSocial;
use Illuminate\Database\Seeder;

class LinktreeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding Linktree data...');

        $vendor = Vendor::first();
        if (!$vendor) {
            $this->command->warn('⚠️ No vendor found. Skipping linktree seeding.');
            return;
        }

        // Create Linktree for vendor
        $linktree = Linktree::firstOrCreate(
            ['custom_url' => 'grafika-printing'],
            [
                'vendor_id' => $vendor->id,
                'title' => 'Grafika Printing',
                'bio' => 'Solusi percetakan terlengkap & terpercaya. 🖨️ Nota | Banner | Kartu Nama | Stiker',
                'template' => 'professional',
                'primary_color' => '#6366f1',
                'secondary_color' => '#ec4899',
                'bg_color' => '#f8fafc',
                'text_color' => '#1e293b',
                'button_style' => 'rounded',
                'is_active' => true,
                'show_qris' => true,
                'meta_title' => 'Grafika Printing - Percetakan Online',
                'meta_description' => 'Solusi percetakan terlengkap untuk bisnis Anda',
                'views_count' => 245,
                'clicks_count' => 89,
            ]
        );

        // Create Links
        $links = [
            [
                'title' => '🛒 Toko Online Kami',
                'url' => 'https://grafika.noteds.com',
                'type' => 'link',
                'is_active' => true,
                'sort_order' => 0,
                'clicks_count' => 34,
            ],
            [
                'title' => '💬 Chat WhatsApp',
                'url' => 'https://wa.me/6281234567890',
                'type' => 'whatsapp',
                'is_active' => true,
                'sort_order' => 1,
                'clicks_count' => 28,
            ],
            [
                'title' => '📋 Katalog Produk',
                'url' => 'https://grafika.noteds.com/katalog',
                'type' => 'link',
                'is_active' => true,
                'sort_order' => 2,
                'clicks_count' => 15,
            ],
            [
                'title' => '📞 Hubungi Kami',
                'url' => 'tel:+6281234567890',
                'type' => 'phone',
                'is_active' => true,
                'sort_order' => 3,
                'clicks_count' => 8,
            ],
            [
                'title' => '✉️ Email Kami',
                'url' => 'mailto:info@grafikaprinting.com',
                'type' => 'email',
                'is_active' => true,
                'sort_order' => 4,
                'clicks_count' => 4,
            ],
        ];

        foreach ($links as $linkData) {
            LinktreeLink::firstOrCreate(
                [
                    'linktree_id' => $linktree->id,
                    'title' => $linkData['title'],
                ],
                array_merge($linkData, ['vendor_id' => $vendor->id])
            );
        }

        // Create Social Links
        $socials = [
            [
                'platform' => 'instagram',
                'url' => 'https://instagram.com/grafikaprinting',
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'platform' => 'facebook',
                'url' => 'https://facebook.com/grafikaprinting',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'platform' => 'whatsapp',
                'url' => 'https://wa.me/6281234567890',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'platform' => 'youtube',
                'url' => 'https://youtube.com/@grafikaprinting',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($socials as $socialData) {
            LinktreeSocial::firstOrCreate(
                [
                    'linktree_id' => $linktree->id,
                    'platform' => $socialData['platform'],
                ],
                array_merge($socialData, ['vendor_id' => $vendor->id])
            );
        }

        $this->command->info("✅ Created linktree: {$linktree->title} (/l/{$linktree->custom_url})");
        $this->command->info("   ├── " . $linktree->links()->count() . " links");
        $this->command->info("   └── " . $linktree->socials()->count() . " social links");
    }
}
