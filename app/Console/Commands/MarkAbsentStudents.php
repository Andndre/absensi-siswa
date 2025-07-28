<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Attendance;
use Carbon\Carbon;

class MarkAbsentStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:mark-absent {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark students who did not attend as alpha for a given date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::today();
        
        // Check if it's a working day
        $workingDays = config('attendance.working_days', [1, 2, 3, 4, 5, 6]);
        if (!in_array($date->dayOfWeek, $workingDays)) {
            $this->info("Skipping {$date->format('Y-m-d')} - Not a working day");
            return;
        }

        $this->info("Marking absent students for {$date->format('Y-m-d')}...");

        // Get all students
        $students = Student::all();
        $markedCount = 0;

        foreach ($students as $student) {
            // Check if student already has attendance record for this date
            $existingAttendance = Attendance::where('student_id', $student->id)
                ->whereDate('attendance_time', $date)
                ->first();

            if (!$existingAttendance) {
                // Create alpha attendance record
                Attendance::create([
                    'student_id' => $student->id,
                    'attendance_time' => $date->setTime(23, 59, 59), // Set to end of day
                    'status' => 'alpha',
                    'scan_method' => 'auto_marked',
                    'notes' => 'Automatically marked as alpha - no attendance recorded'
                ]);

                $markedCount++;
                $this->line("Marked {$student->name} (NIS: {$student->nis}) as alpha");
            }
        }

        $this->info("Completed: {$markedCount} students marked as alpha");
    }
}
