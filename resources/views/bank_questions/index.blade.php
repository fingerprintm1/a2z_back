@extends('layouts/layoutMaster')
@section('title', trans('global.question'))
@section('vendor-style')
	<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
	@can("export")
		<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
	@endcan
	<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection
@section('vendor-script')
	<script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
	<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
	<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
	<script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
	<script src="{{ asset('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
@endsection

@section('content')
	@php
		$links = [
			"start" => trans("global.questions"),
			"/" => trans("global.dashboard"),
			"/bank_questions/categories" => $category->name,
			"end" => trans("global.all_question"),
	]
	@endphp
	@include("layouts.breadcrumbs")
	<div class="card">
		<div class="card-header border-bottom">
			<div class="d-flex align-items-center justify-content-between">
				<h5 class="card-title mb-3">{{ trans('global.search_filed') }}</h5>
				<div class="d-flex align-items-center">
					<div class="demo-inline-spacing  me-3">
						<div class="btn-group mt-0">
							<button id="button-actions" type="button" class="btn btn-label-secondary btn-secondary dropdown-toggle disabled" data-bs-toggle="dropdown" aria-expanded="false">{{ trans('global.action_select') }}</button>
							<ul class="dropdown-menu">
								<li>
									<form action="{{ route('bank_questions_delete_all', $bank_category_id) }}" method="POST" id="form-delete-all" class="position-relative dropdown-item cursor-pointer" tabindex="0" aria-controls="DataTables_Table_0">
										<i class="ti ti-trash ti-sm me-2"></i> @csrf
										<input type="text" name="ids" value="" hidden id="ids-delete">{{ trans('global.delete') }}
									</form>
								</li>
								<li>
									<a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-printer me-2"></i>{{ trans('global.print') }}</a>
								</li>
							</ul>
						</div>
					</div>
					<label for="exel" class="btn btn-secondary btn-primary position-relative me-3">
						<input id="exel" type="file" name="file" hidden="hidden" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
						<i class="fa-solid fa-file-arrow-up me-0 me-sm-1 ti-xs"></i>
						<span class="d-none d-sm-inline-block">{{ trans('global.import_exel') }}</span>
					</label>
					<a class="btn btn-secondary btn-primary position-relative" tabindex="0" aria-controls="DataTables_Table_0" href="{{route('bank_question_create', $bank_category_id)}}">
						<span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">{{trans("global.question_create")}}</span></span>
					</a>

				</div>
			</div>
			<div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
				<div class="col-md-4 user_role"></div>
				<div class="col-md-4 user_plan"></div>
				<div class="col-md-4 user_status"></div>
			</div>
		</div>
		<div class="card">
			<div class="card-datatable text-nowrap table-responsive">
				<table class="datatables-ajax table ">
					<thead>
					<tr>
						<th class="text-start check-all"><label for="checkbox_delete"><input type="checkbox" id="checkbox_delete" class="form-check-input me-3">@lang("global.number")</label></th>
						<th class="text-start">{{ trans('global.question') }}</th>
						<th class="text-start">{{ trans('global.created_at') }}</th>
						<th class="text-start">{{ trans('global.actions') }}</th>
					</tr>
					</thead>
					<tbody id="all_questions">
					@foreach ($questions as $question)
						<tr>
							<td class="text-start"><input type="checkbox" data-id="{{$question->id}}" name="deleteAll[]" class="delete-all dt-checkboxes form-check-input me-3">{{$question->id}}</td>
							{{--							d-flex align-items-center justify-content-center--}}
							<td class="text-start ">
								@if($question->type === "text")
									<span class='text-truncate  fw-bold badge bg-label-success w-100 overflow-hidden m-0' style="height: 100px">@php echo $question["question_" . app()->getLocale()] @endphp</span>
								@elseif ($question->type === "image")
									<a href="{{asset("images/$question->file")}}" target="_blank" class="avatar avatar-xl d-block ">
										<img src="{{asset("images/$question->file")}}" alt="Avatar" class="rounded-circle object-cover">
									</a>
								@elseif ($question->type === "video")
									<a href="{{asset("images/$question->file")}}" target="_blank" class="avatar avatar-xl d-block ">@lang("global.video")</a>
								@elseif ($question->type === "audio")
									<audio controls class="w-100">
										<source src="{{asset("images/$question->file")}}" type="audio/mpeg">
										Your browser does not support the audio tag.
									</audio>
								@endif
							</td>
							<td class="text-start">{{DATE($question->created_at)}}</td>
							<td class="text-start">
								<div class="d-flex align-items-center">
									<a href="{{route('bank_question_edit', [$bank_category_id, $question->id])}}" class="text-body ms-1"><i class="ti ti-edit ti-sm me-2"></i></a>
									<a href="{{route('bank_question_delete', [$bank_category_id, $question->id])}}" onclick="return confirm('هل انت متأكد')" class="text-body "><i class="ti ti-trash ti-sm mx-2"></i></a>
								</div>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection
@section('my-script')
	<script>
    $(".datatables-ajax").DataTable({
      displayLength: 100
    });
    $("body").on("change", "#exel", function() {
      const file = this.files[0];
      if (!file) return;
      let formData = new FormData();
      formData.append("file", file);
      formData.append("_token", "{{ csrf_token() }}");
      $.ajax({
        type: "post",
        processData: false,
        contentType: false,
        cache: false,
        enctype: "multipart/form-data",
        url: "{{ route('questions_import', $bank_category_id) }}",
        data: formData,
        success: function(data) {
          $("#all_questions").html(data);
          showMessage("success", "تم اضافة الأسئلة من Exel");
        },
        error: function({ responseJSON }) {
          showMessage("error", responseJSON.message);
        }
      });
    });

    function buttonActionDisable(checked) {
      if (checked) {
        $("#button-actions").removeClass("disabled btn-label-secondary btn-secondary");
        $("#button-actions").addClass("btn-success");
      } else {
        $("#button-actions").addClass("disabled btn-label-secondary btn-secondary");
        $("#button-actions").removeClass("btn-success");
      }
    }

    $(function() {
      $(".check-all input").on("click", function() {
        let valueIds = [];
        let inputIdsValue = $("#ids-delete");
        if ($(this)[0].checked) {
          $(".delete-all").each(function() {
            $(this).prop("checked", true);
            valueIds.push($(this).data("id"));
            inputIdsValue.val(`[${valueIds}]`);
          });
          buttonActionDisable(true);
        } else {
          $(".delete-all").each(function() {
            $(this).prop("checked", false);
          });
          buttonActionDisable(false);
          valueIds = [];
          inputIdsValue.val(`[${valueIds}]`);
        }
      });
      $("#form-delete-all").on("click", function() {
        if (confirm("Are you sure?")) {
          $("#form-delete-all").submit();
        }
        return false;
      });
      setTimeout(() => {
        let inputIdsValue = $("#ids-delete");
        let valueIds = [];
        $(".delete-all").on("change", function() {
          if ($(this)[0].checked) {
            buttonActionDisable(true);
            valueIds.push($(this).data("id"));
            inputIdsValue.val(`[${valueIds}]`);
          } else {
            buttonActionDisable(false);
            valueIds = valueIds.filter((ele) => ele != $(this).data("id"));
            inputIdsValue.val(`[${valueIds}]`);
          }
        });
      }, 500);
    });
	</script>
@endsection
