{{-- это оптималный код? --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Students certificat') }}
        </h2>
    </x-slot>
    <div class="container">
        <main>
            <section>
                <div
                    class=" sm:grid grid-cols-5 grid-rows-2 px-4 py-6 min-h-full lg:min-h-screen space-y-6 sm:space-y-0 sm:gap-4">
                    <div class="h-96 col-span-1 ">
                        <form action="{{ route('students.index') }}" method="get">
                            <div class="bg-white py-3 px-4 rounded-lg flex justify-around items-center ">
                                <input
                                    type="text"
                                    name="search"
                                    placeholder="seach"
                                    class="bg-gray-100 rounded-md  outline-none pl-2 ring-indigo-700 w-full mr-2 p-2">
                                    <span>
                                        <button type="submit">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-6 w-6"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor ">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                        </button>
                                    </span>
                                </div>
                            </form>
                            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mt-3">
                                <form
                                    method="POST"
                                    action="{{ route('students.store') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="flex justify-between">
                                        <div class="mb-4">
                                            <label class="block text-red-700 text-sm font-bold mb-2" for="username">
                                                * Format file only .xlsx
                                            </label>
                                            <input
                                                name="file"
                                                id="file"
                                                type="file"
                                                class="appearance-none w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></div>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <button
                                                type="submit"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                                submit
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="h-96 col-span-4 rounded-md items-center">
                                <div class="bg-white shadow-md rounded">
                                    <table class="min-w-max w-full table-auto">
                                        <thead>
                                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                                <th class="py-3 px-6 text-left">Seria</th>
                                                <th class="py-3 px-6 text-left">Seria Number</th>
                                                <th class="py-3 px-6 text-left">Full Name</th>
                                                <th class="py-3 px-6 text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-gray-600 text-sm font-light">
                                            @foreach($students_db as $student)
                                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                                <td class="py-3 px-6 text-left">
                                                    <div class="flex items-center">
                                                        <span>{{ $student->seria }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-6 text-left">
                                                    <div class="flex items-center">
                                                        <span>{{ $student->seria_number }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <span class="font-medium">{{ $student->full_name }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-6 text-center">
                                                    <div class="flex item-center justify-center">
                                                        <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                            <form action="{{ route('students.destroy', $student->id) }}" method="POST">
                                                                @csrf
                                                                <a href="{{ route('students.show', $student->id) }}">
                                                                    <svg
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="none"
                                                                        viewBox="0 0 24 24"
                                                                        stroke="currentColor">
                                                                        <path
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                        <path
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                    </svg>
                                                                </a>
                                                            </form>
                                                        </div>

                                                        <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                            <a href="{{ route('student.download', $student->id) }}">
                                                                <svg
                                                                    class="http://www.w3.org/2000/svg"
                                                                    fill="none"
                                                                    viewBox="0 0 24 24"
                                                                    stroke="currentColor">
                                                                    <path
                                                                        stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                </svg>
                                                            </a>
                                                        </div>

                                                        <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                            <a href="{{ route('students.edit', $student->id) }}">
                                                                <svg
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="none"
                                                                    viewBox="0 0 24 24"
                                                                    stroke="currentColor">
                                                                    <path
                                                                        stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                                </svg>
                                                            </a>
                                                        </div>

                                                        <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                            <form action="{{ route('students.destroy', $student->id) }}" method="POST">
                                                                @csrf @method('DELETE')
                                                                <button
                                                                    type="submit"
                                                                    title="Delete"
                                                                    onclick="return confirm('Are you sure delete user {{ $student->full_name }}?')">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{ $students_db->links('pagination::tailwind') }}
                            </div>

                        </div>
                    </section>
                </main>
            </div>
        </x-app-layout>