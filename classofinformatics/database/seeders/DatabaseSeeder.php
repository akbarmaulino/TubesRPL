<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\School;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $schoolId = School::factory(['name' => 'Class Of Informatics'])->create()->id;

        $users = [
            [
                'name' => 'Admin',
                'username' => 'Admin123',
                'password' => Hash::make('Admin123'),
                'role' => User::ADMIN,
                'status' => true,
                'grade' => 12,
                'school_id' => $schoolId,
            ],
            [
                'name' => 'Mentor',
                'username' => 'Mentor123',
                'password' => Hash::make('Mentor123'),
                'role' => User::TEACHER,
                'status' => true,
                'grade' => 12,
                'school_id' => $schoolId,
            ],
            [
                'name' => 'Murid',
                'username' => 'Murid123',
                'password' => Hash::make('Murid123'),
                'role' => User::STUDENT,
                'status' => true,
                'grade' => 12,
                'school_id' => $schoolId,
            ],
        ];

        $selectedTeacherId = '';
        foreach ($users as $user) {
            $createdUser = User::factory($user)->create();

            if ($createdUser->role === 'TEACHER') {
                $selectedTeacherId = $createdUser->id;
            }

            if ($createdUser->role === 'STUDENT') {
                DB::table('experiences')->insert([
                    'id' => Uuid::uuid4()->toString(),
                    'school_id' => $schoolId,
                    'grade' => 12,
                    'user_id' => $createdUser->id,
                    'experience_point' => 0,
                    'level' => 0,
                ]);
            }
        }

        $subjects = [
            [
                'name' => 'Kalkulus',
                'school_id' => $schoolId,
            ],
            [
                'name' => 'DAP',
                'school_id' => $schoolId,
            ],
            [
                'name' => 'RPL',
                'school_id' => $schoolId,
            ],
        ];

        $subjectIds = [];
        foreach ($subjects as $subject) {
            $createdSubject = Subject::factory($subject)->create();
            $subjectIds[] = $createdSubject->id;

            SubjectTeacher::factory(['subject_id' => $createdSubject->id, 'teachers' => [$selectedTeacherId]])->create();
        }

        $courseDescription = collect([
            'BAB 1',
            'BAB 2',
            'BAB 3',
        ]);

        $courseIds = [];
        foreach ($subjectIds as $subjectId) {
            $course = [
                'description' => $courseDescription->random(),
                'grade' => 12,
                'created_by' => $selectedTeacherId,
                'subject_id' => $subjectId,
            ];

            $courseIds[] = Course::factory($course)->create()->id;
        }

        foreach ($subjectIds as $subjectId) {
            $course = [
                'description' => $courseDescription->random(),
                'grade' => 11,
                'created_by' => $selectedTeacherId,
                'subject_id' => $subjectId,
            ];

            $courseIds[] = Course::factory($course)->create()->id;
        }

        foreach ($subjectIds as $subjectId) {
            foreach ($courseIds as $courseId) {
                for ($i = 0; $i < 3; $i++) {
                    Topic::factory(['course_id' => $courseId, 'subject_id' => $subjectId])->create();
                }
            }
        }
    }
}
