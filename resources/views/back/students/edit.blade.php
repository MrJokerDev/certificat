<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Edit') }}
        </h2>
    </x-slot>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- component -->
            <div class="flex items-center justify-center">
                <div class="mt-6 m-5 rounded-lg md:w-2/4">
                    <h1 class="m-5 text-center">Student Info</h1>
                    <form class="h-full rounded-lg border bg-white p-6 shadow-md" action="{{ route('students.update', $student->id ) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-5">
                            <label for="countries" class="mb-3 block text-base font-medium text-[#07074D]">Seria</label>
                            <select id="seria" name="seria" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md">      
                                <option selected>Choose a seria</option>
                                <option value="BD" {{ ( 'BD' == $student->seria) ? 'selected' : '' }}>BD</option>
                                <option value="DS" {{ ( 'DS' == $student->seria) ? 'selected' : '' }}>DS</option>
                                <option value="FD" {{ ( 'FD' == $student->seria) ? 'selected' : '' }}>FD</option>
                                <option value="FS" {{ ( 'FS' == $student->seria) ? 'selected' : '' }}>FS</option>
                                <option value="SE" {{ ( 'SE' == $student->seria) ? 'selected' : '' }}>SE</option>
                            </select>
                        </div>

                        <div class="mb-5">
                            <label
                            for="seria_number"
                            class="mb-3 block text-base font-medium text-[#07074D]"
                            >
                            #ID
                            </label>
                            <input
                            type="number"
                            name="seria_number"
                            id="seria_number"
                            value="{{ $student->seria_number }}"
                            class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
                            />
                        </div>
                        <div class="mb-5">
                            <label
                            for="full_name"
                            class="mb-3 block text-base font-medium text-[#07074D]"
                            >
                            Full Name
                            </label>
                            <input
                            type="text"
                            name="full_name"
                            id="full_name"
                            value="{{ $student->full_name }}"
                            class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
                            />
                        </div>
                        <div class="mb-5">
                            <label for="countries" class="mb-3 block text-base font-medium text-[#07074D]">Select an courses</label>
                            <select id="course" name="course" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md">      
                                <option selected>Choose a course</option>
                                <option value="Full_Stack" {{ ( 'Full Stack' == $student->course) ? 'selected' : '' }}>Full Stack</option>
                                <option value="Back_End" {{ ( 'Back End' == $student->course) ? 'selected' : '' }}>Back End</option>
                                <option value="Front_End" {{ ( 'Front End' == $student->course) ? 'selected' : '' }}>Front End</option>
                                <option value="Data_Science" {{ ( 'Data Science' == $student->course) ? 'selected' : '' }}>Data Science</option>
                                <option value="Software_Engineering" {{ ( 'Software Engineering' == $student->course) ? 'selected' : '' }}>Software Engineering</option>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label
                            for="name"
                            class="mb-3 block text-base font-medium text-[#07074D]"
                            >
                            Season
                            </label>
                            <input
                            type="text"
                            name="season"
                            value="{{ $student->season }}"
                            class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
                            />
                        </div>
                        <div class="mb-5">
                            <label
                            for="sertificat_1"
                            class="mb-3 block text-base font-medium text-[#07074D]"
                            >
                            Certificate 1
                            </label>
                            <input
                            type="text"
                            name="sertificat_1"
                            id="sertificat_1"
                            value="{{ $student->sertificat_1 }}"
                            class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
                            />
                        </div>
                        <div class="mb-5">
                            <label
                            for="sertificat_2"
                            class="mb-3 block text-base font-medium text-[#07074D]"
                            >
                            Certificate 2
                            </label>
                            <input
                            type="text"
                            name="sertificat_2"
                            id="sertificat_2"
                            value="{{ $student->sertificat_2 }}"
                            class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
                            />
                        </div>

                        <div>
                            <button
                            class="hover:shadow-form rounded-md bg-[#6A64F1] py-3 px-8 text-base font-semibold text-white outline-none"
                            >
                            Submit
                            </button>
                        </div>
                    </form>
                </div>
                <div class="mt-6 rounded-lg md:w-2/4">
                    <h1 class="m-5 text-center">Certificats</h1>
                    @foreach($studentCertificat as $s)
                        <div class="justify-start mb-6 rounded-lg bg-white p-6 shadow-md  sm:justify-start">
                            <div class="sm:flex mb-3" style="justify-content: space-between;">
                                <a target="_blank" class="underline decoration-sky-500" href="{{ $s->certificate }}">{{ $s->certificate_name }}</a>
                                <p class="justify-end">{{ $s->certificate_date }}</p>
                            </div>
                            <div class="sm:flex justify-end">
                                <input type="text" name="myvalue"  id="myvalue" value="{{ $s->certificate }}" readonly class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"/>
                                <button class="btn btn-primary btn-block" onclick="copyToClipboard()">Copy</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
</x-app-layout>

<script>
    function copyToClipboard() {
        var textBox = document.getElementById("myvalue");
        textBox.select();
        document.execCommand("copy");
    }
</script>