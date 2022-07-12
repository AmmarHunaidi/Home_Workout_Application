<?php

namespace App\Http\Controllers;

use App\Models\HealthRecord;
use App\Models\Disease;
use App\Models\RecordDisease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\GeneralTrait;

class HealthRecordsController extends Controller
{
    use GeneralTrait;
    public function index(Request $request)
    {
        try {
            $record = $request->user()->healthRecords->last();
            if ($record) {
                $data = [];
                $recordArr = [
                    "record_id" => $record->id,
                    "record_desc" => $record->description,
                ];
                $dsieasesArr = [];
                $diseases = $record->diseases;
                if ($diseases)
                    foreach ($diseases as $dis) {
                        $dsieasesArr[] = [
                            'dis_id' => Disease::where('id', $dis->disease_id)->first()->id,
                            'dis_name' => Disease::where('id', $dis->disease_id)->first()->name,
                        ];
                    }
                $data = ["record" => $recordArr, "dis" => $dsieasesArr];
                return $this->success('ok', $data);
            }
            return $this->success();
        } catch (\Exception $e) {
            return $this->fail(__('messages.somthing went wrong'), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->only(['dis', 'desc']), [
                'dis' => ['array', 'nullable'],
                'dis.*' => ['integer', 'exists:diseases,id', 'nullable'],
                'desc' => ['string', 'max:5000', 'nullable'],
            ]);
            if ($validator->fails())
                return $this->fail($validator->errors()->first(), 400);
            // return is_integer((int)$request->dis[1]);
            $user = $request->user();
            $record = HealthRecord::create([
                'user_id' => $user->id,
                'description' => $request->desc,
            ]);
            foreach ($request->dis as $disease) {
                RecordDisease::create([
                    'record_id' => $record->id,
                    'disease_id' => (int)$disease,
                ]);
            }
            return $this->success();
        } catch (\Exception $e) {
            return $this->fail(__('messages.somthing went wrong'), 500);
        }
    }


    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->only(['dis', 'desc']), [
                'dis' => ['array', 'nullable'],
                'dis.*' => ['integer', 'exists:diseases,id', 'nullable'],
                'desc' => ['string', 'max:5000', 'nullable'],
            ]);
            if ($validator->fails())
                return $this->fail($validator->errors()->first(), 400);
            $user = $request->user();
            $record = HealthRecord::updateOrCreate([
                'user_id' => $user->id
            ], [
                'description' => $request->desc
            ]);
            RecordDisease::query()->where('record_id', $record->id)->delete();
            $data = collect($request->dis)->filter();
            if (($request->dis) && !($data->isEmpty())) {
                foreach ($data as $disease) {
                    RecordDisease::create([
                        'record_id' => $record->id,
                        'disease_id' => (int)$disease,
                    ]);
                }
            }
            return $this->success();
        } catch (\Exception $e) {
            return $this->fail(__('messages.somthing went wrong'), 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $request->user()->healthRecords()->delete();
            return $this->success();
        } catch (\Exception $e) {
            return $this->fail(__('messages.somthing went wrong'), 500);
        }
    }
}
