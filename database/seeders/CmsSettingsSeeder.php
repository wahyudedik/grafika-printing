<?php

namespace Database\Seeders;

use App\Models\CmsSetting;
use Illuminate\Database\Seeder;

class CmsSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding CMS Settings...');

        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'Grafika Printing', 'type' => 'text', 'category' => 'general', 'label' => 'Nama Situs', 'description' => 'Nama website yang ditampilkan', 'is_active' => true, 'sort_order' => 0],
            ['key' => 'site_tagline', 'value' => 'Smart Printing Management System', 'type' => 'text', 'category' => 'general', 'label' => 'Tagline Situs', 'description' => 'Tagline website', 'is_active' => true, 'sort_order' => 1],

            // Hero
            ['key' => 'hero_title', 'value' => 'NOTA ONLINE SHOP', 'type' => 'text', 'category' => 'hero', 'label' => 'Judul Hero', 'description' => 'Judul utama di halaman depan', 'is_active' => true, 'sort_order' => 0],
            ['key' => 'hero_subtitle', 'value' => 'Bahan HVS. NCR Sudah termasuk porforasi & potong', 'type' => 'text', 'category' => 'hero', 'label' => 'Subtitle Hero', 'description' => 'Subtitle di halaman depan', 'is_active' => true, 'sort_order' => 1],

            // Contact
            ['key' => 'contact_phone', 'value' => '081515876755', 'type' => 'phone', 'category' => 'contact', 'label' => 'Telepon', 'description' => 'Nomor telepon kontak', 'is_active' => true, 'sort_order' => 0],
            ['key' => 'contact_email', 'value' => 'info@grafikaprinting.com', 'type' => 'email', 'category' => 'contact', 'label' => 'Email', 'description' => 'Email kontak', 'is_active' => true, 'sort_order' => 1],
            ['key' => 'contact_address', 'value' => 'Pesantren Peterongan Jombang', 'type' => 'text', 'category' => 'contact', 'label' => 'Alamat', 'description' => 'Alamat kantor', 'is_active' => true, 'sort_order' => 2],
            ['key' => 'contact_hours', 'value' => "Senin - Jum'at : 09:00 - 17:00 WIB, Sabtu - Minggu : 09:00 - 15:00 WIB", 'type' => 'text', 'category' => 'contact', 'label' => 'Jam Pelayanan', 'description' => 'Jam operasional', 'is_active' => true, 'sort_order' => 3],

            // Social Media
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/grafikaprinting', 'type' => 'social', 'category' => 'social', 'label' => 'Instagram', 'description' => 'URL Instagram', 'is_active' => true, 'sort_order' => 0],
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/grafikaprinting', 'type' => 'social', 'category' => 'social', 'label' => 'Facebook', 'description' => 'URL Facebook', 'is_active' => true, 'sort_order' => 1],
            ['key' => 'social_twitter', 'value' => 'https://twitter.com/grafikaprinting', 'type' => 'social', 'category' => 'social', 'label' => 'Twitter', 'description' => 'URL Twitter', 'is_active' => true, 'sort_order' => 2],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com/@grafikaprinting', 'type' => 'social', 'category' => 'social', 'label' => 'YouTube', 'description' => 'URL YouTube', 'is_active' => true, 'sort_order' => 3],
            ['key' => 'social_linkedin', 'value' => 'https://linkedin.com/company/grafikaprinting', 'type' => 'social', 'category' => 'social', 'label' => 'LinkedIn', 'description' => 'URL LinkedIn', 'is_active' => true, 'sort_order' => 4],
            ['key' => 'social_tiktok', 'value' => 'https://tiktok.com/@grafikaprinting', 'type' => 'social', 'category' => 'social', 'label' => 'TikTok', 'description' => 'URL TikTok', 'is_active' => true, 'sort_order' => 5],
            ['key' => 'social_whatsapp', 'value' => '6281515876755', 'type' => 'phone', 'category' => 'social', 'label' => 'WhatsApp', 'description' => 'Nomor WhatsApp (tanpa +)', 'is_active' => true, 'sort_order' => 6],

            // Footer
            ['key' => 'footer_about', 'value' => '#', 'type' => 'url', 'category' => 'footer', 'label' => 'Link Tentang', 'description' => 'URL halaman tentang', 'is_active' => true, 'sort_order' => 0],
            ['key' => 'footer_terms', 'value' => '#', 'type' => 'url', 'category' => 'footer', 'label' => 'Link Syarat', 'description' => 'URL syarat penggunaan', 'is_active' => true, 'sort_order' => 1],
            ['key' => 'footer_privacy', 'value' => '#', 'type' => 'url', 'category' => 'footer', 'label' => 'Link Privasi', 'description' => 'URL kebijakan privasi', 'is_active' => true, 'sort_order' => 2],
            ['key' => 'footer_copyright', 'value' => '©2025 Grafika Printing. Hak Cipta Terpelihara CV. Grafika Digital Solution', 'type' => 'text', 'category' => 'footer', 'label' => 'Copyright', 'description' => 'Teks copyright di footer', 'is_active' => true, 'sort_order' => 3],
        ];

        foreach ($settings as $setting) {
            CmsSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info("✅ Created " . count($settings) . " CMS settings");
    }
}
