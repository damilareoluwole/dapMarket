<?php

use App\Models\mProductCategory;
use App\Models\mProductBrand;
use App\Models\mProduct;  
use App\Models\mShop;
use App\Models\mProductSize;
use App\Models\mProductCondition;
use App\Models\mProductImages;
use App\Models\PendingWallet;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

define('APP_NAME','DAP');
define('APP_DESCRIPTION','DAP');
define('APP_FAQ','https://www.afritsolutions.com');
define('APP_WEBSITE','https://www.afritsolutios.com');
define('APP_COMPANY','DAP');
//added
define('VOUCHER_USER_EMAIL','voucher@afritsolutions.com'); //please use supper complicated password when creating the account.

//test env.
/**/
define('PUBLIC_KEY','FLWPUBK_TEST-eadd0fc34e83db6bfa65e132c8ee4c7e-X');
define('SECRET_KEY','FLWSECK_TEST-a12d2018125818846294f5578eb01de3-X');
define('ENCRYPTION_KEY','FLWSECK_TEST199b00ed6095');


/*
//live env.
define('PUBLIC_KEY','FLWPUBK-b8cf231effaa0393728bdca496bba777-X');
define('SECRET_KEY','FLWSECK-d340c1d3379efcf0f9bb1c610e3bb97e-X');
define('ENCRYPTION_KEY','d340c1d3379ed843fd34e279');
*/

define('WEBHOOK_URL','');

define('DEFAULT_EMAIL_FOR_PAYMENTS','abonuoha@gmail.com');


function successResponse ($message, $data=[], $status_code = 0, $header=200) {
    $status = true;
    return response()->json(compact('status', 'status_code', 'message', 'data'), $header);
}

function errorResponse ($message, $data=[], $status_code = 1, $header=200) {
    $status = false;
    if($status_code == 99){
        $header=401;
    }
    return response()->json(compact('status', 'status_code', 'message', 'data'), $header);
}

//added
function get_voucher_user()
{
    return User::where('email',VOUCHER_USER_EMAIL)->first();
}

//added
function get_format_user_basic_info($user){
    return [
        'id'=>$user->id,
        'name'=>$user->name,
        'phone'=>$user->phone,
        'email'=>$user->email,
        'picURL'=>resolve_user_pic_path($user->pic),
    ];
}

function get_user_id()
{
    if(!Auth::guest())
        return Auth::user()->id;

    return 0;
}

function get_user_name()
{
    if(!Auth::guest())
        return Auth::user()->name;
    return '';
}

function get_user_email()
{
    if(!Auth::guest())
        return Auth::user()->email;
    return '';
}

function get_user_phone()
{
    if(!Auth::guest())
        return Auth::user()->phone;
    return '';
}

function get_user_pic()
{
    if(!Auth::guest())
        return Auth::user()->pic;
    return null;
}

function search_user($username){

    $phone = validatePhoneToNational($username);
    if($phone){
        $user = User::where('phone', $phone)->first();
        if ($user){
            return $user;
        }
    }

    $user = User::where('email',$username)->first();
    if ($user){
        return $user;
    }

    $user = User::where('phone', $username)->first();
    if($user){
        return $user;
    }

    return null;
}

function get_user_email_by_id($user_id = 0)
{
    if(!$user_id){
        $user_id = get_user_id();
    }

    $email = User::where('id',$user_id)->first()->email;

    if($email)
        return $email;

    return 0;
}

function get_user_phone_by_id($user_id = 0)
{
    if(!$user_id){
        $user_id = get_user_id();
    }

    $phone = User::where('id',$user_id)->first()->phone;

    if($phone)
        return $phone;

    return 0;
}

function get_wallet_id($user_id = 0)
{
    if(!$user_id){
        $user_id = get_user_id();
    }

    $wallet = Wallet::where('user_id',$user_id)->first(['id']);

    if($wallet)
        return $wallet->id;

    return 0;
}

function get_pending_wallet_id($user_id = 0)
{
    if(!$user_id){
        $user_id = get_user_id();
    }
    $wallet = PendingWallet::where('user_id',$user_id)->first(['id']);

    if($wallet)
        return $wallet->id;

    return 0;
}

function get_balance($user_id = 0){

    if(!$user_id){
        $user_id = get_user_id();
    }

    $wallet = Wallet::where('user_id',$user_id)->first();
    $pending = PendingWallet::where('user_id',$user_id)->first();
    $total = $wallet->amount + $pending->amount;
    return ['total'=>$total,'available'=>$wallet->amount, 'pending'=>$pending->amount, 'currency_symbol'=>get_currency_symbole()];
}

