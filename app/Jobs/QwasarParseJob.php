<?php

namespace App\Jobs;

use App\Models\Student;
use App\Models\StudentCertificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Goutte\Client;

class QwasarParseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $students;

    public function __construct($students)
    {
        $this->students = $students;
    }

    public function handle()
    {
        $client = new Client();

        $crawler = $client->request('GET', 'https://casapp.us.qwasar.io/login?service=https%3A%2F%2Fupskill.us.qwasar.io%2Fusers%2Fservice');
        $form = $crawler->selectButton('Login')->form();
        $form['username'] = 'jas.884@mail.ru';
        $form['password'] = 'jasur2171517';
        $crawler = $client->submit($form);


        foreach ($this->students as $student) {
            $crawler = $client->request('GET', "https://upskill.us.qwasar.io/users/{$student}");
            $student_full_name = $crawler->filter('.text-c-yellow')->first()->text();
            $student_season = $crawler->filter('.label')->eq(1)->text();
            $studentProgresses = $crawler->filter('div.row.p-t-10.align-items-center');
            $certificates = $studentProgresses->filter('a[href^="/certificates/"]');

            $userFind = Student::where('nik_name', $student)->first();

            $certificateUrls = $certificates->each(function ($node) {
                return $node->attr('href');
            });

            $students = [];

            $studentProgresses->each(function ($studentProgress) use (&$students) {
                $searchKeyword = [
                    'Preseason Web',
                    'Preseason Data',

                    'Season 01 Arc',
                    'Season 02 Arc',

                    'Season 02 Software Engineer',
                    'Season 02 Data Science',
                    'Season 03 Machine Learning',

                    'Season 02 Fullstack',
                    'Season 03 Frontend',
                    'Season 03 Backend'
                ];

                $currentProgress = trim($studentProgress->text());

                foreach ($searchKeyword as $keyword) {
                    if (strpos($currentProgress, $keyword) !== false) {
                        $students[] = $currentProgress;
                        break;
                    }
                }
            });

            $students = array_reverse($students);

            if ($userFind) {
                Student::where('nik_name', $userFind->nik_name)->update([
                    'progress_0' => isset($students[0]) ? $students[0] : null,
                    'progress_1' => isset($students[1]) ? $students[1] : null,
                    'progress_2' => isset($students[2]) ? $students[2] : null,
                    'progress_3' => isset($students[3]) ? $students[3] : null,
                    'progress_4' => isset($students[4]) ? $students[4] : null,
                    'progress_5' => isset($students[5]) ? $students[5] : null,
                ]);
            } else {
                $studentId = Student::create([
                    'seria' => 'FS',
                    'seria_number' => mt_rand(100000, 9999999),
                    'nik_name' => $student,
                    'full_name' => $student_full_name,
                    'course' => 'None',
                    'season' => $student_season,
                    'progress_0' => isset($students[0]) ? $students[0] : null,
                    'progress_1' => isset($students[1]) ? $students[1] : null,
                    'progress_2' => isset($students[2]) ? $students[2] : null,
                    'progress_3' => isset($students[3]) ? $students[3] : null,
                    'progress_4' => isset($students[4]) ? $students[4] : null,
                    'progress_5' => isset($students[5]) ? $students[5] : null,
                ]);

                foreach ($certificateUrls as $certificate) {
                    $certificates = $client->request('GET', "https://upskill.us.qwasar.io{$certificate}");
                    $certificate_name = $certificates->filter('h2.text-cyan')->first()->text();
                    $certificate_date = $certificates->filter('div.completion')->first()->text();
                    StudentCertificate::create([
                        'student_id' => $studentId->id,
                        'certificate_name' => $certificate_name,
                        'certificate' => 'https://upskill.us.qwasar.io' . $certificate,
                        'certificate_date' => $certificate_date
                    ]);
                }
            }
        }
    }
}
