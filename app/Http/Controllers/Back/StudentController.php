<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Jobs\QwasarParseJob;
use App\Models\FailedJobs;
use App\Models\Job;
use App\Models\Student;
use App\Models\StudentCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
//pptx file
use Goutte\Client;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Slide\Background\Image;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Shape\Drawing;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\Style\Alignment;

class StudentController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $students_db = Student::search($request->search)->paginate(20);

        $job_error = FailedJobs::all();
        $jobs = Job::all();

        foreach ($students_db as $student) {
            $qrcode = $this->qrCodeGenerateStudent($student->sertificat_1, $student->sertificat_2);

            $student_fullName = str_replace(["'", " ", "`", "?", ",", "!", "@", "#", "$", "%", "^", "&", "*", "."], '', $student->full_name);


            if (!Storage::exists('qrcode/students/1/' . $student_fullName . '.png') || !Storage::exists('qrcode/students/2/' . $student_fullName . '.png')) {

                if ($qrcode['sertificat_1'] !== "X") {
                    $qrcode_1 = 'qrcode/students/1/' . $student_fullName . '.png';
                    Storage::disk('local')->put($qrcode_1, $qrcode['sertificat_1']);
                }
                if ($qrcode['sertificat_2'] !== "X") {
                    $qrcode_2 = 'qrcode/students/2/' . $student_fullName . '.png';
                    Storage::disk('local')->put($qrcode_2, $qrcode['sertificat_2']);
                }
            }
        }

        return view('back.students.index', compact('students_db', 'job_error', 'jobs'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);

        $file = $request->file('file');

        $data = Excel::toArray(new StudentsImport(), $file, null, \Maatwebsite\Excel\Excel::XLSX);

        $dataArray = $data[0];
        $students = [];

        foreach ($dataArray as $data) {
            $students[] = $data[0];
        }

        dispatch(new QwasarParseJob($students));

        return redirect()->route('students.index');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $client = new Client();

        $student = Student::find($id);
        $studentCertificat = StudentCertificate::where('student_id', $id)->get();

        return view('back.students.show', compact('student', 'studentCertificat'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Student $student)
    {
        $studentCertificat = StudentCertificate::where('student_id', $student->id)->get();
        return view('back.students.edit', compact('student', 'studentCertificat'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        $course = str_replace('_', ' ', $request->course);
        $student->update(array_merge($request->all(), ['course' => $course]));

        return redirect()->route('students.index')->with('success', 'Student updated successfully');
    }

    protected function studentCertificatName($studet_id)
    {
        $studentCertificatName = StudentCertificate::where('student_id', $studet_id)->get();
        return $studentCertificatName;
    }

    protected function qrCodeGenerateStudent($link1, $link2)
    {
        if ($link1 == null) {
            $sertificat_1_qrcode = "X";
        } else {
            $sertificat_1_qrcode = QrCode::format('png')
                ->size(200)
                ->generate(
                    $link1
                );
        }

        if ($link2 == null) {
            $sertificat_2_qrcode = "X";
        } else {
            $sertificat_2_qrcode = QrCode::format('png')
                ->size(200)
                ->generate(
                    $link2
                );
        }
        return [
            'sertificat_1' => $sertificat_1_qrcode,
            'sertificat_2' => $sertificat_2_qrcode,
        ];
    }

    protected function generatePresentation($student)
    {
        $student_fullName = str_replace(["'", " ", "`", "?", ",", "!", "@", "#", "$", "%", "^", "&", "*", "."], '', $student->full_name);

        $dateS = date('d.m.Y', strtotime($student->date_of_issue));
        $presentation = new PhpPresentation();

        // Create slide 1
        $slide_001 = $presentation->getActiveSlide();

        $presentation->getLayout()->setDocumentLayout(['cx' => 26, 'cy' => 17], true)
            ->setCX(26, DocumentLayout::UNIT_CENTIMETER)
            ->setCY(17, DocumentLayout::UNIT_CENTIMETER);

        // Set the dimensions and offsets for slide 1 Student Full name
        $slide_full_name = $slide_001->createRichTextShape();
        $slide_full_name->setHeight(100);
        $slide_full_name->setWidth(600);
        $slide_full_name->setOffsetX(200);
        $slide_full_name->setOffsetY(270);

        // Set the dimensions and offsets for slide 1 Student Course
        $slide_course = $slide_001->createRichTextShape();
        $slide_course->setHeight(100);
        $slide_course->setWidth(420);
        $slide_course->setOffsetX(290);
        $slide_course->setOffsetY(475);

        // Set the dimensions and offsets for slide 1 Student Data
        $slide_dateS = $slide_001->createRichTextShape();
        $slide_dateS->setHeight(600);
        $slide_dateS->setWidth(600);
        $slide_dateS->setOffsetX(422);
        $slide_dateS->setOffsetY(570);

        // Set the dimensions and offsets for slide 1 Student seria number
        $slide_seria_number = $slide_001->createRichTextShape();
        $slide_seria_number->setHeight(600);
        $slide_seria_number->setWidth(600);
        $slide_seria_number->setOffsetX(261);
        $slide_seria_number->setOffsetY(570);

        $slide_seria = $slide_001->createRichTextShape();
        $slide_seria->setHeight(600);
        $slide_seria->setWidth(600);
        $slide_seria->setOffsetX(245);
        $slide_seria->setOffsetY(570);

        $alignment = new Alignment();
        $alignment->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $slide_course->getActiveParagraph()->setAlignment($alignment);
        $slide_full_name->getActiveParagraph()->setAlignment($alignment);

        // Set the background image for slide 1
        $background_001 = new Image();
        $background_001->setPath(storage_path('img/sertificat001.png'));
        $slide_001->setBackground($background_001);

        // Set the text for slide 1
        $slide_full_name = $slide_full_name->createTextRun($student->full_name);
        $slide_full_name->getFont()->setSize(28);
        $slide_full_name->getFont()->setColor(new Color('FF5430CE'));
        $slide_full_name->getFont()->setBold(true);

        $slide_course = $slide_course->createTextRun($student->course);
        $slide_course->getFont()->setSize(28);
        $slide_course->getFont()->setColor(new Color('FF5430CE'));
        $slide_course->getFont()->setBold(true);


        $slide_dateS = $slide_dateS->createTextRun($dateS);
        $slide_dateS->getFont()->setSize(12);
        $slide_dateS->getFont()->setBold(true);

        $slide_seria_number = $slide_seria_number->createTextRun($student->seria_number);
        $slide_seria_number->getFont()->setSize(12);
        $slide_seria_number->getFont()->setBold(true);

        $slide_seria = $slide_seria->createTextRun($student->seria);
        $slide_seria->getFont()->setSize(12);
        $slide_seria->getFont()->setBold(true);
        // end the text for slide 1





        // Create slide 2
        $slide_002 = $presentation->createSlide();
        // Set the dimensions and offsets for slide 2 Student full name
        $full_name = $slide_002->createRichTextShape();
        $full_name->setHeight(50);
        $full_name->setWidth(600);
        $full_name->setOffsetX(200);
        $full_name->setOffsetY(95);

        // Set the dimensions and offsets for slide 2 Student seria number
        $seria_number = $slide_002->createRichTextShape();
        $seria_number->setHeight(20);
        $seria_number->setWidth(100);
        $seria_number->setOffsetX(65);
        $seria_number->setOffsetY(32);

        $seria = $slide_002->createRichTextShape();
        $seria->setHeight(20);
        $seria->setWidth(100);
        $seria->setOffsetX(50);
        $seria->setOffsetY(32);

        // Set the dimensions and offsets for slide 2 Student QrCode 1
        $completed = $slide_002->createRichTextShape();
        $completed->setHeight(100);
        $completed->setWidth(250);
        $completed->setOffsetX(50);
        $completed->setOffsetY(470);

        $not_completed = $slide_002->createRichTextShape();
        $not_completed->setHeight(100);
        $not_completed->setWidth(250);
        $not_completed->setOffsetX(50);
        $not_completed->setOffsetY(470);

        $not_completed_x = $slide_002->createRichTextShape();
        $not_completed_x->setHeight(600);
        $not_completed_x->setWidth(250);
        $not_completed_x->setOffsetX(372);
        $not_completed_x->setOffsetY(510);


        // Set the dimensions and offsets for slide 2 Student QrCode 2
        $completed_2 = $slide_002->createRichTextShape();
        $completed_2->setHeight(100);
        $completed_2->setWidth(250);
        $completed_2->setOffsetX(525);
        $completed_2->setOffsetY(470);

        $not_completed_2 = $slide_002->createRichTextShape();
        $not_completed_2->setHeight(100);
        $not_completed_2->setWidth(250);
        $not_completed_2->setOffsetX(525);
        $not_completed_2->setOffsetY(470);

        $not_completed_2_x = $slide_002->createRichTextShape();
        $not_completed_2_x->setHeight(600);
        $not_completed_2_x->setWidth(250);
        $not_completed_2_x->setOffsetX(860);
        $not_completed_2_x->setOffsetY(510);

        $full_name->getActiveParagraph()->setAlignment($alignment);

        // Set the text for slide 2 full_name
        $full_name = $full_name->createTextRun($student->full_name);
        $full_name->getFont()->setSize(28);
        $full_name->getFont()->setColor(new Color('FF5430CE'));

        $seria = $seria->createTextRun($student->seria);
        $seria->getFont()->setSize(12);
        $seria->getFont()->setBold(true);

        $seria_number = $seria_number->createTextRun($student->seria_number);
        $seria_number->getFont()->setSize(12);
        $seria_number->getFont()->setBold(true);

        // Set the background image for slide 2
        if ($student->course == "DATA SCIENCE") {

            // Set the text for slide 2 Progress 0
            $ds_progress_0 = $slide_002->createRichTextShape();
            $ds_progress_0->setHeight(50);
            $ds_progress_0->setWidth(100);
            $ds_progress_0->setOffsetX(350);
            $ds_progress_0->setOffsetY(360);

            // Set the text for slide 2 Progress 1
            $ds_progress_1 = $slide_002->createRichTextShape();
            $ds_progress_1->setHeight(50);
            $ds_progress_1->setWidth(100);
            $ds_progress_1->setOffsetX(850);
            $ds_progress_1->setOffsetY(360);

            // Set the text for slide 2 Progress 2
            $ds_progress_2 = $slide_002->createRichTextShape();
            $ds_progress_2->setHeight(50);
            $ds_progress_2->setWidth(100);
            $ds_progress_2->setOffsetX(350);
            $ds_progress_2->setOffsetY(415);

            // Set the text for slide 2 Progress 3
            $ds_progress_3 = $slide_002->createRichTextShape();
            $ds_progress_3->setHeight(50);
            $ds_progress_3->setWidth(100);
            $ds_progress_3->setOffsetX(850);
            $ds_progress_3->setOffsetY(415);

            $background_002 = new Image();
            $background_002->setPath(storage_path('img/DATA_SCIENCE_2.png'));
            $slide_002->setBackground($background_002);

            // Set the text for slide 2 Progress 0
            $progress_0 = substr($student->progress_0, -4);
            $ds_progress_0 = $ds_progress_0->createTextRun($progress_0);
            $ds_progress_0->getFont()->setSize(14);
            $ds_progress_0->getFont()->setBold(true);

            // Set the text for slide 2 Progress 1
            if ($student->progress_1) {
                $progress_1 = substr($student->progress_1, -4);
                $ds_progress_1 = $ds_progress_1->createTextRun($progress_1);
                $ds_progress_1->getFont()->setSize(14);
                $ds_progress_1->getFont()->setBold(true);
            } else {
                $progress_1 = substr($student->progress_1, -4);
                $ds_progress_1 = $ds_progress_1->createTextRun("0%");
                $ds_progress_1->getFont()->setSize(14);
                $ds_progress_1->getFont()->setBold(true);
            }
            // Set the text for slide 2 Progress 2
            if ($student->progress_2) {
                $progress_2 = substr($student->progress_2, -4);
                $ds_progress_2 = $ds_progress_2->createTextRun($progress_2);
                $ds_progress_2->getFont()->setSize(14);
                $ds_progress_2->getFont()->setBold(true);
            } else {
                $ds_progress_2 = $ds_progress_2->createTextRun("0%");
                $ds_progress_2->getFont()->setSize(14);
                $ds_progress_2->getFont()->setBold(true);
            }

            // Set the text for slide 2 Progress 2
            if ($student->progress_3) {
                $progress_3 = substr($student->progress_3, -4);
                $ds_progress_3 = $ds_progress_3->createTextRun($progress_3);
                $ds_progress_3->getFont()->setSize(14);
                $ds_progress_3->getFont()->setBold(true);
            } else {
                $ds_progress_3 = $ds_progress_3->createTextRun("0%");
                $ds_progress_3->getFont()->setSize(14);
                $ds_progress_3->getFont()->setBold(true);
            }
        } else if ($student->course == "SOFTWARE ENGINEERING") {

            // Set the text for slide 2 Progress 0
            $se_progress_0 = $slide_002->createRichTextShape();
            $se_progress_0->setHeight(50);
            $se_progress_0->setWidth(100);
            $se_progress_0->setOffsetX(300);
            $se_progress_0->setOffsetY(360);

            // Set the text for slide 2 Progress 1
            $se_progress_1 = $slide_002->createRichTextShape();
            $se_progress_1->setHeight(50);
            $se_progress_1->setWidth(100);
            $se_progress_1->setOffsetX(560);
            $se_progress_1->setOffsetY(360);

            // Set the text for slide 2 Progress 2
            $se_progress_2 = $slide_002->createRichTextShape();
            $se_progress_2->setHeight(50);
            $se_progress_2->setWidth(100);
            $se_progress_2->setOffsetX(850);
            $se_progress_2->setOffsetY(360);

            // Set the text for slide 2 Progress 3
            $se_progress_3 = $slide_002->createRichTextShape();
            $se_progress_3->setHeight(50);
            $se_progress_3->setWidth(100);
            $se_progress_3->setOffsetX(350);
            $se_progress_3->setOffsetY(415);

            // Set the text for slide 2 Progress 4
            $se_progress_4 = $slide_002->createRichTextShape();
            $se_progress_4->setHeight(50);
            $se_progress_4->setWidth(100);
            $se_progress_4->setOffsetX(840);
            $se_progress_4->setOffsetY(420);

            $background_002 = new Image();
            $background_002->setPath(storage_path('img/SOFTWARE_ENGINEERING_2.png'));
            $slide_002->setBackground($background_002);

            // Set the text for slide 2 Progress 0
            $progress_0 = substr($student->progress_0, -4);
            $shape_002_22 = $se_progress_0->createTextRun($progress_0); // $student->progress_0
            $shape_002_22->getFont()->setSize(14);
            $shape_002_22->getFont()->setBold(true);

            // Set the text for slide 2 Progress 1
            if ($student->progress_1) {
                $progress_1 = substr($student->progress_1, -4);
                $se_progress_1 = $se_progress_1->createTextRun($progress_1);
                $se_progress_1->getFont()->setSize(14);
                $se_progress_1->getFont()->setBold(true);
            } else {
                $se_progress_1 = $se_progress_1->createTextRun("0%");
                $se_progress_1->getFont()->setSize(14);
                $se_progress_1->getFont()->setBold(true);
            }
            // Set the text for slide 2 Progress 2
            if ($student->progress_2) {
                $progress_2 = substr($student->progress_2, -4);
                $se_progress_2 = $se_progress_2->createTextRun($progress_2);
                $se_progress_2->getFont()->setSize(14);
                $se_progress_2->getFont()->setBold(true);
            } else {
                $se_progress_2 = $se_progress_2->createTextRun("0%");
                $se_progress_2->getFont()->setSize(14);
                $se_progress_2->getFont()->setBold(true);
            }

            // Set the text for slide 2 Progress 2
            if ($student->progress_3) {
                $progress_3 = substr($student->progress_3, -4);
                $se_progress_3 = $se_progress_3->createTextRun($progress_3);
                $se_progress_3->getFont()->setSize(14);
                $se_progress_3->getFont()->setBold(true);
            } else {
                $se_progress_3 = $se_progress_3->createTextRun("0%");
                $se_progress_3->getFont()->setSize(14);
                $se_progress_3->getFont()->setBold(true);
            }
            // Set the text for slide 2 Progress 2
            if ($student->progress_4) {
                $progress_4 = substr($student->progress_4, -4);
                $se_progress_4 = $se_progress_4->createTextRun($progress_4);
                $se_progress_4->getFont()->setSize(14);
                $se_progress_4->getFont()->setBold(true);
            } else {
                $se_progress_4 = $se_progress_4->createTextRun("0%");
                $se_progress_4->getFont()->setSize(14);
                $se_progress_4->getFont()->setBold(true);
            }
        } else if ($student->course == "FULL STACK DEVELOPER") {

            // Set the text for slide 2 Progress 0
            $fs_progress_0 = $slide_002->createRichTextShape();
            $fs_progress_0->setHeight(50);
            $fs_progress_0->setWidth(100);
            $fs_progress_0->setOffsetX(300);
            $fs_progress_0->setOffsetY(360);

            // Set the text for slide 2 Progress 1
            $fs_progress_1 = $slide_002->createRichTextShape();
            $fs_progress_1->setHeight(50);
            $fs_progress_1->setWidth(100);
            $fs_progress_1->setOffsetX(560);
            $fs_progress_1->setOffsetY(360);

            // Set the text for slide 2 Progress 2
            $fs_progress_2 = $slide_002->createRichTextShape();
            $fs_progress_2->setHeight(50);
            $fs_progress_2->setWidth(100);
            $fs_progress_2->setOffsetX(850);
            $fs_progress_2->setOffsetY(360);

            // Set the text for slide 2 Progress 4
            $fs_progress_3 = $slide_002->createRichTextShape();
            $fs_progress_3->setHeight(50);
            $fs_progress_3->setWidth(100);
            $fs_progress_3->setOffsetX(350);
            $fs_progress_3->setOffsetY(415);

            // Set the text for slide 2 Progress 5
            $fs_progress_4 = $slide_002->createRichTextShape();
            $fs_progress_4->setHeight(50);
            $fs_progress_4->setWidth(100);
            $fs_progress_4->setOffsetX(840);
            $fs_progress_4->setOffsetY(420);

            $background_002 = new Image();
            $background_002->setPath(storage_path('img/FULLSTACK_DEVELOPER_2.png'));
            $slide_002->setBackground($background_002);

            // Set the text for slide 2 Progress 0
            $progress_0 = substr($student->progress_0, -4);
            $fs_progress_0 = $fs_progress_0->createTextRun($progress_0); // $student->progress_0
            $fs_progress_0->getFont()->setSize(14);
            $fs_progress_0->getFont()->setBold(true);

            // Set the text for slide 2 Progress 1
            if ($student->progress_1) {
                $progress_1 = substr($student->progress_1, -4);
                $fs_progress_1 = $fs_progress_1->createTextRun($progress_1);
                $fs_progress_1->getFont()->setSize(14);
                $fs_progress_1->getFont()->setBold(true);
            } else {
                $fs_progress_1 = $fs_progress_1->createTextRun("0%");
                $fs_progress_1->getFont()->setSize(14);
                $fs_progress_1->getFont()->setBold(true);
            }
            // Set the text for slide 2 Progress 2
            if ($student->progress_2) {
                $progress_2 = substr($student->progress_2, -4);
                $fs_progress_2 = $fs_progress_2->createTextRun($progress_2);
                $fs_progress_2->getFont()->setSize(14);
                $fs_progress_2->getFont()->setBold(true);
            } else {
                $fs_progress_2 = $fs_progress_2->createTextRun("0%");
                $fs_progress_2->getFont()->setSize(14);
                $fs_progress_2->getFont()->setBold(true);
            }

            // Set the text for slide 2 Progress 2
            if ($student->progress_3) {
                $progress_3 = substr($student->progress_3, -4);
                $fs_progress_3 = $fs_progress_3->createTextRun($progress_3);
                $fs_progress_3->getFont()->setSize(14);
                $fs_progress_3->getFont()->setBold(true);
            } else {
                $fs_progress_3 = $fs_progress_3->createTextRun("0%");
                $fs_progress_3->getFont()->setSize(14);
                $fs_progress_3->getFont()->setBold(true);
            }
            // Set the text for slide 2 Progress 2
            if ($student->progress_4) {
                $progress_4 = substr($student->progress_4, -4);
                $fs_progress_4 = $fs_progress_4->createTextRun($progress_4);
                $fs_progress_4->getFont()->setSize(14);
                $fs_progress_4->getFont()->setBold(true);
            } else {
                $fs_progress_4 = $fs_progress_4->createTextRun("0%");
                $fs_progress_4->getFont()->setSize(14);
                $fs_progress_4->getFont()->setBold(true);
            }
        }

        // Set the text for slide 2 Progress 2
        if ($student->sertificat_1 == null) {
            $not_completed = $not_completed->createTextRun('Tamomlamagan / Not completed');
            $not_completed->getFont()->setSize(14);
            $not_completed->getFont()->setBold(true);

            $not_completed_x = $not_completed_x->createTextRun('X');
            $not_completed_x->getFont()->setSize(20);
            $not_completed_x->getFont()->setBold(true);
        } else {
            // Start Advanced Frontend Development
            $studentCertificatName = $this->studentCertificatName($student->id);

            foreach ($studentCertificatName as $certificatName) {
                if ($certificatName->certificate_name == "Advanced Frontend Development") {
                    $completed = $completed->createTextRun('Front End dasturlash tushunchalarini o\'z ichiga olgan 50 ta mashqni va 4 ta loihani "ReactJS"da yakunladi');
                    $completed->getFont()->setSize(14);
                    $completed->getFont()->setBold(true);
                } else if ($certificatName->certificate_name == "Associate Data Scientist") {
                    $completed = $completed->createTextRun('Python dasturlash tilida Data Science asoslarini o\'z ichiga olgan 20 ta mashqni va 14 loyihani to\'liq yakunladi');
                    $completed->getFont()->setSize(14);
                    $completed->getFont()->setBold(true);
                }
            }
            // End Advanced Frontend Development

            $qrCodeImg = new Drawing\File;
            $qrCodeImg->setPath(storage_path('app/qrcode/students/1/' . $student_fullName . '.png'))
                ->setWidth(110)
                ->setOffsetX(333)
                ->setOffsetY(475);
            $slide_002->addShape($qrCodeImg);
        }

        if ($student->sertificat_2 == null) {
            $not_completed_2 = $not_completed_2->createTextRun('Tamomlamagan / Not completed                          ');
            $not_completed_2->getFont()->setSize(14);
            $not_completed_2->getFont()->setBold(true);
            $not_completed_2_x = $not_completed_2_x->createTextRun('X');
            $not_completed_2_x->getFont()->setSize(20);
            $not_completed_2_x->getFont()->setBold(true);
        } else {
            // Start Advanced Backend Development
            $studentCertificatName = $this->studentCertificatName($student->id);

            foreach ($studentCertificatName as $certificatName) {
                if ($certificatName->certificate_name == "Advanced Backend Development") {
                    $completed_2 = $completed_2->createTextRun('Backend dasturlash tushunchalarini o\'z ichiga olgan 4 ta loyihani to\'liq yakunladi');
                    $completed_2->getFont()->setSize(14);
                    $completed_2->getFont()->setBold(true);
                } else if ($certificatName->certificate_name == "Associate Data Scientist") {
                    $completed_2 = $completed_2->createTextRun('Python dasturlash tilida Data Science asoslarini o\'z ichiga olgan 20 ta mashqni va 14 loyihani to\'liq yakunladi');
                    $completed_2->getFont()->setSize(14);
                    $completed_2->getFont()->setBold(true);
                }
            }
            // End Advanced Backend Development
            $qrCodeImg = new Drawing\File;
            $qrCodeImg->setPath(storage_path('app/qrcode/students/2/' . $student_fullName . '.png'))
                ->setWidth(110)
                ->setOffsetX(823)
                ->setOffsetY(475);
            $slide_002->addShape($qrCodeImg);
        }

        return $presentation;
    }

    public function download($id)
    {
        $student = Student::where('id', $id)->first();

        $presentation = $this->generatePresentation($student);
        $student_fullName =  str_replace(["'", " ", "`", "?", ",", "!", "@", "#", "$", "%", "^", "&", "*", "."], '', $student->full_name);

        $fileName = $student_fullName . '.pptx';

        $filePath = 'certificats/students/' . $fileName;

        $writer = IOFactory::createWriter($presentation, 'PowerPoint2007');
        $tempFilePath = tempnam(sys_get_temp_dir(), 'pptx');
        $writer->save($tempFilePath);

        $fileContents = file_get_contents($tempFilePath);

        if (Storage::disk('public')->exists($filePath)) {
            // Update the existing file
            Storage::disk('public')->put($filePath, $fileContents);
        } else {
            // Create a new file
            Storage::disk('public')->put($filePath, $fileContents);
        }

        return Storage::disk('public')->download($filePath, $fileName);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        $studentCertificat = StudentCertificate::where('student_id', $student->id)->first();
        $student = Student::where('id', $student->id)->first();

        $student_fullName = str_replace(' ', '', $student->fill_name);
        $student_fullName = str_replace("'", '', $student_fullName);
        Storage::delete('public/sertificat/' . $student_fullName . '.pptx');
        Storage::delete('qrcode/students/1/' . $student_fullName . '.png');
        Storage::delete('qrcode/students/2/' . $student_fullName . '.png');
        if ($studentCertificat !== null) {
            $studentCertificat->delete();
        }
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student has been deleted successfully');
    }
}
