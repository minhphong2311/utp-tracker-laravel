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

          @if (session()->has('delete'))
            <div class="alert alert-success alert-dismissible">
              {{ session()->get('delete') }}
            </div>
          @endif

          <div class="card card-primary card-outline">
            <div class="card-header card-header-transparent">

              {{-- <div class="card-tools pt-1 pr-2">
                <div class="input-group input-group-sm" style="width: 150px;">
                  <input type="text" name="table_search" class="form-control float-right" placeholder="Search mail">
                  <div class="input-group-append">
                    <button type="submit" class="btn btn-default">
                      <i class="fas fa-search"></i>
                    </button>
                  </div>
                </div>
              </div> --}}

              <div class="row">
                <div class="col-sm-8">
                  <form class="form-horizontal">
                    <div class="form-row" style="max-width: 500px;">
                      <div class="col-9">
                        <input type="text" name="search_text" id="search_text" class="form-control form-control-sm"
                          value="{{ request()->input('search_text') }}" placeholder="Event, User" autocomplete="off">
                      </div>
                      <div class="col-3">
                        <button type="submit" class="btn btn-default btn-sm"><i class="fas fa-search"></i></button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

            </div>
            <div class="card-body table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Events</th>
                    <th>Employees</th>
                    <th>Submitted At</th>
                    <th>Clock In</th>
                    <th>Clock Out</th>
                    <th>Comment</th>
                    <th>Status</th>
                    <th>Working Days Clock</th>
                    <th></th>
                  </tr>
                </thead>
                @if ($data)
                  <tbody>
                    @foreach ($data as $index)
                      <tr @if($index['status']=='Rejected') class="table-danger" @endif @if($index['status']=='Requested') style="background-color: #0000AF;color:white !important;" @endif role="row" data-widget="expandable-table" aria-expanded="false">
                        <td @if($index['status']=='Requested') style="color:white !important;" @endif>{{ $index->getClock->getEvent->event_name }}</td>
                        <td @if($index['status']=='Requested') style="color:white !important;" @endif>{{ $index->getClock->getUser->name }}</td>
                        <td @if($index['status']=='Requested') style="color:white !important;" @endif>{{ $index->created_at->format('Y-m-d H:i:s') }}</td>
                        <td @if($index['status']=='Requested') style="color:white !important;" @endif>{{ date("g:i A", strtotime($index->change_clockin)) }}</td>
                        <td @if($index['status']=='Requested') style="color:white !important;" @endif>{{ date("g:i A", strtotime($index->change_clockout)) }}</td>
                        <td @if($index['status']=='Requested') style="color:white !important;" @endif>{{ $index->comment }}</td>
                        <td @if($index['status']=='Requested') style="color:white !important;" @endif>{{ $index->status }}</td>
                        <td @if($index['status']=='Requested') style="color:white !important;" @endif>{{ $index->getClock->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                          <a class="btn btn-default btn-sm" href="{{ url('admincp/' . $type . '/edit/'.$index->id) }}" title="Edit"
                            style="color: green">
                            <span class="fa fa-edit"></span> Edit
                          </a>
                        </td>
                      <tr>
                    @endforeach
                  </tbody>
                @endif

                </tbody>
              </table>

              {!! $data->links() !!}

            </div>
          </div>

        </div>
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
@endsection
