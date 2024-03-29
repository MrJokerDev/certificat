<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Astrum | Certificats') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Scripts -->
        <style>
            table, th, td {
                border: 1px solid black;
                border-collapse: collapse;
            }
            th, td {
                padding: 5px;
                text-align: left;
            }
        </style>
    </head>
    <body>
        <div class="min-h-screen bg-gray-100">
            <div class="w-11/12 m-auto">
                <div class="flex justify-center p-12 flex-shrink-0 text-white">
                    <a href="/"><img src="{{ asset('logo_1.png') }}" class="w-60"/></a>
                </div>
                <div class="sm:container mx-auto flex justify-center">
                    <div class="text-center md:bg-white md:shadow-md md:rounded md:py-14 md:px-32">
                        <p class="text-4xl text-blue-600 font-semibold">" Astrum Certified IT Educator "</p>
                        <p>Tasdiqlangan sertifikati</p>
                        <p class="font-bold">{{ $student_certificat->ism }} {{ $student_certificat->familiya }} {{ $student_certificat->sharif }}</p>
                        <p>Malaka oshirish o'quv kursini muvaffaqiyatli tamomladi.</p>
                        <div>
                            <div class="py-10">
                                <table style="width:100%">
                                    <tr>
                                        <th>Natija:</th>
                                        <th>To'plangan ball</th>
                                        <th>Toplangan %</th>
                                    </tr>
                                    <tr>
                                        <th>1 - Modul:</th>
                                        <td>{{ $student_certificat->modul_ball_1 }}</td>
                                        <td>{{ $student_certificat->modul_1 }}</td>
                                    </tr>
                                    <tr>
                                        <th>2 - Modul:</th>
                                        <td>{{ $student_certificat->modul_ball_2 }}</td>
                                        <td>{{ $student_certificat->modul_2 }}</td>
                                    </tr>
                                    <tr>
                                        <th>3 - Modul:</th>
                                        <td>{{ $student_certificat->modul_ball_3 }}</td>
                                        <td>{{ $student_certificat->modul_3 }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div><p class="font-semibold">{{ $student_certificat->berilgan_sana }}</p> Berilgan sana</div>
                                <div><p class="font-semibold">{{ $final_date }}</p> Amal qilish muddati</div>
                                <div><p class="font-semibold">{{ $student_certificat->seria }}{{ $student_certificat->seria_number }}</p> Sertifikat raqami</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="sm:container mx-auto flex justify-center py-12">
                    <a href="/" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">Qidiruvga qaytish</a>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    </body>
</html>