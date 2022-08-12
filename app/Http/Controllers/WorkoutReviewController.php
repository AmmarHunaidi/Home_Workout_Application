<?php

namespace App\Http\Controllers;

use App\Models\WorkoutReview;
use App\Http\Requests\StoreWorkoutReviewRequest;
use App\Http\Requests\UpdateWorkoutReviewRequest;
use App\Models\Workout;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkoutReviewController extends Controller
{
    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        try {
        } catch (Exception $exception) {
            return $this->fail();
        }
        $fields = Validator::make($request->only('workout_id', 'description', 'stars'), [
            'workout_id' => 'required|integer'
        ]);
        if ($fields->fails()) {
            return $this->fail($fields->errors()->first(), 400);
        }
        $fields = $fields->safe()->all();
        $workout = Workout::find($fields['workout_id']);
        $reviews = $workout->review;
        return $this->success("Success", $reviews, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $fields = Validator::make($request->only('workout_id', 'description', 'stars'), [
            'workout_id' => 'required|integer',
            'description' => 'required|string',
            'stars' => 'required|integer:1,2,3,4,5'
        ]);
        if ($fields->fails()) {
            return $this->fail($fields->errors()->first(), 400);
        }
        $fields = $fields->safe()->all();
        $fields['user_id'] = $request->user()->id;
        $review = WorkoutReview::create($fields);
        $workout = Workout::find($fields['workout_id']);
        $review_count = $workout->reviews->count();
        $review_rating = (float)(($workout->review_count * ($review_count - 1)) + $fields['stars']) / ($review_count);
        $workout->review_count = $review_rating;
        $workout->update();
        return $this->success("Done", $workout, 200);
    }
}
