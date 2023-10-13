@extends('admincp.master')
@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-12">
          <h1 class="m-0">Hi {{ Auth::user()->name }}!</h1>
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

              <div class="row">
                <div class="col-sm-8">
                  <form class="form-horizontal">
                    <label class="col-form-label-sm float-left mr-3">Date Ranges</label>
                    <div class="form-row" style="max-width: 500px;">
                      <div class="col-5">
                        <i class="fas fa-calendar-alt" style="position: absolute;top: 8px; left: 20px;"></i>
                        <input type="text" style="padding-left: 30px" name="date" id="date"
                          class="form-control form-control-sm" value="{{ request()->input('date') }}" placeholder=""
                          autocomplete="off">
                      </div>
                      <div class="col-4">
                        <input type="text" name="search_text" id="search_text" class="form-control form-control-sm"
                          value="{{ request()->input('search_text') }}" placeholder="Event, User" autocomplete="off">
                      </div>
                      <div class="col-3">
                        <button type="submit" class="btn btn-default btn-sm btn-search"><i
                            class="fas fa-search"></i></button>
                      </div>
                    </div>
                  </form>
                </div>

                <div class="col-sm-4">
                  <button type="button" class="btn btn-primary btn-sm float-right ml-2" onclick="exportTasks(event.target);">
                    Download CSV <i class="fa-solid fa-file-csv ml-3"></i>
                  </button>
                  <a href="{{ url('/admincp/' . $type . '/create') }}" class="btn btn-danger btn-sm float-right"><span
                      class="fas fa-plus"></span>
                    Add</a>
                </div>
              </div>

            </div>
            <div class="card-body table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Events</th>
                    <th>Employees</th>
                    <th>Working Days</th>
                    <th>Clock In</th>
                    <th>Clock Out</th>
                    <th>Total Time</th>
                    <th style="width: 157px;"></th>
                  </tr>
                </thead>
                @if ($data)
                  <tbody>
                    @foreach ($data as $index)
                      <tr role="row" data-widget="expandable-table" aria-expanded="false">
                        <td>{{ $index->getEvent->event_name }} @if($index->getReceipts->where('status','Requested')->count() > 0) <span class="badge bg-danger">Receipt</span> @endif</td>
                        <td>{{ $index->getUser->name }}</td>
                        <td>{{ $index->created_at->format('Y-m-d') }}</td>
                        <td>{{ date('g:i A', strtotime($index->clockin)) }}</td>
                        <td>
                          @if ($index->clockout)
                            {{ date('g:i A', strtotime($index->clockout)) }}
                          @endif
                        </td>
                        <td>{{ $index->total_time }}</td>
                        <td class="text-center">
                          <a class="btn btn-default btn-sm"
                            href="{{ url('admincp/' . $type . '/edit/' . $index->id) }}" title="Edit"
                            style="color: green">
                            <span class="fa fa-edit"></span> Edit
                          </a>
                          &nbsp;&nbsp;
                          <a class="btn btn-default btn-sm"
                            href="{{ url('admincp/' . $type . '/delete/' . $index->id) }}" title="Delete"
                            style="color: red"
                            onclick="return confirm('Delete {{ $index->getEvent->event_name }} of {{ $index->getUser->name }}?')">
                            <span class="fa fa-trash"></span> Delete
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
@section('script')
  <script src="{{ asset('public/js/moment.min.js') }}"></script>
  <script type="text/javascript">
    function exportTasks() {
      @if (request()->input('date') || request()->input('search_text'))
        window.location.href =
        '/admincp/clocks/export?date={{ request()->input('date') }}&search_text={{ request()->input('search_text') }}';
      @else
        window.location.href = '/admincp/clocks/export';
      @endif
    }

    jQuery("#date").daterangepicker({
      locale: {
        format: 'D MMM YYYY',
        cancelLabel: 'Clear',
        firstDay: 1,
      },
      maxDate: moment().format("D MMM YYYY"),
      autoUpdateInput: false,
    });
    jQuery('input[name="date"]').on('apply.daterangepicker', function(ev, picker) {
      jQuery(this).val(picker.startDate.format('D MMM YYYY') + ' - ' + picker.endDate.format('D MMM YYYY'));
      jQuery('.btn-search').trigger('click');
    });
    jQuery('input[name="date"]').on('cancel.daterangepicker', function(ev, picker) {
      jQuery(this).val('');
      jQuery('.btn-search').trigger('click');
    });
    jQuery(".fa-calendar-alt").on("click", function() {
      jQuery("#date").trigger("click");
    });
  </script>
@endsection
