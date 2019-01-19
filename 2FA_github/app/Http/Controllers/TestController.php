<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use First\Helper;
use First\GoogleAuthenticator;
use PragmaRX\Google2FA\Google2FA;
use PragmaRX\Google2FA\Support\Constants;

class TestController extends Controller
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

        $twoFa_Obj = new Google2FA();
        $secretKey = $twoFa_Obj->generateSecretKey();
        $twoFa_Obj->setAllowInsecureCallToGoogleApis(true);
        $twoFa_Qr = $twoFa_Obj->getQRCodeGoogleUrl('DC','a925d37@yahoo.com.tw',$secretKey);

        return view('test', compact('twoFa_Qr', 'secretKey'));

    }

    public function verify()
    {

        $g = new GoogleAuthenticator();
        if (!$g->checkCode($_POST['secretKey'], $_POST['vericode'])) {
            echo json_encode(array('status' => 'error','message' => '輸入錯誤'));
            exit();
        } else {
            echo json_encode(array('status' => 'success','message' => '驗證成功'));
        }


    }

}
