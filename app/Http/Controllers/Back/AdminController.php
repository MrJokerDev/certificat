<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherCertificat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $students = Teacher::query()
            ->where('full_name', 'LIKE', "%{$search}%")
            ->paginate(20);

        return view('back.teachers.index', compact('students'));
    }

    public function date_students(): JsonResponse
    {
        $teachers = Teacher::all();

        $certificats = [];
        foreach ($teachers as $teacher) {
            $teacherCertificate = TeacherCertificat::where('teacher_id', $teacher->id)->pluck('certificate_path')->first();
            $certificats[] = $teacherCertificate;
        }

        $teachersWithCertificates = [];
        foreach ($teachers as $key => $teacher) {
            $teachersWithCertificates[] = [
                'teacher' => $teacher,
                'certificate' => $certificats[$key],
            ];
        }

        return response()->json([
            'data' => $teachersWithCertificates
        ]);
    }

    public function show(Request $request, $id): View
    {
        $students = Student::find($id);

        return view('actions.show', compact('students'));
    }
}