function get_currency_symbole()
{
    return '₦';
}

function format_currency($amount)
{
    return get_currency_symbole().' '.number_format($amount, 2);
}

function format_amount($amount)
{
    return round($amount, 2);
}

function get_balance_total()
{
    $balance = get_balance();
    return $balance['total'];
}


function createUserWallets($user_id){
    $wallet = Wallet::firstOrCreate(['user_id' => $user_id]);
    $pending_wallet = PendingWallet::firstOrCreate(['user_id' => $user_id]);
}


/**
 * Generates the transaction id
 * @returns String
 */
function generateransactionId($user_id) {
    $currentTime = time();
    return $user_id.$currentTime;
}

/**
 * Determin if the transaction can be released or refunded
 * @returns Boolean
 */
function transaction_can_release_refund($status)
{
    if(!in_array($status,['RELEASED', 'REFUND', 'NA']))
        return true;

    return false;
}

function encrypt3Des($data){
    $key = ENCRYPTION_KEY;
    $encData = openssl_encrypt($data, 'DES-EDE3', $key);
    return base64_encode($encData);

}


function my_substring($string, $lenght)
{
    $surfix = '';
    if(strlen($string) > $lenght){
        $lenght = $lenght - 3;
        $surfix = '...';
    }
    return substr($string, 0, $lenght).$surfix;
}

function get_count_transaction_requests($response)
{
    $user_id = get_user_id();
    return TransactionRequest::where('request_to', $user_id)
        ->where('response', 'PENDING')
        ->count('id');
}

function sendWhatsappMessage($type, $data){
    if($type == 'Registration'){
        $message = 'Yaaayyy!!! Hello '.$data->name.', Welcome to DAP!';
        redirect()->away('https://api.whatsapp.com/send?phone='.$data->phone.'&text='.$message);
    }
}

function sendSms($type, $data){
    if($type == 'Registration'){
        $message = 'Yaaayyy!!! Hello '.$data->name.', Welcome to DAP!';
        redirect()->away('https://smsexperience.com/api/sms/sendsms?username=SuprixTech&password=LATA3H8&sender=DAP&recipient='.$data->phone.'&message='.$message);
    }

    if($type == 'Transaction'){
        $message = 'Credit Alert - DAP. Dear '.$data->r_name.', your account has been credited with '.format_currency($data->amount). ' by '.$data->s_name.'/'.$data->s_phone.'.';
        redirect()->away('https://smsexperience.com/api/sms/sendsms?username=SuprixTech&password=LATA3H8&sender=DAP&recipient='.$data->r_phone.'&message='.$message);
    }
}

