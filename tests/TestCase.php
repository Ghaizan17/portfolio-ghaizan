<?php

namespace Tests;

use App\Models\Profile;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Seed the settings and a single profile required by every public page.
     */
    protected function createSiteContext(): Profile
    {
        $this->seed(SettingSeeder::class);

        return Profile::factory()->create();
    }
}
