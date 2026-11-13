<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\LiveTV;
use App\TvCategory;
use App\RecentlyWatched;
use App\LiveTVSubmission;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;


class LiveTvController extends MainAdminController
{
	public function __construct()
    {
		 $this->middleware('auth');
		  
		parent::__construct(); 
        check_verify_purchase();	
		  
    }
    public function live_tv_list()
    { 
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
            {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');
                
             }
        
        $page_title=trans('words.live_tv_list');
        
        $cat_list = TvCategory::orderBy('category_name')->get();      

        if(isset($_GET['s']))
        {
            $keyword = $_GET['s'];  
            $live_tv_list = LiveTV::where("channel_name", "LIKE","%$keyword%")->orderBy('channel_name')->paginate(12);

            $live_tv_list->appends(\Request::only('s'))->links();
        }    
        else if(isset($_GET['cat_id']))
        {
            $cat_id = $_GET['cat_id'];
            $live_tv_list = LiveTV::where("channel_cat_id", "=",$cat_id)->orderBy('id','DESC')->paginate(12);

            $live_tv_list->appends(\Request::only('cat_id'))->links();
        }
        else
        {
            $live_tv_list = LiveTV::orderBy('id','DESC')->paginate(12);
        } 
         
        return view('admin.pages.livetv.list',compact('page_title','live_tv_list','cat_list'));
    }

