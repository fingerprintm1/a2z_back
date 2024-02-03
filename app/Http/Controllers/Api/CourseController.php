<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CommentCourseRequest;
use App\Http\Requests\Api\UserAnswersRequest;
use App\Http\Requests\Api\UserExamRequest;
use App\Http\Requests\Api\UserQuestionsRequest;
use App\Models\Comment;
use App\Models\Course;
use App\Models\DetailExam;
use App\Models\Lecture;
use App\Models\Question;
use App\Models\Section;
use App\Models\UserAnswer;
use App\Services\Api\GetCourseService;
use App\Traits\GlobalTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

define("PAGINATE", 2);

class CourseController extends Controller
{
	use GlobalTrait;

	public function searchCourses(Request $request)
	{
		try {
			$courses = Course::where("status", 1)->where("name", "LIKE", "%$request->search%")->get();
			return returnData($courses, "");
		} catch (\Exception $ex) {
			return returnError($ex->getCode(), $ex->getMessage());
		}
	}

	public function filterCourses(Request $request)
	{
		try {
			if ($request->sections_id == 0) {
				$sections = Section::with("courses")->orderBy("id", "ASC")->get();
				return returnData(["sections" => $sections, "courses" => []]);
			}
			$sections = Section::where("id", $request->sections_id)->orderBy("id", "ASC")->get();
			if (!$sections) {
				$courses = Course::with("section", "teacher", "subject", "currency", "comments")->where("status", 1);
				if (!empty($request->sections_id)) {
					$courses = $courses->where("section_id", $request->sections_id);
				}
				return returnData(["courses" => $courses->get(), "sections" => []]);
			}
			return returnData(["sections" => $sections, "courses" => []]);
		} catch (\Exception $ex) {
			return returnError($ex->getCode(), $ex->getMessage());
		}
	}


	public function getCourse($id)
	{
		try {
			$data = (new GetCourseService($id))->course();
			if (!$data) {
				return returnData($data, "Course Not Found");
			}
			return returnData($data);
		} catch (\Exception $ex) {
			return returnError($ex->getCode(), $ex->getMessage());
		}
	}

	public function commentCourse(CommentCourseRequest $request)
	{
		try {
			if (empty(auth()->user())) {
				return returnError(401, trans("user_not_found"));
			}
			$validator = Validator::make($request->all(), $request->rules());
			if ($validator->fails()) {
				return returnError(422, json_decode($validator->errors()->toJson()));
			}
			Comment::create([
				"comment" => $request->comment,
				"course_id" => $request->course_id,
				"user_id" => auth()->user()->id,
				"status" => 0,
			]);
			return returnData("", trans("global.success_send_comment"));
		} catch (\Exception $ex) {
			return returnError($ex->getCode(), $ex->getMessage());
		}
	}


	public function getQuestions(UserQuestionsRequest $request)
	{
		try {
			$user = auth()->guard('api')->user();
			if (empty($user)) {
				return returnError(401, trans("user_not_found"));
			}
			$validator = Validator::make($request->all(), $request->rules());
			if ($validator->fails()) {
				return returnError(422, json_decode($validator->errors()->toJson()));
			}
			$course = Course::with("section")->find($request->course_id);
			if (empty($course)) {
				return returnError(404, trans("global.course_not_found"));
			}
			$lecture = Lecture::find($request->lecture_id);
			$count_questions = $lecture->count_questions;
			$questions = Question::with("answersRandom")->where("course_id", $request->course_id)
				->where("lecture_id", $request->lecture_id)->where("related", $request->related)->inRandomOrder()
				->when($request->related === "exams", function ($query) use ($count_questions) {
					return $query->limit($count_questions);
				})->get();
			$latestLecture = Lecture::where("course_id", $course->id)->latest("id")->first();
			$available_re_exam = DetailExam::whereDate("created_at", Carbon::today())->where("course_id", $request->course_id)->where("lecture_id", $request->lecture_id)->where("user_id", $user->id)->where("score", "<=", (int)env("SCORE"))->count() >= $lecture->re_exam_count;
			$lecture->latest = 0;
			if ($lecture->id === $latestLecture->id) {
				$lecture->latest = 1;
			}

			return returnData(["available_re_exam" => $available_re_exam, "course" => $course, "lecture" => $lecture, "questions" => $questions]);
		} catch (\Exception $ex) {
			return returnError($ex->getCode(), $ex->getMessage());
		}
	}


	public function userExams(UserExamRequest $request)
	{
		try {
			$user = auth()->guard('api')->user();
			if ($user === null) {
				return returnError(401, trans("user_not_found"));
			}
			$validator = Validator::make($request->all(), $request->rules());
			if ($validator->fails()) {
				return returnError(422, json_decode($validator->errors()->toJson()));
			}
			$course = Course::with("subject")->find($request->course_id);
			$user = \App\Models\User::find(auth()->user()->id);
			$detailExams = DetailExam::where("course_id", $course->id)->where("user_id", $user->id)->get();
			return returnData(["detailExams" => $detailExams, "course" => $course, "user" => $user]);
		} catch (\Exception $ex) {
			return returnError($ex->getCode(), $ex->getMessage());
		}
	}

	public function userShowAnswers(UserAnswersRequest $request)
	{
		try {
			if (auth()->guard('api')->user() === null) {
				return returnError(401, trans("user_not_found"));
			}
			$validator = Validator::make($request->all(), $request->rules());
			if ($validator->fails()) {
				return returnError(422, json_decode($validator->errors()->toJson()));
			}
			$course = Course::with("subject")->find($request->course_id);
			$user = \App\Models\User::find(auth()->user()->id);
			$userAnswers = UserAnswer::where("detail_exam_id", $request->exam_id)->get();
			$answersIDS = $userAnswers->pluck("answer_id")->toArray();
			$questions = Question::with("answers")->whereIn("id", $userAnswers->pluck("question_id"))->get();
			return returnData(["answersIDS" => $answersIDS, "questions" => $questions, "course" => $course, "user" => $user]);
		} catch (\Exception $ex) {
			return returnError($ex->getCode(), $ex->getMessage());
		}
	}


}
