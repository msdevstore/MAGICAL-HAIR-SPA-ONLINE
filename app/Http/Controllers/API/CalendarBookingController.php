<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Libraries\AllSettingsFormat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Services;
use App\Models\Booking;
use App\Models\Setting;

class CalendarBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('calendar.index');
    }

    public function getServicesBooks(Request $request) 
    {
        $date = $request->date;
        $business_type = Setting::getSettingValue('business_type');
        $services = Services::with('bookings.user')
            ->where('service_starting_date', '<=', $date)
            ->where('service_ending_date', '>=', $date)
            ->where('services.business_type', '=', $business_type->setting_value)
            ->get();

        $allSet = new AllSettingsFormat();

        foreach ($services as $service) {
            if (count($service->bookings)) {
                $service->bookings->map(function ($item) use ($allSet) {
                    $item->booking_time = $allSet->timeFormat(unserialize($item->booking_time));
                });
            }
        }
        return response()->json($services);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