    public function submissions(Request $request)
    {
        if (Auth::User()->usertype != "Admin" AND Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $query = LiveTVSubmission::with(['user', 'liveTv'])->orderBy('id', 'DESC');

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('s')) {
            $search = trim($request->input('s'));
            $query->where(function ($q) use ($search) {
                $q->where('channel_name', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        $submissions = $query->paginate(20)->appends($request->query());
        $pendingCount = LiveTVSubmission::where('status', 'pending')->count();
        $approvedCount = LiveTVSubmission::where('status', 'approved')->count();
        $rejectedCount = LiveTVSubmission::where('status', 'rejected')->count();
        $page_title = 'Live TV User Submissions';

        return view('admin.pages.livetv.submissions', compact(
            'submissions', 'pendingCount', 'approvedCount', 'rejectedCount', 'page_title'
        ));
    }

    public function approveSubmission($id)
    {
        if (Auth::User()->usertype != "Admin" AND Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $submission = LiveTVSubmission::where('status', 'pending')->findOrFail($id);
        $tv = new LiveTV;
        $tv->channel_access = $submission->channel_access ?: 'Free';
        $tv->channel_cat_id = $submission->channel_cat_id;
        $tv->channel_name = addslashes($submission->channel_name);
        $tv->channel_slug = Str::slug($submission->channel_name, '-', null);
        $tv->channel_description = $submission->channel_description ? addslashes($submission->channel_description) : null;
        $tv->channel_url_type = $submission->channel_url_type ?: 'HLS';
        $tv->channel_url = $submission->channel_url;
        $tv->channel_url2 = $submission->channel_url2;
        $tv->channel_url3 = $submission->channel_url3;
        $tv->channel_thumb = $submission->channel_thumb;
        $tv->status = 1;
        $tv->seo_title = $submission->seo_title ?: $submission->channel_name;
        $tv->seo_description = $submission->seo_description;
        $tv->seo_keyword = $submission->seo_keyword;
        $tv->save();

        $submission->status = 'approved';
        $submission->live_tv_id = $tv->id;
        $submission->reviewed_by = Auth::id();
        $submission->reviewed_at = now();
        $submission->save();

        Session::flash('flash_message', "Live TV '{$submission->channel_name}' approved and published.");
        return redirect()->back();
    }

    public function rejectSubmission(Request $request, $id)
    {
        if (Auth::User()->usertype != "Admin" AND Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $submission = LiveTVSubmission::where('status', 'pending')->findOrFail($id);
        $submission->status = 'rejected';
        $submission->admin_notes = $request->input('admin_notes');
        $submission->reviewed_by = Auth::id();
        $submission->reviewed_at = now();
        $submission->save();

        Session::flash('flash_message', "Live TV '{$submission->channel_name}' rejected.");
        return redirect()->back();
    }
    
    public function addTv()    { 
        
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
            {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');
                
             }

        $page_title=trans('words.live_tv_add');

        $cat_list = TvCategory::orderBy('category_name')->get();   
 
        return view('admin.pages.livetv.addedit',compact('page_title','cat_list'));
    }
    
    public function addnew(Request $request)
    { 
        
        $data =  \Request::except(array('_token')) ;
        
        if(!empty($inputs['id'])){
                $rule=array(
                        'tv_category' => 'required',
                        'channel_name' => 'required'               
                         );
        }else
        {
            $rule=array(
                        'tv_category' => 'required',
                        'channel_name' => 'required',
                         'channel_thumb' => 'required'                
                         );
        }

         $validator = \Validator::make($data,$rule);
 
        if ($validator->fails())
        {
                return redirect()->back()->withInput()->withErrors($validator->messages());
        } 
        $inputs = $request->all();
        
        if(!empty($inputs['id'])){
           
            $tv_obj = LiveTV::findOrFail($inputs['id']);

        }else{

            $tv_obj = new LiveTV;

        }

         $tv_slug = Str::slug($inputs['channel_name'], '-',null);

         
   
         $tv_obj->channel_access = $inputs['channel_access'];
         $tv_obj->channel_cat_id = $inputs['tv_category'];
         $tv_obj->channel_name = addslashes($inputs['channel_name']);
         $tv_obj->channel_slug = $tv_slug;
         $tv_obj->channel_description = addslashes($inputs['channel_description']);

         $tv_obj->channel_url_type = $inputs['channel_url_type'];

         if($inputs['channel_url_type']=="Embed")
         {
            $tv_obj->channel_url=$inputs['channel_url_embed'];          
         }
         else if($inputs['channel_url_type']=="youtube")
         {
            $tv_obj->channel_url=$inputs['channel_url_youtube'];          
         }
         else if($inputs['channel_url_type']=="dash")
         {
            $tv_obj->channel_url=$inputs['channel_url_dash'];
 
            $tv_obj->channel_url2=$inputs['channel_url2_dash'];
                           
            $tv_obj->channel_url3=$inputs['channel_url3_dash'];      
         }
         else
         { 
            $tv_obj->channel_url=$inputs['channel_url'];
 
            $tv_obj->channel_url2=$inputs['channel_url2'];
                           
            $tv_obj->channel_url3=$inputs['channel_url3'];     
                  
         }
           
         $tv_obj->channel_thumb = $inputs['channel_thumb'];
         $tv_obj->status = $inputs['status'];  

         
         $tv_obj->seo_title = addslashes($inputs['seo_title']);  
         $tv_obj->seo_description = addslashes($inputs['seo_description']);  
         $tv_obj->seo_keyword = addslashes($inputs['seo_keyword']);  
         
         $tv_obj->save();
         
        
        if(!empty($inputs['id'])){

            \Session::flash('flash_message', trans('words.successfully_updated'));

            return \Redirect::back();
        }else{

            \Session::flash('flash_message', trans('words.added'));

            return \Redirect::back();

        }            
        
         
    }     
   
    
    public function editTv($tv_id)    
    {      
          if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
            {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');
                
             }

          $page_title=trans('words.live_tv_edit');

          $cat_list = TvCategory::orderBy('category_name')->get();
          $tv_info = LiveTV::findOrFail($tv_id);   

          return view('admin.pages.livetv.addedit',compact('page_title','tv_info','cat_list'));
        
    }	 
    
    public function delete($tv_id)
    {
    	if(Auth::User()->usertype=="Admin" OR Auth::User()->usertype=="Sub_Admin")
        {
       
            $recently = RecentlyWatched::where('video_type','LiveTV')->where('video_id',$tv_id)->delete(); 	

            $sports = LiveTV::findOrFail($tv_id);
            $sports->delete();

            \Session::flash('flash_message', trans('words.deleted'));

            return redirect()->back();
        }
        else
        {
            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
            
        
        }
    }

     
     
    	
}
