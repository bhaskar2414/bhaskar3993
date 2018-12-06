@extends('layouts.inner_app')

@section('content')
<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            {{ $title }}
          </h1>
          <ol class="breadcrumb">
            <li><a href="{{url('admin')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li>{{ $title }}</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div class="box-header">
                  
                  <div class="btn-group pull-right">
                      <button class="btn btn-warning" type="button">Action</button>
                      <button data-toggle="dropdown" class="btn btn-warning dropdown-toggle" type="button" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul role="menu" class="dropdown-menu">
                          <li><a onclick="doaction('user','Active');" >Active</a></li>
                        <li><a onclick="doaction('user','Inactive');" >Inactive</a></li>
                        <li><a onclick="doaction('user','Delete');" >Delete</a></li>
                      </ul>
                    </div>
                  <a href="{{url('')}}/{{$controller}}/create" class="btn btn-primary pull-left">Add {{ucfirst($controller)}}</a>
                </div>
                <div class="box-body">
                  <table id="example2" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th><input class="mainchk" type="checkbox"></th>
                        <th>Company name</th>
                        <th>Contact Person name</th>
                        <th>Email</th>
                        <!-- <th>Role</th> -->
                        <th>Registration Date</th>
                         <th>Premium user</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                    
                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div>
<script>
$(function() {
    var url = "{{url('')}}/company/getall";
    $('#example2').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
                'url': url,
                'type': 'POST',
                'data' : {controller : '{{$controller}}'},
                'headers': {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
        },
        columns: [
            { data: 'id'},
            { data: 'company_name'},
            { data: 'contact_person_name'},
            { data: 'email'},
            //{ data: 'role'},
            { data: 'reg_date'},
            { data: 'is_premium'},
            { data: 'status'},
            { data: 'action'}
        ]
    });
});
$("body").delegate(".delsing","click",function () {
    var id = $(this).attr("id");
    $(".innerallchk, .mainchk").prop("checked","");
    $(this).parents("tr").find(".innerallchk").prop("checked",true);
    doaction("user","Delete");
});
</script>
@endsection