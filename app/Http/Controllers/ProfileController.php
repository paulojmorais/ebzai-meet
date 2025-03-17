<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateUserSecurityRequest;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\GlobalConfig;
use App\Models\EmailTemplates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\CancelSubscriptionMail;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        if (getSetting('VERIFY_USERS') == 'enabled') {
            $this->middleware('customVerified');
        }
    }

    //show the index page
    public function index(Request $request)
    {
        return view('profile.profile', ['user' => $request->user(), 'page' => __('Basic Information'), 'route' => 'basic']);
    }

    //update profile
    public function updateProfile(UpdateUserProfileRequest $request)
    {
        if (isDemoMode()) return back()->with('error', __('This feature is not available in demo mode'));
        try {
            $request->user()->username = $request->username;
            $request->user()->email = $request->email;
            
            $request->user()->save();

            return back()->with('success', __('Settings saved.'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
        
    }

    //delete user avatar
    public function deleteAvatar(Request $request)
    {
        $demoEmails = ['admin@thertclabs.com', 'user1@thertclabs.com', 'user2@thertclabs.com'];

        if (isDemoMode() && in_array($request->user()->email, $demoEmails)) return json_encode(['success' => false, 'error'=>__('This feature is not available in demo mode')]);

        unlink(storage_path('app/public/avatars/'.$request->user()->avatar));
        $request->user()->avatar = null;
        $request->user()->save();

        return json_encode(['success' => true, 'id' => $request->id]);
    }

    //show the security page
    public function security(Request $request)
    {
        return view('profile.security', ['user' => $request->user(), 'page' => __('Security'), 'route' => 'security']);
    }

    //update security
    public function updateSecurity(UpdateUserSecurityRequest $request)
    {
        if (isDemoMode()) return back()->with('error', __('This feature is not available in demo mode'));

        $request->user()->password = Hash::make($request->input('password'));
        $request->user()->save();

        Auth::logoutOtherDevices($request->input('password'));

        return back()->with('success', __('Settings saved.'));
    }

    //show the plan page
    public function myPlan(Request $request)
    {
        return view('profile.plan', ['user' => $request->user(), 'page' => __('Plan'), 'route' => 'plan']);
    }

    //cancel plan
    public function cancelPlan(Request $request)
    {
        $request->user()->planSubscriptionCancel();

        try {
            $emailBody = EmailTemplates::find(2);
            Mail::to($request->user()->email)->send(new CancelSubscriptionMail($request->user(),$emailBody['content']));
        } catch (\Exception $e) {
        }
        return back()->with('success', __('Settings saved.'));
    }

    //show the payments page
    public function payments(Request $request)
    {
        $payments = Payment::where('user_id', $request->user()->id)
            ->orderBy('id', 'DESC')->paginate(config('app.pagination'));

        $plans = Plan::where([['amount_month', '>', 0], ['amount_year', '>', 0]])->withTrashed()->get();

        return view('profile.payments.index', ['payments' => $payments, 'plans' => $plans, 'page' => __('Payments'), 'route' => 'payments']);
    }

    //show the invoice
    public function invoice($id,Request $request)
    {
        $payments = Payment::where('id', $id)
            ->firstOrFail();
        $companyDetails = GlobalConfig::where('key','like','%company%')->orWhere('key','like','%logo%')->get();
        $commonData = [];
        foreach($companyDetails as $key => $val){
            $commonData[$val->key] = $val->value;
        }
        
        
        
        $taxAmount = 0;
        if(isset($payments->tax_rates) && $payments->tax_rates[0]){
            if($payments->tax_rates[0]->type == 0){
                // $taxAmount = (isset($payments->product->amount_year)  ? $payments->product->amount_year : $payments->product->amount_month) * ($payments->tax_rates[0]->percentage /100);
                $taxAmount = 0;
            }elseif($payments->tax_rates[0]->type == 1){
                $taxAmount = (isset($payments->product->amount_year)  ? $payments->product->amount_year : $payments->product->amount_month) * ($payments->tax_rates[0]->percentage /100);
            }
        }

        $discountAmt = 0;
        $discountDetails = [];
        if($payments->coupon){
            $discountAmt = calculateDiscount(isset($payments->product->amount_year)  ? $payments->product->amount_year+floatval($taxAmount) : $payments->product->amount_month+floatval($taxAmount),$payments->coupon->percentage);
            $discountDetails['couponName'] = $payments->coupon->code;
            $discountDetails['discountAmt'] = $discountAmt;
        }

        return view('profile.payments.invoice', ['payments' => $payments, 'commonData' => $commonData, 'userEmail' => $request->user()->email, 'route' => 'payments', 'taxAmount' => $taxAmount, 'discountAmt' => $discountAmt]);
    }

    //API token
    public function api(Request $request)
    {
        $user = $request->user();

        if (!$user->api_token) {
            $user->api_token = Str::random(60);
            $user->save();
        }

        return view('profile.api', ['api_token' => $user->api_token, 'page' => __('API Token'), 'route' => 'api']);
    }

    //contacts
    public function contacts(Request $request)
    {
        $contacts = Contact::where('user_id', $request->user()->id)
            ->orderBy('id', 'DESC')->paginate(config('app.pagination'));

        return view('profile.contacts.index', ['contacts' => $contacts, 'page' => __('Contacts'), 'route' => 'contacts']);
    }

    //contact form
    public function contactForm(Request $request)
    {
        return view('profile.contacts.create', ['page' => __('Create Contact'), 'route' => 'contactCreate']);
    }

    //create contact
    public function createContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:20',
            'email' => 'required|email:filter|unique:contacts,email,NULL,id,user_id,'.Auth::id().'|max:50'
        ], [
            'email.unique' => __('This email already exists in the contacts')
        ]);

        if ($validator->fails()) {
            return redirect()->route('profile.createContactForm')->with('error', $validator->errors()->first())->withInput(); 
        }

        $values = array(
            'name' => $request->name,
            'email' => $request->email,
            'user_id' => Auth::id()
        );

        Contact::create($values);
        
        return redirect()->route('profile.contacts')->with('success', __('Contact has been added'));   
    }

    //edit contact form
    public function editContactForm(Request $request, $id)
    {
        $contact = Contact::where('id', $request->id)->first();

        if (!$contact) {
            return redirect()->route('profile.contacts')->with('error', __('Contact not found')); 
        }

        return view('profile.contacts.edit', ['contact' => $contact, 'page' => __('Edit Contact'), 'route' => 'contactEdit']);   
    }

    //edit contact
    public function editContact(Request $request, $id)
    {
        $contact = Contact::where('id', $id)->firstOrFail();

        if (!$contact) {
            return redirect()->route('profile.contacts')->with('error', __('Contact not found'))->withInput(); 
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:20',
            'email' => 'required|email:filter|unique:contacts,email,'.$id.',id,user_id,'.Auth::id().'|max:50'
        ], [
            'email.unique' => __('This email already exists in the contacts')
        ]);

        if ($validator->fails()) {
            return redirect()->route('profile.editContact',$id)->with('error', $validator->errors()->first()); 
        }   

        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->user_id =  Auth::id();
        $contact->save();
        
        return redirect()->route('profile.contacts')->with('success', __('Contact has been updated'));   
    }

    //delete contact
    public function deleteContact(Request $request)
    {
        $user = Contact::find($request->id);

        if ($user->delete()) {
            return json_encode(['success' => true, 'message' => __('Contact has been deleted')]);
        }

        return json_encode(['success' => false, 'error' => __('An error occurred, please try again')]);
    }

    //contact import form
    public function contactImportForm()
    {
        return view('profile.contacts.import', ['page' => __('Import Contacts'), 'route' => 'contactImport']);
    }

    //download sample CSV file
    public function downloadCsvFile()
    {
        $filepath = public_path('/sources/sample.csv');
        return Response::download($filepath); 
    }

    //import contact 
    public function importContact(Request $request)
    {
        $file = $request->file('file');
        //file details 
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileSize = $file->getSize();
      
        //valid file extensions
        $valid_extension = array("csv");
      
        //2MB in bytes
        $maxFileSize = 2097152; 
      
        //check file extension
        if(in_array(strtolower($extension),$valid_extension)) {
            //check file size
            if($fileSize <= $maxFileSize) {
    
                //file upload location
                $location = 'file_uploads';
        
                //upload file
                $file->move($location,$filename);
        
                //import CSV to Database
                $filepath = public_path($location."/".$filename);
        
                //reading file
                $file = fopen($filepath,"r");
        
                $importData_arr = array();
                $i = 0;
        
                while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $num = count($filedata );
                    
                    //skip first row
                    if($i == 0){
                        $i++;
                        continue; 
                    }
                    for ($c=0; $c < $num; $c++) {
                        $importData_arr[$i][] = $filedata [$c];
                    }
                    $i++;
                }
                fclose($file);

                //insert into database
                $totalRecords = count($importData_arr);
                $totalSuccess = 0;
                $totalFailed  = 0;
                foreach($importData_arr as $importData) {
                    $contactExists = Contact::where('email',trim($importData[1]))->first();
                    if(trim($importData[0]) && trim($importData[1]) && filter_var($importData[1], FILTER_VALIDATE_EMAIL) && !$contactExists) {
                        $insertData = [
                            "name"    => $importData[0],
                            "email"   => $importData[1],
                            "user_id" => auth()->user()->id
                        ];
                        $id = Contact::create($insertData)->id;
                        // activity log
                        logActivity($id, 'Contact', config('constants.LOG_EVENTS.contact_created'), 'Contact Added - '.$importData[0]);
                        $totalSuccess++;
                    } else {
                        $totalFailed++;
                    }
                }
                if ($totalRecords) {
                    unlink(public_path($location."/".$filename));
                    return redirect()->route('profile.contacts')->with('success', __('File imported. Out of total :totalRecords records, :totalSuccess succeeded and :totalFailed failed', ['totalRecords' => $totalRecords, 'totalSuccess' => $totalSuccess, 'totalFailed' => $totalFailed]));
                } else {
                    return redirect()->route('profile.contacts')->with('error', __('No records were found'));
                }
            } else {
                return redirect()->route('profile.importContactForm')->with('error', __('File too large. File must be less than 2MB'));
            }
        } else {
            return redirect()->route('profile.importContactForm')->with('error',__('Invalid file extension'));
        }
    }

    //two factor authentication
    public function tfa(Request $request)
    {
        return view('profile.tfa', ['user' => $request->user(), 'page' => __('Two Factor Authentication'), 'route' => 'tfa']);   
    }
    
    //update two factor authentication
    public function updateTfa(Request $request)
    {
        if (isDemoMode()) return back()->with('error', __('This feature is not available in demo mode'));

        $user = User::find(auth()->user()->id);
        $user->tfa = $request->tfa ?? 'inactive';
        $user->save();

        Session::put('user_tfa', auth()->user()->id);

        if ($user->tfa == 'active') {
            $message = __('Two factor authentication enabled');
        } else {
            $message = __('Two factor authentication disabled');
        }

        return redirect()->route('profile.tfa')->with('success', $message);
    }

    //upload avatar
    public function uploadAvatar(Request $request)
    {
        $demoEmails = ['admin@thertclabs.com', 'user1@thertclabs.com', 'user2@thertclabs.com'];

        if (isDemoMode() && in_array($request->user()->email, $demoEmails)) return response()->json(['error'=>__('This feature is not available in demo mode')]);

        try{
            $avatarPath = storage_path('app/public/avatars/');

            if (!File::exists($avatarPath)) {
                File::makeDirectory($avatarPath);
            }

            if ($request->user()->avatar && File::exists($avatarPath.$request->user()->avatar)) {
                unlink(storage_path('app/public/avatars/'. $request->user()->avatar));
            }
            $image=$request->image;
            $imageName = uniqid().'.'.$request->extension;
            $image->move(storage_path('app/public/avatars/'),$imageName);
            $request->user()->avatar = $imageName;
            $request->user()->save();
            return response()->json(['success'=>__('Data updated successfully')]);
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }           
    }
}
