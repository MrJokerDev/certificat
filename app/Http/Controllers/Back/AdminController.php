<?php

namespace App\Http\Controllers\Back;

use App\Exports\AttendanceExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentCertificate;
use App\Models\Teacher;
use App\Models\TeacherCertificat;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $students = Teacher::query()
            ->where('full_name', 'LIKE', "%{$search}%")
            ->paginate(20);

        return view('back.teachers.index', compact('students'));
    }

    public function date_students()
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *                                                                                                                                   
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $students = Student::find($id);

        return view('actions.show', compact('students'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
