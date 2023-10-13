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
            <form class="form-horizontal" method="POST" action="{{ url('admincp/' . $type . '/edit/' . $data->id) }}"
              enctype="multipart/form-data">
              {{ csrf_field() }}
              <div class="card-header card-header-transparent">
                <h5></h5>
              </div>
              <div class="card-body table-responsive">
                <blockquote>
                  <div class="row">
                    <div class="col-12">
                      <h3 class="card-title">Info</h3>
                    </div>
                    <div class="col-4">
                      <div class="form-group">
                        <label>Event:</label>
                        <div class="input-group date" id="reservationdate" data-target-input="nearest">
                          {{ $data->getClock->getEvent->event_name }}
                        </div>
                      </div>
                    </div>
                    <div class="col-4">
                      <label>User:</label>
                      <div class="input-group date" id="reservationdate" data-target-input="nearest">
                        {{ $data->getClock->getUser->name }}
                      </div>
                    </div>
                    <div class="col-4">
                      <label>Submitted At:</label>
                      <div class="input-group date" id="reservationdate" data-target-input="nearest">
                        {{ $data->created_at->format('Y-m-d H:i:s') }}
                      </div>
                    </div>
                  </div>
                </blockquote>

                <blockquote class="quote-danger">
                  <div class="row">
                    <div class="col-12">
                      <h3 class="card-title">Request</h3>
                    </div>
                    <div class="col-4">
                      <div class="form-group">
                        <label>Clock in:</label>
                        <div class="input-group"> {{ date("g:i A", strtotime($data->change_clockin)) }}</div>
                      </div>
                    </div>
                    <div class="col-4">
                      <label>Clock out:</label>
                      <div class="input-group"> {{ date("g:i A", strtotime($data->change_clockout)) }} </div>
                    </div>
                    <div class="col-12">
                      <label>Comment:</label> {{ $data->comment }}
                    </div>
                  </div>
                </blockquote>

                <blockquote class="quote-secondary">
                  <div class="row">
                    <div class="col-12">
                      <h3 class="card-title font-bold">Current</h3>
                    </div>
                    <div class="col-4">
                      <div class="form-group">
                        <label>Clock in:</label>
                        <div class="input-group"> {{ date("g:i A", strtotime($data->clockin)) }} </div>
                      </div>
                    </div>
                    <div class="col-4">
                      <label>Clock out:</label>
                      <div class="input-group"> {{ date("g:i A", strtotime($data->clockout)) }} </div>
                    </div>
                  </div>
                </blockquote>

                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Status</label>
                      <select name="status" class="form-select">
                        <option value="Approved" @if ($data->status == 'Approved') selected @endif>Approved</option>
                        <option value="Rejected" @if ($data->status == 'Rejected') selected @endif>Rejected</option>
                        {{-- <option value="Cancelled" @if ($data->status == 'Cancelled') selected @endif>Cancelled</option> --}}
                        <option value="Requested" @if ($data->status == 'Requested') selected @endif>Requested</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    @if ($data->approver)
                      <div class="form-group">
                        <label>Approver</label>
                        <div class="input-group"> {{ $data->getUser->name }} </div>
                      </div>
                    @endif
                  </div>
                </div>

              </div>
              <div class="card-footer">
                <a href="{{ url('/admincp/' . $type) }}" class="btn btn-default"><i
                    class="fas fa-angle-left right"></i>
                  Back</a>
                <button type="submit" class="btn btn-primary">Submit</button>
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
