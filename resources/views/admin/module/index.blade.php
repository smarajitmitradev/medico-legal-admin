@extends('admin.layout.master')

@section('content')

<!-- jQuery (required for AJAX) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>

<style>
    .page-container { margin: 30px; }

    .card-custom {
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        border: none;
    }

    .card-header-custom {
        background: linear-gradient(135deg, #36b9cc, #258391);
        color: #fff;
        font-size: 20px;
        font-weight: 600;
        border-radius: 12px 12px 0 0;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-add {
        background: #fff;
        color: #36b9cc;
        font-weight: 600;
        border-radius: 8px;
        padding: 6px 15px;
        border: none;
    }

    .filter-box {
        background: #f8f9fc;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
    }
</style>

<div class="page-container">

    <div class="card card-custom">

        <!-- HEADER -->
        <div class="card-header-custom">
            <span>Module List</span>

            <a id="addBtn" href="#" class="btn btn-add" style="display:none;">
                + Add New
            </a>
        </div>

        <div class="card-body">

            <div class="filter-box">
                <div class="row">

                    <!-- Management -->
                    <div class="col-md-4">
                        <label>Management</label>
                        <select id="management" class="form-control">
                            <option value="">Select</option>
                            @foreach($managements as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- SubManagement -->
                    <div class="col-md-4">
                        <label>Sub Management</label>
                        <select id="submanagement" class="form-control">
                            <option value="">Select</option>
                        </select>
                    </div>

                    <!-- Button -->
                    <div class="col-md-4 d-flex align-items-end">
                        <button id="loadData" class="btn btn-primary w-100">
                            Load Modules
                        </button>
                    </div>

                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Details</th>
                            <th>Video</th>
                            <th>PDF</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>

                    <tbody id="moduleBody">
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Select filters and click "Load Modules"
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>

<script>

$(document).ready(function(){
    
    let subUrl = "{{ url('admin/get-submanagement') }}";
    let moduleUrl = "{{ url('admin/get-modules') }}";
    let moduleBaseUrl = "{{ url('admin/module') }}";

    // 🔽 Load SubManagement
    $('#management').on('change', function(){

        let id = $(this).val();

        if(!id){
            $('#submanagement').html('<option value="">Select</option>');
            return;
        }

        $('#submanagement').html('<option>Loading...</option>');

        $.ajax({
            url: subUrl + '/' + id,
            type: "GET",
            success: function(data){

                console.log(data)

                let html = '<option value="">Select</option>';

                if(data.length > 0){
                    data.forEach(item => {
                        html += `<option value="${item.id}" data-slug="${item.slug}">
                                    ${item.name}
                                 </option>`;
                    });
                }else{
                    html = '<option value="">No SubManagement Found</option>';
                }

                $('#submanagement').html(html);
            },
            error: function(){
                alert('Error loading SubManagement');
            }
        });

    });


    // 🔽 Load Modules
    $('#loadData').on('click', function(){

        let sub_id = $('#submanagement').val();
        let slug = $('#submanagement option:selected').data('slug');

        if(!sub_id){
            alert('Please select SubManagement');
            return;
        }

        // ✅ Set Add Button dynamically
        $('#addBtn')
            .show()
            .attr('href', moduleBaseUrl + '/' + slug + '/create');

        // ✅ Load module table
        $.ajax({
            url: moduleUrl,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                submanagement_id: sub_id
            },
            success: function(data){
                $('#moduleBody').html(data);
            },
            error: function(){
                alert('Error loading modules');
            }
        });

    });

});
</script>



@endsection


