<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SettingSeeder::class);

        if (! Profile::query()->exists()) {
            Profile::query()->create([
                'full_name' => 'Your Name',
                'headline' => 'Creative Full-Stack Developer',
                'class' => 'Your Class',
                'vocational_competency' => 'Software Engineering',
                'bio_short' => "I'm a developer passionate about building modern web applications, solving real-world problems, and exploring the intersection of technology and creative design.",
                'bio_long' => 'Replace this placeholder biography from the admin panel. Describe your background, what you are learning, and the kind of work you want to do.',
                'personal_branding' => 'Beyond Code, Into Experience.',
                'career_goals' => 'Replace this placeholder with your own career goals.',
                'location' => 'Your City, Indonesia',
                'email' => null,
            ]);
        }

        if (! User::query()->where('email', config('digital-dimension.admin_email'))->exists()) {
            User::query()->create([
                'name' => 'Administrator',
                'email' => config('digital-dimension.admin_email'),
                'password' => Hash::make(config('digital-dimension.admin_password')),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);
        }

        if (filter_var(env('SEED_DEMO_CONTENT', false), FILTER_VALIDATE_BOOL)) {
            $this->call(DemoContentSeeder::class);
        }

        Setting::set('demo_mode', env('SEED_DEMO_CONTENT', false) ? '1' : '0', 'general', 'boolean');
    }
}
