<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lecture;
use App\Models\LectureUser;
use App\Traits\GlobalTrait;
use Illuminate\Http\Request;
use MacsiDigital\Zoom\Facades\Zoom;
use App\Http\Requests\LectureRequest;
use App\Services\LectureServices\DeleteLectureService;
use App\Services\LectureServices\StoreLectureService;
use App\Services\LectureServices\UpdateLectureService;

class LectureController extends Controller
{
	use GlobalTrait;

	public function index($chapter_id)
	{
		$chapter = Chapter::find($chapter_id);
		$course = Course::find($chapter->course_id);
		$lectures = Lecture::where("chapter_id", $chapter_id)->orderBy('id', 'ASC')->get();
		foreach ($lectures as $lecture) {
			if ($lecture->type_video === "zoom") {
				$lecture->videoID = "<a href='$lecture->start_url' target='_blank'>$lecture->videoID</a>";
				$lecture->created_at = $lecture->start_time;
				/*$end = Carbon::create($lecture->start_time)->addMinutes($lecture->duration);
				$nowTime = Carbon::now();
				if ($nowTime->gt($end)) {
					$zoom = Zoom::meeting()->find($lecture->videoID);
					if ($zoom != null) {
						$zoom->delete();
					}
					$lecture->deleteLectureUser($lecture->id);
					$lecture->delete();
				} else {

					//	dd(gmdate('H:i:s', $nowTime->diffInSeconds($end)));
					//	dd(date('H:i', strtotime($lecture->start_time) + 60 * 60));
				}*/
			}
		}

		return view("lectures.index", compact("chapter", "course", "lectures"));
	}


	public function create($chapter_id)
	{
//		dd(date('Y-m-d h:i:00'));
		$chapter = Chapter::find($chapter_id);
		$course = Course::find($chapter->course_id);
		return view("lectures.create", compact("chapter", "course"));
	}


	public function store($chapter_id, LectureRequest $request)
	{
		$this->authorize('create_lectures');
		$chapter = Chapter::find($chapter_id);
		return (new StoreLectureService($chapter))->store($request);
	}


	public function show($chapter_id, $id)
	{
		$lecture = Lecture::find($id);
		$chapter = Chapter::find($chapter_id);
		$course = $chapter->course;
		$attachments = $lecture->attachments;

		return view("lectures.show", compact("course", "chapter", "lecture", "attachments"));
	}


	public function edit($chapter_id, $id)
	{
		$this->authorize('edit_lectures');
		$lecture = Lecture::find($id);
//		dd($lecture->start_time);
		$chapter = $lecture->chapter;
		$course = $chapter->course;
		return view('lectures.edit', compact('course', "chapter", "lecture"));
	}

	public function update($chapter_id, $id, LectureRequest $request)
	{
		$this->authorize('edit_lectures');
		$lecture = Lecture::findorFail($id);
		return (new UpdateLectureService($lecture))->update($request);
	}


	public function destroy($chapter_id, $id)
	{
		$this->authorize('remove_lectures');
		$lecture = Lecture::findorFail($id);
		(new DeleteLectureService($lecture))->delete();
		return redirect()->route('lectures', $chapter_id)->with('success', trans("global.success_delete"));
	}

	public function destroyAll($chapter_id, Request $request)
	{
		$this->authorize('remove_lectures');
		$lectures = Lecture::whereIn('id', json_decode($request->ids))->get();
		foreach ($lectures as $lecture) {
			(new DeleteLectureService($lecture))->delete();
		}
		return redirect()->route('lectures', $chapter_id)->with('success', trans("global.success_delete_all"));
	}
}
