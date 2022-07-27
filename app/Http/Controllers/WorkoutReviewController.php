<?php

namespace App\Http\Controllers;

use App\Models\WorkoutReview;
use App\Http\Requests\StoreWorkoutReviewRequest;
use App\Http\Requests\UpdateWorkoutReviewRequest;

class WorkoutReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreWorkoutReviewRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWorkoutReviewRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WorkoutReview  $workoutReview
     * @return \Illuminate\Http\Response
     */
    public function show(WorkoutReview $workoutReview)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WorkoutReview  $workoutReview
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkoutReview $workoutReview)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateWorkoutReviewRequest  $request
     * @param  \App\Models\WorkoutReview  $workoutReview
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWorkoutReviewRequest $request, WorkoutReview $workoutReview)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WorkoutReview  $workoutReview
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkoutReview $workoutReview)
    {
        //
    }
}
