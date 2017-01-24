<?php namespace App\Http\Controllers\Settings;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\TaxCloudRepository;
use Illuminate\Http\Request;
use TaxCloud\Exceptions\VerifyAddressException;
use Auth;

class CreditCardController extends Controller
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
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $curYear = date('Y');
        $validYears = range($curYear, $curYear + 10);

        return view('settings.creditcard.create')->with(compact('validYears'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request, PaymentRepositoryInterface $payments, TaxCloudRepository $taxCloud)
    {
//        $taxCloud = app('TaxCloudRepo');

        list ($fname, $lname) = explode(' ', $request->input('name'), 2); // This will exception upon a single name.

        $address = $taxCloud->createAddress($request->address, '', $request->city, $request->state, $request->zipcode);

        try {
            $verifiedAddress = $taxCloud->verifyAddress($address);
        } catch (VerifyAddressException $e) {
            return back()->withErrors('Unable to verify address.')->withInput();
        }

        if ($verifiedAddress != null) {
        // We have a USPS verified address
            $billAddress = $verifiedAddress->getAddress1();
            $billCity = $verifiedAddress->getCity();
            $billState = $verifiedAddress->getState();
            $billZip = $verifiedAddress->getZip5() . '-' . $verifiedAddress->getZip4();
        } else {
            $billAddress = $request->address;
            $billCity = $request->city;
            $billState = $request->state;
            $billZip = $request->zipcode;
        }

        $payments->newCard(['cardNumber' => $request->input('number'),
                            'cardExp'    => $request->input('expYear') . '-' . $request->input('expMonth'),
                            'CVV'        => $request->input('CVV'),
                            'billTo'     => ['firstName' => $fname,
                                             'lastName'  => $lname,
                                             'address'   => $billAddress,
                                             'city'      => $billCity,
                                             'state'     => $billState,
                                             'zipcode'   => $billZip,
                                             'country'   => $request->country]
        ], Auth::User()->id);

        return redirect()->route('settings.billing');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit(PaymentRepositoryInterface $payments, $id)
    {
        //
//        $card = $payments->get($id);

//        return response()->json($card);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //

//        return redirect()->route('settings.billing');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(PaymentRepositoryInterface $payments, $id)
    {
        //
        $payments->deleteCard($id, Auth::User()->id);

        return 1;
    }
}
