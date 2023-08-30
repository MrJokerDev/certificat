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
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Shape\Drawing;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PhpOffice\PhpPresentation\DocumentLayout;

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

            $student_fullName = str_replace(' ', '', $student->full_name);
            $student_fullName = str_replace("'", '', $student_fullName);

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
        $student_fullName = str_replace(' ', '', $student->full_name);
        $student_fullName = str_replace("'", '', $student_fullName);

        $dateS = date('d.m.Y', strtotime($student->date_of_issue));
        $presentation = new PhpPresentation();

        // Create slide 1
        $slide_001 = $presentation->getActiveSlide();

        $presentation->getLayout()->setDocumentLayout(['cx' => 26, 'cy' => 17], true)
            ->setCX(26, DocumentLayout::UNIT_CENTIMETER)
            ->setCY(17, DocumentLayout::UNIT_CENTIMETER);

        // Set the dimensions and offsets for slide 1 Student Full name
        $shape_001 = $slide_001->createRichTextShape();
        $shape_001->setHeight(600);
        $shape_001->setWidth(1000);
        $shape_001->setOffsetX(150);
        $shape_001->setOffsetY(250);

        // Set the dimensions and offsets for slide 1 Student Course
        $shape_001_1 = $slide_001->createRichTextShape();
        $shape_001_1->setHeight(600);
        $shape_001_1->setWidth(400);
        $shape_001_1->setOffsetX(370);
        $shape_001_1->setOffsetY(460);

        // Set the dimensions and offsets for slide 1 Student Data 
        $shape_001_2 = $slide_001->createRichTextShape();
        $shape_001_2->setHeight(600);
        $shape_001_2->setWidth(600);
        $shape_001_2->setOffsetX(422);
        $shape_001_2->setOffsetY(570);

        // Set the dimensions and offsets for slide 1 Student seria number
        $shape_001_3 = $slide_001->createRichTextShape();
        $shape_001_3->setHeight(600);
        $shape_001_3->setWidth(600);
        $shape_001_3->setOffsetX(263);
        $shape_001_3->setOffsetY(570);

        $shape_001_3_1 = $slide_001->createRichTextShape();
        $shape_001_3_1->setHeight(600);
        $shape_001_3_1->setWidth(600);
        $shape_001_3_1->setOffsetX(245);
        $shape_001_3_1->setOffsetY(570);

        // Set the background image for slide 1
        $background_001 = new Image();
        $background_001->setPath(storage_path('img/sertificat001.png'));
        $slide_001->setBackground($background_001);

        // Set the text for slide 1 
        $textRun_001 = $shape_001->createTextRun($student->full_name);
        $textRun_001->getFont()->setSize(40);
        $textRun_001->getFont()->setColor(new Color('FF5430CE'));

        $shape_001_1 = $shape_001_1->createTextRun($student->course);
        $shape_001_1->getFont()->setSize(35);
        $shape_001_1->getFont()->setColor(new Color('FF5430CE'));

        $shape_001_2 = $shape_001_2->createTextRun($dateS);
        $shape_001_2->getFont()->setSize(13);
        $shape_001_2->getFont()->setBold(true);

        $shape_001_3 = $shape_001_3->createTextRun($student->seria_number);
        $shape_001_3->getFont()->setSize(13);
        $shape_001_3->getFont()->setBold(true);

        $shape_001_4 = $shape_001_3_1->createTextRun($student->seria);
        $shape_001_4->getFont()->setSize(13);
        $shape_001_4->getFont()->setBold(true);
        // end the text for slide 1



        // Create slide 2
        $slide_002 = $presentation->createSlide();
        $getActiveSlide = $presentation->getActiveSlide();
        // Set the dimensions and offsets for slide 2 Student full name
        $shape_002 = $slide_002->createRichTextShape();
        $shape_002->setHeight(600);
        $shape_002->setWidth(1000);
        $shape_002->setOffsetX(150);
        $shape_002->setOffsetY(85);

        // Set the dimensions and offsets for slide 2 Student seria number
        $shape_002_1 = $slide_002->createRichTextShape();
        $shape_002_1->setHeight(600);
        $shape_002_1->setWidth(1000);
        $shape_002_1->setOffsetX(67);
        $shape_002_1->setOffsetY(32);

        $shape_002_1_2 = $slide_002->createRichTextShape();
        $shape_002_1_2->setHeight(600);
        $shape_002_1_2->setWidth(1000);
        $shape_002_1_2->setOffsetX(50);
        $shape_002_1_2->setOffsetY(32);

        // Set the dimensions and offsets for slide 2 Student Progress  0 top
        $shape_002_2 = $slide_002->createRichTextShape();
        $shape_002_2->setHeight(600);
        $shape_002_2->setWidth(270);
        $shape_002_2->setOffsetX(100);
        $shape_002_2->setOffsetY(350);

        // Set the dimensions and offsets for slide 2 Student Progress 1 top
        $shape_002_3 = $slide_002->createRichTextShape();
        $shape_002_3->setHeight(600);
        $shape_002_3->setWidth(270);
        $shape_002_3->setOffsetX(370);
        $shape_002_3->setOffsetY(350);

        // Set the dimensions and offsets for slide 2 Student Progress 3 top
        $shape_002_4 = $slide_002->createRichTextShape();
        $shape_002_4->setHeight(600);
        $shape_002_4->setWidth(270);
        $shape_002_4->setOffsetX(640);
        $shape_002_4->setOffsetY(350);

        // Set the dimensions and offsets for slide 2 Student Progress 4 bottom
        $shape_002_5 = $slide_002->createRichTextShape();
        $shape_002_5->setHeight(600);
        $shape_002_5->setWidth(290);
        $shape_002_5->setOffsetX(100);
        $shape_002_5->setOffsetY(405);

        // Set the dimensions and offsets for slide 2 Student Progress 5 bottom
        $shape_002_6 = $slide_002->createRichTextShape();
        $shape_002_6->setHeight(600);
        $shape_002_6->setWidth(260);
        $shape_002_6->setOffsetX(640);
        $shape_002_6->setOffsetY(405);

        // Set the dimensions and offsets for slide 2 Student QrCode 1
        $shape_002_7 = $slide_002->createRichTextShape();
        $shape_002_7->setHeight(600);
        $shape_002_7->setWidth(250);
        $shape_002_7->setOffsetX(50);
        $shape_002_7->setOffsetY(470);

        $shape_002_7_1 = $slide_002->createRichTextShape();
        $shape_002_7_1->setHeight(600);
        $shape_002_7_1->setWidth(250);
        $shape_002_7_1->setOffsetX(372);
        $shape_002_7_1->setOffsetY(510);

        // Set the dimensions and offsets for slide 2 Student QrCode 2
        $shape_002_8 = $slide_002->createRichTextShape();
        $shape_002_8->setHeight(600);
        $shape_002_8->setWidth(250);
        $shape_002_8->setOffsetX(525);
        $shape_002_8->setOffsetY(470);

        $shape_002_8_1 = $slide_002->createRichTextShape();
        $shape_002_8_1->setHeight(600);
        $shape_002_8_1->setWidth(250);
        $shape_002_8_1->setOffsetX(860);
        $shape_002_8_1->setOffsetY(510);

        $shape_002_9 = $slide_002->createRichTextShape();
        $shape_002_9->setHeight(600);
        $shape_002_9->setWidth(250);
        $shape_002_9->setOffsetX(840);
        $shape_002_9->setOffsetY(525);

        // Set the background image for slide 2
        $background_002 = new Image();
        $background_002->setPath(storage_path('img/sertificat002.png'));
        $slide_002->setBackground($background_002);

        // Set the text for slide 2 full_name
        $textRun_002 = $shape_002->createTextRun($student->full_name);
        $textRun_002->getFont()->setSize(40);
        $textRun_002->getFont()->setColor(new Color('FF5430CE'));

        // Set the text for slide 2 seria_number
        $shape_002_1_2 = $shape_002_1_2->createTextRun($student->seria);
        $shape_002_1_2->getFont()->setSize(13);
        $shape_002_1_2->getFont()->setBold(true);

        // Set the text for slide 2 seria_number
        $shape_002_1_3 = $shape_002_1->createTextRun($student->seria_number);
        $shape_002_1_3->getFont()->setSize(13);
        $shape_002_1_3->getFont()->setBold(true);

        // Set the text for slide 2 Progress 0
        $shape_002_22 = $shape_002_2->createTextRun('Preseason Web         100%'); // $student->progress_0
        $shape_002_22->getFont()->setSize(17);
        $shape_002_22->getFont()->setColor(new Color('FF5430CE'));
        $shape_002_22->getFont()->setBold(true);
        // Set the text for slide 2 Progress 0 title
        $shape_002_23 = $shape_002_2->createTextRun("  (HTML, CSS, Javascript) (1 oy/mo.)");
        $shape_002_23->getFont()->setSize(10);

        // Set the text for slide 2 Progress 1
        if ($student->progress_1) {
            $shape_002_33 = $shape_002_3->createTextRun($student->progress_1);
            $shape_002_33->getFont()->setSize(17);
            $shape_002_33->getFont()->setColor(new Color('FF5430CE'));
            $shape_002_33->getFont()->setBold(true);
            // Set the text for slide 2 Progress 1 title
            $shape_002_33 = $shape_002_3->createTextRun("   (C) (2 oy/mo.)");
            $shape_002_33->getFont()->setSize(10);
        }
        // Set the text for slide 2 Progress 2
        if ($student->progress_2) {
            $shape_002_34 = $shape_002_4->createTextRun($student->progress_2);
            $shape_002_34->getFont()->setSize(17);
            $shape_002_34->getFont()->setColor(new Color('FF5430CE'));
            $shape_002_34->getFont()->setBold(true);
            // Set the text for slide 2 Progress 2 title
            $shape_002_34 = $shape_002_4->createTextRun("    (Ruby, Javascript) (6 oy/mo.)");
            $shape_002_34->getFont()->setSize(10);
        }

        // Set the text for slide 2 Progress 2
        if ($student->progress_3) {
            $shape_002_35 = $shape_002_5->createTextRun($student->progress_3);
            $shape_002_35->getFont()->setSize(15);
            $shape_002_35->getFont()->setColor(new Color('FF5430CE'));
            $shape_002_35->getFont()->setBold(true);
            // Set the text for slide 2 Progress 2 title
            $shape_002_351 = $shape_002_5->createTextRun("    (Javascript) (2 oy/mo.)");
            $shape_002_351->getFont()->setSize(10);
            $shape_002_351->getFont()->setColor(new Color('FF5430CE'));
        }
        // Set the text for slide 2 Progress 2
        if ($student->progress_4) {
            $shape_002_36 = $shape_002_6->createTextRun($student->progress_4);
            $shape_002_36->getFont()->setSize(15);
            $shape_002_36->getFont()->setColor(new Color('FF5430CE'));
            $shape_002_36->getFont()->setBold(true);
            // Set the text for slide 2 Progress 2 title
            $shape_002_36 = $shape_002_6->createTextRun("      (Ruby, Python, Javascript, Java) (2 oy/mo.)");
            $shape_002_36->getFont()->setSize(10);
            $shape_002_36->getFont()->setColor(new Color('FF5430CE'));
        }

        // Set the text for slide 2 Progress 2
        // $student->sertificat_1, $student->sertificat_2
        if ($student->sertificat_1 == null) {
            $shape_002_72 = $shape_002_7->createTextRun('Tamomlamagan / Not completed                          ');
            $shape_002_72->getFont()->setSize(25);
            $shape_002_71 = $shape_002_7_1->createTextRun('X');
            $shape_002_71->getFont()->setSize(22);
        } else {
            // Start Advanced Frontend Development
            $studentCertificatName = $this->studentCertificatName($student->id);

            foreach ($studentCertificatName as $certificatName) {
                if ($certificatName->certificate_name == "Advanced Frontend Development") {
                    $shape_002_72 = $shape_002_7->createTextRun('Front End dasturlash tushunchalarini o\'z ichiga olgan 50 ta mashqni va 4 ta loihani "ReactJS"da yakunladi');
                    $shape_002_72->getFont()->setSize(15);
                    $shape_002_72->getFont()->setBold(true);
                } else if ($certificatName->certificate_name == "Associate Data Scientist") {
                    $shape_002_72 = $shape_002_7->createTextRun('Python dasturlash tilida Data Science asoslarini o\'z ichiga olgan 20 ta mashqni va 14 loyihani to\'liq yakunladi');
                    $shape_002_72->getFont()->setSize(15);
                    $shape_002_72->getFont()->setBold(true);
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
            $shape_002_73 = $shape_002_8->createTextRun('Tamomlamagan / Not completed                          ');
            $shape_002_73->getFont()->setSize(25);
            $shape_002_74 = $shape_002_8_1->createTextRun('X');
            $shape_002_74->getFont()->setSize(22);
        } else {
            // Start Advanced Backend Development
            $studentCertificatName = $this->studentCertificatName($student->id);

            foreach ($studentCertificatName as $certificatName) {
                if ($certificatName->certificate_name == "Advanced Backend Development") {
                    $shape_002_73 = $shape_002_8->createTextRun('Backend dasturlash tushunchalarini o\'z ichiga olgan 4 ta loyihani to\'liq yakunladi');
                    $shape_002_73->getFont()->setSize(15);
                    $shape_002_73->getFont()->setBold(true);
                } else if ($certificatName->certificate_name == "Associate Data Scientist") {
                    $shape_002_73 = $shape_002_8->createTextRun('Python dasturlash tilida Data Science asoslarini o\'z ichiga olgan 20 ta mashqni va 14 loyihani to\'liq yakunladi');
                    $shape_002_73->getFont()->setSize(15);
                    $shape_002_73->getFont()->setBold(true);
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

        // Set the alignment for slide 1
        $paragraph_001 = $shape_001->getActiveParagraph();
        $paragraph_001->setAlignment(new Alignment(Alignment::HORIZONTAL_RIGHT, Alignment::VERTICAL_AUTO));

        // Set the alignment for slide 2
        $paragraph_002 = $shape_002->getActiveParagraph();
        $paragraph_002->setAlignment(new Alignment(Alignment::HORIZONTAL_RIGHT, Alignment::VERTICAL_AUTO));

        return $presentation;
    }

    public function download($id)
    {
        $student = Student::where('id', $id)->first();

        $presentation = $this->generatePresentation($student);
        $student_fullName =  str_replace(' ', '', $student->full_name);
        $student_fullName = str_replace("'", '', $student_fullName);
        $fileName = $student_fullName . '.pptx';

        $filePath = 'certificats/students/' . $fileName;

        $writer = IOFactory::createWriter($presentation, 'PowerPoint2007');
        $tempFilePath = tempnam(sys_get_temp_dir(), 'pptx');
        $writer->save($tempFilePath);

        Storage::disk('public')->put($filePath, file_get_contents($tempFilePath));

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath, $fileName);
        }

        return redirect()->route('students.index');
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
