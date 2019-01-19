<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Members;
use First\Whiteips;
use First\Helper;
use First\GoogleAuthenticator;
use PragmaRX\Google2FA\Google2FA;
// use App\User;
use DB;
use Input;
use Validator;
use Redirect;

class FormController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('formTest');

    }

    public function login_page()
    {

        $ip_Arr = new Helper();
        //autoLoad -> white_ips
        print_r($ip_Arr->white_ips_check());

        return view('logIn');
    }

    public function send()
    {

        $date=date("Y-m-d H:i:s");
        $status = 'error';
        $message = 'ERROR FILTER! PLEASE CHECK AGAIN!';
        //消毒表單內容 can work
        $formData=clean($_POST);
        //dd($_POST);
        $email = $formData['email'];
        $pwd = $formData['pass'];
        $country = $formData['country'];
        $phone = $formData['phone'];
        $birthday = $formData['birthday'];

        //過濾輸入的值(filter)
        if (filter_var($email, FILTER_SANITIZE_EMAIL) == false ||
            filter_var($pwd, FILTER_SANITIZE_STRING) == false ||
            filter_var($country, FILTER_SANITIZE_STRING) == false ||
            filter_var($phone, FILTER_SANITIZE_NUMBER_INT) == false ||
            filter_var($birthday, FILTER_SANITIZE_NUMBER_INT) == false
         ) {
            echo json_encode(array('status' => $status,'message' => $message));
            exit();
        }

        //表單欄位驗證
        $input = Input::all();
        $rules = ['email' => 'email|required',
        'pass' => 'required'];

        $messages = ['email.email' => 'email格式錯誤',
        'email.required' => 'email未填',
        'pass.required' => '密碼未填'];
        $validator = Validator::make($input, $rules, $messages);

        //輸出錯誤訊息 //array -> string
        $output=implode("<br />",$validator->messages()->all());

        if ($validator->fails()) {
            echo json_encode(array('status' => $status,'message' => $output));
            exit();
        }

        //驗證信箱是否被註冊過
        $email_db = DB::table('user')->pluck('email');
        $email_Arr = $email_db->all();
        //print_r($email_db->all());

        if (in_array(strip_tags($email), $email_Arr))
        {

            echo json_encode(array('status' => $status,'message' => '信箱已被註冊過'));
            exit();

        }

        if (count($_POST) === 6) {

            //密碼加密
            $password_encrypted = $this->my_encrypt(strip_tags($pwd),config('constants.EN_KEY'));
            //can work
            DB::table('user')->insert(
                ['email' => strip_tags($email), 
                 'password' => strip_tags($password_encrypted),
                 'country' => strip_tags($country),
                 'phone' => strip_tags($phone),
                 'birthday' => strip_tags($birthday),
                 'created_at' => $date]
                );

            //can work
            echo json_encode(array('status' => 'OK','message' => '註冊成功'));

        }


    }

    public function login()
    {

        //撈符合條件的第一筆資料
        $loginData = DB::table('members')->where('email', '=', $_POST['email'])->first();
        //引入helper->白名單驗證function
        $helper = new Helper();

        if (count($loginData) == 0) {
            echo json_encode(array('status' => 'error',
                'has_secert' => 'F',
                'message' => '帳號或密碼錯誤'));
            exit();
        }

        //var_dump($loginData->tfa_key);

        if ($helper->white_ips_check() === 0) {

            //加密過的密碼
            $pwd_encode = $loginData->pwd;
            //密碼解密
            $pwd_decode=$this->my_decrypt($pwd_encode, config('constants.EN_KEY'));

            if ( $pwd_decode !== $_POST['pass'] ) {
                echo json_encode(array('status' => 'error',
                    'has_secert' => 'F',
                    'message' => '帳號或密碼錯誤'));
                exit();
            }


            if ($loginData->tfa_key == null ||
                empty($loginData->tfa_key) ||
                strlen( $loginData->tfa_key ) == 0
                ) {
                //2FA secretKey
                $twoFa_Obj = new Google2FA();
            $secretKey = $twoFa_Obj->generateSecretKey();
            $twoFa_Obj->setAllowInsecureCallToGoogleApis(true);
            $twoFa_Qr = $twoFa_Obj->getQRCodeGoogleUrl('DevilCase',$_POST['email'],$secretKey);

                    //存secretkey進資料庫
            DB::table('members')
            ->where('email', $loginData->email)
            ->update(['tfa_key'=>$secretKey]);

            echo json_encode(array('status' => 'error',
               'message' => '權限不足，請聯絡系統管理員',
               'qr_url' => $twoFa_Qr,
               'company' => 'DevilCase',
               'email' => $_POST['email'],
               'has_secert' => 'N',
               'secretkey' => $secretKey));

            exit();

        } elseif ($loginData->tfa_key &&
         strlen( $loginData->tfa_key ) == 16) {

            $twoFa_Obj = new Google2FA();
            $loginotp_Obj = DB::table('members')->where('email', '=', $_POST['email'])->first();
            $tfa_key = $loginotp_Obj->tfa_key;

            echo json_encode(array(
                'status' => 'error',
                'message' => 'just otp',
                'has_secert' => 'Y',
                'secretkey' => $tfa_key));

            exit();

        }

    }


        //print_r($_POST);
        //表單欄位驗證
    $input = Input::all();
    $rules = ['email' => 'email|required','pass' => 'required'];
    $messages = ['email.email' => 'email格式錯誤',
    'email.required' => 'email未填',
    'pass.required' => '密碼未填'];
    $validator = Validator::make($input, $rules, $messages);


        //輸出錯誤訊息 //array -> string
    $output=implode("<br />",$validator->messages()->all());



    if ($validator->fails()) {

        echo json_encode(array('status' => 'error','message' => $output));
        exit();

    } else {

        //echo count($_POST);
        if (count($_POST) === 4) {

            //加密過的密碼
            $pwd_encode = $loginData->pwd;
            //密碼解密
            $pwd_decode=$this->my_decrypt($pwd_encode, config('constants.EN_KEY'));

            if ($loginData->email == $_POST['email'] && $pwd_decode == $_POST['pass']) {
                echo json_encode(array('status' => 'success',
                    'has_secert' => 'W',
                    'message' => '登入成功'));

            }else {

                echo json_encode(array('status' => 'error',
                    'has_secert' => 'F',
                    'message' => '帳號或密碼錯誤'));

            }

        }

    }

}

    //2FA input verify
