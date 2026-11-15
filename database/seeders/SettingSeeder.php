<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['site_title', 'Digital Dimension', 'general', 'string'],
            ['site_tagline', 'Beyond Code, Into Experience.', 'general', 'string'],
            ['site_description', 'A creative full-stack developer portfolio exploring the intersection of engineering, design, and interactive experiences.', 'seo', 'text'],
            ['seo_default_title', 'Digital Dimension — Creative Full-Stack Developer', 'seo', 'string'],
            ['seo_default_description', 'Portfolio of a creative full-stack developer building modern web applications and interactive digital experiences with Laravel, Livewire, and Three.js.', 'seo', 'text'],
            ['seo_keywords', 'full-stack developer, Laravel, Livewire, portfolio, creative developer', 'seo', 'string'],
            ['default_theme', 'dark', 'appearance', 'string'],
            ['footer_text', 'Designed and built with Laravel, Livewire, GSAP, and Three.js.', 'general', 'text'],
            ['contact_intro', 'Have a project, an opportunity, or a question? Send a message and I will get back to you.', 'general', 'text'],
            ['guestbook_intro', 'Leave a message, a note, or a piece of advice. Entries appear after moderation.', 'general', 'text'],
            ['demo_mode', '0', 'general', 'boolean'],
        ];

        foreach ($settings as [$key, $value, $group, $type]) {
            Setting::query()->firstOrCreate(['key' => $key], ['value' => $value, 'group' => $group, 'type' => $type]);
        }
    }
}
