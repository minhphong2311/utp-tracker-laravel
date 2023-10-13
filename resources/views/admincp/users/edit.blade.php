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

          <!-- Alert -->
          @if (session()->has('update'))
            <div class="alert alert-success alert-dismissible">
              {{ session()->get('update') }}
            </div>
          @endif
          <!-- /.alert -->

          <div class="card card-primary card-outline">
            <div class="card-header card-header-transparent">
              <h4>Edit</h4>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url('admincp/' . $type . '/edit/' . $data->id) }}"
              enctype="multipart/form-data">
              {{ csrf_field() }}
              <div class="card-body table-responsive">
                <div class="form-group row">
                  <label class="col-sm-2 col-form-label" for="inputActive">Active</label>
                  <div class="col-sm-10">
                    <div class="icheck-success">
                      <input type="checkbox" {{ $data->active ? 'checked' : '' }} class="form-check-input" id="inputActive"
                        name="active" value="1">
                      <label class="form-check-label" for="inputActive"></label>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputName" name="name" placeholder="Name"
                      value="{{ $data->name }}">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                  <div class="col-sm-10">
                    <input type="email" disabled class="form-control" id="inputEmail" placeholder="Email"
                      value="{{ $data->email }}">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputUserWpID" class="col-sm-2 col-form-label">User WP ID</label>
                  <div class="col-sm-10">
                    <input type="text" disabled class="form-control" id="inputUserWpID"
                      value="{{ $data->user_wp_id }}">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputPassword" class="col-sm-2 col-form-label">Change password</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputPassword" name="changepassword"
                      placeholder="Change Password">
                  </div>
                </div>

              </div>

              <div class="card-footer">
                <a href="{{ url('/admincp/' . $type) }}" class="btn btn-default"><i class="fas fa-angle-left right"></i>
                  Back</a>
                <button type="submit" class="btn btn-primary">Update</button>
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
