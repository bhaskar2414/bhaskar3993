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
<!--                          <li><a onclick="doaction('post','Active');" >Active</a></li>
                        <li><a onclick="doaction('post','Inactive');" >Inactive</a></li>-->
                        <li><a onclick="doaction('user','Delete');" title="User will be no longer available to view">Delete User Permanently</a></li>
                        <li><a onclick="not_violating('user/not-violating');" title="Remove user from this list as it's not violating any rules">Remove from list</a></li>
                      </ul>
                    </div>
                </div>
                <div class="box-body">
                  <table id="example2" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th><input class="mainchk" type="checkbox"></th>
                        <th>Name</th>
                        <th>Total Reports</th>                        
                        <th></th>
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
<!--<script>
    $(function () {
        var d = [{
            "targets": -1,"data": 0,
            "render": function(data, type, row) {
                var url = "{{url('location')}}";
                return '<a class="btn btn-info" href="'+url+'">Edit</a>';
            }
        }];
        var url  ="{{url('location')}}";
        //datatable(url,d);
    });
</script>-->
<script>
$(function() {
   $('#example2').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
                'url': "{{url('/')}}/{{$controller}}/get-report/{{Auth::guard('admin')->id()}}",
                'type': 'POST',
                'headers': {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
        },
        columns: [
            { data: 'id'},            
            { data: 'reportedto'},
            { data: 'totalreports'},            
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

function not_violating(url) {
    var len = $(".innerallchk:checked").length;
    $("#msgfortitle").html("Warning Message");
    if (len == 0) {
        $("#msgfordio").html("Please select at least one record to continue.");
        $("#btnfordio").html('<button type="button" data-dismiss="modal" class="btn btn-primary">Ok</button>');
        $(".modal").modal("show");
    } else {
        var val = $('.innerallchk:checked').map(function () {
            return this.value;
        }).get();
        $("#msgfordio").html("Are you sure you want to approve user(s)?");
        val = "'" + val + "'"
        url = "'" + url + "'"
        $("#btnfordio").html('<button type="button" onclick="not_violating_action(' + url + ',' + val + ');" class="btn btn-danger">Ok</button><button type="button" data-dismiss="modal" class="btn btn-default canallchk">Cancel</button>');
        $(".modal").modal("show");
    }
}
function not_violating_action(url, val) {
    load();
    var u = url+"/"+val;
    $.ajax({
        url : "{{ url('') }}/"+u,
        type : "DELETE",
        data : {
            _token : "{{csrf_token()}}"
        },
        success : function (data) {
            unload();
            if(data.status == 200) {
                $("#msgfortitle").html("Success Message");
                $("#msgfordio").html("User(s) deleted from this list and approved.");
                $("#btnfordio").html('<button type="button" data-dismiss="modal" onclick="refresh();" class="btn btn-primary">Ok</button>');
                $(".modal").modal("show");
                $(".innerallchk, .mainchk").prop("checked","");
            } else {
                alert(data.msg);
            }
        },
        error : function (request,msg,error) {
            unload();
            alert("Something went wrong. Please try again.");
        }
    });
}
function get_details(id,type) {
    var u = "user/"+id;
    load();
    $.ajax({
        url: "{{ url('') }}/"+u,
        type: 'GET',
        dataType: 'json',
        headers: {
            'userid': '{{Auth::guard('admin')->id()}}',
            'type':type
        },
        success: function (data) {
            unload();
            if(data.status == 200) {
                $("#msgfortitle").html("Details");
                var ht = "<b>Title : </b>"+data.result.title;
                ht += "<br><b>Group : </b>"+data.result.group.name;
                ht += "<br><b>Description : </b>"+data.result.description;
                ht += "<br><b>Posted By : </b>"+data.result.user.firstname+" "+data.result.user.lastname;
                ht += "<br><b>Onya : </b>"+data.result.likes_count;
                ht += "<br><b>Reckon : </b>"+data.result.comments_count;
                if(data.result.location) {
                    ht += "<br><b>Location : </b>"+data.result.location;
                }
                if(data.result.rate) {
                    ht += "<br><b>Rate : </b>"+data.result.rate;
                }
                if(data.result.category) {
                    ht += "<br><b>Type : </b>"+data.result.category;
                }
                if(data.result.rideshare_date) {
                    var newDate = moment(data.result.rideshare_date).format("DD/MM/YYYY")
                    ht += "<br><b>Rideshare Date : </b>"+newDate;
                }
                if(data.result.datetodisplay) {
                    var newDate = moment(data.result.datetodisplay).format("DD/MM/YYYY")
                    ht += "<br><b>Posted Date : </b>"+newDate;
                }
                if(data.result.expiry) {
                    var newDate = moment(data.result.expiry).format("DD/MM/YYYY")
                    ht += "<br><b>Expiry Date : </b>"+newDate;
                }
                if(data.result.categories.length) {
                    
                    var comma = $(data.result.categories).map(function(e) { 
                        return this.name; 
                    }).get().join(',');
                    ht += "<br><b>Categories : </b>"+comma;
                }
                $("#msgfordio").html(ht);
                $("#btnfordio").html('<button type="button" data-dismiss="modal" class="btn btn-primary">Ok</button>');
                $(".modal").modal("show");
            } else {
                alert(data.msg);
            }
        },
        error: function (error) {
            unload();
            alert("Something went wrong. Please try again.");
        }
    });
}
</script>
@endsection