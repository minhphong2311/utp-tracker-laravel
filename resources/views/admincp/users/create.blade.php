@extends('admincp.master')
@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-12">
          <h1 class="m-0">{{ $title }}</h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">

          <div class="card card-primary card-outline">
            <div class="card-header card-header-transparent">
              <h4>Create</h4>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url('admincp/' . $type . '/store/') }}"
              enctype="multipart/form-data">
              {{ csrf_field() }}
              <div class="card-body table-responsive">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="inputActive">Active</label>
                  <div class="col-sm-10">
                    <div class="icheck-success">
                      <input type="checkbox" checked class="form-check-input" id="inputActive" name="active" value="1">
                      <label for="inputActive"></label>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control @if ($errors->has('name')) is-invalid @endif"
                      id="inputName" name="name" placeholder="Name" value="{{ old('name') }}">
                    @if ($errors->has('name'))
                      <span class="error invalid-feedback">{{ $errors->first('name') }}</span>
                    @endif
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                  <div class="col-sm-10">
                    <input type="email" class="form-control @if ($errors->has('email')) is-invalid @endif"
                      id="inputEmail" name="email" placeholder="Email" value="{{ old('email') }}">
                    @if ($errors->has('email'))
                      <span class="error invalid-feedback">{{ $errors->first('email') }}</span>
                    @endif
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control @if ($errors->has('password')) is-invalid @endif"
                      id="inputPassword" name="password" placeholder="Password">
                    @if ($errors->has('password'))
                      <span class="error invalid-feedback">{{ $errors->first('password') }}</span>
                    @endif
                  </div>
                </div>

              </div>

              <div class="card-footer">
                <a href="{{ url('/admincp/' . $type) }}" class="btn btn-default"><i class="fas fa-angle-left right"></i>
                  Back</a>
                <button type="submit" class="btn btn-primary">Register</button>
              </div>

            </form>


          </div>

        </div>
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
@endsection
