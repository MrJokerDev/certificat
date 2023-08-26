{{-- это оптималный код? --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Students') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="uper">
                <div class="row">
                    <div class="col-4">
                        <form action="{{ route('dashboard.index') }}" method="GET">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="search" placeholder="Search.....">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                                <a class="btn btn-outline-secondary" href="{{ route('dashboard.index') }}"><i class="bi bi-arrow-clockwise"></i></a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-4">
                        @if(session()->get('delete'))
                            <div class="alert alert-danger  alert-dismissible fade show" role="alert">
                                {{ session()->get('delete') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if(session()->get('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session()->get('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('attendanceExport') }}" class="btn btn-outline-primary">Attendance</a>
                        </div>
                    </div>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                        <td>ID</td>
                        <td>Full Name</td>
                        <td>Courses</td>
                        <td>Season</td>
                        <td>Qr code</td>
                        <td colspan="2">Action</td>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td>{{ $student->id }}</td>
                                <td class="name">{{ $student->full_name }}</td>
                                <td>{{ $student->course }}</td>
                                <td>{{ $student->season }}</td>
                                <td>
                                    <a class="btn btn-link" href="{{ route('dashboard.show', $student->id) }}"><i class="bi bi-qr-code"></i></a>
                                </td>
                                <td class="d-flex">
                                    <a href="{{ route('dashboard.edit', $student->id)}}" class="btn btn-primary m-1"><i class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('dashboard.destroy', $student->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger m-1" onclick="return confirm('Are you sure? Delete this student {{ $student->name }}')" type="submit"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $students->links() }}
            <div>
        </div>
    </div>
</x-app-layout>

    
