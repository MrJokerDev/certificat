{{-- это оптималный код? --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Teacher Edit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- component -->
            <div class="flex items-center justify-center">
                <div class="mt-6 m-5 rounded-lg md:w-2/4">
                    <h1 class="m-5 text-center">Student Info</h1>
                    <form
                        class="h-full rounded-lg border bg-white p-6 shadow-md"
                        action="{{ route('teachers.update', $teacher->id ) }}"
                        method="POST">
                        @csrf @method('PUT')

                        <div class="mb-5">
                            <label for="seria" class="mb-3 block text-base font-medium text-[#07074D]">
                                #ID
                            </label>
                            <input
                                type="text"
                                name="seria"
                                id="seria"
                                value="{{ $teacher->seria }}"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"/>
                        </div>

                        <div class="mb-5">
                            <label
                                for="seria_number"
                                class="mb-3 block text-base font-medium text-[#07074D]">
                                #ID
                            </label>
                            <input
                                type="number"
                                name="seria_number"
                                id="seria_number"
                                value="{{ $teacher->seria_number }}"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"/>
                        </div>

                        <div class="mb-5">
                            <label for="ism" class="mb-3 block text-base font-medium text-[#07074D]">
                                Name
                            </label>
                            <input
                                type="text"
                                name="ism"
                                id="ism"
                                value="{{ $teacher->ism }}"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"/>
                        </div>

                        <div class="mb-5">
                            <label for="familiya" class="mb-3 block text-base font-medium text-[#07074D]">
                                Last Name
                            </label>
                            <input
                                type="text"
                                name="familiya"
                                id="familiya"
                                value="{{ $teacher->familiya }}"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"/>
                        </div>

                        <div class="mb-5">
                            <label for="sharif" class="mb-3 block text-base font-medium text-[#07074D]">
                                Last Name
                            </label>
                            <input
                                type="text"
                                name="sharif"
                                id="sharif"
                                value="{{ $teacher->sharif }}"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"/>
                        </div>

                        <div class="mb-5">
                            <label for="sharif" class="mb-3 block text-base font-medium text-[#07074D]">
                                Umumiy %
                            </label>
                            <input
                                type="number"
                                name="umumiy"
                                id="umumiy"
                                value="{{ $teacher->umumiy }}"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"/>
                        </div>

                        <div class="mb-5">
                            <label for="sharif" class="mb-3 block text-base font-medium text-[#07074D]">
                                Umumiy ball
                            </label>
                            <input
                                type="number"
                                name="umumiy_ball"
                                id="umumiy_ball"
                                value="{{ $teacher->umumiy_ball }}"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"/>
                        </div>

                        <div class="mb-5">
                            <label for="sharif" class="mb-3 block text-base font-medium text-[#07074D]">
                                1 Modul %
                            </label>
                            <input
                                type="number"
                                name="modul_1"
                                id="modul_1"
                                value="{{ $teacher->modul_1 }}"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"/>
                        </div>

                        <div class="mb-5">
                            <label for="sharif" class="mb-3 block text-base font-medium text-[#07074D]">
                                1 Modul ball
                            </label>
                            <input
                                type="number"
                                name="modul_ball_1"
                                id="modul_ball_1"
                                value="{{ $teacher->modul_ball_1 }}"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"/>
                        </div>

                        <div class="mb-5">
                            <label for="sharif" class="mb-3 block text-base font-medium text-[#07074D]">
                                2 Modul %
                            </label>
                            <input
                                type="number"
                                name="modul_2"
                                id="modul_2"
                                value="{{ $teacher->modul_2 }}"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"/>
                        </div>

                        <div class="mb-5">
                            <label for="sharif" class="mb-3 block text-base font-medium text-[#07074D]">
                                2 Modul ball
                            </label>
                            <input
                                type="number"
                                name="modul_ball_2"
                                id="modul_ball_2"
                                value="{{ $teacher->modul_ball_2 }}"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"/>
                        </div>

                        <div class="mb-5">
                            <label for="sharif" class="mb-3 block text-base font-medium text-[#07074D]">
                                3 Modul %
                            </label>
                            <input
                                type="number"
                                name="modul_3"
                                id="modul_3"
                                value="{{ $teacher->modul_3 }}"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"/>
                        </div>

                        <div class="mb-5">
                            <label for="sharif" class="mb-3 block text-base font-medium text-[#07074D]">
                                3 Modul ball
                            </label>
                            <input
                                type="number"
                                name="modul_ball_3"
                                id="modul_ball_3"
                                value="{{ $teacher->modul_ball_3 }}"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"/>
                        </div>

                        <div>
                            <button
                                class="hover:shadow-form rounded-md bg-[#6A64F1] py-3 px-8 text-base font-semibold text-white outline-none">
                                Submit
                            </button>
                        </div>

                    </form>
                </div>
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