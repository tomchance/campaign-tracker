/*
 * Let people create / edit campaigns
 */

function isInt(value) {
  return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))
}
 
$(".start-hidden").hide();

var cid = 0;

$(":button").click(function () {
  var btn = $(this);
  var specs = btn.attr('id').split("-");
  id = specs[1];
  if (specs[0] == 'editcamp') {
    $(".cedit-" + id).hide();
    $(".csave-" + id).show();
  } else if (specs[0] == 'cancelcamp') {
    $(".cedit-" + id).show();
    $(".csave-" + id).hide();
  } else if (specs[0] == 'savecamp') {
    var name = $("#name-" + id).val();
    var q = "lib/updatename.php?table=l_campaigns&id=" + id + "&name=" + name;
    console.log("Updating name (" + q + ")");
    $.get(q, function(d) { if (d) { alert(d); } });
    $("#clink-" + id).html(name);
    $(".cedit-" + id).show();
    $(".csave-" + id).hide();
  } else if (specs[0] == 'ac') {
    var name = $("#newname-" + id).val();
    var q = "lib/insert_campaign.php?ward=" + id + "&name=" + name;
    console.log("Creating new campaign (" + q + ")");
    getCampID = function(url, callback) {
      $.get(url, function (data) { callback(data)}, "html");
    }
    getCampID(q,  function(newcampid) {
      console.log("Received data from script: " + newcampid);
      if (!isInt(newcampid)) { alert('Something went wrong: ' + newcampid); }
      newListEntry = '<li><a href="progress.php?campaign='+ newcampid + '&ward=' + id + '">' + name + "</a>"
       + ' (<a href=".">refresh to edit</a>)</li>';
       $("#list-wards-" + id + " li:last").append(newListEntry);
    });
  }
});

$(".confirm").confirm({
  text: "Are you sure you want to delete that campaign?",
  title: "Confirmation required",
  confirm: function(button) {
    var q = "lib/delete_campaign.php?campaign=" + id;
    console.log("Deleting campaign (" + q + ")");
    $.get(q, function (d) { if (d) { alert('Something went wrong: ' + d); } }, "html");
    $("#clink-" + id).parent().parent().parent().remove();
  },
  cancel: function(button) {
      // nothing to do
  },
  confirmButton: "Yes I am",
  cancelButton: "No",
  post: true,
  confirmButtonClass: "btn-danger",
  cancelButtonClass: "btn-default",
  dialogClass: "modal-dialog modal-lg" // Bootstrap classes for large modal
});
