<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\GuestbookEntry;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\Technology;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demonstration content for local previews and testing only.
 * Every record is explicitly labelled as sample content.
 * Do not present this data as real personal history.
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Web Application', 'slug' => 'web-application', 'sort_order' => 1],
            ['name' => 'Backend Engineering', 'slug' => 'backend-engineering', 'sort_order' => 2],
            ['name' => 'Interactive Experience', 'slug' => 'interactive-experience', 'sort_order' => 3],
        ])->mapWithKeys(fn (array $attributes): array => [
            $attributes['slug'] => ProjectCategory::query()->firstOrCreate(['slug' => $attributes['slug']], $attributes),
        ]);

        $technologies = collect([
            'PHP', 'Laravel', 'Livewire', 'Tailwind CSS', 'JavaScript', 'MySQL', 'GSAP', 'Three.js', 'Git', 'REST API',
        ])->mapWithKeys(fn (string $name, int $index): array => [
            $name => Technology::query()->firstOrCreate(
                ['slug' => str($name)->slug()],
                ['name' => $name, 'sort_order' => $index + 1],
            ),
        ]);

        $projectOne = Project::query()->updateOrCreate(
            ['slug' => 'demo-nebula-library'],
            [
                'project_category_id' => $categories['web-application']->id,
                'title' => 'Nebula Library',
                'excerpt' => 'Demonstration content — a library circulation system for cataloguing collections, loans, and returns.',
                'description' => 'This is demonstration content used to preview the case-study layout. Replace it with a real project description through the admin panel. The concept covers catalogue management, member records, lending workflow, and overdue reporting.',
                'problem_statement' => 'Demonstration content — manual circulation records make it difficult to track availability and overdue loans.',
                'objectives' => 'Build a clear circulation workflow, provide searchable catalogue data, and give staff a reliable overview of active loans.',
                'role' => 'Full-Stack Developer',
                'team_collaboration' => 'Demonstration content — a two-person team split the database design and interface implementation.',
                'year' => 2025,
                'featured' => true,
                'is_published' => true,
                'published_at' => now()->subMonths(2),
                'sort_order' => 1,
                'meta_description' => 'Demonstration project case study for a library circulation system.',
            ],
        );

        $projectTwo = Project::query()->updateOrCreate(
            ['slug' => 'demo-signal-board'],
            [
                'project_category_id' => $categories['interactive-experience']->id,
                'title' => 'Signal Board',
                'excerpt' => 'Demonstration content — a real-time announcement board with a focused, typography-led reading experience.',
                'description' => 'This is demonstration content used to preview the case-study layout. Signal Board explores live updates, compact publishing tools, and an accessible dark interface.',
                'problem_statement' => 'Demonstration content — announcements were scattered across chat groups and became difficult to find.',
                'objectives' => 'Create one dependable place for time-sensitive announcements with a readable visual hierarchy.',
                'role' => 'Frontend and Backend Developer',
                'year' => 2025,
                'featured' => false,
                'is_published' => true,
                'published_at' => now()->subMonths(5),
                'sort_order' => 2,
            ],
        );

        $projectThree = Project::query()->updateOrCreate(
            ['slug' => 'demo-atlas-api'],
            [
                'project_category_id' => $categories['backend-engineering']->id,
                'title' => 'Atlas API',
                'excerpt' => 'Demonstration content — a REST API foundation for school data with validation and role-based access.',
                'description' => 'This is demonstration content used to preview the case-study layout. Atlas API is a learning project focused on API resources, authentication, and consistent error responses.',
                'problem_statement' => 'Demonstration content — disconnected applications duplicated student data and produced inconsistent records.',
                'objectives' => 'Define a documented data contract and centralize validation rules.',
                'role' => 'Backend Developer',
                'year' => 2024,
                'featured' => false,
                'is_published' => true,
                'published_at' => now()->subYear(),
                'sort_order' => 3,
            ],
        );

        $projectOne->technologies()->syncWithoutDetaching($technologies->only(['Laravel', 'Livewire', 'Tailwind CSS', 'MySQL'])->pluck('id'));
        $projectTwo->technologies()->syncWithoutDetaching($technologies->only(['Laravel', 'JavaScript', 'Tailwind CSS'])->pluck('id'));
        $projectThree->technologies()->syncWithoutDetaching($technologies->only(['PHP', 'Laravel', 'MySQL', 'REST API'])->pluck('id'));

        $projectOne->features()->updateOrCreate(['title' => 'Demonstration feature — catalogue search'], [
            'description' => 'Search by title, author, or category with paginated results.', 'sort_order' => 1,
        ]);
        $projectOne->features()->updateOrCreate(['title' => 'Demonstration feature — circulation tracking'], [
            'description' => 'Record loans and returns against member accounts.', 'sort_order' => 2,
        ]);

        $projectOne->processes()->updateOrCreate(['title' => 'Demonstration process — schema design'], [
            'description' => 'Defined members, books, copies, and loans as separate entities so that availability can be derived rather than duplicated.',
            'code_snippet' => "Schema::create('loans', function (Blueprint \$table) {\n    \$table->foreignId('book_copy_id')->constrained();\n    \$table->foreignId('member_id')->constrained();\n    \$table->date('due_at');\n    \$table->timestamp('returned_at')->nullable();\n});",
            'sort_order' => 1,
        ]);

        $projectOne->stories()->updateOrCreate(['title' => 'Initial idea'], [
            'content' => '<p>Demonstration content — this section illustrates how a Project Development Story is presented. Describe the first version of the idea and the constraint that started it.</p>',
            'sort_order' => 1,
        ]);
        $projectOne->stories()->updateOrCreate(['title' => 'Problem discovery'], [
            'content' => '<p>Demonstration content — explain who experienced the problem, what you observed, and which assumptions you had to revise.</p>',
            'sort_order' => 2,
        ]);
        $projectOne->stories()->updateOrCreate(['title' => 'Architecture decisions'], [
            'content' => '<p>Demonstration content — document the trade-offs that shaped the implementation rather than just listing tools.</p>',
            'code_snippet' => "// Demonstration snippet\n\$loan->loadMissing('member', 'copy.book');",
            'sort_order' => 3,
        ]);

        $projectOne->feedback()->updateOrCreate(['name' => 'Sample Reviewer'], [
            'role' => 'Demonstration reviewer',
            'content' => 'Demonstration content — this is sample feedback used to preview the layout, not a real user testimonial.',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        foreach ([
            ['name' => 'Hard Skills', 'slug' => 'hard-skills', 'description' => 'Technical capabilities used in day-to-day development.', 'sort_order' => 1],
            ['name' => 'Soft Skills', 'slug' => 'soft-skills', 'description' => 'Working habits that support collaboration and delivery.', 'sort_order' => 2],
        ] as $categoryAttributes) {
            $skillCategory = SkillCategory::query()->updateOrCreate(
                ['slug' => $categoryAttributes['slug']],
                $categoryAttributes,
            );

            $isHard = $categoryAttributes['slug'] === 'hard-skills';
            $skills = $isHard
                ? [['PHP', 70], ['Laravel', 70], ['Livewire', 65], ['JavaScript', 60], ['Tailwind CSS', 70], ['MySQL', 65], ['Git', 65], ['GSAP', 50], ['Three.js', 40]]
                : [['Problem Solving', 75], ['Teamwork', 75], ['Communication', 70], ['Time Management', 65], ['Adaptability', 70], ['Critical Thinking', 70]];

            foreach ($skills as $index => [$name, $percentage]) {
                Skill::query()->updateOrCreate(
                    ['skill_category_id' => $skillCategory->id, 'name' => $name],
                    [
                        'percentage' => $percentage,
                        'description' => 'Demonstration self-assessment — replace this percentage and description with an honest evaluation.',
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ],
                );
            }
        }

        Education::query()->updateOrCreate(['institution' => 'Demonstration School'], [
            'degree' => 'Vocational High School',
            'field_of_study' => 'Software Engineering',
            'level' => 'SMK',
            'start_year' => 2023,
            'end_year' => 2026,
            'is_current' => true,
            'description' => 'Demonstration education record — replace with your real school information.',
            'sort_order' => 1,
        ]);

        Experience::query()->updateOrCreate(['title' => 'Demonstration Internship'], [
            'organization' => 'Sample Organization',
            'position' => 'Software Development Intern',
            'category' => Experience::CATEGORY_INTERNSHIP,
            'start_date' => now()->subMonths(6),
            'end_date' => now()->subMonths(3),
            'is_current' => false,
            'description' => 'Demonstration content — describe the placement, the team, and the work you actually did.',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        Experience::query()->updateOrCreate(['title' => 'Demonstration Student Organization'], [
            'organization' => 'Sample Student Organization',
            'position' => 'Technology Division Member',
            'category' => Experience::CATEGORY_ORGANIZATION,
            'start_date' => now()->subYear(),
            'end_date' => null,
            'is_current' => true,
            'description' => 'Demonstration content — replace with a documented organization role.',
            'is_published' => true,
            'sort_order' => 2,
        ]);

        Certificate::query()->updateOrCreate(['title' => 'Demonstration Certificate — Web Development Fundamentals'], [
            'issuer' => 'Sample Issuer',
            'issued_at' => now()->subMonths(4),
            'category' => 'Course',
            'description' => 'Demonstration content — this certificate is fictional sample data for layout preview only.',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        Testimonial::query()->updateOrCreate(['name' => 'Sample Supervisor'], [
            'position' => 'Demonstration Supervisor',
            'organization' => 'Sample Organization',
            'relationship' => Testimonial::RELATIONSHIP_SUPERVISOR,
            'content' => 'Demonstration content — sample testimonial for layout preview only. Replace it with permissioned, authentic feedback.',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        GuestbookEntry::query()->updateOrCreate(['name' => 'Sample Visitor'], [
            'email' => null,
            'message' => 'Demonstration guestbook entry — approved sample content used to preview the public list.',
            'status' => GuestbookEntry::STATUS_APPROVED,
        ]);

        $author = User::query()->where('is_admin', true)->first();

        foreach ([
            ['name' => 'Laravel Development', 'slug' => 'laravel-development', 'sort_order' => 1],
            ['name' => 'Frontend Development', 'slug' => 'frontend-development', 'sort_order' => 2],
            ['name' => 'Problem Solving', 'slug' => 'problem-solving', 'sort_order' => 3],
        ] as $postCategory) {
            PostCategory::query()->firstOrCreate(['slug' => $postCategory['slug']], $postCategory);
        }

        Post::query()->updateOrCreate(['slug' => 'demo-building-a-livewire-filter'], [
            'post_category_id' => PostCategory::query()->where('slug', 'laravel-development')->value('id'),
            'user_id' => $author?->id,
            'title' => 'Demonstration — Building a Livewire Filter Without Duplicate Queries',
            'excerpt' => 'Demonstration article — a short walkthrough of composing reusable filter scopes in a Livewire component.',
            'content' => '<p>This is demonstration content for the developer journal layout.</p><h2>Keep filters composable</h2><p>Apply each filter as a query scope so the same logic can be tested outside the component.</p><pre><code class="language-php">$query->when($this->search, fn ($q) =&gt; $q-&gt;where(\'title\', \'like\', "%{$this->search}%"));</code></pre><p>Replace this article with your own notes from real work.</p>',
            'is_published' => true,
            'published_at' => now()->subWeeks(2),
        ]);

        Post::query()->updateOrCreate(['slug' => 'demo-reduced-motion-three-js'], [
            'post_category_id' => PostCategory::query()->where('slug', 'frontend-development')->value('id'),
            'user_id' => $author?->id,
            'title' => 'Demonstration — Designing a Three.js Scene for Reduced Motion',
            'excerpt' => 'Demonstration article — why a static, carefully composed frame is a feature, not a fallback.',
            'content' => '<p>This is demonstration content for the developer journal layout.</p><h2>Start from the static frame</h2><p>If the scene is not meaningful without motion, the design needs more work.</p><pre><code class="language-js">const reduceMotion = window.matchMedia(\'(prefers-reduced-motion: reduce)\').matches;</code></pre>',
            'is_published' => true,
            'published_at' => now()->subMonth(),
        ]);

        Post::query()->updateOrCreate(['slug' => 'demo-draft-notes'], [
            'post_category_id' => PostCategory::query()->where('slug', 'problem-solving')->value('id'),
            'user_id' => $author?->id,
            'title' => 'Demonstration — Draft Article',
            'excerpt' => 'A demonstration draft that must never appear publicly.',
            'content' => '<p>Draft demonstration content.</p>',
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
