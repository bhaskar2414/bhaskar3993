<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
/*use Illuminate\Support\Facades\Auth;*/
use Password;
use File;
use App\User;
use App\Projects;
use App\CompanyProfileContacts;
use App\MetaData;
use App\CompanyInfo;
use App\ConsultantInfo;
use App\ConsultantCv;
use App\WorkHistory;
use App\ConsultantViews;
use App\Controllers;
use App\ConsultantEducation;
use App\ConsultantLanguage;
use App\ConsultantExperience;
use App\ConsultantKeywords;
use App\CompanyPackages;
use App\ProjectProposal;
use Yajra\Datatables\Datatables;
use Mail;
use Illuminate\Support\Str;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ConsultantController;
use Auth;
use Cartalyst\Stripe\Stripe;
use Socialite;
use Session;
use App\EmailTemplates;

class UserController extends Controller
{
    protected $request;
    protected $controller;
    protected $action;
    public function __construct(Request $request) {
        $r = \Route::current();
        
        if(!empty($r) && !empty($r->getName())) {
            $a = explode(".",$r->getName());

            $this->controller = $a[0];
            $this->action = $a[1];
        }
        else
        {
            if(!empty($r->parameterNames)){
             $cat_url=$r->parameterNames[0];
         }
         if(isset($r->uri)){
            $arr=explode('/', $r->uri);
            $this->controller=$arr[0];
        } 
    }
    $this->request = $request;


}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $this->title = ucfirst($this->controller)." management";
        $user=array();
        if(Auth::check() )
        {
             $user=Auth::user();
        }
        
