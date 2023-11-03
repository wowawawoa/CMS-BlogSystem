$(document).ready(function () {
  $("#summernote").summernote({
    height: 200,
  });
});

$(document).ready(function () {
  $("#selectAllBoxes").click(function (event) {
    if (this.checked) {
      $(".checkBoxes").each(function () {
        this.checked = true;
      });
    } else {
      $(".checkBoxes").each(function () {
        this.checked = false;
      });
    }
  });

  // var div_box = "<div id='load-screen'><div id='loading'></div></div>";
  // $("body").prepend(div_box);
  // $("#load-screen")
  //   .delay(500)
  //   .fadeOut(300, function () {
  //     $(this).remove();
  //   });
});

function loadUsersOnline() {
  $.get("functions.php?onlineusers=result", function (data) {
    $(".usersonline").text(data);
  });
}

setInterval(function () {
  loadUsersOnline();
}, 500);

function addPostImage(input) {
  var imgElement = $("#add_post_image");

  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      imgElement.attr("src", e.target.result);
      imgElement.show();
    };

    reader.readAsDataURL(input.files[0]);
  } else {
    imgElement.hide();
  }
}

function editPostImage(input) {
  var imgElement = $("#edit_post_image");

  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      imgElement.attr("src", e.target.result);
    };

    reader.readAsDataURL(input.files[0]);
  }
}
