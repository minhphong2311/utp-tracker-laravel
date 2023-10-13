@extends('admincp.master')
@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-12">
          <h1 class="m-0">{{ $title }} in {{ $data->getClock->getEvent->event_name }} of {{ $data->getClock->getUser->name }}</h1>
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
              enctype="multipart/form-data" autocomplete="off">
              {{ csrf_field() }}
              <div class="card-body table-responsive" style="min-height: 320px;">
                <div class="row">

                  <div class="col-6">
                    <div class="form-group">
                      <label for="inputName" class="col-form-label">Start break</label>
                      <div class="input-group date" id="startbreak" data-target-input="nearest">
                        <div class="input-group-prepend" data-target="#startbreak" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="fa fa-clock"></i></div>
                        </div>
                        <input type="text" class="form-control datetimepicker-input" name="startbreak"
                          data-target="#startbreak">
                      </div>
                      @if ($errors->has('startbreak'))
                        <span class="error">{{ $errors->first('startbreak') }}</span>
                      @endif
                    </div>
                  </div>

                  <div class="col-6">
                    <div class="form-group">
                      <label for="inputName" class="col-form-label">End break</label>
                      <div class="input-group date" id="endbreak" data-target-input="nearest">
                        <div class="input-group-prepend" data-target="#endbreak" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="fa fa-clock"></i></div>
                        </div>
                        <input type="text" class="form-control datetimepicker-input" name="endbreak"
                          data-target="#endbreak">
                      </div>
                      @if ($errors->has('endbreak'))
                        <span class="error">{{ $errors->first('endbreak') }}</span>
                      @endif
                    </div>
                  </div>

                </div>
              </div>

              <div class="card-footer">
                <a href="{{ url('/admincp/clocks/edit/' . $data->clock_id) }}" class="btn btn-default"><i
                    class="fas fa-angle-left right"></i>
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
  <link rel="stylesheet" href="{{ asset('public/css/tempusdominus-bootstrap-4.min.css') }}">
@endsection

@section('script')
  <script src="{{ asset('public/js/moment.min.js') }}"></script>
  <script src="{{ asset('public/js/tempusdominus-bootstrap-4.min.js') }}"></script>
  <script>
    jQuery(function() {

      jQuery('#startbreak').datetimepicker({
        format: 'LT',
        @if ($data->startbreak)
          date: '{{ date('g:i A', strtotime($data->startbreak)) }}'
        @endif
      });
      jQuery('#endbreak').datetimepicker({
        format: 'LT',
        @if ($data->endbreak)
          date: '{{ date('g:i A', strtotime($data->endbreak)) }}'
        @endif
      });

      jQuery("[name='startbreak']").focus(function() {
        jQuery("#startbreak .fa-clock").trigger("click");
      });
      jQuery("[name='endbreak']").click(function() {
        jQuery("#endbreak .fa-clock").trigger("click");
      });

    })
  </script>
@endsection
