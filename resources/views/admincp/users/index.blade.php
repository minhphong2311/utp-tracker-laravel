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
              <a href="{{ url('/admincp/' . $type . '/create') }}" class="btn btn-danger btn-sm"><span
                  class="fas fa-plus"></span>
                Add</a>

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

            </div>
            <div class="card-body table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Active</th>
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
                          @if ($index->active)
                            <span class="badge bg-success">On</span>
                          @else
                            <span class="badge bg-danger">Off</span>
                          @endif
                        </td>
                        <td class="text-center">
                          <a class="btn btn-default btn-sm" href="{{ url('admincp/' . $type . '/edit/' . $index->id) }}"
                            title="Edit" style="color: green">
                            <span class="fa fa-edit"></span> Edit
                          </a>
                          @if ($index->id != 1)
                            &nbsp;&nbsp;
                            <a class="btn btn-default btn-sm"
                              href="{{ url('admincp/' . $type . '/delete/' . $index->id) }}" title="Delete"
                              style="color: red"
                              onclick="return confirm('Delete {{ $title }} {{ $index->name }}?')">
                              <span class="fa fa-trash"></span> Delete
                            </a>
                          @endif
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
