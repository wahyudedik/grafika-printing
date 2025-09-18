<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CmsSetting;

class CmsSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'Grafika Printing',
                'type' => 'text',
                'category' => 'general',
                'label' => 'Site Name',
                'description' => 'The name of your website',
                'sort_order' => 1
            ],
            [
                'key' => 'site_tagline',
                'value' => 'Smart Printing Management System',
                'type' => 'text',
                'category' => 'general',
                'label' => 'Site Tagline',
                'description' => 'Short description of your website',
                'sort_order' => 2
            ],
            [
                'key' => 'site_logo',
                'value' => null,
                'type' => 'image',
                'category' => 'general',
                'label' => 'Site Logo',
                'description' => 'Upload your site logo',
                'sort_order' => 3
            ],

            // Hero Section
            [
                'key' => 'hero_title',
                'value' => 'NOTA ONLINE SHOP',
                'type' => 'text',
                'category' => 'hero',
                'label' => 'Hero Title',
                'description' => 'Main title for hero section',
                'sort_order' => 1
            ],
            [
                'key' => 'hero_subtitle',
                'value' => 'Bahan HVS. NCR Sudah termasuk porforasi & potong',
                'type' => 'textarea',
                'category' => 'hero',
                'label' => 'Hero Subtitle',
                'description' => 'Subtitle for hero section',
                'sort_order' => 2
            ],
            [
                'key' => 'hero_image',
                'value' => null,
                'type' => 'image',
                'category' => 'hero',
                'label' => 'Hero Background Image',
                'description' => 'Background image for hero section',
                'sort_order' => 3
            ],

            // Contact Information
            [
                'key' => 'contact_phone',
                'value' => '081515876755',
                'type' => 'phone',
                'category' => 'contact',
                'label' => 'Phone Number',
                'description' => 'Main contact phone number',
                'sort_order' => 1
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@grafikaprinting.com',
                'type' => 'email',
                'category' => 'contact',
                'label' => 'Email Address',
                'description' => 'Main contact email address',
                'sort_order' => 2
            ],
            [
                'key' => 'contact_address',
                'value' => 'Pesantren Peterongan Jombang',
                'type' => 'text',
                'category' => 'contact',
                'label' => 'Address',
                'description' => 'Physical address',
                'sort_order' => 3
            ],
            [
                'key' => 'contact_hours',
                'value' => 'Senin - Jum\'at : 09:00 - 17:00 WIB, Sabtu - Minggu : 09:00 - 15:00 WIB',
                'type' => 'text',
                'category' => 'contact',
                'label' => 'Business Hours',
                'description' => 'Operating hours',
                'sort_order' => 4
            ],

            // Social Media
            [
                'key' => 'social_facebook',
                'value' => '#',
                'type' => 'url',
                'category' => 'social',
                'label' => 'Facebook',
                'description' => 'Facebook page URL',
                'sort_order' => 1
            ],
            [
                'key' => 'social_twitter',
                'value' => '#',
                'type' => 'url',
                'category' => 'social',
                'label' => 'Twitter',
                'description' => 'Twitter profile URL',
                'sort_order' => 2
            ],
            [
                'key' => 'social_instagram',
                'value' => '#',
                'type' => 'url',
                'category' => 'social',
                'label' => 'Instagram',
                'description' => 'Instagram profile URL',
                'sort_order' => 3
            ],
            [
                'key' => 'social_linkedin',
                'value' => '#',
                'type' => 'url',
                'category' => 'social',
                'label' => 'LinkedIn',
                'description' => 'LinkedIn profile URL',
                'sort_order' => 4
            ],
            [
                'key' => 'social_youtube',
                'value' => '#',
                'type' => 'url',
                'category' => 'social',
                'label' => 'YouTube',
                'description' => 'YouTube channel URL',
                'sort_order' => 5
            ],

            // Footer Links
            [
                'key' => 'footer_about',
                'value' => '#',
                'type' => 'url',
                'category' => 'footer',
                'label' => 'About Us Link',
                'description' => 'Link to about us page',
                'sort_order' => 1
            ],
            [
                'key' => 'footer_terms',
                'value' => '#',
                'type' => 'url',
                'category' => 'footer',
                'label' => 'Terms of Service',
                'description' => 'Link to terms of service page',
                'sort_order' => 2
            ],
            [
                'key' => 'footer_privacy',
                'value' => '#',
                'type' => 'url',
                'category' => 'footer',
                'label' => 'Privacy Policy',
                'description' => 'Link to privacy policy page',
                'sort_order' => 3
            ],
            [
                'key' => 'footer_copyright',
                'value' => '©2025 Grafika Printing. Hak Cipta Terpelihara CV. Grafika Digital Solution',
                'type' => 'text',
                'category' => 'footer',
                'label' => 'Copyright Text',
                'description' => 'Copyright notice text',
                'sort_order' => 4
            ]
        ];

        foreach ($settings as $setting) {
            CmsSetting::create($setting);
        }
    }
}