function sendEmail($type, $data){
    if($type == 'Registration'){
        Mail::to($data)->queue(new NewUserNotification($data));
    }

    if($type == 'Transaction'){
        if(($data->type)=='SEND_MONEY'){
            $type = 'SEND_MONEY';
            $user = User::where('email', $data->s_email)->first();
            Mail::to($user)->queue(new DebitTransaction($data, $type));

            $user = User::where('email', $data->r_email)->first();
            Mail::to($user)->queue(new CreditTransaction($data, $type));
        }

        if(($data->type)=='VOUCHER'){
            $type = 'VOUCHER';
            $user = User::where('email', $data->s_email)->first();
            Mail::to($user)->queue(new DebitTransaction($data, $type));
            Mail::to($data->username)->queue(new CreditTransaction($data, $type));
        }
    }

    if($type == 'Release-Transaction'){
            $user = User::where('email', $data->r_email)->first();
            Mail::to($user)->queue(new CreditTransaction($data, $type));

    }

    if($type == 'Claimed-Voucher'){
        Mail::to($data->benefitiary_phone)->queue(new ClaimedVouchers($data));
    }

}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateRandomString1($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateOTP($length = 4) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateapikey($phone){
    return generateRandomString().$phone.generateRandomString1().date('YmdHis');
}

function validatePhoneToNational($phone){
    $phone = str_replace(' ', '', $phone);
    $phone = trim($phone,"+");
    if(substr($phone, 0, 3) == '234'){
        $phone = substr($phone, 3);
        $phone = '0'.$phone;
    }
    if(substr($phone, 0, 2) == '00'){
        $phone = substr($phone, 1);
    }
    if(strlen($phone) == 11){
        return $phone;
    }

    return false;
}


function getPictureFromURL($picUrl){
    $imageString = file_get_contents($picUrl);
    $picture = generateRandomString().time().'.jpg';
    $save = file_put_contents(public_path('images/users/'.$picture),$imageString);
    return $picture;
}

function generateNumberOTP(){
    $otp = rand(100000, 999999);
    return $otp;
}
function sendOTP($purpose, $otp, $user_id){
    //get user phone and email
    //send to phone
    //send to email
    $user = User::where('id', $user_id)->first();
    Mail::to($user)->queue(new OTP_Template($otp));
}

function check_otp_expiry_time($timeStored){
    $timeNow =  $_SERVER["REQUEST_TIME"];
    if(($timeNow - $timeStored) > 60){
        return true;
    }
    return false;
}

//DAP_MARKET 5/12/2021

function getCategories()
{
    $category = mProductCategory::where('is_enabled',0)
                                ->orderByDesc('rank')
                                ->get();
    return $category;
}

function get_category_children_id2($pid,$cat)
{    
    $data = collect($cat)->where('parent_id', $pid);
    foreach($data as $datum)
        {
            array_push($GLOBALS['children'], $datum->id);
            get_category_children_id2($datum->id, $cat);
        }
}

$GLOBALS['children'] = array();

function get_category_children_id1($cat_id = 0)
{       $C = [];
        $cat = getCategories();
        get_category_children_id2($cat_id,$cat);
        $children = $GLOBALS['children'];
        
    $category = mProductCategory::where('is_enabled', 0)
                                ->whereIn('id', $children)
                                ->orderByDesc('rank')
                                ->get();
    return $category;
}

function getBrand($id=0)
{
    if($id){
        $brand = mProductBrand::where('is_enabled',0)
                                ->where('id',$id)
                                ->first();
    }else{
    $brand = mProductBrand::where('is_enabled',0)
                            ->orderByDesc('rank')
                            ->get();
    }
    return $brand;
}

function getProduct($id=0)
{
    if($id){
        $product = mProduct::where('id',$id)
                            ->first();
    }else{
    $product = mProduct::orderByDesc('rank')
                        ->get();
    }
    return $product;
}

function resolve_pic_path($type, $pic){
    if($type == 'category'){
        if($pic){
            return asset('images/category/'.$pic);
        }else{
            return asset('images/category/avatar.jpg');
        }  
    }

    if($type == 'homepage'){
        if($pic){
            return asset('images/home/'.$pic);
        }else{
            return asset('images/home/avatar.jpg');
        }  
    }

    if($type == 'brand'){
        if($pic){
            return asset('images/brand/'.$pic);
        }else{
            return asset('images/brand/avatar.jpg');
        }  
    }

    if($type == 'product'){
        if($pic){
            return asset('images/product/'.$pic);
        }else{
            return asset('images/product/avatar.jpg');
        }  
    }

    if($type == 'ads'){
        if($pic){
            return asset('images/ads/'.$pic);
        }else{
            return asset('images/ads/avatar.jpg');
        }  
    }

    if($type == 'users'){
        if($pic){
            return asset('images/users/'.$pic);
        }else{
            return asset('images/users/avatar.jpg');
        }  
    }
}

function get_category_name($id=0)
{
    $category = mProductCategory::where('id',$id)->first(['name']);
    return $category->name;
}

function get_shop_name($id=0)
{
    $shop = mShop::where('id',$id)->first(['name']);
    return $shop->name;
}

function get_brand_name($id=0)
{
    $brand = mProductBrand::where('id',$id)->first(['name']);
    return $brand->name;
}

function get_condition_name($id=0)
{
    $condition = mProductCondition::where('id',$id)->first(['name']);
    return $condition->name;
}

function get_all_product_images($id=0)
{
    $images = mProductImages::where('product_id',$id)->get();
    return $images;
}

function get_size_name($id=0)
{
    $size = mProductSize::where('id',$id)->first(['name']);
    return $size->name;
}

function getShop($id=0)
{
    if($id){
        $shop = mShop::where('is_enabled',0)
                                ->where('id',$id)
                                ->first();
    }else{
        $shop = mShop::where('is_enabled',0)
                            ->orderByDesc('rank')
                            ->get();
    }
    return $shop;
}

function get_user_data($id = 0)
{
    $user = User::find($id);
    if($user){
        $user->account_number = $user->phone;
        $user->picURL = get_user_pic_path($user->id);
        return $user;
    }

    return [];
}

function get_user_pic_path($id = 0)
{
    $pic = '';
    $user = User::find($id,['pic']);
    $pic = $user->pic;

    return resolve_pic_path('users', $pic);
}



