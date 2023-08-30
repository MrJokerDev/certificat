{{-- @extends('front.layout') 
@section('content')

<div class="w-11/12 m-auto">
    <table class="min-w-max w-full table-auto">
        <thead>
            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                <th class="py-3 px-6 text-left">Seria</th>
                <th class="py-3 px-6 text-left">Seria Nomer</th>
                <th class="py-3 px-6 text-left">Ism Sharif</th>
                <th class="py-3 px-6 text-center">Berilgan sana</th>
                <th class="py-3 px-6 text-center">Umumiy ball (%)</th>
                <th class="py-3 px-6 text-center">Umumiy ball</th>
                <th class="py-3 px-6 text-center">Modul 1 (%)</th>
                <th class="py-3 px-6 text-center">Modul 1 ball</th>
                <th class="py-3 px-6 text-center">Modul 2 (%)</th>
                <th class="py-3 px-6 text-center">Modul 2 ball</th>
                <th class="py-3 px-6 text-center">Modul 3 (%)</th>
                <th class="py-3 px-6 text-center">Modul 3 ball</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 text-sm font-light">
            <tr class="border-b border-gray-200 hover:bg-gray-100">
                <td class="py-3 px-6 text-left">
                    <div class="flex items-center">
                        <span>{{ $student_certificat->seria }}</span>
                    </div>
                </td>
                <td class="py-3 px-6 text-left">
                    <div class="flex items-center">
                        <span>{{ $student_certificat->seria_number }}</span>
                    </div>
                </td>
                <td class="py-3 px-6 text-left whitespace-nowrap">
                    <div class="flex items-center">
                        <span class="font-medium">{{ $student_certificat->ism }}
                            {{ $student_certificat->familiya }}
                            {{ $student_certificat->sharif }}</span>
                    </div>
                </td>
                <td class="py-3 px-6 text-left whitespace-nowrap">
                    <div class="flex items-center">
                        <span class="font-medium">{{ $student_certificat->berilgan_sana }}</span>
                    </div>
                </td>
                <td class="py-3 px-6 text-left whitespace-nowrap">
                    <div class="flex items-center">
                        <span class="font-medium">{{ $student_certificat->umumiy }}%</span>
                    </div>
                </td>
                <td class="py-3 px-6 text-left whitespace-nowrap">
                    <div class="flex items-center">
                        <span class="font-medium">{{ $student_certificat->umumiy_ball }}</span>
                    </div>
                </td>
                <td class="py-3 px-6 text-left whitespace-nowrap">
                    <div class="flex items-center">
                        <span class="font-medium">{{ $student_certificat->modul_1 }}%</span>
                    </div>
                </td>
                <td class="py-3 px-6 text-left whitespace-nowrap">
                    <div class="flex items-center">
                        <span class="font-medium">{{ $student_certificat->modul_ball_1 }}</span>
                    </div>
                </td>
                <td class="py-3 px-6 text-left whitespace-nowrap">
                    <div class="flex items-center">
                        <span class="font-medium">{{ $student_certificat->modul_2 }}%</span>
                    </div>
                </td>
                <td class="py-3 px-6 text-left whitespace-nowrap">
                    <div class="flex items-center">
                        <span class="font-medium">{{ $student_certificat->modul_ball_2 }}</span>
                    </div>
                </td>
                <td class="py-3 px-6 text-left whitespace-nowrap">
                    <div class="flex items-center">
                        <span class="font-medium">{{ $student_certificat->modul_3 }}%</span>
                    </div>
                </td>
                <td class="py-3 px-6 text-left whitespace-nowrap">
                    <div class="flex items-center">
                        <span class="font-medium">{{ $student_certificat->modul_ball_3 }}</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection --}}



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
                <div class="flex justify-center py-12 flex-shrink-0 text-white">
                    <a href="/"><img src="{{ asset('logo_1.png') }}" class="w-60"/></a>
                </div>
                <div class="sm:container mx-auto flex justify-center">
                    <div class="text-center md:bg-white md:shadow-md md:rounded md:py-14 md:px-32">
                        <p class="text-4xl text-red-600 font-semibold">{{ $message }}</p>
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