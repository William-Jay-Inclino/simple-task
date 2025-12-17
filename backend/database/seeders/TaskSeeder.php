<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $charity = User::where('email', 'charity@goteam.com')->first();

        if (!$charity) {
            $this->command->error('Charity user not found. Please run UserSeeder first.');
            return;
        }

        $tasks = [
            // December 2025
            [
                'user_id' => $charity->id,
                'statement' => 'Finalize year-end financial reports',
                'task_date' => '2025-12-01',
                'is_completed' => true,
                'created_at' => '2025-12-01 08:30:00',
                'updated_at' => '2025-12-01 08:30:00',
            ],
            [
                'user_id' => $charity->id,
                'statement' => 'Organize holiday party for the team',
                'task_date' => '2025-12-03',
                'is_completed' => true,
                'created_at' => '2025-12-03 14:00:00',
                'updated_at' => '2025-12-03 14:00:00',
            ],
            [
                'user_id' => $charity->id,
                'statement' => 'Review performance evaluations',
                'task_date' => '2025-12-05',
                'is_completed' => true,
                'created_at' => '2025-12-05 11:15:00',
                'updated_at' => '2025-12-05 11:15:00',
            ],
            [
                'user_id' => $charity->id,
                'statement' => 'Submit annual leave requests',
                'task_date' => '2025-12-07',
                'is_completed' => true,
                'created_at' => '2025-12-07 09:45:00',
                'updated_at' => '2025-12-07 09:45:00',
            ],
            [
                'user_id' => $charity->id,
                'statement' => 'Prepare Q1 2026 objectives',
                'task_date' => '2025-12-09',
                'is_completed' => false,
                'created_at' => '2025-12-09 16:00:00',
                'updated_at' => '2025-12-09 16:00:00',
            ],
            [
                'user_id' => $charity->id,
                'statement' => 'Update project documentation',
                'task_date' => '2025-12-11',
                'is_completed' => false,
                'created_at' => '2025-12-11 10:30:00',
                'updated_at' => '2025-12-11 10:30:00',
            ],
            [
                'user_id' => $charity->id,
                'statement' => 'Schedule year-end team retrospective',
                'task_date' => '2025-12-13',
                'is_completed' => false,
                'created_at' => '2025-12-13 13:00:00',
                'updated_at' => '2025-12-13 13:00:00',
            ],
            [
                'user_id' => $charity->id,
                'statement' => 'Review and archive completed projects',
                'task_date' => '2025-12-14',
                'is_completed' => false,
                'created_at' => '2025-12-14 09:00:00',
                'updated_at' => '2025-12-14 09:00:00',
            ],
            [
                'user_id' => $charity->id,
                'statement' => 'Send holiday greetings to clients',
                'task_date' => '2025-12-15',
                'is_completed' => false,
                'created_at' => '2025-12-15 08:00:00',
                'updated_at' => '2025-12-15 08:00:00',
            ],
        ];

        // Group tasks by date and assign order
        $tasksByDate = [];
        foreach ($tasks as $taskData) {
            $date = $taskData['task_date'];
            if (!isset($tasksByDate[$date])) {
                $tasksByDate[$date] = [];
            }
            $tasksByDate[$date][] = $taskData;
        }

        // Assign order values per date
        foreach ($tasksByDate as $date => $dateTasks) {
            foreach ($dateTasks as $index => $taskData) {
                $taskData['order'] = $index;
                Task::create($taskData);
            }
        }

        $this->command->info('Created ' . count($tasks) . ' tasks for Charity');
    }
}
