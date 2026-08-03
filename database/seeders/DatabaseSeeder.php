<?php

namespace Database\Seeders;

use App\Enums\TaskStatus;
use App\Models\Client;
use App\Models\CommentReaction;
use App\Models\Expense;
use App\Models\Invitation;
use App\Models\Invoice;
use App\Models\MemberProfile;
use App\Models\Project;
use App\Models\SalaryPayment;
use App\Models\SalaryRecord;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    private const PASSWORD = 'password';

    private array $contentTypes = [
        'Instagram Reel', 'Facebook Post', 'Promotional Video', 'Blog Article',
        'Email Newsletter', 'TikTok Video', 'Customer Testimonial', 'Product Photoshoot',
        'Seasonal Offer Post',
    ];

    public function run(): void
    {
        $this->seedRoles();
        $this->seedPermissions();
        $team    = $this->seedTeam();
        $clients = $this->seedClients();
        $this->seedClientUsers($clients);
        $projects = $this->seedProjects($clients);
        $tasks    = $this->seedTasks($projects, $team);
        $this->seedComments($tasks, $team);
        $this->seedInvoices($clients, $projects);
        $this->seedSalaries($team);
        $this->seedMemberProfiles($team);
        $this->seedInvitation($team['super-admin']);
    }

    private function seedRoles(): void
    {
        $roles = [
            ['name' => 'super-admin',     'description' => 'Full access to all features'],
            ['name' => 'project-manager', 'description' => 'Manage clients, projects, tasks, and approvals'],
            ['name' => '3d-artist',       'description' => 'Create 3D assets and graphics'],
            ['name' => 'video-editor',    'description' => 'Create and edit video content'],
            ['name' => 'marketer',        'description' => 'Manage marketing tasks and campaigns'],
            ['name' => 'client',          'description' => 'View and approve content, leave feedback'],
        ];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r['name'], 'guard_name' => 'web'], $r);
        }
    }

    private function seedPermissions(): void
    {
        $permissions = [
            'manage-clients', 'manage-projects', 'manage-tasks', 'manage-invoices',
            'manage-team', 'manage-salaries', 'manage-roles', 'manage-settings',
            'approve-content', 'view-reports',
        ];
        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $defaults = [
            'super-admin'     => $permissions,
            'project-manager' => ['manage-clients', 'manage-projects', 'manage-tasks', 'manage-invoices', 'view-reports'],
            '3d-artist'       => ['manage-tasks'],
            'video-editor'    => ['manage-tasks'],
            'marketer'        => ['manage-tasks'],
            'client'          => ['approve-content'],
        ];
        foreach ($defaults as $role => $perms) {
            Role::findByName($role, 'web')->syncPermissions($perms);
        }
    }

    /**
     * @return array<string, User> one representative user per staff role
     */
    private function seedTeam(): array
    {
        $verified = now();
        $members  = [
            'super-admin'     => ['name' => 'Noor E Alam',   'email' => 'noorealamdev@gmail.com'],
            'project-manager' => ['name' => 'Sarah Chen',    'email' => 'sarah@karigorhq.test'],
            '3d-artist'       => ['name' => 'Alex Rivera',   'email' => 'alex@karigorhq.test'],
            'video-editor'    => ['name' => 'Jordan Kim',    'email' => 'jordan@karigorhq.test'],
            'marketer'        => ['name' => 'Taylor Brooks', 'email' => 'taylor@karigorhq.test'],
        ];

        $users = [];
        foreach ($members as $role => $info) {
            $user = User::firstOrCreate(
                ['email' => $info['email']],
                ['name' => $info['name'], 'password' => Hash::make(self::PASSWORD), 'email_verified_at' => $verified]
            );
            $user->syncRoles([$role]);
            $users[$role] = $user;
        }

        // A second 3D artist so "assign to" pickers have more than one option per role.
        $maya = User::firstOrCreate(
            ['email' => 'maya@karigorhq.test'],
            ['name' => 'Maya Patel', 'password' => Hash::make(self::PASSWORD), 'email_verified_at' => $verified]
        );
        $maya->syncRoles(['3d-artist']);
        $users['3d-artist (2)'] = $maya;

        return $users;
    }

    /**
     * @return Client[]
     */
    private function seedClients(): array
    {
        $definitions = [
            [
                'name' => 'Michael Hasan', 'company' => 'Trusted AC Service', 'email' => 'contact@trustedacservice.test',
                'phone' => '+1 555-0110', 'whatsapp' => '+1 555-0110', 'facebook_page' => 'https://facebook.com/trustedacservice',
                'website' => 'https://trustedacservice.test', 'status' => 'active', 'package' => 'Growth Plan',
                'brand_colors' => ['#1e88e5', '#0d1b2a'], 'fonts' => ['Poppins', 'Inter'],
                'renewal_date' => Carbon::now()->addMonths(2), 'notes' => 'HVAC service company. Focused on seasonal promotions and testimonials.',
            ],
            [
                'name' => 'Leila Haque', 'company' => 'GlowUp Cosmetics', 'email' => 'hello@glowupcosmetics.test',
                'phone' => '+1 555-0111', 'whatsapp' => '+1 555-0111', 'facebook_page' => 'https://facebook.com/glowupcosmetics',
                'website' => 'https://glowupcosmetics.test', 'status' => 'active', 'package' => 'Premium Plan',
                'brand_colors' => ['#e91e63', '#fce4ec', '#212121'], 'fonts' => ['Playfair Display', 'Lato'],
                'renewal_date' => Carbon::now()->addMonth(), 'notes' => 'Beauty brand — heavy on Reels and influencer-style content.',
            ],
            [
                'name' => 'Omar Siddique', 'company' => 'Peak Fitness Gym', 'email' => 'info@peakfitnessgym.test',
                'phone' => '+1 555-0112', 'whatsapp' => '+1 555-0112', 'facebook_page' => 'https://facebook.com/peakfitnessgym',
                'website' => 'https://peakfitnessgym.test', 'status' => 'active', 'package' => 'Starter Plan',
                'brand_colors' => ['#ff5722', '#212121'], 'fonts' => ['Montserrat'],
                'renewal_date' => Carbon::now()->addDays(20), 'notes' => 'Local gym, wants more member transformation stories.',
            ],
            [
                'name' => 'Farah Islam', 'company' => 'Urban Bites Cafe', 'email' => 'team@urbanbitescafe.test',
                'phone' => '+1 555-0113', 'whatsapp' => '+1 555-0113', 'facebook_page' => 'https://facebook.com/urbanbitescafe',
                'website' => null, 'status' => 'prospect', 'package' => null,
                'brand_colors' => ['#6d4c41', '#fff8e1'], 'fonts' => ['Nunito'],
                'renewal_date' => null, 'notes' => 'Discovery call scheduled — evaluating packages.',
            ],
            [
                'name' => 'David Cole', 'company' => 'TechNest Solutions', 'email' => 'david@technestsolutions.test',
                'phone' => '+1 555-0114', 'whatsapp' => null, 'facebook_page' => 'https://facebook.com/technestsolutions',
                'website' => 'https://technestsolutions.test', 'status' => 'inactive', 'package' => 'Growth Plan',
                'brand_colors' => ['#6c63ff', '#0d0f14'], 'fonts' => ['Inter', 'Roboto Mono'],
                'renewal_date' => Carbon::now()->subMonth(), 'notes' => 'Paused campaigns while they rebuild their website.',
            ],
        ];

        $clients = [];
        foreach ($definitions as $data) {
            $client = Client::firstOrCreate(['email' => $data['email']], $data);

            if (extension_loaded('gd') && $client->getFirstMedia('logo') === null) {
                $initials = collect(explode(' ', $client->company))->map(fn ($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                $path     = $this->makePlaceholderLogo($client->brand_colors[0] ?? '#6c63ff', $initials ?: 'CL');
                $client->addMedia($path)->toMediaCollection('logo');
            }

            $clients[] = $client;
        }

        return $clients;
    }

    /**
     * @param Client[] $clients
     */
    private function seedClientUsers(array $clients): void
    {
        $verified = now();

        // Give the first two active clients a portal login.
        foreach (array_slice($clients, 0, 2) as $client) {
            $user = User::firstOrCreate(
                ['email' => $client->email],
                [
                    'name'              => $client->name,
                    'password'          => Hash::make(self::PASSWORD),
                    'email_verified_at' => $verified,
                    'client_id'         => $client->id,
                ]
            );
            $user->client_id = $client->id;
            $user->save();
            $user->syncRoles(['client']);
        }
    }

    /**
     * @param Client[] $clients
     * @return Project[]
     */
    private function seedProjects(array $clients): array
    {
        $statuses = ['active', 'active', 'on_hold', 'completed', 'cancelled'];
        $projects = [];

        foreach ($clients as $i => $client) {
            $name = $client->company . ' — August 2026 Content';
            $project = Project::firstOrCreate(
                ['name' => $name],
                [
                    'description' => 'Monthly content package: social posts, videos, and campaign creative for ' . $client->company . '.',
                    'status'      => $statuses[$i] ?? 'active',
                    'priority'    => ['high', 'high', 'medium', 'medium', 'low'][$i] ?? 'medium',
                    'start_date'  => Carbon::now()->startOfMonth(),
                    'deadline'    => Carbon::now()->endOfMonth(),
                    'client_id'   => $client->id,
                ]
            );
            $projects[] = $project;
        }

        return $projects;
    }

    /**
     * @param Project[] $projects
     * @param array<string, User> $team
     * @return Task[]
     */
    private function seedTasks(array $projects, array $team): array
    {
        $assigneeByStatus = [
            TaskStatus::Todo->value   => null,
            TaskStatus::Doing->value  => $team['marketer'],
            TaskStatus::Review->value => $team['project-manager'],
            TaskStatus::Done->value   => $team['3d-artist (2)'],
        ];
        $priorities = ['low', 'medium', 'high', 'urgent'];

        $tasks = [];
        foreach ($projects as $pi => $project) {
            foreach (TaskStatus::cases() as $si => $status) {
                $type = $this->contentTypes[($pi + $si) % count($this->contentTypes)];
                $name = "{$type} — {$project->client->company}";

                $dueOffset = $si - 1; // spreads due dates before/after today across the pipeline
                $task = Task::firstOrCreate(
                    ['name' => $name, 'project_id' => $project->id],
                    [
                        'description' => "Prepare the {$type} deliverable for {$project->client->company}'s content calendar.",
                        'status'      => $status->value,
                        'priority'    => $priorities[($pi + $si) % count($priorities)],
                        'due_date'    => Carbon::now()->addDays($dueOffset),
                    ]
                );
                if ($assigneeByStatus[$status->value]) {
                    $task->assignees()->syncWithoutDetaching([$assigneeByStatus[$status->value]->id]);
                }
                $tasks[] = $task;
            }
        }

        return $tasks;
    }

    /**
     * @param Task[] $tasks
     * @param array<string, User> $team
     */
    private function seedComments(array $tasks, array $team): void
    {
        $commenters = [$team['project-manager'], $team['marketer'], $team['3d-artist']];
        $notes = [
            'Looks good — just tighten up the caption a bit.',
            'Client loved the direction on the last one, let\'s keep this style.',
            'Can we get a version with the new brand colors?',
            'Approved from my side, ready to schedule.',
            'Added a revision note in the attached file.',
        ];
        $emojis = ['👍', '🔥', '❤️'];

        $commentable = array_filter($tasks, fn (Task $t) => in_array($t->status, [
            TaskStatus::Review->value, TaskStatus::Done->value,
        ], true));

        foreach (array_values($commentable) as $i => $task) {
            $author = $commenters[$i % count($commenters)];
            $comment = TaskComment::firstOrCreate(
                ['task_id' => $task->id, 'user_id' => $author->id, 'body' => $notes[$i % count($notes)]]
            );

            $reactor = $commenters[($i + 1) % count($commenters)];
            CommentReaction::firstOrCreate([
                'comment_id' => $comment->id,
                'user_id'    => $reactor->id,
                'emoji'      => $emojis[$i % count($emojis)],
            ]);
        }
    }

    /**
     * @param Client[] $clients
     * @param Project[] $projects
     */
    private function seedInvoices(array $clients, array $projects): void
    {
        $statuses = ['paid', 'sent', 'draft', 'overdue', 'cancelled'];

        foreach ($clients as $i => $client) {
            $project = $projects[$i] ?? null;
            $amount  = [150000, 220000, 80000, 50000, 100000][$i] ?? 90000;

            foreach ([0, 1] as $n) {
                $number = sprintf('INV-%04d', ($i * 2) + $n + 1);
                Invoice::firstOrCreate(
                    ['invoice_number' => $number],
                    [
                        'client_id'   => $client->id,
                        'project_id'  => $project?->id,
                        'amount'      => $amount,
                        'status'      => $statuses[($i + $n) % count($statuses)],
                        'issued_date' => Carbon::now()->subMonths($n)->startOfMonth(),
                        'due_date'    => Carbon::now()->subMonths($n)->startOfMonth()->addDays(15),
                        'notes'       => $n === 0 ? 'Monthly retainer — ' . $client->package : null,
                    ]
                );
            }
        }
    }

    /**
     * @param array<string, User> $team
     */
    private function seedSalaries(array $team): void
    {
        $admin = $team['super-admin'];
        $staff = array_filter($team, fn ($role) => $role !== 'super-admin', ARRAY_FILTER_USE_KEY);

        foreach ($staff as $user) {
            foreach ([1, 0] as $monthsAgo) {
                $record = SalaryRecord::firstOrCreate(
                    ['user_id' => $user->id, 'effective_date' => Carbon::now()->subMonths($monthsAgo)->startOfMonth()],
                    [
                        'amount'     => 40000,
                        'currency'   => 'BDT',
                        'period'     => 'monthly',
                        'created_by' => $admin->id,
                        'notes'      => $monthsAgo === 0 ? 'Pending payout' : null,
                    ]
                );

                // Last month's salary was already paid — seed a payment + matching expense.
                if ($monthsAgo === 1 && !$record->payments()->exists()) {
                    $periodMonth = Carbon::now()->subMonths($monthsAgo)->startOfMonth();

                    $expense = Expense::create([
                        'title'       => "Salary — {$user->name} ({$periodMonth->format('F Y')})",
                        'amount'      => $record->amount,
                        'spent_at'    => $periodMonth->copy()->addDays(2),
                        'recorded_by' => $admin->id,
                    ]);

                    SalaryPayment::create([
                        'user_id'          => $user->id,
                        'salary_record_id' => $record->id,
                        'expense_id'       => $expense->id,
                        'amount'           => $record->amount,
                        'period_month'     => $periodMonth,
                        'paid_at'          => $periodMonth->copy()->addDays(2),
                        'paid_by'          => $admin->id,
                    ]);
                }
            }
        }
    }

    /**
     * @param array<string, User> $team
     */
    private function seedMemberProfiles(array $team): void
    {
        $staff = array_filter($team, fn ($role) => $role !== 'super-admin', ARRAY_FILTER_USE_KEY);

        foreach ($staff as $user) {
            MemberProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'phone'                      => '+1 555-01' . str_pad((string) $user->id, 2, '0', STR_PAD_LEFT),
                    'date_of_birth'               => Carbon::now()->subYears(28)->subDays($user->id),
                    'address'                    => '123 Market Street, Suite ' . $user->id . ', Dhaka',
                    'emergency_contact_name'      => 'Emergency Contact ' . $user->id,
                    'emergency_contact_phone'     => '+1 555-02' . str_pad((string) $user->id, 2, '0', STR_PAD_LEFT),
                    'emergency_contact_relation'  => 'Spouse',
                    'bank_name'                  => 'City Bank',
                    'bank_account_holder'         => $user->name,
                    'bank_account_number'         => '00112233' . str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
                    'bank_branch'                => 'Gulshan Branch',
                ]
            );
        }
    }

    private function seedInvitation(User $admin): void
    {
        Invitation::firstOrCreate(
            ['email' => 'new.writer@karigorhq.test'],
            [
                'name'       => 'Prospective Writer',
                'token'      => Str::random(64),
                'role_ids'   => ['marketer'],
                'invited_by' => $admin->id,
                'expires_at' => Carbon::now()->addDays(7),
            ]
        );
    }

    private function makePlaceholderLogo(string $hex, string $initials): string
    {
        $hex = ltrim($hex, '#');
        $r   = hexdec(substr($hex, 0, 2) ?: '6c');
        $g   = hexdec(substr($hex, 2, 2) ?: '63');
        $b   = hexdec(substr($hex, 4, 2) ?: 'ff');

        $size  = 200;
        $image = imagecreatetruecolor($size, $size);
        $bg    = imagecolorallocate($image, $r, $g, $b);
        imagefill($image, 0, 0, $bg);

        $white      = imagecolorallocate($image, 255, 255, 255);
        $font       = 5;
        $textWidth  = imagefontwidth($font) * strlen($initials);
        $textHeight = imagefontheight($font);
        imagestring($image, $font, (int) (($size - $textWidth) / 2), (int) (($size - $textHeight) / 2), $initials, $white);

        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'client-logo-' . Str::random(8) . '.png';
        imagepng($image, $path);
        imagedestroy($image);

        return $path;
    }
}
