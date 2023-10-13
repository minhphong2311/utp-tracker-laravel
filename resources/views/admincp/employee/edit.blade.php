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
              <h5></h5>
            </div>
            <div class="card-body table-responsive">
              <blockquote>
                <div class="row">
                  <div class="col-12">
                    <h3 class="card-title">Info</h3>
                  </div>
                  <div class="col-3">
                    <div class="form-group">
                      <label>Name:</label>
                      <div class="input-group date" id="reservationdate" data-target-input="nearest">
                        {{ $data->name }}
                      </div>
                    </div>
                  </div>
                  <div class="col-3">
                    <label>Email:</label>
                    <div class="input-group date" id="reservationdate" data-target-input="nearest">
                      {{ $data->email }}
                    </div>
                  </div>
                  <div class="col-3">
                    <label>Total Hours:</label>
                    <div class="input-group date" id="reservationdate" data-target-input="nearest">
                      {{ $data->total_time }}
                    </div>
                  </div>
                  <div class="col-3">
                    <label>Total Earned:</label>
                    <div class="input-group date" id="reservationdate" data-target-input="nearest">
                        @if($data->total_earned != 0){{ round(floatval($data->total_earned), 2) }}@endif
                    </div>
                  </div>

                  <div class="col-3">
                    <form class="form-horizontal" method="POST"
                      action="{{ url('admincp/' . $type . '/edit/' . $data->id) }}" enctype="multipart/form-data">
                      {{ csrf_field() }}
                      <label>Hourly rate:</label>
                      <div class="input-group date" id="inputHourlyRate" data-target-input="nearest">
                        <div class="form-row">
                          <div class="col-8">
                            <div class="input-group">
                              <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                              </div>
                              <input type="text" class="form-control" id="inputHourlyRate" name="hourly_rate"
                                placeholder="Hourly rate" value="{{ $data->hourly_rate }}">
                            </div>
                          </div>
                          <div class="col-4">
                            <button type="submit" class="btn btn-primary form-control">Save</button>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>

                  @if($data->earn && $data->hourly_rate)
                  <div class="col-3">
                    <form class="form-horizontal" method="POST"
                      action="{{ url('admincp/' . $type . '/pay/' . $data->id) }}" enctype="multipart/form-data">
                      {{ csrf_field() }}
                      <label>Earn is not counted: {{ $data->earn }}</label>
                      <div class="input-group date" id="inputHourlyRate" data-target-input="nearest">
                        <div class="form-row">
                          <div class="col-12">
                            <input type="hidden" name="clocks_id" value="{{ $data->earn_id }}">
                            <button type="submit" class="btn btn-danger form-control">Pay <i class="fa-solid fa-hand-holding-dollar"></i></button>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                  @endif

                </div>
              </blockquote>

              <div class="row">
                <div class="card-header card-header-transparent">
                  <div class="row">
                    <div class="col-sm-8">

                      <form class="form-horizontal">
                        <div class="form-row" style="max-width: 500px;">
                          <div class="col-3">
                            <select class="form-control form-select" name="search_text" id="search_text">
                              <option value="">Select</option>
                              <option @if (request()->input('search_text') == 'Weekly') selected @endif value="Weekly">Weekly</option>
                              <option @if (request()->input('search_text') == 'Bi-Weekly') selected @endif value="Bi-Weekly">Bi-Weekly
                              </option>
                              <option @if (request()->input('search_text') == 'Monthly') selected @endif value="Monthly">Monthly</option>
                              <option @if (request()->input('search_text') == 'Yearly') selected @endif value="Yearly">Yearly</option>
                            </select>
                          </div>
                          <div class="col-6">
                            <i class="fas fa-calendar-alt" style="position: absolute;top: 12px; left: 20px;"></i>
                            <input type="text" style="padding-left: 30px" name="date" id="date"
                              class="form-control form-control" value="{{ request()->input('date') }}" placeholder=""
                              autocomplete="off">
                          </div>
                          <div class="col-3">
                            <button type="submit" class="btn btn-default form-control btn-search"><i
                                class="fas fa-search"></i></button>
                          </div>
                        </div>
                      </form>
                    </div>

                    <div class="col-sm-4">
                      <button type="button" class="btn btn-primary float-right" onclick="exportTasks(event.target);">
                        Download CSV <i class="fa-solid fa-file-csv ml-3"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="card-body table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Events</th>
                        <th>Working Days</th>
                        <th>Total Time (hh:mm)</th>
                        <th>Total Earned</th>
                        <th>Other Pay</th>
                        <th style="width: 157px;"></th>
                      </tr>
                    </thead>
                    @if ($clock)
                      <tbody>
                        @foreach ($clock as $index)
                          <tr role="row" data-widget="expandable-table" aria-expanded="false">
                            <td>{{ $index->getEvent->event_name }}</td>
                            <td>{{ $index->created_at->format('Y-m-d') }}</td>
                            <td>{{ $index->total_time }}</td>
                            <td>@if($index->earned_amount)$ {{ round(floatval($index->earned_amount), 2) }}@endif</td>
                            <td>@if($index->bonus_pay)$ {{ round(floatval($index->bonus_pay), 2) }}@endif</td>
                            <td class="text-center">
                                <a class="btn btn-default btn-sm"
                                  href="{{ url('admincp/clocks/edit/' . $index->id) }}" title="Edit"
                                  style="color: green">
                                  <span class="fa fa-edit"></span> Edit
                                </a>
                                &nbsp;&nbsp;
                                <a class="btn btn-default btn-sm"
                                  href="{{ url('admincp/clocks/delete/' . $index->id) }}" title="Delete"
                                  style="color: red"
                                  onclick="return confirm('Delete {{ $index->getEvent->event_name }} of {{ $index->getUser->name }}?')">
                                  <span class="fa fa-trash"></span> Delete
                                </a>
                              </td>
                          </tr>
                        @endforeach
                      </tbody>
                    @endif
                    </tbody>
                  </table>
                  {!! $clock->links() !!}
                </div>
              </div>


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
      @if (request()->input('date'))
        window.location.href =
        '/admincp/{{ $type }}/edit/{{ $data->id }}/export?date={{ request()->input('date') }}';
      @elseif (request()->input('search_text'))
        window.location.href =
        '/admincp/{{ $type }}/edit/{{ $data->id }}/export?search_text={{ request()->input('search_text') }}';
      @else
        window.location.href = '/admincp/{{ $type }}/edit/{{ $data->id }}/export';
      @endif
    }

    if (jQuery('select[name="search_text"]').val()) {
      jQuery('input[name="date"]').val('');
      jQuery('input[name="date"]').attr("disabled", "disabled");
    }

    jQuery('select[name="search_text"]').on('change', function(event) {
      if (jQuery(this).val()) {
        jQuery('input[name="date"]').val('');
        jQuery('input[name="date"]').attr("disabled", "disabled");
      } else {
        jQuery('input[name="date"]').removeAttr("disabled");
      }
    //   jQuery('.btn-search').trigger('click');
    });

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
      if (!jQuery('select[name="search_text"]').val()) {
        jQuery("#date").trigger("click");
      }
    });
  </script>
@endsection