public function tfalogin()
{

        //print_r($_POST);
        //抓前端過來的值
    $opt = $_POST['otp'];
    $secretkey = $_POST['secretKey'];

    if (empty($opt) || strlen($opt) == 0) {
        echo json_encode(array('status' => 'error','message' => '驗證碼未填'));
        exit();
    }

    $g = new GoogleAuthenticator();

        //進行兩階段驗證
    if (!$g->checkCode($secretkey, $opt)) {

        echo json_encode(array('status' => 'error','message' => '驗證錯誤'));

    } elseif ($g->checkCode($secretkey, $opt)) {

        echo json_encode(array('status' => 'success','message' => 'ok'));

    } else {

        echo json_encode(array('status' => 'error','message' => '系統錯誤'));

    }

}

    //密碼加密
function my_encrypt($pw, $key)
{

        // Remove the base64 encoding from our key
    $encryption_key = base64_decode($key);
        // Generate an initialization vector
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        // Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
    $encrypted = openssl_encrypt($pw, 'aes-256-cbc', $encryption_key, 0, $iv);
        // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
    return base64_encode($encrypted . '::' . $iv);

}

    //密碼解密
function my_decrypt($data, $key)
{

        // Remove the base64 encoding from our key
    $encryption_key = base64_decode($key);
        // To decrypt, split the encrypted data from our IV - our unique separator used was "::"
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);

}

}
