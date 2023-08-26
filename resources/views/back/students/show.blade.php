{{-- это оптималный код? --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Students Info') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="h-screen bg-gray-100">
            <div class="mx-auto max-w-5xl justify-center px-6 md:flex md:space-x-6 xl:px-0">
                <!-- Sub total -->
                <div class="mt-6 h-full rounded-lg border bg-white p-6 shadow-md md:mt-0 md:w-2/4">
                    <div class="mb-2 flex justify-between">
                        <p class="text-gray-700">{{ $student->full_name }}</p>
                        <p class="text-gray-700"></p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-700">{{ $student->nik_name }}</p>
                        <p class="text-gray-700"></p>
                    </div>
                    <hr class="my-4" />
                    <div class="justify-between">
                        <div class="">
                            @if($student->progress_0 == null) @else<p class="justify-between mb-6 rounded-lg bg-white p-6 shadow-md sm:justify-start text-center">{{ $student->progress_0 }}</p>@endif
                            @if($student->progress_1 == null) @else<p class="justify-between mb-6 rounded-lg bg-white p-6 shadow-md sm:justify-start text-center">{{ $student->progress_1 }}</p>@endif
                            @if($student->progress_2 == null) @else<p class="justify-between mb-6 rounded-lg bg-white p-6 shadow-md sm:justify-start text-center">{{ $student->progress_2 }}</p>@endif
                            @if($student->progress_3 == null) @else<p class="justify-between mb-6 rounded-lg bg-white p-6 shadow-md sm:justify-start text-center">{{ $student->progress_3 }}</p>@endif
                            @if($student->progress_4 == null) @else<p class="justify-between mb-6 rounded-lg bg-white p-6 shadow-md sm:justify-start text-center">{{ $student->progress_4 }}</p>@endif
                            @if($student->progress_5 == null) @else<p class="justify-between mb-6 rounded-lg bg-white p-6 shadow-md sm:justify-start text-center">{{ $student->progress_5 }}</p>@endif
                        </div>
                    </div>
                </div>
                <div class="rounded-lg md:w-2/4">
                    @foreach($studentCertificat as $s)
                        <div class="justify-between mb-6 rounded-lg bg-white p-6 shadow-md sm:flex sm:justify-start" style="justify-content: space-between;">
                            <a target="_blank" class="underline decoration-sky-500" href="{{ $s->certificate }}">{{ $s->certificate_name }}</a>
                            <p class="justify-end">{{ $s->certificate_date }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


