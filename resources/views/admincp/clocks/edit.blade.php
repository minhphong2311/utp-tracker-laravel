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
              enctype="multipart/form-data" autocomplete="off">
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
                        <div class="input-group">
                          <select class="form-control select2" id="event_wp_id" name="event_wp_id" style="width: 100%;">
                            <option value="{{ $data->getEvent->event_wp_id }}">{{ $data->getEvent->event_name }}
                            </option>
                          </select>
                          <input type="hidden" class="form-control" id="event_name" name="event_name"
                            value="{{ $data->getEvent->event_name }}">
                          @if ($errors->has('event_name'))
                            <span class="error">{{ $errors->first('event_name') }}</span>
                          @endif
                        </div>
                      </div>
                    </div>
                    <div class="col-4">
                      <label>User:</label>
                      <div class="input-group">
                        <select class="form-control select2" id="user_wp_id" name="user_wp_id" style="width: 100%;">
                          <option value="{{ $data->getUser->user_wp_id }}">{{ $data->getUser->name }}</option>
                        </select>
                        @if ($errors->has('user_wp_id'))
                          <span class="error">{{ $errors->first('user_wp_id') }}</span>
                        @endif
                        <input type="hidden" class="form-control" id="name" name="name"
                          value="{{ $data->getUser->name }}">
                        <input type="hidden" class="form-control" id="email" name="email"
                          value="{{ $data->getUser->email }}">
                      </div>
                    </div>
                    <div class="col-4">
                      <label>Working Day:</label>
                      <div class="input-group date" id="created_at" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" name="created_at"
                          data-target="#created_at" value="{{ $data->created_at->format('Y-m-d') }}" />
                        <div class="input-group-append" data-target="#created_at" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                        @if ($errors->has('created_at'))
                          <span class="error">{{ $errors->first('created_at') }}</span>
                        @endif
                      </div>
                    </div>
                  </div>
                </blockquote>

                <blockquote class="quote-olive">
                  <div class="row">
                    <div class="col-12">
                      <h3 class="card-title">Clock in</h3>
                    </div>
                    <div class="col-8">
                      <div class="row">
                        <div class="col-6">
                          <div class="form-group">
                            <div class="input-group date" id="clockin" data-target-input="nearest">
                              <div class="input-group-prepend" data-target="#clockin" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-clock"></i></div>
                              </div>
                              <input type="text" class="form-control datetimepicker-input" name="clockin"
                                data-target="#clockin">
                            </div>
                            @if ($errors->has('clockin'))
                              <span class="error">{{ $errors->first('clockin') }}</span>
                            @endif
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="form-group">
                            <input type="file" class="form-control" name="clockin_photo" id="clockin_photo"
                              placeholder="clockin_photo">
                            @if ($errors->has('clockin_photo'))
                              <span class="error">{{ $errors->first('clockin_photo') }}</span>
                            @endif
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <div class="input-group-text open-map-clockin" data-toggle="modal"
                              data-target="#modal-clockin_location" style="font-size: 17px;cursor: pointer;">
                              <i class="fa fa-location-arrow"></i>
                            </div>
                          </div>
                          <input type="text" class="form-control" id="inputClockinAddress" name="clockin_address"
                            value="{{ $data->clockin_address }}">
                          <input type="hidden" class="form-control" id="inputClockinLocation" name="clockin_location"
                            value="{{ $data->clockin_location }}">
                        </div>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="input-group" data-target-input="nearest">
                        @if ($data->clockin_photo)
                          <a href="{{ $data->clockin_photo }}" data-lightbox="lightbox" title="Clock In">
                            <img src="{{ $data->clockin_photo }}" alt="{{ $data->clockin_address }}"
                              style="height: 92px;">
                          </a>
                        @endif
                      </div>
                    </div>
                </blockquote>

                @if ($data->getBreaks->count() > 0)
                <blockquote class="quote-secondary">
                  <div class="row">

                      <div class="col-8 ">
                        <div class="card-body table-responsive">
                          <table class="table table-striped">
                            <thead>
                              <tr>
                                <th>Start break</th>
                                <th>End break</th>
                                <th>Total break</th>
                                <th style="width: 100px;"></th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach ($data->getBreaks as $break)
                                <tr role="row" data-widget="expandable-table" aria-expanded="false">
                                  <td>{{ date('g:i A', strtotime($break['startbreak'])) }}</td>
                                  <td>
                                    @if ($break['endbreak'])
                                      {{ date('g:i A', strtotime($break['endbreak'])) }}
                                    @endif
                                  </td>
                                  <td>{{ $break['total_time'] }}</td>
                                  <td class="text-center">
                                    <a class="btn btn-default btn-sm"
                                      href="{{ url('admincp/breaks/edit/' . $break->id) }}" title="Edit"
                                      style="color: green">
                                      <span class="fa fa-edit"></span> Edit
                                    </a>
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>

                      <div class="col-4">
                        <div class="card-body table-responsive">
                          <div class="form-group">
                            <label>Total time Break:</label>
                            <div class="input-group" data-target-input="nearest">
                              {{ $data->getTotalBreak() }}
                            </div>
                          </div>
                        </div>
                      </div>

                  </div>
                </blockquote>
                @endif

                <blockquote class="quote-olive">
                  <div class="row">
                    <div class="col-12">
                      <h3 class="card-title">Clock out</h3>
                    </div>
                    <div class="col-8">
                      <div class="row">
                        <div class="col-6">
                          <div class="form-group">
                            <div class="input-group date" id="clockout" data-target-input="nearest">
                              <div class="input-group-prepend" data-target="#clockout" data-toggle="datetimepicker">
                                <span class="input-group-text"><i class="fa fa-clock"></i></span>
                              </div>
                              <input type="text" class="form-control datetimepicker-input" name="clockout"
                                data-target="#clockout">
                            </div>
                            @if ($errors->has('clockout'))
                              <span class="error">{{ $errors->first('clockout') }}</span>
                            @endif
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="form-group">
                            <input type="file" class="form-control" name="clockout_photo" id="clockout_photo"
                              placeholder="clockout_photo">
                            @if ($errors->has('clockout_photo'))
                              <span class="error">{{ $errors->first('clockout_photo') }}</span>
                            @endif
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <div class="input-group-text open-map-clockout" data-toggle="modal"
                              data-target="#modal-clockout_location" style="font-size: 17px;cursor: pointer;">
                              <i class="fa fa-location-arrow"></i>
                            </div>
                          </div>
                          <input type="text" class="form-control" id="inputClockoutAddress" name="clockout_address"
                            value="{{ $data->clockout_address }}">
                          <input type="hidden" class="form-control" id="inputClockoutLocation" name="clockout_location"
                            value="{{ $data->clockout_location }}">
                        </div>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="input-group" data-target-input="nearest">
                        @if ($data->clockout_photo)
                          <a href="{{ $data->clockout_photo }}" data-lightbox="lightbox" title="Clock Out">
                            <img src="{{ $data->clockout_photo }}" alt="{{ $data->clockout_address }}"
                              style="height: 92px;">
                          </a>
                        @endif
                      </div>
                    </div>

                  </div>
                </blockquote>

                @if ($data->getReceipts->count() > 0)
                  <blockquote class="quote-secondary">
                    <div class="row">

                      <div class="col-12">
                        <h3 class="card-title">Receipts</h3>
                      </div>
                      <div class="col-12">
                        <div class="card-body table-responsive">
                          <table class="table table-bordered table-hover">
                            <thead>
                              <tr>
                                <th>Image</th>
                                <th>Receipt</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th style="width: 100px;"></th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach ($data->getReceipts as $receipt)
                                <tr @if($receipt['status']=='Rejected') class="table-danger" @endif @if($receipt['status']=='Requested') style="background-color: #0000AF;color:white !important;" @endif role="row" data-widget="expandable-table" aria-expanded="false">
                                  <td @if($receipt['status']=='Requested') style="color:white !important;" @endif>@if($receipt['image']) <a href="{{ $receipt['image'] }}" data-lightbox="lightbox"
                                      title="{{ $receipt['receipt'] }}">
                                      <img src="{{ $receipt['image'] }}" alt="{{ $receipt['receipt'] }}"
                                        style="height: 30px;">
                                    </a> @endif</td>
                                  <td @if($receipt['status']=='Requested') style="color:white !important;" @endif>
                                    {{ $receipt['receipt'] }}
                                  </td>
                                  <td @if($receipt['status']=='Requested') style="color:white !important;" @endif>{{ $receipt['amount'] }}</td>
                                  <td @if($receipt['status']=='Requested') style="color:white !important;" @endif>{{ $receipt['status'] }}</td>
                                  <td class="text-center">
                                    <a class="btn btn-default btn-sm" href="#" data-toggle="modal"
                                      data-target="#modal-receipt-{{ $receipt['id'] }}" title="Edit"
                                      style="color: green">
                                      <span class="fa fa-edit"></span> View
                                    </a>
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>

                    </div>
                  </blockquote>
                @endif

                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="inputHourlyRate">Total time</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fa fa-clock"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Hourly rate"
                          value="{{ $data->total_time }}" disabled>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="inputHourlyRate">Hourly rate</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">$</span>
                        </div>
                        <input type="text" class="form-control" id="inputHourlyRate" name="hourly_rate"
                          placeholder="Hourly rate" value="{{ $data->hourly_rate }}">
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="inputEarnedAmount">Earned amount</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">$</span>
                        </div>
                        <input type="text" class="form-control" id="inputEarnedAmount" name="earned_amount"
                          placeholder="Earned amount" value="{{ $data->earned_amount }}">
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="inputBonusPay">Other Pay</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">$</span>
                        </div>
                        <input type="text" class="form-control" id="inputBonusPay" name="bonus_pay"
                          placeholder="Other Pay" value="{{ $data->bonus_pay }}">
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="inputComment">Notes</label>
                    <textarea class="form-control" id="inputComment" name="comment" rows="3"
                      placeholder="Enter ...">{{ $data->comment }}</textarea>
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

  @include('admincp.clocks.receipt')
  <div class="modal fade" id="modal-clockin_location">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header">
          <form style="width: 100%">
            <div class="input-group input-group-sm">
              <input type="text" id="search_location" class="form-control" placeholder="Search location">
              <span class="input-group-append">
                <button type="submit" class="btn btn-info btn-flat get_map">Locate</button>
              </span>
            </div>
          </form>
        </div>
        <div class="box-body">
          <div id="geomap">Google Maps</div>
          <input type="hidden" class="search_latitude"
            value="@if ($data->clockin_location) {{ explode(',', $data->clockin_location)[0] }} @endif">
          <input type="hidden" class="search_longitude"
            value="@if ($data->clockin_location) {{ explode(',', $data->clockin_location)[1] }} @endif">
          <div class="m-3">
            <input type="text" class="form-control search_addr"
              value="@if ($data->clockin_address) {{ $data->clockin_address }} @endif">
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default modal-close" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="SelectLocationClockIn">Select
            Location</button>
        </div>

      </div>
    </div>
  </div>

  <div class="modal fade" id="modal-clockout_location">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header">
          <form style="width: 100%">
            <div class="input-group input-group-sm">
              <input type="text" id="clockout_location" class="form-control" placeholder="Search location">
              <span class="input-group-append">
                <button type="submit" class="btn btn-info btn-flat clockout_get_map">Locate</button>
              </span>
            </div>
          </form>
        </div>
        <div class="box-body">
          <div id="clockout_geomap">Google Maps</div>
          <input type="hidden" class="clockout_latitude"
            value="@if ($data->clockout_location) {{ explode(',', $data->clockout_location)[0] }} @endif">
          <input type="hidden" class="clockout_longitude"
            value="@if ($data->clockout_location) {{ explode(',', $data->clockout_location)[1] }} @endif">
          <div class="m-3">
            <input type="text" class="form-control clockout_address"
              value="@if ($data->clockout_address) {{ $data->clockout_address }} @endif">
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default modal-close" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="SelectLocationClockOut">Select
            Location</button>
        </div>

      </div>
    </div>
  </div>
  <!-- /.content -->
  <link rel="stylesheet" href="{{ asset('public/css/lightbox.css') }}">
  <link rel="stylesheet" href="{{ asset('public/css/tempusdominus-bootstrap-4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('public/css/select2.min.css') }}">
  <style>
    #geomap,
    #clockout_geomap {
      width: 100%;
      height: 450px;
    }

    .gllpSearchField:focus,
    .gllpSearchButton:focus {
      box-shadow: none;
    }

    .select2-container .select2-selection--single {
      height: 38px;
      border: 1px solid #ced4da;
      border-radius: 4px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 38px;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
      padding-left: 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__clear {
      display: none;
    }

  </style>
@endsection
@section('script')
  <script src="{{ asset('public/js/lightbox.js') }}"></script>
  <script src="{{ asset('public/js/moment.min.js') }}"></script>
  <script src="{{ asset('public/js/tempusdominus-bootstrap-4.min.js') }}"></script>
  <script src="{{ asset('public/js/select2.full.min.js') }}"></script>
  <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyB9kWiQIyl6SMoWAQ3xg7bJOYM43LP50bc&libraries=places">
  </script>
  <script src="{{ asset('public/js/map.js') }}"></script>
  <script src="{{ asset('public/js/clockout-map.js') }}"></script>

  <script src="{{ asset('public/js/bootstrap.bundle.min.js') }}"></script>
  <script>
    jQuery(function() {

      jQuery('#event_wp_id').select2({
        placeholder: "",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
          delay: 1000,
          url: "/admincp/clocks/get-event",
          dataType: 'json',
          data: function(term, page) {
            return {
              q: term,
              page_limit: 10,
            };
          },
          processResults: function(data) {
            return {
              results: jQuery.map(data, function(item) {
                return {
                  id: item.ID,
                  text: item.event_title
                }
              })
            };
          },
        },
        templateSelection: formatRepoSelectionEvent
      });

      function formatRepoSelectionEvent(repo) {
        jQuery('#event_name').val(repo.text);
        return repo.full_name || repo.text;
      }

      jQuery('#user_wp_id').select2({
        placeholder: "",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
          delay: 1000,
          url: "/admincp/clocks/get-user",
          dataType: 'json',
          data: function(term, page) {
            return {
              q: term,
              page_limit: 10,
            };
          },
          processResults: function(data) {
            return {
              results: jQuery.map(data, function(item) {
                return {
                  id: item.id,
                  text: item.name,
                  email: item.email
                }
              })
            };
          },
        },
        templateSelection: formatRepoSelectionUser
      });

      function formatRepoSelectionUser(repo) {
        jQuery('#name').val(repo.text);
        jQuery('#email').val(repo.email);
        return repo.full_name || repo.text;
      }

      jQuery('#created_at').datetimepicker({
        format: 'YYYY-MM-DD'
      });
      jQuery("[name='created_at']").focus(function() {
        jQuery("#created_at .fa-calendar").trigger("click");
      });

      jQuery('#clockin').datetimepicker({
        format: 'LT',
        @if ($data->clockin)
          date: '{{ date('g:i A', strtotime($data->clockin)) }}'
        @endif
      });
      jQuery('#clockout').datetimepicker({
        format: 'LT',
        @if ($data->clockout)
          date: '{{ date('g:i A', strtotime($data->clockout)) }}'
        @endif
      });
      jQuery("[name='clockin']").focus(function() {
        jQuery("#clockin .fa-clock").trigger("click");
      });
      jQuery("[name='clockout']").click(function() {
        jQuery("#clockout .fa-clock").trigger("click");
      });

      jQuery("[name='clockin_address']").focus(function() {
        jQuery(".open-map-clockin").trigger("click");
      });

      jQuery("[name='clockout_address']").focus(function() {
        jQuery(".open-map-clockout").trigger("click");
      });

      jQuery("#SelectLocationClockIn").click(function() {
        jQuery("#inputClockinAddress").val(jQuery("#modal-clockin_location .gllpLocationName").val());
        jQuery("#inputClockinLocation").val(jQuery("#modal-clockin_location .gllpLatitude").val() + ',' + jQuery(
          "#modal-clockin_location .gllpLongitude").val());
        jQuery("#modal-clockin_location .modal-close").trigger("click");
      });

      jQuery("#SelectLocationClockOut").click(function() {
        jQuery("#inputClockoutAddress").val(jQuery("#modal-clockout_location .gllpLocationName").val());
        jQuery("#inputClockoutLocation").val(jQuery("#modal-clockout_location .gllpLatitude").val() + ',' +
          jQuery(
            "#modal-clockout_location .gllpLongitude").val());
        jQuery("#modal-clockout_location .modal-close").trigger("click");
      });


      jQuery("#SelectLocationClockIn").click(function() {
        jQuery("#inputClockinAddress").val(jQuery("#modal-clockin_location .search_addr").val());
        jQuery("#inputClockinLocation").val(jQuery("#modal-clockin_location .search_latitude").val() + ',' +
          jQuery(
            "#modal-clockin_location .search_longitude").val());
        jQuery("#modal-clockin_location .modal-close").trigger("click");
      });

      jQuery("#SelectLocationClockOut").click(function() {
        jQuery("#inputClockoutAddress").val(jQuery("#modal-clockout_location .clockout_address").val());
        jQuery("#inputClockoutLocation").val(jQuery("#modal-clockout_location .clockout_latitude").val() + ',' +
          jQuery(
            "#modal-clockout_location .clockout_longitude").val());
        jQuery("#modal-clockout_location .modal-close").trigger("click");
      });

    })
  </script>
@endsection
