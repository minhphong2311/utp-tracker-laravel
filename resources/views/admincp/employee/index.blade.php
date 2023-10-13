@extends('admincp.master')
@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-12">
          <h1 class="m-0">{{ $title }}s</h1>
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
              {{-- <a href="{{ url('/admincp/' . $type . '/create') }}" class="btn btn-danger btn-sm"><span
                  class="fas fa-plus"></span>
                Add</a> --}}

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
                          value="{{ request()->input('search_text') }}" placeholder="User" autocomplete="off">
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
                    <th>#</th>
                    <th>Employees</th>
                    <th>Email</th>
                    <th>Total Time</th>
                    <th>Total Earned</th>
                    <th></th>
                  </tr>
                </thead>
                @if ($data)
                  <tbody>
                    @foreach ($data as $index)
                      <tr role="row" data-widget="expandable-table" aria-expanded="false">
                        <td>{{ $index->id }}</td>
                        <td>{{ $index->name }}</td>
                        <td>{{ $index->email }}</td>
                        <td>
                            @php
                                $sum_minutes = 0;
                                $sumTime = "00:00";
                                if($index->getClocks){
                                    foreach($index->getClocks as $item):
                                        if($item['total_time'] != ''){
                                            $explodedTime = array_map('intval', explode(':', $item['total_time'] ));
                                            $sum_minutes += $explodedTime[0]*60+$explodedTime[1];
                                        }
                                    endforeach;
                                    $sumTime = sprintf('%02d:%02d',(floor($sum_minutes/60)), floor($sum_minutes % 60));
                                }
                            @endphp
                            {{ $sumTime }}</td>
                        <td>
                            @php
                                $result = 0;
                                if($index->getClocks){
                                    foreach ($index->getClocks as $item){
                                        $result += round(floatval($item['earned_amount']), 2) ;
                                    }
                                }

                                $total_earned = round(floatval($result), 2);
                            @endphp
                            @if($total_earned) $ {{ $total_earned }} @endif
                        </td>
                        <td class="text-center">
                          <a class="btn btn-default btn-sm" href="{{ url('admincp/' . $type . '/edit/' . $index->id) }}"
                            title="Edit" style="color: green">
                            <span class="fa fa-eye"></span> View
                          </a>
                        </td>
                      </tr>
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
