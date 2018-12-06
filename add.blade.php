@extends('layouts.inner_app')

@section('content')
<div class="content-wrapper" style="min-height: 946px;">
  <section class="content">
    <div class="row">
      <!-- left column -->
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header">
            <h3 class="box-title">{{$title}}</h3>
            <div id="msg"></div>
          </div><!-- /.box-header -->
          <hr>
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs" id="mytab">
              <li class="active"><a href="#tab_1" data-toggle="tab">General</a></li>
              <li class="other-tabs" id="cv_docs"><a href="#tab_2" data-toggle="tab">CV Document</a></li>
              <li class="other-tabs" id="work_history" ><a href="#tab_3" data-toggle="tab">Work history</a></li>
              <li class="other-tabs" id="education"><a href="#tab_4" data-toggle="tab">Education</a></li>
              <li class="other-tabs" id="language"><a href="#tab_5" data-toggle="tab">Language</a></li>
              <li class="other-tabs" id="industry_experience"><a href="#tab_6" data-toggle="tab">Industry experience</a></li>
              <li class="other-tabs" id="project_experience"><a href="#tab_7" data-toggle="tab">Project experience</a></li>
              <li class="other-tabs" id="role_experience"><a href="#tab_8" data-toggle="tab"> Specialization </a></li>
              <li class="other-tabs" id="keywords"><a href="#tab_9" data-toggle="tab">Keyword</a></li> 
              <li class="other-tabs" id="references"><a href="#tab_10" data-toggle="tab">References </a></li>  
             <!--  <li class="company-tabs" id="tender_pdf"><a href="#tab_11" data-toggle="tab">Tender Document</a></li>  -->      
              <li class="pull-right"><a href="#" class="text-muted"></a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
                <form id="frm1"  method="post" action="" enctype="multipart/form-data">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  @isset($data)
                    <input type="hidden" name="_method" value="PUT">
                  @endisset
                  <div class="box-body">
                    @isset($data)
                      @if($controller == 'company')
                        <h3 class="box-title">Company Info</h3>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">                                          
                            <div class="form-group">
                              <label>Company Name<span class="error">*</span> </label>
                              <input type="text" class="form-control" value="@isset($companydata){{$companydata->company_name}}@endisset" name="company_name" placeholder="Enter company name">
                            </div>
                                <div class="form-group">
                              <label>County<span class="error">*</span></label>
                              <select name="county_id" class="form-control">
                                  <option value="">Select Country</option>
                                  @forelse($countries as $country)
                                    <option @isset($companydata) @if($companydata->county_id == $country->id) selected="selected" @endif @endisset value="{{$country->id}}">{{$country->name}}</option>
                                  @empty
                                    <option value="">Select Country</option>
                                  @endforelse
                              </select>
                            </div>  
                            <div class="form-group">
                              <label>Telephone<span class="error">*</span></label>
                              <input type="text" class="form-control phone-validation" value="@isset($companydata){{$companydata->telephone}}@endisset" name="telephone" placeholder="Enter telephone">
                            </div>
                              <div class="form-group">
                              <label>Annual Sales Turnover ('000s USD)<span class="error">*</span></label>
                                <!-- @php 
                                    $anualTurnover_array = array();
                                    $anualTurnover_array = $anualTurnover->toArray();
                                    if(!empty($anualTurnover_array)){
                                        foreach($anualTurnover_array as $key=> $anual){
                                            $amount_array = explode(' ',$anual['name']);
                                            //var_dump($amount_array);
                                            $amount_str = array_first($amount_array);
                                            $amount = str_replace( ',', '', $amount_str);
                                            if( is_numeric( $amount ) ) {
                                                $amount_str = $amount;
                                            }
                                            $key_array[$key] = $amount;
                                            asort($key_array);
                                        }
                                        //dd($key_array);
                                    }
                                    
                                @endphp
                               <select name="annual_turnover" class="form-control">
                                  <option value="">Select Anual Tunrnover</option>
                                  @forelse($key_array as $key=>$anual)
                                    <option @isset($companydata) @if($companydata->annual_turnover == $anualTurnover_array[$key]['id']) selected="selected" @endif @endisset value="{{$anualTurnover_array[$key]['id']}}">{{$anualTurnover_array[$key]['name']}}</option>
                                  @empty
                                    <option value="">Select Anual Tunrnover</option>
                                  @endforelse
                              </select> -->

                              <select name="annual_turnover" class="form-control">
                                  <option value="">Select Annual Sales Tunrnover</option>
                                  @forelse($anualTurnover as $anual)
                                    <option @isset($companydata) @if($companydata->annual_turnover == $anual->id) selected="selected" @endif @endisset value="{{$anual->id}}">{{$anual->name}}</option>
                                  @empty
                                    <option value="">Select Annual Sales Turnover</option>
                                  @endforelse
                              </select>
                             <!--  <input type="text" class="form-control" value="@isset($companydata){{$companydata->annual_turnover}}@endisset" name="annual_turnover" placeholder="Enter annual turnover"> -->
                            </div>
                                                      
                          </div>
                          <div class="col-md-4">
                             <div class="form-group">
                              <label>Address<span class="error">*</span></label>
                              <textarea class="form-control" rows="4" name="address" placeholder="Enter address">@isset($companydata){{$companydata->address}}@endisset</textarea>
                            </div>
                             <div class="form-group">
                              <label>Industry<span class="error">*</span> </label>
                              <select name="industry_id" class="form-control">
                                <option value="">Select Industry</option>
                                @forelse($industries as $industry)
                                  <option @isset($companydata) @if($companydata->industry_id == $industry->id) selected="selected" @endif @endisset value="{{$industry->id}}">{{$industry->name}}</option>
                                @empty
                                  <option value="">Select Industry</option>
                                @endforelse
                              </select>
                            </div>
                           <div class="form-group">
                              <label>Company headcount</label>
                              <input type="text" class="form-control" value="@isset($companydata){{$companydata->company_size}}@endisset" name="company_size" placeholder="Enter company size">
                            </div>
                           
                          
                          </div>
                          <div class="col-md-4">  
                            <div class="form-group">
                              <label>Postcode<span class="error">*</span></label>
                              <input type="text" class="form-control" value="@isset($companydata){{$companydata->postcode}}@endisset" name="postcode" placeholder="Enter postcode">
                            </div> 
                                                               
                                 <div class="form-group">
                              <label>Company Type<span class="error">*</span></label>
                              <select name="company_type" class="form-control">
                                  <option value="">Select Company Type</option>
                                  @forelse($company_types as $company_type)
                                  <option @isset($companydata) @if($companydata->company_type == $company_type->id) selected="selected" @endif @endisset value="{{$company_type->id}}">{{$company_type->name}}</option>
                                  @empty
                                  <option value="">Select Company Type</option>
                                  @endforelse
                              </select>
                             <!--  <input type="text" class="form-control" value="@isset($companydata){{$companydata->company_type}}@endisset" name="company_type" placeholder="Enter company type"> -->
                            </div>                    
                          
                            <div class="form-group">
                              <label>website<span class="error">*</span></label>
                              <input type="text" class="form-control" value="@isset($companydata){{$companydata->website}}@endisset" name="website" placeholder="Enter website">
                            </div>
                            <div class="form-group">
                              <label>Image</label>
                              <input type="file" name="profile_pic" class="imagefile" accept='image/*'>
                            </div>
                        @isset($data)
                          <div class="imagecont">
                            @if(!empty($data->profile_pic))
                              <a class="fancybox-media" rel="profile_pic" href="{{url('/')}}/{{$data->profile_pic}}" alt="Image"><img onclick="openfancy();" src="{{url('/')}}/{{$data->profile_pic}}" heigth="50" width="50"></a>
                              <a class="cpforall deletecurrentimage"><i class="fa fa-trash" title="Delete image"></i></a>
                            @endif
                          </div>
                        @endisset
                           
                          </div>        
                          <div class="col-md-12">
                            <div class="form-group">
                              <label>About Company Info<span class="error">*</span></label>
                              <textarea class="form-control" name="comp_info_description" placeholder="Enter description about company Info">@isset($companydata){{$companydata->description}}@endisset</textarea>
                              <!-- <input type="text" name="userid" value="@isset($data){{$userid}}@endisset"> -->
                            </div>
                          </div>
                        </div>
                        <!--  <h3 class="box-title">Project Manager Contact</h3>
                        <hr>
                        
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="lead_contact_name">Name</label>
                              <input type="text" name="lead_contact_name" value="@isset($leadcontactdata){{$leadcontactdata->name}}@endisset" class="form-control" id="contact_name" />
                            </div>
                            <div class="form-group">
                              <label for="job-title1">Job Title</label>
                              <input type="text" name="lead_contact_job_title" value="@isset($leadcontactdata){{$leadcontactdata->job_title}}@endisset" class="form-control" id="lead_contact_job_title" />
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="department1">Department</label>
                              <input type="text" name="lead_contact_department" value="@isset($leadcontactdata){{$leadcontactdata->department}}@endisset" class="form-control" id="lead_contact_department" />
                            </div>
                            <div class="form-group">
                              <label for="lead_contact_telephone">Telephone</label>
                              <input type="tel" name="lead_contact_telephone" value="@isset($leadcontactdata){{$leadcontactdata->telephone}}@endisset" class="form-control" id="lead_contact_telephone" />
                            </div>
                            </div>
                            
                        </div>  

                        <h3 class="box-title">Company Contact</h3>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="contact_name">Name</label>
                              <input type="text" name="contact_name" value="@isset($contactdata){{$contactdata->name}}@endisset" class="form-control" id="contact_name" />
                            </div>
                            <div class="form-group">
                              <label for="job-title1">Job Title</label>
                              <input type="text" name="job_title" value="@isset($contactdata){{$contactdata->job_title}}@endisset" class="form-control" id="job-title1" />
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="department1">Department</label>
                              <input type="text" name="department" value="@isset($contactdata){{$contactdata->department}}@endisset" class="form-control" id="department1" />
                            </div>
                            <div class="form-group">
                              <label for="company_telephone">Telephone</label>
                              <input type="tel" name="company_telephone" value="@isset($contactdata){{$contactdata->telephone}}@endisset" class="form-control" id="telephone1" />
                            </div>
                            </div>
                            
                        </div>     -->  
                      @endif
                    @endisset
                     <h3 class="box-title">Contact Person</h3>
                        <hr>
                    <div class="row">
                      <div class="col-md-4">
                        <input type="hidden" name="role" value="{{ucfirst($controller)}}">    
                        <div class="form-group">
                          <label>First name <span class="error">*</span></label>
                          <input type="text" class="form-control" value="@isset($data){{$data->first_name}}@endisset" name="first_name" placeholder="Enter first name">
                        </div>
                        <div class="form-group">
                          <label>Last name <span class="error">*</span></label>
                          <input type="text" class="form-control" value="@isset($data){{$data->last_name}}@endisset" name="last_name" placeholder="Enter last name">
                        </div>
                        <div class="form-group">
                          <label>Email <span class="error">*</span></label>
                          <input type="text" class="form-control" value="@isset($data){{$data->email}}@endisset" name="email" placeholder="Enter email">
                        </div>
                        
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label>Password @if(!isset($data))<span class="error">*</span>@endif</label>
                          <input type="password" class="form-control"  name="password" id="password" placeholder="Enter password">
                        </div>
                        <div class="form-group">
                          <label>Confirm Password @if(!isset($data))<span class="error">*</span>@endif</label>
                          <input type="password" class="form-control" name="confirmpassword" placeholder="Enter password again">
                        </div>
                        <div class="form-group">
                          <label>Phone<span class="error">*</span></label>
                          <input type="text" class="form-control phone-validation" value="@isset($data){{$data->phone}}@endisset" name="phone" placeholder="Enter phone">
                        </div>
                      
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="exampleInputPassword1">Proposal Alert</label>
                          <select name="job_alert" class="form-control">
                            <option value="">Select Proposal Alert</option>
                            <option value="Yes" @isset($data) @if($data->job_alert == 'Yes') selected @endisset @endif >Yes</option>
                           <!--  <option value="Individual" @isset($data) @if($data->job_alert == 'Individual') selected @endisset @endif>Individual</option> -->
                            <option value="No" @isset($data) @if($data->job_alert == 'No') selected @endisset @endif>No</option>
                          </select>
                        </div>
                   
                      </div>
                      @isset($data)
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="exampleInputPassword1">Verify User</label>
                            <input type="checkbox" @isset($data) @if($data->is_email_verified == 1) disabled checked @endif @endisset class="verify-user-field" name="user_verify" value="1" id="user_verify_btn">
                        </div>
                   
                      </div>
                      @endisset
                    
                    </div>                    
                    @if($controller == 'consultant')
                      <h3 class="box-title">Consultant Info</h3>
                      <hr>
                      <div class="row">
                        <div class="col-md-4">                                          
                          <!-- <div class="form-group">
                            <label>Company Name <span class="error">*</span></label>
                            <input type="text" class="form-control" value="@isset($consultantdata){{$consultantdata->company_name}}@endisset" name="company_name" placeholder="Enter company name">
                          </div> -->
                 
                        <div class="form-group">
                          <label>Country<span class="error">*</span></label>
                          <select name="location_id" class="form-control">
                            <option value="">Select Country</option>
                            @forelse($countries as $country)
                              <option @isset($data) @if($data->location_id == $country->id) selected="selected" @endif @endisset value="{{$country->id}}">{{$country->name}}</option>
                            @empty
                              <option value="">Select Country</option>
                            @endforelse
                          </select>
                        </div>

                           <div class="form-group">
                            <label>Highest Qualification</label>
                            <select name="highest_qualification_id" class="form-control">
                              <option value="">Select</option>
                              @forelse($highest_qualifications as $qualification)
                                <option @isset($consultantdata) @if($consultantdata->highest_qualification_id == $qualification->id) selected="selected" @endif @endisset value="{{$qualification->id}}">{{$qualification->name}}</option>
                              @empty
                                <option value=""></option>
                              @endforelse
                            </select>
                          </div> 
                          <div class="form-group">
                            <label>Day rate range (optional)</label>
                            <select name="currency_id" class="form-control">
                              @forelse($currencies as $currency)
                                <option @isset($consultantdata) @if($consultantdata->currency_id == $currency->id) selected="selected" @endif @endisset value="{{$currency->id}}">{{$currency->name}}</option>
                              @empty
                                <option value="">Select Industry</option>
                                @endforelse
                            </select>
                          </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-group">
                            <label>Field Of Study</label>
                            <select name="field_of_study" class="form-control">
                              <option value="">Select</option>
                              @forelse($field_of_studies as $field)
                                <option @isset($consultantdata) @if($consultantdata->field_of_study == $field->id) selected="selected" @endif @endisset value="{{$field->id}}">{{$field->name}}</option>
                              @empty
                                <option value=""></option>
                              @endforelse
                            </select>
                          </div>                       
                          <div class="form-group">
                            <label>When can you start? </label>
                            <select name="when_start_work" class="form-control">
                              <option value="">Select</option>
                              <option @isset($consultantdata) @if($consultantdata->when_start_work == 'Immediately') selected="selected" @endif @endisset value="Immediately">Immediately</option>
                              <option @isset($consultantdata) @if($consultantdata->when_start_work == 'Within 1-2 weeks') selected="selected" @endif @endisset value="Within 1-2 weeks">Within 1-2 weeks</option>
                              <option @isset($consultantdata) @if($consultantdata->when_start_work == '1 month') selected="selected" @endif @endisset value="1 month">1 month</option>
                              <option @isset($consultantdata) @if($consultantdata->when_start_work == '>1 month') selected="selected" @endif @endisset value=" >1 month"> >1 month</option>
                            </select>
                          </div>
                           <div class="form-group">
                            <label>LinkedIn Profile Link</label>
                            <input type="text" class="form-control" value="@isset($data){{$data->linkedin_profile_link}}@endisset" name="linkedin_profile_link" placeholder="Enter LinkedIn Link">
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>How many hours can you work a week?</label>
                            <select name="how_many_hours_can_work" class="form-control">
                              <option value="">Select</option>
                              <option @isset($consultantdata) @if($consultantdata->how_many_hours_can_work == '< 5 hours per week') selected="selected" @endif @endisset value="< 5 hours per week"> <5 hours per week </option>
                              <option @isset($consultantdata) @if($consultantdata->how_many_hours_can_work == '5 - 10 hours per week') selected="selected" @endif @endisset value="5 - 10 hours per week">5 - 10 hours per week</option>
                              <option @isset($consultantdata) @if($consultantdata->how_many_hours_can_work == '10 - 20 hours per week') selected="selected" @endif @endisset value="10 - 20 hours per week">10 - 20 hours per week</option>
                              <option @isset($consultantdata) @if($consultantdata->how_many_hours_can_work == '20 - 40 hours per week month') selected="selected" @endif @endisset value="20 - 40 hours per week month">20 - 40 hours per week</option>
                              <option @isset($consultantdata) @if($consultantdata->how_many_hours_can_work == '+40 hours per week') selected="selected" @endif @endisset value="+40 hours per week">+40 hours per week</option>
                            </select>
                          </div>                                          
                          <div class="form-group">
                            <label>Would you travel? </label>
                            <select name="whould_travel" class="form-control">
                              <option value="">Select</option>
                              <option @isset($consultantdata) @if($consultantdata->whould_travel == 1) selected="selected" @endif @endisset value="1">Yes</option>
                              <option @isset($consultantdata) @if($consultantdata->whould_travel == 0) selected="selected" @endif @endisset value="0">No</option>
                            </select>
                          </div> 
 
                        
                   
                        </div> 
                      </div>
                      <div class="row">                       
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>From</label>
                            <input type="text" class="form-control" value="@isset($consultantdata){{$consultantdata->rate_from}}@endisset" name="rate_from" placeholder="Enter rate from">
                          </div>
                        </div> 
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>To</label>
                            <input type="text" class="form-control" value="@isset($consultantdata){{$consultantdata->rate_to}}@endisset" name="rate_to" placeholder="Enter rate to">
                          </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-group">
                              <label>Image</label>
                              <input type="file" name="profile_pic" class="imagefile" accept='image/*'>
                            </div>
                            </div>
                        @isset($data)
                          <div class="imagecont">
                            @if(!empty($data->profile_pic))
                              <a class="fancybox-media" rel="profile_pic" href="{{url('/')}}/{{$data->profile_pic}}" alt="Image"><img onclick="openfancy();" src="{{url('/')}}/{{$data->profile_pic}}" heigth="50" width="50"></a>
                              <a class="cpforall deletecurrentimage"><i class="fa fa-trash" title="Delete image"></i></a>
                            @endif
                          </div>
                        @endisset 
                      </div>

                      <div class="row"> 
                        <div class="col-md-12">
                            <div class="form-group">
                              <label>Profile Summary<span class="error">*</span></label>
                              <textarea class="form-control" name="description" placeholder="Enter description about user">@isset($data){{$data->description}}@endisset</textarea>
                              
                            </div>
                            </div>
                          </div>
                    @endif
                    
                  </div><!-- /.box-body --> 
                  <div class="box-footer">
                    <input type="submit" id="btn" class="btn btn-primary" value="Submit">
                   <!--  <a class="btn btn-warning" href="{{url('user')}}">Cancel</a> -->
                    <a class="btn btn-warning" href="{{url('')}}/{{$controller}}">Cancel</a>
                  </div>
                </form>
              </div>   
             
              <div class="tab-pane" id="tab_2">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    CV Documents                                      
                  </div>                  
                  <div class="error"></div>
                  <div class="panel-body">
                    <table class="table table-hover cv_docs">
                      <thead>
                        <th>File name</th>
                        <th>Date</th>
                        <th>Delete</th>
                      </thead>
                      <tbody>
                        @isset($users)
                         @if($users->consultantCv->count() > 0)
                          @foreach($users->consultantCv as $data)
                            <tr>
                              <td><a href="{{url('')}}/{{$data->path}}" target="_blank">{{$data->file_name}}</a></td>  
                              <td>{{dateFormat($data->created_at)}}</td>           
                              <td><a href="javascript:void(0);" id="destroyCV/{{$data->id}}" class="btn btn-xs btn-danger delete" title="Delete record" >Delete</a></td>
                            </tr>
                          @endforeach                                        
                          @endif 
                          @endisset
                      </tbody>
                    </table>
                  </div>
                </div>
                <form method="post" id="cvfrm"  enctype="multipart/form-data" >
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <div class="form-group">
                    <label for="WorkHistory">CV Document<span class="required_sign">*</span></label>
                    @if(count($errors) > 0)
                      <ul class="list-group">
                        @foreach($errors->all() as $error)
                          <li class="list-group-item text-danger">
                            {{$error}}
                          </li>
                        @endforeach
                      </ul>
                    @endif
                    <input type="hidden" id="user_id" name="user_id" value="@isset($data) {{$userid}} @endisset">
                    <input type="file" name="file_name" class="file_name" id="file_name">
                    <p class="text-sm text-bold show color-lighter-gray">Word or PDF format only</p>
                    <input type="submit" id="cvdoc" class="btn btn-primary btn-edu" value="Submit">            
                  </div>
                </form>
              </div>

              <div class="tab-pane" id="tab_3">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    Work Histories
                    <button type="button" class="pull-right btn btn-info btn-sm openModelBtn open-popup"  id="getWork"  data-target="#workHistory">Add work history</button>
                  </div>
                  <div class="panel-body">
                    <table class="table table-hover work_history">
                      <thead>
                        <th>Role</th>
                        <th>Company name</th>
                        <th>Experiece From-To</th>
                        <th>Description</th>
                        <th>Edit</th>
                        <th>Delete</th>
                      </thead>
                      <tbody>
                        @isset($users)
                          @if($users->workHistory->count() > 0)
                            @foreach($users->workHistory as $data)
                              <tr>
                                <td>{{$data->role}}</td>
                                <td>{{$data->company_name}}</td>
                             
                                <td>{{frontDateFormat($data->start)}}  -  
                                  @if($data->current == '1')
                                    {{'Current'}}
                                  @else
                                {{frontDateFormat($data->end)}}
                                  @endif
                                </td>

                                <td>
                                  <span>
                                    <a type="button" class="btn btn-link opendescription">View</a>
                                    <p style="display: none;">{!! nl2br($data->description) !!}</p>
                                  </span>
                                </td>
                                
                                <td><a href="javascript:void(0);" id="editWorkHistory/{{$data->id}}" class="openModelBtn btn btn-xs btn-info edit-row" title="Edit record" >Edit</a></td>
                                <td><a href="javascript:void(0);" id="destroyWorkHistory/{{$data->id}}" title="Delete record" class="btn btn-xs btn-danger delete">Delete</a></td>
                              </tr>
                            
                            @endforeach
                          @endif 
                        @endisset
                      </tbody>
                    </table>
                  </div>
                </div>                               
              </div>

              <div class="tab-pane" id="tab_4">
                <div class="panel panel-default">
                  <div class="panel-heading">
                      Educations
                         @php
                      $file_style = '';                    
                        if(!empty($users) && ($users->educations->count() === 3)):
                            $file_style = 'display:none';
                        endif;         
                    @endphp
                    <div class="education_hide" style="{!! $file_style !!}"> 
                      <button type="button" class="pull-right btn btn-info btn-sm openModelBtn open-popup" id="getEducation" data-target="#education"  style="margin-top: -21px;">Add education</button>
                    </div>
                  </div>
                  <div class="panel-body">
                    <table class="table table-hover education">
                      <thead>
                        <th>Degree</th>
                        <th>Field Of Study </th>
                        <th>Education From-To</th>
                        <th>Edit</th>
                        <th>Delete</th>
                      </thead>
                      <tbody>
                        @isset($users)
                          @if($users->educations->count() > 0)
                            @foreach($users->educations as $data)
                                <tr>
                                  <td>{{$data->degree}}</td>
                                  <td>{{$data->fieldOfStudy->name}}</td>
                                  <td>{{$data->start_year}} - {{$data->end_year}}</td>
                                  <td><a href="javascript:void(0);" id="editEducation/{{$data->id}}" title="Edit record" class=" openModelBtn btn btn-xs btn-info edit-row" >Edit</a></td>
                                  <td><a href="javascript:void(0);" id="destroyEducation/{{$data->id}}" title="Delete record" class="btn btn-xs btn-danger delete" >Delete</a></td>
                                </tr>
                            @endforeach
                          @endif
                        @endisset
                      </tbody>
                    </table>
                  </div>
                </div>                              
              </div>

              <div class="tab-pane" id="tab_5">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    Languages
                    <button type="button" class="pull-right btn btn-info btn-sm openModelBtn" id="getLanguage" data-target="#language">Add language</button>
                  </div>
                  <div class="panel-body">
                    <table class="table table-hover language">
                      <thead>
                        <th>Language</th>
                        <th>Proficiency</th>
                        <th>Edit</th>
                        <th>Delete</th>
                      </thead>
                      <tbody>
                        @isset($users)
                          @if($users->languages->count() > 0)
                            @foreach($users->languages as $data)
                              <tr>
                                <td>{{$data->languages->name}}</td>
                                <td>{{$data->proficiency}}</td>
                                <td><a href="javascript:void(0);" title="Edit record" id="editLanuage/{{$data->id}}" class=" openModelBtn btn btn-xs btn-info edit-row" >Edit</a></td>
                                <td><a href="javascript:void(0);" title="Delete record" id="destroylanguage/{{$data->id}}" class="btn btn-xs btn-danger delete" >Delete</a></td>                                 
                              </tr>
                            @endforeach
                          @endif 
                        @endisset
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="tab_6">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    Industry experience
                    <button type="button" class="pull-right btn btn-info btn-sm openModelBtn" id="getIndustryExperience" data-target="#industryExperience">Add industry experience</button>
                  </div>
                  <div class="panel-body">
                    <table class="table table-hover industry_experience">
                      <thead>
                        <th>Industry</th>
                        <th>Year experience</th>
                        <th>Edit</th>
                        <th>Delete</th>
                      </thead>
                      <tbody>
                        @isset($users)
                          @if($users->industry_experience->count() > 0)
                            @foreach($users->industry_experience as $data)
                              <tr>
                                <td>{{$data->experiences->name}}</td>
                                <td>{{$data->years}}</td>
                                <td><a href="javascript:void(0);" title="Edit record" id="editIndustryExperience/{{$data->id}}" class=" openModelBtn btn btn-xs btn-info edit-row" >Edit</a></td>
                                <td><a href="javascript:void(0);" title="Delete record" id="destroyIndustryExperience/{{$data->id}}" class="btn btn-xs btn-danger delete">Delete</a></td>
                              </tr>
                            @endforeach
                          @endif
                        @endisset
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="tab_7">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    Project type experience
                    <button type="button" class="pull-right btn btn-info btn-sm openModelBtn" id="getProjectExperience" data-target="#projectTypeExperience">Add project type experience</button>
                  </div>
                  <div class="panel-body">
                    <table class="table table-hover project_experience">
                      <thead>
                        <th>Project type</th>
                        <th>Year experience</th>
                        <th>Edit</th>
                        <th>Delete</th>
                      </thead>
                      <tbody>
                        @isset($users)
                          @if($users->project_experience->count() > 0)
                            @foreach($users->project_experience as $data)
                              <tr>
                                <td>{{$data->experiences->name}}</td>
                                <td>{{$data->years}}</td>
                                <td><a href="javascript:void(0);" title="Edit record" id="editProjectExperience/{{$data->id}}" class=" openModelBtn btn btn-xs btn-info edit-row" >Edit</a></td>
                                <td><a href="javascript:void(0);" title="Delete record" id="destroyProjectExperience/{{$data->id}}" class="btn btn-xs btn-danger delete">Delete</a></td>
                              </tr>
                            @endforeach
                          @endif 
                        @endisset
                      </tbody>
                    </table>
                  </div>
                </div>                               
              </div>

              <div class="tab-pane" id="tab_8">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    Specialization 
                    <button type="button" class="pull-right btn btn-info btn-sm openModelBtn" id="getRoleExperience" data-target="#roleExperience">Add  Specialization </button>
                  </div>
                  <div class="panel-body">
                    <table class="table table-hover role_experience">
                      <thead>
                      
                        <th>Specialization</th>
                        <th>Year experience</th>
                        <th>Edit</th>
                        <th>Delete</th>
                      </thead>
                      <tbody>
                        @isset($users)
                          @if($users->role_experience->count() > 0)
                            @foreach($users->role_experience as $data)
                              <tr>
                              
                                <td>{{$data->experiences->name}}</td>
                                <td>{{$data->years}}</td>
                                <td><a href="javascript:void(0);" title="Edit record" id="editRoleExperience/{{$data->id}}" class=" openModelBtn btn btn-xs btn-info edit-row" >Edit</a></td>
                                 <td><a href="javascript:void(0);" title="Delete record" id="destroyRoleExperience/{{$data->id}}" class="btn btn-xs btn-danger delete" >Delete</a></td>
                              </tr>
                            @endforeach
                          @endif 
                          @endisset
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="tab_9">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    Keywords
                    <button type="button" class="pull-right btn btn-info btn-sm openModelBtn add-info" id="getKeyword" data-target="#keywords">Add keyword</button>
                  </div>
                  <div class="panel-body">
                    <table class="table table-hover keywords">
                      <thead>
                        <th>Keywords</th>
                        <th>Delete</th>
                      </thead>
                      <tbody>
                        @isset($users)
                          @if($users->consultantkeywords->count() > 0)
  											    @foreach($users->consultantkeywords as $data)
    													<tr>
                                <td>{{$data->name}}</td>
                                <td><a href="javascript:void(0);" title="Delete record" id="destroyKeyword/{{$data->pivot->id}}" class="btn btn-xs btn-danger delete" >Delete</a></td>
                              </tr>
                            @endforeach
                          @endif
                        @endisset
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="tab_10">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    References
                    @php
                      $file_style = '';                    
                        if(!empty($users) && ($users->references->count() === 3)):
                            $file_style = 'display:none';
                        endif;         
                    @endphp
                    <div class="refrence_hide" style="{!! $file_style !!}">                      
                      <button type="button" class="pull-right btn btn-info btn-sm openModelBtn add-info" id="getReference" data-target="#reference" style="margin-top: -21px;">Add reference</button>
                    </div>
                  </div>
                  <div class="panel-body">
                    <table class="table table-hover references">
                      <thead>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Edit</th>
                        <th>Delete</th>
                      </thead>
                      <tbody>
                        @isset($users)
                          @if($users->references->count() > 0)
                            @foreach($users->references as $data)
                              <tr>
                                <td>{{$data->first_name}}</td>
                                <td>{{$data->last_name}}</td>
                                <td>{{$data->email}}</td>
                                <td>{{$data->company_name}}</td>
                                <td><a href="javascript:void(0);" title="Edit record" id="editReference/{{$data->id}}" class="openModelBtn btn btn-xs btn-info edit-row" ">Edit</a></td>
                                <td><a href="javascript:void(0);" title="Delete record" id="destroyReference/{{$data->id}}" class="btn btn-xs btn-danger delete txt1" value="{{$data->id}}" >Delete</a></td>
                              </tr>
                            @endforeach
                          @endif
                        @endisset
                      </tbody>
                    </table>
                  </div>
                </div>              
              </div>

            <!-- /.tab-pane -->
            </div>
          <!-- /.tab-content -->
          </div>
          <!-- nav-tabs-custom -->
        </div><!-- /.box -->
        <div  class="modal fade consultantmodel" role="dialog">
          
        </div>
      </div><!--/.col (left) -->
    </div>
  </section>
</div>
@endsection
@push('scripts')
  <script type="text/javascript">
    $(function () {
      $("body").on("click",".opendescription",function () {
          var t = $(this);
          var des = t.next("p").html();
          var html = '<div class="modal-dialog modal-md model-general "><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Description</h4><div class="error"></div></div><div class="modal-body">'+des+'</div></div></div>';
          $(".consultantmodel").html(html);
          $(".consultantmodel").modal('show');
          //alert(des);
      });
      $('.select2').select2()
      var active_table="";
      active_table=$('.nav-tabs li.active').attr('id'); 
      
      $('body').on('click','.edit-row',function(){
        $('.table-hover tr').removeClass("current-row");
        $(this).closest('tr').addClass("current-row");
      });

      var action = "{{$action}}";

      var controller = "{{$controller}}";
      if(action == 'edit' && controller=='consultant')
      {
        $('.other-tabs').attr('style','display:block !important;');
      
      
      } 

      if(action == 'edit' && controller=='company')
      {
        $('.company-tabs').attr('style','display:block !important;');
      } 
      $(".comp_image").on("change",function () {
          $(".hidden_comp_image").val('');
      });

      $("body").on("click","#user_verify_btn",function () {
          
          if($(this).is(":checked")){
            var checkbox = $(this);
            var url = "@isset($data){{url('verify-userby-admin/'.$userid)}}@endisset".trim();
            load();
            $.ajax({
              url : url,
              type: 'GET',
              headers: {
                  'X_CSRF_TOKEN':'{{ csrf_token() }}',
              },
              processData: false,
              contentType: false,
              success:function(data, textStatus, jqXHR){

                $("#msg").fadeIn("slow");
                $("#msg").removeClass();
                  if(data.status == 200) {
                      $("#msg").addClass("alert alert-success");
                      checkbox.attr("disabled", true);
                  } else {
                    $("#msg").addClass("alert alert-danger");
                  }
                  $("#msg").html(data.msg);
                  unload();
                  hidemsg(5000,2000);
              }
            });

          }
      });


      $("#btn").on("click",function (e) {
        e.preventDefault();
        var val = $("#frm1").validate({
          rules: {
              type : {
                  required: true
              },
              first_name : {
                  required: true
              },
              last_name : {
                  required: true
              },
              email : {
                  required: true,
                  email: true
              },
              location_id : {
                  required: true
              },
              description : {
                  required: true
              },            
              phone : {
                  required: true
              },
              company_name: {
                  required: true
              },
              industry_id: {
                  required: true
              },
              /*based_in: {
                  required: true
              },*/
             /* county_region: {
                  required: true
              },*/
              county_id: {
                  required: true
              },
              company_type: {
                  required: true
              },
              company_size: {
                  number: true
              },
              annual_turnover: {
                  required: true
              },
              postcode: {
                  required: true,
                  number: true
              },
              telephone: {
                  required: true
              },
              website: {
                  required: true
              },
              address: {
                  required: true
              },
              comp_info_description: {
                 required: true
              },
             /* whould_travel: {
                 required: true
              },
              how_many_hours_can_work: {
                 required: true
              },
               when_start_work: {
                 required: true
              },
               field_of_study: {
                 required: true
              },
               highest_qualification_id: {
                 required: true
              },
               where_located: {
                 required: true
              },*/
              
              @if(!isset($data))
              password :{
                  required:true
              },
              confirmpassword: {
                  required: true,
                  equalTo: "#password"
              } 
              @else
                  confirmpassword: {
                      equalTo: "#password"
                  } ,
                 /* tender_pdf:{                     
                      extension: "pdf"
                  }*/
             @endif
          },
          messages : {
              image : {
                  accept : "Please upload valid image file."
              },
              /* tender_pdf:{                                 
                  extension:"Please upload valid pdf file."
              }*/
          }          
        });       

        if(val.form() != false) {
          var url = "@isset($data){{url('user/'.$userid)}}@endisset @empty($data){{url('user')}}@endempty".trim();
          var action = "@isset($data){{'PUT'}}@endisset @empty($data){{'POST'}}@endempty".trim();
          var fdata = new FormData($("#frm1")[0]);
         
          "@isset($data)";
          fdata.append("type","{{$data->type}}");
          "@endisset";
          load();
          $.ajax({
            url : url,
            type: 'POST',
            data : fdata,
            headers: {
                'X_CSRF_TOKEN':'{{ csrf_token() }}',
            },
            processData: false,
            contentType: false,
            success:function(data, textStatus, jqXHR){
              var res = data;
          
              $("#msg").fadeIn("slow");
              $("#msg").removeClass();
              if(data.status == 200) {

                $("#msg").addClass("alert alert-success");
                if(action == 'POST') {
                  //alert("{{url('')}}/{{$controller}}/"+res.data.id+"/edit");
                  //$("#frm1")[0].reset();
                  //window.location("{{url('')}}/{{$controller}}/"+res.data.id);
                    window.location.href = "{{url('')}}/{{$controller}}/"+res.data.id+"/edit";
                }
                //console.log(res.data.consultantinfo);
                $(".comp_image").val('');
                if(res.data && res.data.profile_pic) {
                  $(".imagecont").html('<input type="hidden" class="hidden_comp_image" name="profile_pic" value="'+res.data.profile_pic+'"><a class="fancybox-media" rel="image" href="'+res.data.profile_pic+'" alt="Image"><img onclick="openfancy();" src="{{url('')}}/'+res.data.profile_pic+'" heigth="50" width="50"></a><a class="cpforall deletecurrentimage"><i class="fa fa-trash" title="Delete image"></i></a>');
                }
              } else {
                $("#msg").addClass("alert alert-danger");
              }
              $("#msg").html(res.msg);
              unload();
              hidemsg(5000,2000);
              scrolltop();
            },
            error: function(jqXHR, textStatus, errorThrown){
              alert("Something went wrong. Please try again.")
              unload();
            }
          });
        } else {
          return false;
        }
      });

      //Move on current tab
      $(function() {

        $('#mytab a').click(function(e) {
          e.preventDefault();
          $(this).tab('show');
        });
        // store the currently selected tab in the hash value
        $("ul.nav-tabs > li > a").on("shown.bs.tab", function(e) {
          var id = $(e.target).attr("href").substr(1);
          window.location.hash = id;
        });
        // on load of the page: switch to the currently selected tab
        var hash = window.location.hash;
       
        $('#mytab a[href="' + hash + '"]').tab('show');    
             
      });
       $('#mytab a').click(function (e) {
        e.preventDefault();
        $('html,body').animate({scrollTop: 0}, 800);
    });

      //Model open
      $("body").on('click','.openModelBtn',function () {
        var t=$(this);
        var url=t.attr("id");
        load();
        $.ajax({
          url : "{{url('consultant')}}/"+url,
          type: 'GET',
          headers: {
              'X_CSRF_TOKEN':'{{ csrf_token() }}',
          },  
          success:function(data, textStatus, jqXHR){
           unload();
            $(".consultantmodel").html(data);
            $(".consultantmodel").modal('show');
          },
          error: function(jqXHR, textStatus, errorThrown){
              alert("Something went wrong. Please try again.")
              unload();
          }
        });
      });

      //Insert Consultant tab
      $("body").on('click','#btn-general',function (e) {
        active_table=$('.nav-tabs li.active').attr('id'); 

        e.preventDefault();
        var formdata =new FormData($('#frm')[0]);         
        var userid = "@isset($data){{$userid}}@endisset @empty($data) '0' @endempty".trim();          
        formdata.append('user_id',userid);   
        var url=$('#frm').attr('action');      
        load();
        $.ajax({
          url : url,
          type: 'POST',
          data : formdata,
          headers: {
              'X_CSRF_TOKEN':'{{ csrf_token() }}',
          },
          processData: false,
          contentType: false,
          success:function(data){
            if(data.status == 400) {
              $(".error").css({"color":"red",'display':'block'}).html(data.msg).fadeOut(5000);
            } else {
                $(".error").css({"color":"green",'display':'block'}).html(data.msg).fadeOut(5000);
                 if(data.totalRefrences == 2) {
                  $(".refrence_hide").hide();
                } 

                //alert(data.totalEducation);
              /*  if(data.totalEducation == 2) {
                  $(".education_hide").hide();
                } 
*/
              
                $(".consultantmodel").modal('hide');
                
                if(data.action=='edit')
                {
                  $('.current-row').html(data.data);
                }
                else
                {                  
                  $('.'+active_table+' tbody').prepend(data.data);
                }
            }                
            unload();
          },
          error: function(){
            alert("Something went wrong. Please try again.")
            unload();
          }
        });       
      });

      //Insert Consultant CV Document
      $("body").on('click','#cvdoc',function (e) {
        active_table=$('.nav-tabs li.active').attr('id'); 
        e.preventDefault();
        var formdata =new FormData($('#cvfrm')[0]);         
        load();
        $.ajax({
          url : "{{url('consultant/addCV')}}",
          type: 'POST',
          data : formdata,
          headers: {
              'X_CSRF_TOKEN':'{{ csrf_token() }}',
          },
          processData: false,
          contentType: false,
          success:function(data){
            if(data.status == 400) {
              $(".error").css({"color":"red",'display':'block','font-size':'18px','padding-left': '23px','padding-top': '20px'}).html(data.msg).fadeOut(5000);
            } else {
              $(".error").css({"color":"green",'display':'block','font-size':'18px','padding-left': '23px','padding-top': '20px'}).html(data.msg).fadeOut(5000);
              $('.'+active_table+' tbody').prepend(data.data);              
            }  
            $("#file_name").val(''); 
            unload();            
          },
          error: function(){
            alert("Something went wrong. Please try again.")
            unload();
          }
        });       
      });
       
      //Delete Consultant tabs record 
      $("body").on('click','.delete',function(event){
        var t=$(this);
        var url=t.attr("id");
        var consultant_id = "@isset($data){{$userid}}@endisset";
        var conform = confirm("Are you sure you want to delete?");
        if (conform) {
          load();
          $.ajax({
            "type" : "delete",
            "url" : "{{url('consultant')}}/"+url,
            "data" : {'consultant_id' : consultant_id },
            'headers': {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            'success' : function (data) {
              if(data.status == 200) {
                t.parents("tr").fadeOut(function () {
                  $(this).remove();
                });
                if(data.totalRefrences < 3){
                  $(document).find('.refrence_hide').show();
                } 
                /* if(data.totalEducation < 3){
                  $(document).find('.education_hide').show();
                } */
              }
               unload();
            },
            'error' : function () {
              alert("Error");
              unload();
            },
          });
          return true;
        }
        else {
          event.preventDefault();
          return false;
        }
      });

      $("body").on("click",'.deletecurrentimage',function () {
        load();
        $.post("{{url('user/delete-image')}}",{_token:"{{csrf_token()}}","id":"@isset($data){{$userid}}@endisset"},function (data) {
            unload();
            if(data.status == 200) {
                $(".imagecont").html('');
            } else {
                alert(data.msg);
            }
        })
      });
    });


    //Current is active then end-month and end-year is sisabled
    $(function(){
      $(document).on('change', '#current', function(){  
        if($(this).is(":checked")){
            $("#end").attr("disabled", "disabled");
        } else {
            $("#end").removeAttr("disabled");
            $("#end").focus();
          
        }
      });

     $("body").on("blur",".phone-validation",function(){
            
            var phone_val = $(this).val();
            var field_name = $(this).attr('name');
             $('#'+field_name+'-error').remove();
            if(phone_val!=""){

                if(!$.isNumeric( phone_val))
                {
                   $('<label id="'+field_name+'-error" class="error" for="phone"></label>').insertAfter( $(this) );
                   $('#'+field_name+'-error').show();
                   $('#'+field_name+'-error').text("Please enter a valid number.");
                  
                }
                else if($.isNumeric( phone_val))
                {
                   if(phone_val.length < 6)
                   {
                       $('<label id="'+field_name+'-error" class="error" for="phone"></label>').insertAfter($(this));
                       $('#'+field_name+'-error').show();
                       $('#'+field_name+'-error').text("Phone number value must be between 6 to 16 digits.");
                       
                   }
                   if(phone_val.length > 17){
                        $('<label id="'+field_name+'-error" class="error" for="phone"></label>').insertAfter($(this));
                        $('#'+field_name+'-error').show();
                        $('#'+field_name+'-error').text("Phone number value must be between 6 to 16 digits.");
                        $(this).val('');
                        
                   }
                }
                else
                {
                    $('#'+field_name+'-error').hide();
                }
            }   
        });    
    });    
  </script>
@endpush

