{{-- это оптималный код? --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Teachers Info') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="h-screen bg-gray-100">
            <div class="mx-auto max-w-5xl justify-center px-6 md:flex md:space-x-6 xl:px-0">
                <!-- Sub total -->
                <div class="mt-6 h-full rounded-lg border bg-white p-6 shadow-md md:mt-0 md:w-2/4">
                    <div class="mb-2 flex justify-between">
                        <p class="text-gray-700 font-black">Certificat seria number:</p>
                        <p class="text-blue-800">{{ $teacher->seria }}{{ $teacher->seria_number }}</p>
                    </div>
                    <div class="mb-2 flex justify-between">
                        <p class="text-gray-700 font-black">Student Full Name:</p>
                        <p class="text-blue-800">{{ $teacher->ism }} {{ $teacher->familiya }} {{ $teacher->sharif }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-700 font-black">Umumiy %:</p>
                        <p class="text-blue-800">{{ $teacher->umumiy }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-700 font-black">Umumiy ball:</p>
                        <p class="text-blue-800">{{ $teacher->umumiy_ball }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-700 font-black">Birinchi Modul %:</p>
                        <p class="text-blue-800">{{ $teacher->modul_1 }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-700 font-black">Birinchi Modul ball:</p>
                        <p class="text-blue-800">{{ $teacher->modul_ball_1 }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-700 font-black">Ikkinchi Modul %:</p>
                        <p class="text-blue-800">{{ $teacher->modul_2 }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-700 font-black">Ikkinchi Modul ball:</p>
                        <p class="text-blue-800">{{ $teacher->modul_ball_2 }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-700 font-black">Uchinchi Modul %:</p>
                        <p class="text-blue-800">{{ $teacher->modul_3 }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-700 font-black">Uchinchi Modul ball:</p>
                        <p class="text-blue-800">{{ $teacher->modul_ball_3 }}</p>
                    </div>
                </div>

                {{-- <div class="mt-6 h-full rounded-lg border bg-white p-6 shadow-md md:mt-0 md:w-2/4">
                    <iframe src="{{ asset('storage/'. $teacherCertificate) }}" frameborder="0" style="width:100%; height:500px;"></iframe>
                </div> --}}
            </div>
        </div>
    </div>
</x-app-layout>


