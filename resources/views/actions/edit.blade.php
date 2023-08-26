@extends('layouts.layout')

@section('content')
<style>
  .uper {
    margin-top: 40px;
  }
  .form-group{
      padding: 10px;
  }
</style>
<div class="card uper">
  <div class="card-header">
    Edit student Data
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
      <form method="post" action="{{ route('dashboard.update', $students->id ) }}" enctype="multipart/form-data">
            <div class="form-group">
                @csrf
                @method('PATCH')
                <label for="name">Name:</label>
                <input type="text" class="form-control" name="name" value="{{ $students->name }}"/>
                </div>
            <div class="form-group">
                <label for="lastname">Last name</label>
                <input type="text" class="form-control" name="lastname" value="{{ $students->lastname }}"/>
             </div>

            <div class="form-group">
                <label for="pre_season">Preseason</label>
                <input type="text" class="form-control" name="pre_season" value="{{ $students->pre_season }}"/>
            </div>
        
			<div class="form-group">
                <label for="directions">Directions</label>
                <select class="form-select" name="directions" aria-label="Default select example">
                  	<option value="{{ $students->directions }}">{{ $students->directions }}</option>
                    <option value="FullStack">1. Full Stack</option>
                    <option value="Date science">2. Date science</option>
                    <option value="Software Engineering">3. Software Engineering</option>
                </select>
            </div>
        

            <div class="form-group">
                <label for="status">Active status</label>
                <input class="form-control" id="date" type="date" name="deadline" value="{{ $students->deadline }}">
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" class="form-control" name="phone_number" value="{{ $students->phone_number }}"/>
            </div>

            <div class="form-group">
                <label for="exampleFormControlFile1">Foto student</label>
                <img src="{{ asset('images/' . $students->image) }}" style="width: 100px" alt="">
                <input type="file" name="image"  class="form-control-file" id="exampleFormControlFile1">
            </div>

          <button type="submit" class="btn btn-primary">Update</button>
      </form>
  </div>
</div>
@endsection