        $arr = array('controller'=>  $this->controller,'action'=>  $this->action,'title'=>$this->title,'user'=>$user);
         if($this->controller == 'consultant')
            return view('user.consultant_index',$arr);
         else
            return view('user.index',$arr);
     }

     public function stripfunction()
     {
        $stripe = Stripe::make('rk_test_DDDrPD8EoMI3QeOpHQjFEAjX'); // local testing : rk_test_5vTvk6BBPGmXchwenA56IX6R

        

        $customer = $stripe->customers()->create([
            'email' => 'john@doe.com',
        ]);

        $customers = $stripe->customers()->all();

        foreach ($customers['data'] as $customer) {
            var_dump($customer['email']);
        }

        $charge = $stripe->charges()->create([
            'customer' => 'cus_CcnFrFbe5wiLuS',
            'currency' => 'USD',
            'amount'   => 50.49,
        ]);

        //dd($charge);
        
    }

    public function getall() {

        $input = $this->request->all();
        
        if($input['controller']=='company'){
           $data = User::where('role','Company')->orderby('id','desc');
       }
       else{
           $data = User::where('role','Consultant')->orderby('id','desc');
       }

       return Datatables::of($data->with('company')->get())
       ->addColumn('id','<input class="innerallchk" onclick="chkmain();" type="checkbox" name="allchk[]" value="{{ $id }}">')
       ->addColumn('company_name',function ($q) use ($input) {
        if(isset($input["company"]) && !empty($input["company"])) {
            return '<a href="company/job-history/'.$q->abn.'">'.$q->firstname.'</a>';
        } else {
            $company = $q->company;
            return $company['company_name'];
        }
    })
    ->addColumn('contact_person_name',function ($q) {
        return $q->first_name." ".$q->last_name;
    })
       ->addColumn('email',function ($q) {
        return $q->email;
    })
    ->addColumn('reg_date',function ($q) {
        return date('Y-m-d',strtotime($q->created_at));
    })
       ->addColumn('is_premium',function ($q) {
        return ($q->is_premium==1) ? 'Yes' : 'No';
                    //return $q->is_premium;
    })
       ->addColumn('status',function ($q) {
        return $q->status;
    })
       ->addColumn('action',function ($q) use ($input) {
        $url = "consultant";
        if($q->role=='Company') {
            $url = "company";
        }
        if(isset($input["employer"]) && !empty($input["employer"])) {
            $url = "employer";
            return '<a class="delsing cpforall" id="'.$q->id.'" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></a>';
        } else {
            return '<a href="'.$url.'/'.$q->id.'/edit" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a><a class="delsing cpforall" id="'.$q->id.'" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></a>';
        }

    })
       ->rawColumns(['id','action','firstname'])
       ->make(true);
   }


   public function getAllConsultant() {

        $input = $this->request->all();
        
        $data = User::where('role','Consultant')->orderby('id','desc');
        

       return Datatables::of($data->get())
       ->addColumn('id','<input class="innerallchk" onclick="chkmain();" type="checkbox" name="allchk[]" value="{{ $id }}">')
       ->addColumn('firstname',function ($q) use ($input) {
        if(isset($input["company"]) && !empty($input["company"])) {
            return '<a href="company/job-history/'.$q->abn.'">'.$q->firstname.'</a>';
        } else {
            return $q->first_name;
        }
        })
           ->addColumn('lastname',function ($q) {
            return $q->last_name;
        })
           ->addColumn('email',function ($q) {
            return $q->email;
        })
        ->addColumn('is_premium',function ($q) {
            return ($q->is_premium==1) ? 'Yes' : 'No';
                        //return $q->is_premium;
        })
           ->addColumn('status',function ($q) {
            return $q->status;
        })
        ->addColumn('action',function ($q) use ($input) {
            $url = "consultant";
            if($q->role=='Company') {
                $url = "company";
            }
            if(isset($input["employer"]) && !empty($input["employer"])) {
                $url = "employer";
                return '<a class="delsing cpforall" id="'.$q->id.'" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></a>';
            } else {
                return '<a href="'.$url.'/'.$q->id.'/edit" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a><a class="delsing cpforall" id="'.$q->id.'" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></a>';
            }

        })
           ->rawColumns(['id','action','firstname'])
           ->make(true);
   }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->title = "Add new ".$this->controller;

        $locations=MetaData::where([['type','=','location'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $industries=MetaData::where([['type','=','industry'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $countries=MetaData::where([['type','=','country'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $currencies=MetaData::where([['type','=','currency'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $highest_qualifications=MetaData::where([['type','=','highest_qualification'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $field_of_studies=MetaData::whereIn('type', ['science', 'media', 'business', 'medicine', 'engineering', 'others'])->where('status','Active')->select('id','name')->orderby('name')->get();
        
       $seniorities=MetaData::where([['type','=','seniority_level'],['status','=','Active']])->select('id','name')->orderby('name')->get();
       
        $arr = array('controller'=>  $this->controller,'action'=>  $this->action,'title'=>$this->title,'locations'=>$locations,'industries'=>$industries,'countries'=>$countries,'highest_qualifications'=>$highest_qualifications,'seniorities'=>$seniorities,'field_of_studies'=>$field_of_studies,'currencies'=>$currencies);
        return view('user.add',$arr);
        //return view('user.add',$arr);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $rules = array(
            'first_name'=>"required|max:100",
            'last_name'=>"required|max:100",
            'phone'=>"nullable|numeric",
            //'phone'=>"nullable|digits_between:6,16|numeric|Regex:/^\+?[0,9]{6,16}$/",
           /* 'location_id'=>"required",   */         
            //'job_alert'=>"required",
            /*'company_name'=>"max:191",
            'industry_id'=>"nullable|numeric",
            'based_in'=>"nullable|numeric",            
            'company_type'=>"max:191",
            'company_size'=>"nullable|numeric",
            'annual_turnover'=>"nullable|numeric",
            'county_id'=>"nullable|numeric",
            'telephone'=>"nullable|digits_between:10,20|numeric",*/
            //'ext' => 'nullable|in:pdf',
            //'profile_pic' => 'mimes:jpeg,jpg,png,gif|dimensions:max_width=150,max_height=150',
        );

        //$input = $request->all();
        
        
            
            if(isset($input['marketing_email']))
                $input['marketing_email']= 1 ;
            else
                $input['marketing_email']= 0 ;

            if(isset($input['progress_email']))
                $input['progress_email']= 1 ;
            else
                $input['progress_email']= 0 ;

            $file = "";

            if(isset($input["profile_pic"]) && !empty($input["profile_pic"])) {
                $file = $input["profile_pic"];
                
            }  
            if(!empty($file) && !is_string($file)) {
                $rules['profile_pic'] = 'mimes:jpeg,jpg,png,gif';
            }
            
            $id = 0;
            $msg = "Account created successfully.";
            if(isset($input["id_for_update"]) && !empty($input["id_for_update"])) {
                $action = "edit";
                $msg = "Profile updated successfully.";
                $id = $input["id_for_update"];
                $user = User::find($id);
             
                if(!empty($user)) {
                    $rules["email"] = 'required|email|unique:users,email,'.$id;
                }
                $rules["password"] = 'nullable|min:6';
                $rules["role"] = 'required|in:Consultant,Company,Admin';
                if(isset($input["role"]) && $input["role"]=='Company')
                {
                    $rules['company_name'] = "nullable|max:191";
                    $rules['industry_id'] = "required|numeric";
               
                    $rules['company_type'] = "max:191";
                    if(isset($rules['company_size']))
                        $rules['company_size'] = "numeric";
                    $rules['annual_turnover'] = "required|numeric";
                    $rules['county_id'] = "required|numeric";
                    $rules['telephone'] = "required|numeric";
                    //$rules['telephone'] = "required|numeric|digits_between:6,10";
                    $rules['website'] = "required|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/";

                }
            //$rules["password"] = 'nullable|min:6';
           // $rules["role"] = 'required|in:Consultant,Company,Admin';

                if(isset($input["status"]))
                    $input["status"] = $input["status"];
            } else {
                $action = "add";
                $rules["password"] = 'required|min:6';
                $rules["email"] = 'required|email|unique:users';
                $rules["role"] = 'required|in:Consultant,Company,Admin';            
                $user = new User();
                $input["status"] = 'Active';

                if(Auth::check() && Auth::user()->role == 'Admin'){
                    $input['is_premium']= 1 ;
                    $input['is_email_verified'] = 1 ;
                }

                
            }
            $validation_msg = [
                'mimes' => 'The profile image must be a file type of:.JPEG,.JPG,.PNG,.GIF',
            ];
            $validator = Validator::make($input, $rules,$validation_msg);
            if($validator->fails()) {
                $arr = array("status"=>400,"msg"=>$validator->errors()->first(),"result"=>array());
            } else {
                if(isset($input["password"])) {
                    $password = \Hash::make($input["password"]);
                    $input["password"] = $password;
                } else if(!isset($input["password"]) && empty($input["password"]) && !empty($user)) {
                    $input["password"] = $user->password;
                }

            //$user = User::updateOrCreate(['id'=>$id],$input)  ;
                try {   
                    if(!empty($user)) {
                        $laststr="";

                        if(!empty($file) && !is_string($file)) {
                            $destinationPath = 'uploads/'.time().$file->getClientOriginalName();
                            $a = \Image::make($file->getRealPath())->save(img($destinationPath));
                            $input["profile_pic"] = $destinationPath;
                        }
                        
                        $user->fill($input)->save();
                        
                        if(isset($input["role"]) && $input["role"]=='Company')
                        {
                            
                            $conpanyinfo=CompanyInfo::where('user_id',$user->id)->first();                          
                            $request->request->add(['user_id' => $user->id]);
                            $obj_company_info = new CompanyController($request);

                            if(!empty($conpanyinfo))
                            {
                                $obj_company_info->update($request,$conpanyinfo->id);

                            }
                            else
                            {
                                $request->request->add(['company_name' => $input["first_name"]." ".$input["last_name"]]);
                                if(isset($input["location_id"]) && $input["location_id"] !=""){
                                    $request->request->add(['county_id' => $input["location_id"]]);
                                }
                               // dd($request);
                                $request->request->remove('id_for_update');
                                $obj_company_info->store($request);

                            }



                        //---------save Company contact data--------------
                        /* $personalContactInfo=array();
                         if(!empty($input['contact_name']) && !empty($input['job_title']) && !empty($input['department']) && !empty($input['telephone']))
                         {
                            
                            foreach ($input['contact_name'] as $key => $value) {

                                $personalContactInfo[]=array('name'=>$value,'job_title'=>$input['job_title'][$key],'department'=>$input['department'][$key],'telephone'=>$input['telephone'][$key],'type'=>$input['type'][$key]);
                               
                            }
                            $projects->companyProfileContacts()->sync($project_language);
                        }*/
                        $company_contact=array();
                        if(isset($input['contact_name']))
                            $company_contact['name']=$input['contact_name'];
                        if(isset($input['department']))
                            $company_contact['department']=$input['department'];
                        if(isset($input['company_telephone']))
                            $company_contact['telephone']=$input['company_telephone'];
                        if(isset($input['job_title']))
                            $company_contact['job_title']=$input['job_title'];
                        $company_contact['type']='Company';
                        /*$companyprofilecontacts = CompanyProfileContacts::where([['user_id','=',$user->id],['type','=','Company']])->first();
                        if(empty($companyprofilecontacts))
                        {
                            $company_contact['user_id']=$user->id;
                            $companyprofilecontacts = new CompanyProfileContacts;
                        }
                        $companyprofilecontacts->fill($company_contact)->save();*/
                        
                        //---------save lead accountant contact data--------------
                        $company_contact=array();
                        if(isset($input['lead_contact_name']))
                            $company_contact['name']=$input['lead_contact_name'];
                        if(isset($input['lead_contact_department']))
                            $company_contact['department']=$input['lead_contact_department'];
                        if(isset($input['lead_contact_telephone']))
                           $company_contact['telephone']=$input['lead_contact_telephone'];
                        if(isset($input['lead_contact_job_title']))
                            $company_contact['job_title']=$input['lead_contact_job_title'];
                        /*$company_contact['type']='Lead Account';
                        $companyprofilecontacts = CompanyProfileContacts::where([['user_id','=',$user->id],['type','=','Lead Account']])->first();
                        if(empty($companyprofilecontacts))
                        {                           
                            $company_contact['user_id']=$user->id;
                            $companyprofilecontacts = new CompanyProfileContacts;
                        }
                        $companyprofilecontacts->fill($company_contact)->save();*/
                        
                    }
                    if(isset($input["role"]) && $input["role"]=='Consultant')
                    {
                        if(isset($input['where_located']) && count($input['where_located'])>0){
                            $user->whereLocated()->sync($input['where_located']);
                        }
                        $consultantinfo=ConsultantInfo::where('consultant_id',$user->id)->first();
                        $obj_consultant_info = new ConsultantController($this->request);
                        $request->request->add(['consultant_id' => $user->id]);
                        
                        if(!empty($consultantinfo))
                        {
                            $obj_consultant_info->update($request,$consultantinfo->id);
                           
                     }
                     else
                     {

                        $request->request->remove('id_for_update');
                        $obj_consultant_info->store($request);
                            //$user->consultantinfo = $user->consultantinfo;
                    }
                    $laststr="";
                    if(isset($input['view_side']) && $input['view_side']=='front_end')
                    {

                        $laststr='<h3>'.$user->first_name.' '.$user->last_name.'</h3>';
                        $laststr.='<ul>';
                        $laststr.='<li><i class="fa fa-map-marker"></i>'.$user->location->name.'</li>';
                        if(isset($user->phone) && $user->phone!=''){
                            $laststr.='<li><i class="fa fa-phone"></i> '.$user->phone.'</li>';
                        }
                        if($user->linkedin_profile_link!="")
                            $laststr.='<li><i class="fa fa-linkedin" aria-hidden="true"></i> <a target="_blank" href="'.$user->linkedin_profile_link.'"> LinkedIn Profile</a></li>';
                        $laststr.='<li><i class="fa fa-envelope"></i> '.$user->email.'</li>';

                           /* if(isset($user->consultantinfo))
                           {*/
                            if(!empty($user->consultantinfo->currency)){
                                    $laststr.='<li><i class="fa fa-money" aria-hidden="true"></i>'.$user->consultantinfo->rate_from.' - '.$user->consultantinfo->rate_to.' '.$user->consultantinfo->currency->name.' / day </li>';
                                }
                            /* }*/
                            if(isset($user->description) && $user->description!=''){
                                $laststr.='<li><i class="profileSummary">Profile Summary</i><p>'.$user->description.' </p></li>';
                            }
                            
                            $laststr.='</ul>';
                        }
                    }    
                    if(!Auth::check() && $action == 'add' && ($user->role == 'Consultant' || $user->role == 'Company')) {

                        //$admin = User::find(1);
                        
                        $confirmation_code = str_random(30);
                        $user->where('id',$user->id)->update(['verfication_code'=>$confirmation_code]);
                        $mailarr['confirmation_code'] = $confirmation_code;
                        $verify_url=url('register/verify/').'/'.$confirmation_code;
                        $button = '<a href="'.$verify_url.'" class="button button-blue" target="_blank" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; border-radius: 3px; box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16); color: #FFF; display: inline-block; text-decoration: none; -webkit-text-size-adjust: none; background-color: #3097D1; border-top: 10px solid #3097D1; border-right: 18px solid #3097D1; border-bottom: 10px solid #3097D1; border-left: 18px solid #3097D1;">Verify Email</a>'; 

                            $shortcodes=array('[button]');
                            $variables=array($button);
                            $template = EmailTemplates::where('template','verify_email')->first();
                            
                            $mailtemplate="";
                            $template_subject='Verify your email address';
                            if(!empty($template))
                            {
                                $mailtemplate=str_replace($shortcodes,$variables,$template->mail_body);
                                $template_subject=$template->subject;
                            }
                            $mailarr['mailtemplate'] = $mailtemplate;
                            
                            /*Mail::send('email.verify', $mailarr, function($message) use($user,$template_subject) {
                                $message->to($user->email, $user->firstname)
                                ->subject($template_subject);
                            });*/
                            $a = Mail::send('email.verify', $mailarr, function($message) use($user,$template_subject) {
                                $message->to($user->email, $user->first_name)
                                ->subject($template_subject);
                                
                            });
                            
                                           
                    }

                  /*  echo "<pre>";
                    print_r($user->toArray());
                    exit();*/

                    $arr = array("status"=>200,"msg"=>$msg, "action"=>$action, "data"=>replace_null_with_empty_string($user->toArray()),'laststr'=>$laststr);
                } else {
                    $arr = array("status"=>400,"msg"=>"User not found.","data"=>[]);
                }
                
            } catch (\Exception $ex) {
                if(isset($ex->errorInfo[2])) {
                    $msg = $ex->errorInfo[2];
                } else {
                    $msg = $ex->getMessage();
                }
                
                $arr = array("status"=>400,"msg"=>$msg,"data"=>[]);
            }
        }
        return \Response::json($arr);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    { 
        $users=User::with(['industry_experience.experiences',
            'project_experience.experiences',
            'role_experience.experiences',
            'consultantCv' => function($q){
                $q->orderby('id','desc');
            },'workHistory'=>function($q){
                $q->orderby('id','desc');
            },'educations'=>function($q){
                $q->orderby('id','desc');
            },'languages'=>function($q){
                $q->orderby('id','desc');
            },'consultantkeywords'=>function($q){
                $q->orderby('id','desc');
            }
        ])->find($id);   

        /*dd($users->workHistory);*/
        //$locations=MetaData::where([['type','=','location'],['status','=','Active']])->select('id','name')->get();
        $industries=MetaData::where([['type','=','industry'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $countries=MetaData::where([['type','=','country'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $anualTurnover=MetaData::where([['type','=','anual_turnover'],['status','=','Active']])->select('id','name')->orderby('id','desc')->get();
        $currencies=MetaData::where([['type','=','currency'],['status','=','Active']])->select('id','name')->orderby('name')->get();
          $seniorities=MetaData::where([['type','=','seniority_level'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $company_types=MetaData::where([['type','=','company_type'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $data = User::findOrFail($id);

        $companydata = $data->company;
        $consultantdata = $data->consultantinfo;
        //dd($consultantdata);
        //$contactdata = $data->companyProfileContacts;
       // $leadcontactdata = CompanyProfileContacts::where([['user_id','=',$id],['type','=','Lead Account']])->first();
        //$contactdata = CompanyProfileContacts::where([['user_id','=',$id],['type','=','Company']])->first();
        $where_located_array=$data->whereLocated()->pluck('location_id')->toArray();

        
        $highest_qualifications=MetaData::where([['type','=','highest_qualification'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $field_of_studies=MetaData::whereIn('type', ['science', 'media', 'business', 'medicine', 'engineering','others'])->where('status','Active')->select('id','name')->orderby('name')->get();

        if(!empty($data)) {
            $t = "Consultant";
            if($data->role == 'Company') {
                $t = "Company";
            }
            $this->title = "Edit ".$t;
            $arr = array('controller'=>  $this->controller,'action'=>  $this->action,'title'=>$this->title,'data'=>$data,'companydata'=>$companydata,'industries'=>$industries,'countries'=>$countries,'anualTurnover'=>$anualTurnover,'currencies'=>$currencies,'userid'=>$id,'users'=>$users , 'highest_qualifications'=>$highest_qualifications,'field_of_studies'=>$field_of_studies,'consultantdata'=>$consultantdata,'where_located_array'=>$where_located_array,'seniorities'=>$seniorities,'company_types'=>$company_types);
            return view("user.add",$arr);

        } else {
            pagenotfound();
        }
    }
    
    //------------FRONT-END EDIT PAGE-----------------
    
    public function editPage($id)
    { 

       /* echo checkCompanyPackageExpireOrNot(Auth::id());
        exit();*/
        $obj_consultant_info = new ConsultantController($this->request);
       // dd($obj_consultant_info);
        $users=User::with(['industry_experience.experiences',
            'project_experience.experiences',
            'role_experience.experiences',
            'consultantCv' => function($q){
                $q->orderby('id','desc');
            },'workHistory'=>function($q){
                $q->orderby('start','desc');
            },'educations.fieldOfStudy'=>function($q){
                $q->orderby('id','desc');
            },'languages'=>function($q){
                $q->orderby('id','desc');
            },'consultantkeywords'=>function($q){
                $q->orderby('id','desc');
            }
        ])->find($id); 
        
        if(empty($users)){
            return view('front-end.pageNotFound');
        }

        $locations=MetaData::where([['type','=','location'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $industries=MetaData::where([['type','=','industry'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $countries=MetaData::where([['type','=','country'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $currencies=MetaData::where([['type','=','currency'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $languages=MetaData::where([['type','=','language'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $company_types=MetaData::where([['type','=','company_type'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $data = User::findOrFail($id);
        $userdatadata = User::findOrFail($id);
       // dd($userdatadata);
        $companydata = $data->company;
       
        $whereLocated=$data->whereLocated;
      
        $companyDataSpecified = '';
        if(!empty($companydata)):
            $companyDataSpecified=$companydata->with('country','industry','location','anualTurnover')->where('user_id',$companydata->user_id)->first();
            //dd($dataSpecified);
        endif;
        
       
       // $fieldOfStudies=$data->fieldOfStudies;

        //$contactdata = $data->companyProfileContacts;

        //$fieldOfStudies= ConsultantInfo::with('fieldOfStudies')->where('consultant_id',$id)->get();
        //$leadcontactdata = CompanyProfileContacts::where([['project_id','=',$id],['type','=','Lead Account']])->first();
        //$contactdata = CompanyProfileContacts::where([['project_id','=',$id],['type','=','Company']])->first();
        $consultantdata = $data->consultantinfo;
       // dd($consultantdata);
        $consultantDataSpecified = '';
        if(!empty($consultantdata)):
            $consultantDataSpecified=$consultantdata->with('fieldOfStudies','highestQualification','consultantLocated.locations')->where('consultant_id',$consultantdata->consultant_id)->first();
            //dd($consultantDataSpecified->toArray());
        endif;
        $years= $obj_consultant_info->getYears();
        $proficiencies= $obj_consultant_info->getproficiencies();
        $where_located_array=$data->whereLocated()->pluck('location_id')->toArray();
        //$anualTurnover=MetaData::where([['type','=','anual_turnover'],['status','=','Active']])->select('id','name')->orderByRaw('LENGTH(name) asc')->get();
        $anualTurnover=MetaData::where([['type','=','anual_turnover'],['status','=','Active']])->select('id','name')->orderby('id','desc')->get();

        
        $highest_qualifications=MetaData::where([['type','=','highest_qualification'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $field_of_studies=MetaData::whereIn('type', ['science', 'media', 'business', 'medicine', 'engineering', 'others'])->where('status','Active')->select('id','name')->orderby('name')->get();
        $seniorities=MetaData::where([['type','=','seniority_level'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        
        if(!empty($data)) {
            $t = "Consultant";
            if($data->role == 'Company') {
                $t = "Company";
            }
            $this->title = "Edit ".$t;
            $arr = array('role'=>$data->role,'controller'=>  $this->controller, 'years'=>$years, 'currencies'=>$currencies, 'action'=>  $this->action,'title'=>$this->title,'data'=>$data,'companydata'=>$companydata,'locations'=>$locations,'industries'=>$industries,'countries'=>$countries,'userid'=>$id,'users'=>$users , 'highest_qualifications'=>$highest_qualifications,'field_of_studies'=>$field_of_studies,'consultantdata'=>$consultantdata,'where_located_array'=>$where_located_array,'languages'=>$languages,'proficiencies'=>$proficiencies,'userdatadata'=>$userdatadata,'anualTurnover'=>$anualTurnover,'companyDataSpecified'=>$companyDataSpecified,'consultantDataSpecified'=>$consultantDataSpecified,'seniorities'=>$seniorities,'company_types'=>$company_types);

           if($data->role == 'Company') {
               return view("front-end.company-profile-edit",$arr);
           }
           else
           {
                if(Auth::user()->role=='Company' && Auth::user()->is_premium==0)
                {   
                    $msg='You do not have enough search credit for search.';
                    $flag=checkCompanyPackageExpireOrNot(Auth::user()->id);
                    if($flag==0)
                    {
                        \Session::put('previous_url',url()->current());
                        return redirect('packages/'.Auth::user()->role)->withErrors($msg);
                    }   
                    else{  
                        $current_date=\Carbon\Carbon::now();
                        $pacakge_data=CompanyPackages::where([['user_id','=',Auth::user()->id],['expiry_date','>',$current_date]])->whereNull('consultant_view_status')->first();
                        $consultant_views=ConsultantViews::where([['company_packages_id','=', $pacakge_data->id],['company_id','=',Auth::user()->id],['consultant','=',$id]])->count(); 
                       
                        if($consultant_views < 1) {
                            
                            if(!empty($pacakge_data) )
                            {
                                 $consultant_views_array['company_id']=Auth::user()->id;
                                 $consultant_views_array['consultant']=$id;
                                 $consultant_views_array['company_packages_id']=$pacakge_data->id;
                                 $consultant_views= new ConsultantViews();
                                 $consultant_views->fill($consultant_views_array)->save();

                                 return view("front-end.consultant-profile-edit",$arr);
                            }
                            else{
                                \Session::put('previous_url',url()->current());
                                return redirect('packages/'.$role)->withErrors($msg);
                            }
                           
                        }
                        else
                        {
                            return view("front-end.consultant-profile-edit",$arr);
                        }
                    }
                }
                else
                {
                	
                    return view("front-end.consultant-profile-edit",$arr);
                }
                
           	}
	     } else {
	        pagenotfound();
	    }
	}

	/**
	 * Consultant ratings
	*/
	public function consultantRatings(Request $request,$id)
    {
    	
    	if(Auth::check())
    	{
    		$user = User::find($id);
	    	$obj_company_info = new CompanyController($this->request);
	    	$array = $obj_company_info->getStars();    
	    	$forConsultant = $user->ratingAndReviewForConsultant()->with(['userForConsultant','projects'])->orderby('id','desc')->paginate(5);
            //dd($forConsultant->toArray());
            if(count($forConsultant) > 0){
                $ratinghtml=View('front-end.modal-views.consultant-ratings', compact('forConsultant','array'))->render();
            }
            else {
                $ratinghtml='<p class="noRecordFountMsg">No records found.</p>';
            }
             
	    	return \Response::json(array('ratingshtml'=>$ratinghtml));
    	}
    	else
    	{
    		return \Response::json(array('status'=>400,'msg'=>'User not logged in','data'=>array()));
    	}

    }
    	
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->request->add(['id_for_update' => $id]); //send id into store function
        return $this->store($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $input = $this->request;

       $id = explode(",",$id);
         // dd($id);
       if(in_array(1,$id)) {
        return \Response::json(array("status"=>400,"msg"=>"You can't perform this action. Please provide valid id of user you want to delete.","result"=>array()));
    }

    $data = User::whereIn('id',$id);
    if(count($data->get()) == 0) {
        return \Response::json(array("status"=>400,"msg"=>"No users found.","result"=>array()));
    }
    try {
        $for_delete = $data->get();
        $result = aid($data,$input);
        $json = json_decode($result->getContent(),true);
        if($json["status"] == 200) {
            if($input["type"] == 'Delete') {

                foreach ($for_delete as $u) :
                    if(isset($u->profile_pic) && !empty($u->profile_pic)) {
                        unlink(public_path($u->profile_pic));
                    }

                endforeach;
            }
        }
        return $result;
    } catch ( \Illuminate\Database\QueryException $ex) {
        $msg = $ex->getMessage();
        if(isset($ex->errorInfo[2])) {
            $msg = $ex->errorInfo[2];
        }
        $arr = array("status" => 400, "msg" =>$msg, "result" => array());
    } catch (Exception $ex) {
        $msg = $ex->getMessage();
        if(isset($ex->errorInfo[2])) {
            $msg = $ex->errorInfo[2];
        }
        $arr = array("status" => 400, "msg" =>$msg, "result" => array());
    }
    return \Response::json($arr);
}

public function loginpage(Request $request,$type="") {

    if(!Auth::check())
    {
       return view("auth.login",compact('type'));
    }
    else
    {
        return \Redirect::back();
    }
}


public function login(Request $request) {

	
    $input =$request->all();
    $rules = array(
        'email'=>"required|email",
        'password'=>"required",
    );

    $validator = Validator::make($input,$rules);
    if($validator->fails()) {
        $arr = array("status"=>400,"msg"=>$validator->errors()->first(),"result"=>array());
    } else {
        $find = User::where('email',$input["email"])->first();
        if(!empty($find)) {
        	if($find->role==$input["user_type"])
        	{
				$pass = $input["password"];
	            $remember = isset($input["remember"]) ? $input["remember"] : 0 ;
				if(\Hash::check($pass,$find->password)) {
	                if($find->status == 'Active') {
	                    if($find->is_email_verified == 1) {
	                        Auth::guard('web')->attempt ( array (
	                            'email' => $input["email"],
	                            'password' => $input["password"]),$remember); 

	                        $arr = array("status"=>200,"msg"=>"Success","result"=>replace_null_with_empty_string($find->toArray()));
	                    } else {
	                        $arr = array("status"=>400,"msg"=>"Your account is not verfied yet. Please check you email inbox and verify your account.","result"=>[]);
	                    }
	                } else {
	                    $arr = array("status"=>400,"msg"=>"Your account is inactive. Please contact administrator.","result"=>[]);
	                }


	            } else {
	                $arr = array("status"=>400,"msg"=>"Please provide valid password.","result"=>[]);
	            }
        	}
	        else{
	        	$arr = array("status"=>400,"msg"=>"Please provide valid email and password.","result"=>[]);
	        }    

        } else {
            $arr = array("status"=>400,"msg"=>"Email doesn't exist in our database.","result"=>[]);
        }
    }
    return \Response::json($arr);
}

public function confirm($confirmation_code)
{
    if( ! $confirmation_code)
    {   
        $msg = "Invalid Confirmation Code!";
        return view('auth.verify',compact('msg'));
    }

    $user = User::where('verfication_code',$confirmation_code)->first();

    if ( ! $user)
    {            
        $msg = "Invalid Confirmation Code!";
    } else {
        $user->is_email_verified = 1;
        $user->verfication_code = null;
        $user->save();
        $msg = "You have successfully verified your account.";
    }

        //Flash::message('You have successfully verified your account.');
    return view('auth.verify',compact('msg'));
}

public function verifyUser($userid){
    $result=User::find($userid)->update(array('is_email_verified'=>1,'verfication_code'=>null));
    if($result){
        $arr = array("status"=>200,"msg"=>"User verified successfully.","result"=>$result);
    }
    else{
        $arr = array("status"=>400,"msg"=>"User not verified successfully.","result"=>[]);
    }
    return \Response::json($arr);
}

/*forgot password for user*/
public function forgot_password(Request $request) {
    $input =$request->all();
    $rules = array(
        'email'=>"required|email",
    );
    $validator = Validator::make($input,$rules);
    if($validator->fails()) {
        $arr = array("status"=>400,"msg"=>$validator->errors()->first(),"result"=>array());
    } else {
        try {
            $response = Password::sendResetLink($request->only('email'), function (Message $message) {
                $message->subject($this->getEmailSubject());
            });
            switch ($response) {
                case Password::RESET_LINK_SENT:
                $arr = array("status"=>200,"msg"=>trans($response),"result"=>array());
                case Password::INVALID_USER:
                $arr = array("status"=>200,"msg"=>trans($response),"result"=>array());
            }
        } catch (\Swift_TransportException $ex) {
            echo "<pre>";
            print_r($ex->getMessage());
            exit;
        } catch (Exception $ex) {
            echo "<pre>";
            print_r($ex->getMessage());
            exit;
        }
    }
    return \Response::json($arr);
}
/*forgot password for user*/

public function resetPasswordPage($token)
{
    return view('auth.passwords.reset',compact('token'));
}

public function resetPassword()
{

    $input =$request->all();
    $rules = array(
        'email'=>"required|email",
        'password'=>'required|min:8',
        'password_confirmation' => 'required_with:password|same:password|min:8',
    );
    $validator = Validator::make($input,$rules);
    if($validator->fails()) {
        $arr = array("status"=>400,"msg"=>$validator->errors()->first(),"result"=>array());
    } else {
       try {
        $find = User::where('email',$input["email"])->first();
        if(!empty($find)) {
            if(isset($input["password"])) {
                $password = \Hash::make($input["password"]);
                $input["password"] = $password;
                $input["remember_token"] = Str::random(60);
                $find->fill($input)->save();
                $arr = array("status"=>200,"msg"=>"Success","result"=>replace_null_with_empty_string($find->toArray()));
            }

        } else {
            $arr = array("status"=>400,"msg"=>"Email doesn't exist in our database.","result"=>[]);
        }
    } catch (Exception $ex) {
        echo "<pre>";
        print_r($ex->getMessage());
        exit;
    }    
}
return \Response::json($arr);
}

    //--------CHANGE PASSWORD PAGE----------
public function changePasswordPage()
{
 return view("front-end.change-password");
}
    //--------CHANGE PASSWORD FUNCTION---------
public function changePassword(Request $request)
{

    $input =$request->all();
    $rules = array(
        'old_password'=>"required",
        'password'=>'required|min:6',
        'password_confirmation' => 'required_with:password|same:password|min:6',
    );
    $validator = Validator::make($input,$rules);
    if($validator->fails()) {
        $arr = array("status"=>400,"msg"=>$validator->errors()->first(),"result"=>array());
    } else {
       try {
           if(isset($input["old_password"])) {
            $old_password = \Hash::make($input["old_password"]);
            $find = User::find(Auth::user()->id);
            if(!empty($find)) {
                if(!\Hash::check($input["old_password"], $find->password))
                    {
                       $arr = array("status"=>400,"msg"=>"Old password doesn't match in our database.","result"=>[]);
                       return \Response::json($arr);
                   }
                   else
                   {
                    if(isset($input["password"])) {
                        $password = \Hash::make($input["password"]);
                        $input["password"] = $password;
                        $input["remember_token"] = Str::random(60);
                        $find->fill($input)->save();
                        $arr = array("status"=>200,"msg"=>"Password updated successfully.","result"=>replace_null_with_empty_string($find->toArray()));
                    }
                }


            } else {

                $arr = array("status"=>400,"msg"=>"You can't change password.","result"=>[]);
            }
        }
        else
        {
            $arr = array("status"=>400,"msg"=>"Please enter old password.","result"=>[]);
        }
    } catch (Exception $ex) {
        echo "<pre>";
        print_r($ex->getMessage());
        exit;
    }    
}
return \Response::json($arr);
}

    //---Add Consaltant CV
public function addConsultantCv(Request $request)
{
    $rules = array(
        'user_id'=>"required|numeric",
        'file'=>"required",
    );

    $file = "";
    if(isset($input["file"]) && !empty($input["file"])) {
        $file = $input["file"];
    }   
} 

    //----------store report data---------------
public function report(Request $request) {
    $input =$request->all();

    $rules = array(
        'reported_on'=>"required|exists:users,id",
        'reported_by'=>"required|exists:users,id",            
    );
    $validator = Validator::make($input, $rules);

    if($validator->fails()) {
        return \Response::json(array("status"=>400,"msg"=>$validator->errors()->first(),"result"=>array()));
    } else {
        $find = User::where('id',$input["reported_on"])->first();
        $t = "User";            
        try {
            if($find->id != $input["reported_by"]) {
                $input['status']='Active';
                $input['type']=$find->role;
                $find->report()->create($input);
                return \Response::json(array("status" => 200, "msg" =>$t." reported successfully.", "result" => array()));
            } else {
                return \Response::json(array("status" => 400, "msg" =>"You can not report to own", "result" => array()));
            }
        } catch ( \Illuminate\Database\QueryException $ex) {
            $msg = $ex->getMessage();
            if(isset($ex->errorInfo[2])) {
                $msg = $ex->errorInfo[2];
            }
            if(isset($ex->errorInfo[1]) && $ex->errorInfo[1] == '1062') {
                $msg = "You've already reported this $t";
            }
            return \Response::json(array("status" => 400, "msg" =>$msg, "result" => array()));
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            if(isset($ex->errorInfo[2])) {
                $msg = $ex->errorInfo[2];
            }
            return \Response::json(array("status" => 400, "msg" =>$msg, "result" => array()));
        }
    }
}

    //----------Reported Users Lists--------------
public function getReportedList($userid) {

    if($this->controller=='company')
    {
        $data = User::where('role','Company')->withCount('report')->has('report')->orderby('report_count','desc');
    }
    else
    {
        $data = User::where('role','Consultant')->withCount('report')->has('report')->orderby('report_count','desc');
    }

    $finduser = User::find($userid);

    if($finduser->role == "Admin") {
        return Datatables::of($data->get())
        ->addColumn('id','<input class="innerallchk" onclick="chkmain();" type="checkbox" name="allchk[]" value="{{ $id }}">')                    
        ->addColumn('reportedto',function ($q) {
            return $q->first_name." ".$q->last_name;
        })
        ->addColumn('reportedby',function ($q) {
            return $q->report[0]->reported_by;                        
        })
        ->addColumn('totalreports',function ($q) {
            return $q->report_count;
        })                    
        ->addColumn('action',function ($q) {
            $type = "'".$q->role."'";
            $m = "user"; 
            $view_icon = "";                        
            return $view_icon.'<a class="delsing cpforall" id="'.$q->id.'" title="Delete '.$m.' permanently"><i class="fa fa-trash" aria-hidden="true"></i></a>';
        })
        ->rawColumns(['id','action'])
        ->make(true);

    } else {
        return \Response::json(array("status"=>400,"msg"=>"You don't have access.","result"=>array()));
    }
}

public function displayReportedUserForAdmin() {
    $this->title = "Reported ".$this->controller." list";
    $arr = array('controller'=>$this->controller,'action'=>  $this->action,'title'=>$this->title);
    return view('user.reported_user',$arr);
}

//-------Front-end reported users------------

public function displayReportedUserForFront() {

    $arr["title"]="Report";
    /*echo "user id: ".Auth::id()." role : ".Auth::user()->role;
    exit();*/
    $reports = User::with('report')->where('id',Auth::id())->first();
    
    $arr["reports"] = $reports->report()->paginate(10); 
   
    return view('front-end.my-reports',$arr);
}


public function delete_image(Request $request) {
    $input = $request->all();
    $find = User::find($input["id"]);
    if(!empty($find)) {
        if(!empty($find->profile_pic)) {
            unlink(public_path($find->profile_pic));
            $find->update(array('profile_pic'=>''));
        }
        return \Response::json(array("status"=>200,"msg"=>"Success.","result"=>array()));
    } else {
        return \Response::json(array("status"=>400,"msg"=>"No data found.","result"=>array()));
    }
}

public function getTransactionHistory($id)
{
    $companyInfo =CompanyInfo::with('companyPackages')->find($id);
    if($companyInfo) {
        dd($companyInfo);
    }
    return \Response::json($arr);
}

public function registerStep()
{
  return view("front-end.register-step");
}
public function loginStep()
{
    if(!Auth::check())
    {
        return view("front-end.login-step");
    }
    else
    {
        return \Redirect::back();
    }
  
}
public function registerpage($type)
{
    if($type=='Consultant' || $type=='Company')
    {
        $countries=MetaData::where([['type','=','country'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        $arr = array('countries'=>$countries,'role'=>$type);
        return view("auth.register",$arr);
    }
}
public function logout() {
    Auth::logout();
    return redirect('/login-step');
}

public function updateDescription(Request $request,$id)
{
    $input =$request->all();

    $user = User::find($id);
    try {    
        $user->fill($input)->save();
        return \Response::json(array("status"=>200,"msg"=>"Record Updated successfully", "data"=>replace_null_with_empty_string($user->toArray())));

    }
    catch (Exception $ex) {
        $msg = $ex->getMessage();
        if(isset($ex->errorInfo[2])) {
            $msg = $ex->errorInfo[2];
        }
        return \Response::json(array("status" => 400, "msg" =>$msg, "result" => array()));
    }
}
public function updateProfilePic(Request $request)
{
    $input =$request->all();
    if(isset($input['userid']))
    {
        if(isset($input["profile_pic"]) && !empty($input["profile_pic"])) {
            $file = $input["profile_pic"];
        }  
        $user = User::find($input['userid']);

            $rules = array(
                'profile_pic' => 'mimes:jpeg,jpg,png,gif',
            );

            $validation_msg = [
                'mimes' => 'File type should be allow only jpeg,jpg,png,gif',
            ];
            $validator = Validator::make($input, $rules,$validation_msg);
            if($validator->fails()) {
                //$arr = array("status"=>400,"msg"=>$validator->errors()->first(),"result"=>array());
                return \Response::json(array("status"=>400,"msg"=>$validator->errors()->first(),"result"=>array()));
            } 
            else {
                try {
                    if(!empty($file) && !is_string($file)) {
                        $destinationPath = 'uploads/'.time().$file->getClientOriginalName();
                        $a = \Image::make($file->getRealPath())->save(img($destinationPath));
                        $input["profile_pic"] = $destinationPath;
                    }    
                    $user->fill($input)->save();
                    return \Response::json(array("status"=>200,"msg"=>"Record Updated successfully", "data"=>url('').'/'.$user->profile_pic));

                }
                catch (Exception $ex) {
                    $msg = $ex->getMessage();
                    if(isset($ex->errorInfo[2])) {
                        $msg = $ex->errorInfo[2];
                    }
                    return \Response::json(array("status" => 400, "msg" =>$msg, "result" => array()));
                }
            }
    }
    else
    {
        return \Response::json(array("status" => 400, "msg" =>"User id not found", "result" => array()));
    }

}
public function updateUserSetting(Request $request)
{
    $input =$request->all();
    if(isset($input['marketing_email']))
        $input['marketing_email']= 1 ;
    else
        $input['marketing_email']= 0 ;

    if(isset($input['progress_email']))
        $input['progress_email']= 1 ;
    else
        $input['progress_email']= 0 ;
    $user = User::find($input['user_id']);
    try {

        $user->fill($input)->save();
        return \Response::json(array("status"=>200,"msg"=>"Record Updated successfully", "data"=>url('').'/'.$user->profile_pic));

    }
    catch (Exception $ex) {
        $msg = $ex->getMessage();
        if(isset($ex->errorInfo[2])) {
            $msg = $ex->errorInfo[2];
        }
        return \Response::json(array("status" => 400, "msg" =>$msg, "result" => array()));
    }
}

public function updateConsultantInfo(Request $request)
{
    $input =$request->all();
    //dd($input);
    $user = User::find($input['user_id']);
    try {

        if(isset($input['where_located']) && count($input['where_located'])>0){
           $user->whereLocated()->sync($input['where_located']);
       }
       $consultantinfo=ConsultantInfo::where('consultant_id',$user->id)->first();
            //dd($consultantinfo);
       $obj_consultant_info = new ConsultantController($this->request);
            //dd($obj_consultant_info);
       $request->request->add(['consultant_id' => $user->id]);

       if(!empty($consultantinfo))
       {
              // echo "string";exit();
         $obj_consultant_info->update($request,$consultantinfo->id);
               //$user->consultantinfo = $user->consultantinfo;
     }
     else
     {
          //echo "string";exit();
         // dd($request);
        $request->request->remove('id_for_update');
        $obj_consultant_info->store($request);

    }
    $user->fill($input)->save();
   /* echo "<pre>";
    print_r($user->toArray());
    exit();*/
    return \Response::json(array("status"=>200,"msg"=>"Record Updated successfully", "data"=>$user->toArray()));
}
catch (Exception $ex) {
    $msg = $ex->getMessage();
    if(isset($ex->errorInfo[2])) {
        $msg = $ex->errorInfo[2];
    }
    return \Response::json(array("status" => 400, "msg" =>$msg, "result" => array()));
}
}
public function myDashboard($type = '', $orderby = '')
{
    if(Auth::check()){
        $consultant_cvlist=array();
        $currencies=array();
        if(Auth::user()->role=='Consultant')
        {
           $user = User::withCount(['projectProposals','activeProjectsProposals','awaitingProjectsProposals','hiringProjectsProposals','completeProjectsProposals','cancelledProjectsProposals','declinedProjectsProposals','WithdrawnProjectsProposals','shortlistedProjectsProposals'])->find(Auth::id());
          
           $projects = $user->projectProposals()->with('project.activeProjectsProposals','project.shortlistedProjectsProposals');
            //$projects = $projects->orderBy('created_at', $orderby);
            if(!empty($type) && $type!='all') {
    			$projects = $projects->where('status',$type);
    		}

            if(!empty($user->consultantCv))
            {
                $consultant_cvlist=$user->consultantCv;
            }
            $currencies=MetaData::where([['type','=','currency'],['status','=','Active']])->select('id','name')->orderby('name')->get();
        }
        else
        {   
    		$user = User::withCount(['projects','activeProjects','awaitingProjects','hiringProjects','inProcessProjects','completeProjects','cancelledProjects','draftProjects','expiredProjects'])->find(Auth::id());
    		$projects = $user->projects()->withCount(['activeProjectsProposals','shortlistedProjectsProposals','projectViews','HiringProjectsProposals','CompleteProjectsProposals']);
    		if(!empty($type) && $type!='all') {
    			$projects = $projects->where('status',$type);
    		}
            else
            {
                $projects = $projects->whereNotIn('status',['Draft','Expired']);   
            }
        	}
     /* dd($projects->get()->toArray());*/
    $arr["user"] = $user;
	$arr["consultant_cvlist"] = $consultant_cvlist;  
    $arr["currencies"] = $currencies;
    $arr["title"] = 'Dashboard';
    $arr["projects"] = $projects->orderBy('updated_at', $orderby)->paginate(10); 

    return view('front-end.my-dashboard',$arr);
    }
    else{
        return view('front-end.login-step');
    }
}

public function projectViewDetail($slug)
{
   
    $temp=explode("-",$slug);
    $id=end($temp);   
    $proposal_id=1;

    $project = Projects::with(['tenderPdf','user','projectLanguages','location','projectExpertise','companyProfileContacts'
        ,'proposals'=> function($q){
                $q->with(['currency','user.consultantinfo.highestQualification','user.consultantinfo.fieldOfStudies','user.consultantinfo.currency','cvDocs','proposalsDocs','user.consultantkeywords','user.role_experience.experiences']);
            }])->withCount(['proposals','activeProjectsProposals','shortlistedProjectsProposals','DeclinedProjectsProposals','WithdrawnProjectsProposals','CompleteProjectsProposals','HiringProjectsProposals'])->where('id',$id)->first();
    //dd($project->toArray());
    foreach ($project->proposals->toArray() as $key => $value) {

            $projectProposals[$value["status"]][] = $value;
    }

     //dd($projectProposals);
    $currencies=MetaData::where([['type','=','currency'],['status','=','Active']])->select('id','name')->orderby('name')->get();
    $user=Auth::user();
    $consultant_cvlist=array();
    if(!empty($user->consultantCv))
    {
        $consultant_cvlist=$user->consultantCv;
    }
    if(empty($project)){
         return view('front-end.pageNotFound');
    }
      
    if(collect(request()->segments())->last() == 'overview'){

        if($project->user_id == Auth::id() ){  
            return view('front-end.project-details',compact('project','projectProposals'));
        }
        else{
            return view('front-end.pageNotFound');
        }
    }
    else{
       
        // if(Auth::user()->role=='Consultant')   
         if($project->user_id != Auth::id())   
         {
            $project->projectViews()->syncWithoutDetaching(Auth::id());
         }

        return view('front-end.projects-view',compact('project','currencies','proposal_id','consultant_cvlist'));
    }
    return redirect('/dashboard');
}
public function redirectToLinkedin($type)
{
    session(['user_role' => $type]);
    return Socialite::driver('linkedin')->redirect();
}
public function handleLinkedinCallback()
{
    $request = $this->request;
    if($request->has('error')){
        return redirect('/');
    }
    else
    {
        try {

            $user = Socialite::driver('linkedin')->user();
            if(!empty($user))
            {
                $userdata=User::where('email', '=', $user->email)->first();
                if (!empty($userdata)) {
                    $role=session('user_role');
                    if($userdata->role==$role)
                    {
                        Auth::loginUsingId($userdata->id);
                    }
                    else{
                        $other_role=(($role=='Consultant') ? 'Company' : 'Consultant' );
                        \Session::put('error',"This email already registered with ".$other_role.".");
                        return redirect('/login/'.$role);
                    }
                }
                else{
                    $userdata=$user->user;
                    $location=MetaData::where('type','location')->first();
                    $role=session('user_role');
                    $create['role'] = $role;
                    $create['first_name'] = $userdata['firstName'];
                    $create['last_name'] = $userdata['lastName'];
                    $create['email'] = $user->email;
                    $create['linkedin_id'] = $user->id;
                    $create['status'] = 'Active';
                    if(!empty($location))
                        $create['location_id'] = $location->id;

                     $rules["email"] = 'required|email|unique:users';
                   
                
                    $validator = Validator::make($create, $rules);
                    if($validator->fails()) {
                       \Session::put('error',$validator->errors()->first());
                       return redirect('/login/'.$role);
                    } else {
                        $createdUser = new User;
                        $createdUser->fill($create)->save();
                        Session::forget('user_role');
                        Auth::loginUsingId($createdUser->id);
                    }
                }
                return redirect('/dashboard');

            }
            else
            {
                return redirect()->route('/')->with('msg', 'Linkedin user not found!');
            }


        } catch (Exception $e) {
            return redirect('/');
        }
    }
}
}

