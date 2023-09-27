<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        return view('front.search');
    }

    public function data()
    {
        $teachers = Teacher::all();
        $students = Student::all();

        return response()->json([
            'teachers' => $teachers,
            'students' => $students
        ]);
    }

    public function show($seria_number)
    {
        $seria = substr($seria_number, 0, 2);
        $seria_number = substr($seria_number, 2, strlen($seria_number));

        if ($seria == "MK") {
            $student_certificat = Teacher::where('seria_number', $seria_number)
                ->where('seria', $seria)
                ->first();

            if ($student_certificat) {
                $registered_at = Carbon::parse($student_certificat->berilgan_sana);
                $final_date = $registered_at->addYear(2)->format('d.m.Y');

                return view('front.showTeacher', compact('student_certificat', 'final_date'));
            } else {
                return view('front.notFound')->with([
                    'message' => 'Bunday seria raqamlik ' . $seria . $seria_number . ' sertifikat mavjud emas!'
                ]);
            }
        } else {
            $student_certificat = Student::where('seria_number', $seria_number)
                ->where('seria', $seria)
                ->first();

            if ($student_certificat) {
                $registered_at = Carbon::parse($student_certificat->date_of_issue)->toDateString();

                return view('front.showStudent', compact('student_certificat', 'registered_at'));
            } else {
                return view('front.notFound')->with([
                    'message' => 'Bunday seriya raqamlik ' . $seria . $seria_number . ' sertifikat mavjud emas!'
                ]);
            }
        }
        // $student_certificat = Teacher::where('seria_number', $seria_number)->where('seria', $seria)->first();
        // $students = Student::where('seria_number', $seria_number)->where('seria', $seria)->first();


    }
}
