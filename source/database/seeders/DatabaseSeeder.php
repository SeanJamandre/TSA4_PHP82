<?php
namespace Database\Seeders;
use App\Models\Task;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder {
    public function run(): void {
        foreach ([['Plan the weekly schedule','pending','high'], ['Review Laravel validation','in_progress','medium'], ['Read the assessment instructions','done','low']] as [$title,$status,$priority]) {
            Task::firstOrCreate(['title'=>$title], ['description'=>'Sample task for the Tasks for Today assessment.', 'due_date'=>now()->addDays(2)->toDateString(), 'status'=>$status, 'priority'=>$priority]);
        }
    }
}
