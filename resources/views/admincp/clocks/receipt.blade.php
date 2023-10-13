@if ($data->getReceipts)
  @foreach ($data->getReceipts as $item)
    <div class="modal fade" id="modal-receipt-{{ $item['id'] }}">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">

          <div class="modal-header">
            <h4 class="modal-title">Receipt: {{ $item['receipt'] }}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>

          <form class="form-horizontal" method="POST" action="{{ url('admincp/receipt/edit/' . $item['id']) }}"
            enctype="multipart/form-data" autocomplete="off">
            {{ csrf_field() }}
            <div class="box-body">

              <div class="card-body table-responsive">
                <div class="row">
                  <div class="col-12 text-center mb-3">
                    @if ($item['image'])
                      <img src="{{ $item['image'] }}" alt="{{ $item['receipt'] }}"
                        style="max-height: 500px;max-width: 100%;">
                    @endif
                  </div>
                </div>
                <div class="row">
                  <div class="col-6">
                    <div class="form-group">
                      <label>Amount:</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">$</span>
                        </div><input type="text" class="form-control" value="{{ $item['amount'] }}" disabled>
                      </div>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="form-group">
                      <label>Status</label>
                      <select name="status" class="form-select">
                        <option value="Approved" @if ($item->status == 'Approved') selected @endif>Approved</option>
                        <option value="Rejected" @if ($item->status == 'Rejected') selected @endif>Rejected</option>
                        <option value="Requested" @if ($item->status == 'Requested') selected @endif>Requested</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default modal-close" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Update</button>
            </div>

          </form>

        </div>
      </div>
    </div>
  @endforeach
@endif
