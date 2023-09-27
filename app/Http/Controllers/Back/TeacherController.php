<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Imports\TeachersImport;
use App\Models\Teacher;
use App\Models\TeacherCertificat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\Drawing\File;
use PhpOffice\PhpPresentation\Slide\Background\Image;
use PhpOffice\PhpPresentation\Style\Color;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\Style\Alignment;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $teachers = Teacher::search($request->search)->paginate(20);

        $certificats = [];

        foreach ($teachers as $teacher) {
            $qrcode = $this->qrCodeGenerateStudent(env('APP_URL') . "/student/" . $teacher->seria . $teacher->seria_number);

            $fullName = $teacher->ism . ' ' . $teacher->familiya . ' ' . $teacher->sharif;
            $teacher_fullName = str_replace(["'", " ", "`", "?", ",", "!", "@", "#", "$", "%", "^", "&", "*", "."], '', $fullName);

            if (!Storage::exists('qrcode/teachers/' . $teacher_fullName . '.png')) {
                $qrcode_img = 'qrcode/teachers/' . $teacher_fullName . '.png';
                Storage::disk('local')->put($qrcode_img, $qrcode['sertificat']);
            }
        }

        return view('back.teachers.index', compact('teachers'));
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

        $data = Excel::toArray(new TeachersImport(), $file, null, \Maatwebsite\Excel\Excel::XLSX);

        $nextSeriaNumber = Teacher::max('seria_number'); // Get the highest existing seria_number

        // $nextSeriaNumber = str_pad(intval($nextSeriaNumber) + 1, 7, '0', STR_PAD_LEFT); // Increment and format
        // dd($data);
        foreach ($data as $rowData) {
            foreach ($rowData as $row) {
                $existingStudent = Teacher::where('ism', $row['ism'])->where('familiya', $row['familiya'])->first();

                if (!$existingStudent && $row != null) {
                    $nextSeriaNumber = str_pad(intval($nextSeriaNumber) + 1, 7, '0', STR_PAD_LEFT);
                    Teacher::create([
                        'seria' => 'MK',
                        'seria_number' => $nextSeriaNumber,
                        'ism' => $row['ism'],
                        'familiya' => $row['familiya'],
                        'sharif' => $row['sharif'],
                        'berilgan_sana' => Date::excelToDateTimeObject($row["berilgan_sana"])->format('d.m.Y'),
                        'umumiy' => $row['umumiy'],
                        'umumiy_ball' => $row['umumiy_ball'],
                        'modul_1' => $row['1_modul'],
                        'modul_ball_1' => $row['1_modul_ball'],
                        'modul_2' => $row['2_modul'],
                        'modul_ball_2' => $row['2_modul_ball'],
                        'modul_3' => $row['3_modul'],
                        'modul_ball_3' => $row['3_modul_ball']
                    ]);
                }
            }
        }

        return redirect()->route('teachers.index')->with('success', 'Teachers imported successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $teacher = Teacher::find($id);
        $teacherCertificate = TeacherCertificat::where('teacher_id', $teacher->id)->pluck('certificate_path')->first();

        $certificate = storage_path('app/public/' . $teacherCertificate);

        if (!Storage::exists('public/' . $teacherCertificate)) {
            abort(404);
        }

        return view('back.teachers.show', compact('teacher', 'teacherCertificate'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Teacher $teacher)
    {
        return view('back.teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Teacher $teacher)
    {
        $teacher->update($request->all());
        return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Teacher $teacher)
    {
        $teacherCertificat = TeacherCertificat::where('teacher_id', $teacher->id)->first();
        $teacher = Teacher::where('id', $teacher->id)->first();

        $CertificatfullName = $teacher->ism . ' ' . $teacher->familiya . ' ' . $teacher->sharif;
        $teacher_fullName = str_replace(' ', '', $CertificatfullName);
        $teacher_fullName = str_replace("'", '', $teacher_fullName);
        Storage::delete('public/sertificat/' . $teacher_fullName . '.pptx');
        Storage::delete('qrcode/teachers/' . $teacher_fullName . '.png');

        if ($teacherCertificat !== null) {
            $teacherCertificat->delete();
        }
        $teacher->delete();


        return redirect()->route('teachers.index')->with('success', 'Teacher has been deleted successfully');
    }

    // protected function studentCertificatName($teacher_id)
    // {
    //     $teacherCertificatName = TeacherCertificat::where('teacher_id', $teacher_id)->get();
    //     return $teacherCertificatName;
    // }

    protected function qrCodeGenerateStudent($link)
    {
        $sertificat_qrcode = QrCode::format('png')
            ->size(200)
            ->generate(
                $link
            );

        return [
            'sertificat' => $sertificat_qrcode,
        ];
    }

    protected function generatePresentation($student)
    {
        $fullName = $student->ism . ' ' . $student->familiya . ' ' . $student->sharif;

        $CertificatfullName = $student->ism . ' ' . $student->familiya . ' ' . $student->sharif;
        $student_fullName = str_replace(' ', '', $CertificatfullName);
        $student_fullName = str_replace("'", '', $student_fullName);

        $registered_at = Carbon::parse($student->berilgan_sana);
        $final_date = $registered_at->addYear(2)->format('d.m.Y');

        $presentation = new PhpPresentation();

        $presentation->getLayout()->setDocumentLayout(['cx' => 26, 'cy' => 17], true)
            ->setCX(26, DocumentLayout::UNIT_CENTIMETER)
            ->setCY(17, DocumentLayout::UNIT_CENTIMETER);

        // Create slide 1
        $slide_001 = $presentation->getActiveSlide();

        // Set the dimensions and offsets for slide 1 Student Full name
        $shape_001 = $slide_001->createRichTextShape();
        $shape_001->setHeight(100);
        $shape_001->setWidth(900);
        $shape_001->setOffsetX(40);
        $shape_001->setOffsetY(270);

        // Set the dimensions and offsets for slide 1 Student Data 
        $shape_001_2 = $slide_001->createRichTextShape();
        $shape_001_2->setHeight(25);
        $shape_001_2->setWidth(100);
        $shape_001_2->setOffsetX(370);
        $shape_001_2->setOffsetY(570);

        $shape_001_2_1 = $slide_001->createRichTextShape();
        $shape_001_2_1->setHeight(25);
        $shape_001_2_1->setWidth(100);
        $shape_001_2_1->setOffsetX(483);
        $shape_001_2_1->setOffsetY(570);

        // Set the dimensions and offsets for slide 1 Student seria number
        $shape_001_3 = $slide_001->createRichTextShape();
        $shape_001_3->setHeight(25);
        $shape_001_3->setWidth(100);
        $shape_001_3->setOffsetX(268);
        $shape_001_3->setOffsetY(570);

        $shape_001_3_1 = $slide_001->createRichTextShape();
        $shape_001_3_1->setHeight(25);
        $shape_001_3_1->setWidth(100);
        $shape_001_3_1->setOffsetX(245);
        $shape_001_3_1->setOffsetY(570);

        // Set the background image for slide 1
        $background_001 = new Image();
        $background_001->setPath(storage_path('img/sertificat005.png'));
        $slide_001->setBackground($background_001);

        $alignment = new Alignment();
        $alignment->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $shape_001->getActiveParagraph()->setAlignment($alignment);

        // Set the text for slide 1 
        $textRun_001 = $shape_001->createTextRun(strtoupper($fullName));
        $textRun_001->getFont('Gilroy');
        $textRun_001->getFont()->setColor(new Color('FF5430CE'));
        $textRun_001->getFont()->setSize(30);
        $textRun_001->getFont()->setBold(true);


        $shape_001_2 = $shape_001_2->createTextRun($student->berilgan_sana);
        $shape_001_2->getFont()->setSize(12);
        $shape_001_2->getFont()->setBold(true);

        $shape_002_2 = $shape_001_2_1->createTextRun($final_date);
        $shape_002_2->getFont()->setSize(12);
        $shape_002_2->getFont()->setBold(true);

        $shape_001_3 = $shape_001_3->createTextRun($student->seria_number);
        $shape_001_3->getFont()->setSize(12);
        $shape_001_3->getFont()->setBold(true);

        $shape_001_4 = $shape_001_3_1->createTextRun($student->seria);
        $shape_001_4->getFont()->setSize(12);
        $shape_001_4->getFont()->setBold(true);
        // end the text for slide 1

        $qrCodeImg = new File;
        $qrCodeImg->setPath(storage_path('app/qrcode/teachers/' . $student_fullName . '.png'))
            ->setWidth(110)
            ->setOffsetX(50)
            ->setOffsetY(472);
        $slide_001->addShape($qrCodeImg);

        // Create slide 2
        $slide_002 = $presentation->createSlide();
        // Set the dimensions and offsets for slide 2 Student full name
        $shape_002 = $slide_002->createRichTextShape();
        $shape_002->setHeight(50);
        $shape_002->setWidth(900);
        $shape_002->setOffsetX(40);
        $shape_002->setOffsetY(105);

        // Set the dimensions and offsets for slide 2 Student seria number
        $shape_002_1 = $slide_002->createRichTextShape();
        $shape_002_1->setHeight(30);
        $shape_002_1->setWidth(100);
        $shape_002_1->setOffsetX(80);
        $shape_002_1->setOffsetY(30);

        $shape_002_1_2 = $slide_002->createRichTextShape();
        $shape_002_1_2->setHeight(30);
        $shape_002_1_2->setWidth(50);
        $shape_002_1_2->setOffsetX(50);
        $shape_002_1_2->setOffsetY(30);

        // Set the dimensions and offsets for slide 2 Student Progress 4 bottom
        $shape_002_5 = $slide_002->createRichTextShape();
        $shape_002_5->setHeight(30);
        $shape_002_5->setWidth(290);
        $shape_002_5->setOffsetX(200);
        $shape_002_5->setOffsetY(347);

        $shape_002_5_1 = $slide_002->createRichTextShape();
        $shape_002_5_1->setHeight(30);
        $shape_002_5_1->setWidth(200);
        $shape_002_5_1->setOffsetX(235);
        $shape_002_5_1->setOffsetY(367);

        // Set the dimensions and offsets for slide 2 Student Progress 5 bottom
        $shape_002_6 = $slide_002->createRichTextShape();
        $shape_002_6->setHeight(30);
        $shape_002_6->setWidth(200);
        $shape_002_6->setOffsetX(480);
        $shape_002_6->setOffsetY(347);
        // Set the dimensions and offsets for slide 2 Student Progress 5 bottom
        $shape_002_6_1 = $slide_002->createRichTextShape();
        $shape_002_6_1->setHeight(30);
        $shape_002_6_1->setWidth(200);
        $shape_002_6_1->setOffsetX(515);
        $shape_002_6_1->setOffsetY(367);

        // Set the dimensions and offsets for slide 2 Student QrCode 1
        $shape_002_7 = $slide_002->createRichTextShape();
        $shape_002_7->setHeight(30);
        $shape_002_7->setWidth(200);
        $shape_002_7->setOffsetX(770);
        $shape_002_7->setOffsetY(347);

        $shape_002_7_1 = $slide_002->createRichTextShape();
        $shape_002_7_1->setHeight(30);
        $shape_002_7_1->setWidth(200);
        $shape_002_7_1->setOffsetX(805);
        $shape_002_7_1->setOffsetY(367);

        $shape_002_8 = $slide_002->createRichTextShape();
        $shape_002_8->setHeight(30);
        $shape_002_8->setWidth(200);
        $shape_002_8->setOffsetX(770);
        $shape_002_8->setOffsetY(420);

        $shape_002_8_1 = $slide_002->createRichTextShape();
        $shape_002_8_1->setHeight(30);
        $shape_002_8_1->setWidth(200);
        $shape_002_8_1->setOffsetX(800);
        $shape_002_8_1->setOffsetY(440);


        // Set the background image for slide 2
        $background_002 = new Image();
        $background_002->setPath(storage_path('img/sertificat006.png'));
        $slide_002->setBackground($background_002);

        $shape_002->getActiveParagraph()->setAlignment($alignment);

        // Set the text for slide 2 full_name
        $textRun_002 = $shape_002->createTextRun(strtoupper($fullName));
        $textRun_002->getFont()->setColor(new Color('FF5430CE'));
        $textRun_002->getFont('Gilroy');
        $textRun_002->getFont()->setSize(25);
        $textRun_002->getFont()->setBold(true);


        // Set the text for slide 2 seria_number
        $shape_002_1_2 = $shape_002_1_2->createTextRun($student->seria);
        $shape_002_1_2->getFont()->setSize(15);
        $shape_002_1_2->getFont()->setBold(true);

        // Set the text for slide 2 seria_number
        $shape_002_1_3 = $shape_002_1->createTextRun($student->seria_number);
        $shape_002_1_3->getFont()->setSize(15);
        // $shape_002_1_3->getFont()->setColor(new Color('231f1f'));
        $shape_002_1_3->getFont()->setBold(true);

        // Set the text for slide 2 Progress 2
        $shape_002_35 = $shape_002_5->createTextRun($student->modul_ball_1 . ' ball/points');
        $shape_002_35->getFont()->setSize(15);
        $shape_002_35->getFont()->setBold(true);

        // Set the text for slide 2 Progress 2 title
        $shape_002_351 = $shape_002_5_1->createTextRun($student->modul_1 . '%');
        $shape_002_351->getFont()->setSize(15);
        $shape_002_351->getFont()->setBold(true);

        // Set the text for slide 2 Progress 2
        $shape_002_36 = $shape_002_6->createTextRun($student->modul_ball_2 . ' ball/points');
        $shape_002_36->getFont()->setSize(15);
        $shape_002_36->getFont()->setBold(true);

        // Set the text for slide 2 Progress 2 title
        $shape_002_36 = $shape_002_6_1->createTextRun($student->modul_2 . '%');
        $shape_002_36->getFont()->setSize(15);
        $shape_002_36->getFont()->setBold(true);

        $shape_002_37 = $shape_002_7->createTextRun($student->modul_ball_3 . ' ball/points');
        $shape_002_37->getFont()->setSize(15);
        $shape_002_37->getFont()->setBold(true);

        $shape_002_37 = $shape_002_7_1->createTextRun($student->modul_3 . '%');
        $shape_002_37->getFont()->setSize(15);
        $shape_002_37->getFont()->setBold(true);

        $shape_002_38 = $shape_002_8->createTextRun($student->umumiy_ball . ' ball/points');
        $shape_002_38->getFont()->setSize(15);
        $shape_002_38->getFont()->setBold(true);

        $shape_002_39 = $shape_002_8_1->createTextRun($student->umumiy . '%');
        $shape_002_39->getFont()->setSize(15);
        $shape_002_39->getFont()->setBold(true);

        $qrCodeImg = new File;
        $qrCodeImg->setPath(storage_path('app/qrcode/teachers/' . $student_fullName . '.png'))
            ->setWidth(110)
            ->setOffsetX(822)
            ->setOffsetY(477);
        $slide_002->addShape($qrCodeImg);

        // //Set the alignment for slide 1
        // $paragraph_001 = $shape_001->getActiveParagraph();
        // $paragraph_001->setAlignment(new Alignment(Alignment::HORIZONTAL_RIGHT, Alignment::VERTICAL_AUTO));

        // // Set the alignment for slide 2
        // $paragraph_002 = $shape_002->getActiveParagraph();
        // $paragraph_002->setAlignment(new Alignment(Alignment::HORIZONTAL_RIGHT, Alignment::VERTICAL_AUTO));

        return $presentation;
    }

    public function download($id)
    {
        $teacher = Teacher::where('id', $id)->first();
        $teacherFull_name = $teacher->ism . $teacher->familiya . $teacher->sharif;

        $presentation = $this->generatePresentation($teacher);
        $student_fullName =  str_replace(["'", " ", "`", "?", ",", "!", "@", "#", "$", "%", "^", "&", "*", "."], '', $teacherFull_name);

        $fileName = $student_fullName . '.pptx';

        $filePath = 'certificats/teachers/' . $fileName;

        if (!TeacherCertificat::where('teacher_id', $teacher->id)->first()) {
            $teacherCertificate = new TeacherCertificat();
            $teacherCertificate->teacher_id = $teacher->id;
            $teacherCertificate->certificate_path = $filePath;
            $teacherCertificate->save();
        }


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

    public function downloadExcelExample(Request $request)
    {
        $excel_example = storage_path("app/public/example/example.xlsx");

        return response()->download($excel_example);
    }
}
