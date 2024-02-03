"use strict";
let image_path =
  document.documentElement.getAttribute("data-base-url") + "/images/";
// Datatable (jquery)
$(function() {
  let borderColor, bodyBg, headingColor;

  if (isDarkStyle) {
    borderColor = config.colors_dark.borderColor;
    bodyBg = config.colors_dark.bodyBg;
    headingColor = config.colors_dark.headingColor;
  } else {
    borderColor = config.colors.borderColor;
    bodyBg = config.colors.bodyBg;
    headingColor = config.colors.headingColor;
  }

  // Variable declaration for table
  var dt_user_table = $(".datatables-ajax"),
    select2 = $(".select2"),
    userView = baseUrl + "app/user/view/account",
    statusObj = {
      0: { title: "Pending", class: "bg-label-warning" },
      1: { title: "Active", class: "bg-label-success" }
      // 3: { title: 'Inactive', class: 'bg-label-secondary' }
    };

  if (select2.length) {
    var $this = select2;
    $this.wrap("<div class=\"position-relative\"></div>").select2({
      placeholder: "Select Country",
      dropdownParent: $this.parent()
    });
  }

  // Users datatable
  let lang = document.documentElement.dir == "rtl" ? "ar" : "en";
  let domain = location.host.slice(0, 9) == "127.0.0.1";

  if (dt_user_table.length) {
    var dt_user = dt_user_table.DataTable({
      ajax: "/questions/getQuestionsApi", // JSON file to add data
      columns: [
        // columns according to JSON
        { data: "id" },
        { data: `question_${lang}` },
        { data: `course` },
        { data: `created_at` },
        { data: "action" },
        { data: "" }
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          orderable: false,
          searchable: false,
          className: "check-all",
          responsivePriority: 3,
          render: function(data, type, full) {
            var $id = full["id"];
            return `<input type="checkbox" data-id="${$id}" name="deleteAll[]" class="delete-all dt-checkboxes form-check-input">`;
          },
          checkboxes: {
            selectAllRender: "<input type=\"checkbox\" class=\"form-check-input\">"
          }
        },
        {
          // product
          targets: 1,
          render: function(data, type, full, meta) {
            var $id = full["id"];

            return "<span class=\"badge\">" + $id + "</span>";
          }
        },
        {
          // product name
          targets: 2,
          responsivePriority: 2,
          render: function(data, type, full, meta) {
            var question = full[`question_${lang}`];
            return (`<span class="text-truncate d-flex align-items-center"> ${question}</span>`);
          }
        },

        {
          // product name
          targets: 3,
          responsivePriority: 3,
          render: function(data, type, full, meta) {
            var course = full[`course`];
            return `<a href="/course/${course.id}" class="text-truncate d-flex align-items-center">${course.subject[`name_${lang}`]}</a>`;

          }
        },
        {
          // product date created
          targets: 4,
          render: function(data, type, full, meta) {
            var created_at = new Date(full["created_at"]).toISOString();
            if (lang == "ar") {
              created_at = created_at.slice(11, 19) + " " + created_at.slice(0, 10);
            } else {
              created_at = created_at.slice(0, 10) + " " + created_at.slice(11, 19);
            }
            return `<span class="text-truncate d-flex align-items-center">${created_at}</span>`;
          }
        },
        {
          // Actions
          targets: -1,
          title: lang == "ar" ? "الإجراءات" : "Actions",
          searchable: false,
          orderable: false,
          render: function(data, type, full, meta) {
            var course = full[`course`];
            let parent = `
              <div class="d-flex align-items-center">
                <a href="/questions/course/${course.id}/edit/${full.id}" class="text-body"><i class="ti ti-edit ti-sm me-2"></i></a>
                <a onclick="return confirm('Are you sure?')" href="/questions/course/${course.id}/delete/${full.id}" class="text-body "><i class="ti ti-trash ti-sm mx-2"></i></a>
              </div>
            `;
            return parent;
          }
        }
      ],
      order: [
        [1, "asc"]
      ],
      dom: "<\"row me-2\"" + "<\"col-md-2\"<\"me-3\"l>>" + "<\"col-md-10\"<\"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0\"fB>>" + ">t" + "<\"row mx-2\"" + "<\"col-sm-12 col-md-6\"i>" + "<\"col-sm-12 col-md-6\"p>" + ">",
      language: {
        sLengthMenu: "_MENU_",
        search: "",
        searchPlaceholder: "بحث.."
      },
      buttons: [
        {
          extend: "collection",
          className: "btn btn-label-secondary dropdown-toggle mx-3",
          text: "<i class=\"ti ti-screen-share me-1 ti-xs\"></i>Export",
          buttons: [
            {
              extend: "print",
              text: "<i class=\"ti ti-printer me-2\" ></i>Print",
              className: "dropdown-item",
              exportOptions: {
                columns: [0, 1, 2, 3],
                // prevent photo to be print
                format: {
                  body: function(inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = "";
                    $.each(el, function(index, item) {
                      if (item.classList !== undefined && item.classList.contains("user-name")) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              },
              customize: function(win) {
                //customize print view for dark
                $(win.document.body).css("color", headingColor).css("border-color", borderColor).css("background-color", bodyBg);
                $(win.document.body).find("table").addClass("compact").css("color", "inherit").css("border-color", "inherit").css("background-color", "inherit");
              }
            },
            {
              extend: "excel",
              text: "<i class=\"ti ti-file-spreadsheet me-2\"></i>Excel",
              className: "dropdown-item",
              exportOptions: {
                columns: [0, 1, 2, 3],
                // prevent photo to be display
                format: {
                  body: function(inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = "";
                    $.each(el, function(index, item) {
                      if (item.classList !== undefined && item.classList.contains("user-name")) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            },
            {
              extend: "pdf",
              text: "<i class=\"ti ti-file-code-2 me-2\"></i>Pdf",
              className: "dropdown-item",
              exportOptions: {
                columns: [0, 1, 2, 3],
                // prevent photo to be display
                format: {
                  body: function(inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = "";
                    $.each(el, function(index, item) {
                      if (item.classList !== undefined && item.classList.contains("user-name")) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            },
            {
              extend: "copy",
              text: "<i class=\"ti ti-copy me-2\" ></i>Copy",
              className: "dropdown-item",
              exportOptions: {
                columns: [0, 1, 2, 3],
                // prevent photo to be display
                format: {
                  body: function(inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = "";
                    $.each(el, function(index, item) {
                      if (item.classList !== undefined && item.classList.contains("user-name")) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            }
          ]
        }
      ]
    });
  }

  // Delete Record
  $(".datatables-ajax tbody").on("click", ".delete-record", function() {
    dt_user.row($(this).parents("tr")).remove().draw();
  });

  // Filter form control to default size
  // ? setTimeout used for multilingual table initialization
  setTimeout(() => {
    $(".dataTables_filter .form-control").removeClass("form-control-sm");
    $(".dataTables_length .form-select").removeClass("form-select-sm");
  }, 300);
});

// Validation & Phone mask
(function() {
  const phoneMaskList = document.querySelectorAll(".phone-mask"),
    addNewUserForm = document.getElementById("addNewUserForm");

  // Phone Number
  if (phoneMaskList) {
    phoneMaskList.forEach(function(phoneMask) {
      new Cleave(phoneMask, {
        phone: true,
        phoneRegionCode: "US"
      });
    });
  }
  // Add New User Form Validation
  /*const fv = FormValidation.formValidation(addNewUserForm, {
    fields: {
      userFullname: {
        validators: {
          notEmpty: {
            message: 'Please enter fullname '
          }
        }
      },
      userEmail: {
        validators: {
          notEmpty: {
            message: 'Please enter your email'
          },
          emailAddress: {
            message: 'The value is not a valid email address'
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        // Use this for enabling/changing valid/invalid class
        eleValidClass: '',
        rowSelector: function (field, ele) {
          // field is the field name & ele is the field element
          return '.mb-3';
        }
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      // Submit the form when all fields are valid
      // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  });*/
})();
