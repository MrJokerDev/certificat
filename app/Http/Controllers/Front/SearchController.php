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

        return response()->json([
            'students' => $teachers
        ]);
    }

    public function show($seria_number)
    {
        $seria = substr($seria_number, 0, 2);
        $seria_number = substr($seria_number, 2, strlen($seria_number));

        $student_certificat = Teacher::where('seria_number', $seria_number)->where('seria', $seria)->first();

        if ($student_certificat) {
            $registered_at = Carbon::parse($student_certificat->berilgan_sana);
            $final_date = $registered_at->addYear(2)->format('d.m.Y');

            return view('front.show', compact('student_certificat', 'final_date'));
        } else {
            return view('front.notFound')->with([
                'message' => 'Bunday seria raqamlik ' . $seria . $seria_number . ' sertifikat mavjud emas!'
            ]);
        }
    }
}
