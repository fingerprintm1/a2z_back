<?php

namespace App\Services\LectureServices;
use App\Models\Lecture;
use App\Traits\GlobalTrait;



class StoreLectureService
{
    use GlobalTrait;
    public $chapter;
    function __construct($chapter)
    {
        $this->chapter = $chapter;
    }

    public function zoom($data)
    {
        $meeting = $this->createMeeting(request());
        $data["title"] = $meeting->topic;
        $data["videoID"] = $meeting->id;
        $data["duration"] = $meeting->duration;
        $data["start_time"] = request()->start_time;
        $data["start_url"] = $meeting->start_url;
        $data["join_url"] = $meeting->join_url;
        return $data;
    }

    public function server($data)
    {
        $collectionID = $this->chapter->course->collectionID;
        $upload = $this->uploadVideo(request()->title, $collectionID, request()->file("video"));
        $data["videoID"] = $upload["guid"];
        $data["views"] = $upload["views"];
        return $data;
    }
    protected function setData($data)
    {
        if (request()->type_video === "zoom") {
            $data = $this->zoom($data);
        } else if (request()->type_video === "server") {
            $data = $this->server($data);
        }
        $data["chapter_id"] = $this->chapter->id;
        $data["course_id"] = $this->chapter->course_id;
        return $data;
    }


    protected function createLecture($data)
    {
        $lecture = Lecture::create($data);
        return $lecture;
    }

    public function store($request)
    {
        try {
            $data = $request->all();
            $data = $this->setData($data);
            $lecture = $this->createLecture($data);
            $chapter = $lecture->chapter;
            $description = " تم إضافة محاضرة " . $request->name_ar . " الي قسم " . $chapter->name_ar . " الي دورة " . $chapter->course->name_ar;
            transaction("lectures", $description);
            return redirect()->route('lectures', $this->chapter->id)->with('success', trans("global.success_create"));
        } catch (\Exception $e) {
            return redirect()->route('lectures', $this->chapter->id)->with('error', $e->getMessage());
        }
    }
}
