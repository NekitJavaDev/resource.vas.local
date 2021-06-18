<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Meter;
use App\Models\Type;
use App\Constants\Constants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuildingsController extends Controller
{
    public function show(Building $building)
    {
        return view('pages.building', compact('building'));
    }

    public function list()
    {
        $building = Building::all()->each(function ($building) {
            $building->meters_arr = Meter::whereBuildingId($building->id)
                ->select('meters.*', 'types.name as typeName')
                ->leftJoin('types', 'meters.type_id', '=', 'types.id')
                ->get();
        });

        return $building;
    }

    public function listNightWorkedWater()
    {
        $building = Building::all()->each(function ($building) {
            $building->meters_arr = Meter::whereBuildingId($building->id)
                ->select('meters.*', 'types.name as typeName')
                ->leftJoin('types', 'meters.type_id', '=', 'types.id')
                ->where('type_id', 2)
                ->where('worked', 1)
                ->get();
        });

        return $building;
    }

    public function listBolidsByBuildings()
    {
        $building = Building::all()->each(function ($building) {
            $building->meters_arr = Meter::whereBuildingId($building->id)
                ->select('meters.*', 'types.name as typeName')
                ->leftJoin('types', 'meters.type_id', '=', 'types.id')
                ->where('type_id', 2)
                ->where('worked', 1)
                ->get();
        });

        return $building;
    }

    private function getMetersByStatusAndBuildingId($buildingId, $meterStatus, $meterTypeId){
        $const = new Constants;

        $meters = Meter::whereBuildingId($buildingId)
            ->select('meters.*', 'types.name as typeName')
            ->leftJoin('types', 'meters.type_id', '=', 'types.id');
        
        if ($meterTypeId != null) {
            $meters = $meters->where('type_id', '=', $meterTypeId);
        } 

        switch ($meterStatus) { 
            case $const->METER_STATUS['worked']:
                return $meters
                    ->where('worked', '=', 1)
                    ->get();
            case $const->METER_STATUS['active']:
                return $meters
                    ->where('active', '=', 1)
                    ->get();
            case $const->METER_STATUS['notActive']:
                return $meters
                    ->where('active', '=', 0)
                    ->get();
            case $const->METER_STATUS['errorConnect']:
                return $meters
                    ->whereNull('server_ip')
                    ->get();
            default:
                return $meters
                    ->get();
        }
    }

    public function listWithFilters(Request $request)
    {
        $meterStatus = $request->meterStatus;
        $meterTypeId = $request->meterTypeId;
        $sectorId = $request->sectorId;

        if ($meterStatus == null && $meterTypeId  == null && $sectorId == null) {
            return $this->list();
        }

        $buildings = [];

        if ($sectorId != null) {
            $buildings = Building::where('sector_id', '=', $sectorId)->get();
        } else {
            $buildings = Building::all();
        }

        return $buildings
            ->each(function ($building) use ($meterStatus, $meterTypeId) {
                $building->meters_arr = $this->getMetersByStatusAndBuildingId($building->id, $meterStatus, $meterTypeId);
        });
    }
}
