<?php namespace App\Http\Controllers\Settings;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Mail;
use Auth;
use App\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VouchersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $vouchers = Auth::User()->vouchers;
        return view('settings.vouchers')->with(compact('vouchers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        // NOT IN USE
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        // NOT IN USE
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        // NOT IN USE
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        // NOT IN USE
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        // $id in this context is voucher number.

        $this->validate($request, ['email' => 'required|email']);

        Mail::send('emails.voucher', ['number' => $id], function ($mail) use ($request) {
            $mail->from('support@stratostack.com', 'StratoSTACK');
            $mail->to($request->email)->subject('Voucher');
        });

        $voucher = Voucher::whereNumber($id)->first();
        $voucher->recipient_email = $request->email;
        $voucher->save();

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        // NOT IN USE
    }

    public function redeem(Request $request)
    {
        $this->validate($request, [
            'number' => 'required|alpha_num|exists:vouchers,number,redeemed_by,NULL'
        ]);

        // Redeem voucher $id (number) for the posting User.
        $voucher = Voucher::whereNumber($request['number'])->first();

        // Grab user and add to their balance.
        $user = Auth::User();

        // Check to verify that this isn't a user redeeming their own vouchers.
        if ($voucher->user_id == $user->id) {
            flash()->error('Unable to redeem voucher issued to yourself.');
            return redirect()->route('settings.billing');
        }

        $user->credit += $voucher->amount;
        $user->save();

        // Mark the voucher as claimed.
        $voucher->redeemed_by = $user->id;
        $voucher->redeemed_at = Carbon::now();
        $voucher->save();

        // Return to billing page wish a success message
        flash()->success('Successfully redeemed voucher.');
        return redirect()->route('settings.billing');
    }
}
