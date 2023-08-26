@extends('layouts.layout')

@section('content')
<style>
  .uper {
    margin-top: 40px;
  }
  .form-group, .md-form{
      margin: 20px;
  }
</style>

<div class="card uper">
    <div class="card-header">
        Add Games Data
    </div>
    <div class="card-body">
        @if(session()->get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session()->get('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div><br />
        @endif

        <form action="{{ route('dashboard.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name"/>
            </div>

            <div class="form-group">
                <label for="lastname">Last name</label>
                <input type="text" class="form-control" name="lastname"/>
            </div>

            <div class="form-group">
                <label for="Preseason">Preseason</label>
                <input type="text" class="form-control" name="pre_season"/>
            </div>

            <div class="form-group">
                <label for="directions">Directions</label>
                <select class="form-select" name="directions" aria-label="Default select example">
                    <option selected>Select directions</option>
                    <option value="Fullstack">1. Full Stack</option>
                    <option value="Date science">2. Date science</option>
                    <option value="Software Engineering">3. Software Engineering</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Active status</label>
                <input class="form-control" id="date" type="date" name="deadline" value="2022-03-01">
            </div>

            <div class="form-group">
                <label for="status">Phoner number</label>
                <input class="form-control" id="phone_number" type="text" name="phone_number" value="99 876 54 32">
            </div>

            {{-- <div class="row">
                <div class="col-md-6">
                    <div class="md-form md-outline">
                        <label for="default-picker">Select start time lessons</label>
                        <input type="time" name="start_time_lessons" id="default-picker" class="form-control" placeholder="Select time">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="md-form md-outline">
                        <label for="default-picker">Select end time lessons</label>
                        <input type="time" name="end_time_lessons" id="default-picker" class="form-control" placeholder="Select time">
                    </div>
                </div>
            </div> --}}

            <div class="form-group">
                <label for="exampleFormControlFile1">Foto student</label>
                <input type="file" name="image" class="form-control-file" id="exampleFormControlFile1">
            </div>

            <button type="submit" class="btn btn-primary mt-5">Add</button>
        </form>

    </div>
</div>

<script>
    $('.datepicker').datepicker({
        // Escape any “rule” characters with an exclamation mark (!).
        format: 'You selecte!d: dddd, dd mmm, yyyy',
        formatSubmit: 'yyyy/mm/dd',
        hiddenPrefix: 'prefix__',
        hiddenSuffix: '__suffix'
    })
</script>
@endsection
