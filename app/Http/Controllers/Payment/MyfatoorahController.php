<?php

namespace App\Http\Controllers;

use App\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\Course\Entities\CourseFee;
use Modules\Payment\Entities\Payment;
use Modules\Payment\Entities\PaymentDetail;
use Modules\Program\Entities\Program;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;

class MyfatoorahController extends Controller {

    public $mfObj;

    public function __construct() {

        $this->mfObj = new PaymentMyfatoorahApiV2(config('myfatoorah.api_key'), config('myfatoorah.country_iso'), config('myfatoorah.test_mode'));

    }
    public function index(Request $request) {

        try {
            $user = auth()->user();
            $user['amount'] = $request->amount;
            $user['program'] = Program::whereIn('id',$request->program_id)->with('courses')->get();
            Session::put('pay_user', $user);
            $paymentMethodId = 0; // 0 for MyFatoorah invoice or 1 for Knet in test mode
            $orderId = null;
            $curlData = $this->getPayLoadData($user,$orderId);
            $data     = $this->mfObj->getInvoiceURL($curlData, $paymentMethodId);
            return redirect()->to($data['invoiceURL']);
            $response = ['IsSuccess' => 'true', 'Message' => 'Invoice created successfully.', 'Data' => $data];
        } catch (\Exception $e) {
            $response = ['IsSuccess' => 'false', 'Message' => $e->getMessage()];
        }
        return response()->json($response);

    }
    private function getPayLoadData($user,$orderId) {

        $callbackURL = route('myfatoorah.callback');

        return [
            'CustomerName'       => $user->name,
            'InvoiceValue'       => $user->amount,
            'DisplayCurrencyIso' => 'KWD',
            'CustomerEmail'      => $user->email,
            'CallBackUrl'        => $callbackURL,
            'ErrorUrl'           => $callbackURL,
            'MobileCountryCode'  => '+965',
            'CustomerMobile'     => '12345678',
            'Language'           => 'en',
            'CustomerReference'  => $orderId,
            'SourceInfo'         => 'Laravel ' . app()::VERSION . ' - MyFatoorah Package ' . MYFATOORAH_LARAVEL_PACKAGE_VERSION
        ];

    }
    public function callback() {

        try {

            $paymentId = request('paymentId');
            $data      = $this->mfObj->getPaymentStatus($paymentId, 'PaymentId');

            if ($data->InvoiceStatus == 'Paid') {
                $msg = 'Invoice is paid.';
            } else if ($data->InvoiceStatus == 'Failed') {
                $msg = 'Invoice is not paid due to ' . $data->InvoiceError;
            } else if ($data->InvoiceStatus == 'Expired') {
                $msg = 'Invoice is expired.';
            }
            $payment = $this->createPayment($data);
            $response = ['IsSuccess' => 'true', 'Message' => $msg, 'Data' => $data];

        } catch (\Exception $e) {

            $response = ['IsSuccess' => 'false', 'Message' => $e->getMessage()];

        }

        $payment = rawurlencode(json_encode($payment));
        return redirect()->route('add-payment',['payment' => $payment]);
        // return response()->json($response);

    }
    public function addPayment(Request $request){

        $payment = json_decode(rawurldecode($request->payment));
        $payment = Payment::find($payment->id);
        $payment['subscriber_id'] = auth()->user()->id;
        $payment->save();

        $payment_de = Session::get('pay_user');
        foreach ($payment_de->program as $key => $program) {
            foreach ($program->courses as $key => $course) {
                $CourseFee = CourseFee::where(['course_id' => $course->id])->first();
                if($CourseFee){
                    $payment_details = new PaymentDetail();
                    $payment_details['payment_id'] = $payment->id;
                    $payment_details['program_id'] = $program->id;
                    $payment_details['course_id'] = $course->id;
                    $payment_details['amount'] = $payment->amount;
                    $payment_details['course_package_id'] = $CourseFee->course_package_id ?? null;
                    $payment_details->save();
                }
            }
            $cart = Cart::where(['program_id' => $program->id,'user_id' => auth()->user()->id])->first();
            if($cart){
                $cart->delete();
            }
        }

        return redirect()->route('index');

    }
    public function createPayment($data) {

        $order = new Payment();
        $order['paymentid'] = $data->InvoiceId;
        $order['trackid'] = $data->focusTransaction->TransactionId;
        $order['amount'] = $data->InvoiceValue;
        $order['payment_type'] = $data->focusTransaction->PaymentGateway;
        $order['status'] = $data->InvoiceStatus;
        $order['created_at'] = date('Y-m-d h:i:s');
        $order['updated_at'] = date('Y-m-d h:i:s');
        $order->save();

        return $order;

    }

}
