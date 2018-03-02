//vars
//an array of JSON objects, each obthis.ject represents info about the course that is going to be subbed.
/*var cts = function(){
    return {
      data: [],
      init: function(){
        this.data = [];
      },
      get: function(){
        return this.data;
      }
    };
};
var coursesToSub = new cts();*/

var coursesToSub = null;
var isOneDay = false 



/*AJAX FUNCTIONS*/
function deleteById(id){
  return $.ajax({
    type: 'DELETE',
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    url: '/substitutions/' + id
  });
}

function getAvailableReplacements(instructor_id, cdate, instructor_type, course_id, crsindex){
    var toSend = { instructor_id: instructor_id, cdate: cdate, instructor_type: instructor_type, course_id:course_id};
    return $.ajax({
      type: 'POST',
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      url: '/substitutions/replacements',
      data: toSend,
      dataType: 'json'
    });
}

function getCoursesInRange(start_date, end_date, instructor_id){
  var rangeData = {"start_date":start_date, "end_date":end_date, "instructor_id":instructor_id};
  return $.ajax({
      type: 'POST',
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      url: '/substitutions/range',
      data: rangeData,
      dataType: 'json'
    });
}

function postSubstitutions(){
  if(coursesToSub!==null){
    for (var i = 0; i < coursesToSub.length; i++) {
      coursesToSub[i].start_date = $('#modal_substitution_start_date').val();
      coursesToSub[i].end_date = (isOneDay) ? $('#modal_substitution_start_date').val() : $('#modal_substitution_end_date').val();
    }
    console.log(coursesToSub);
  return $.ajax({
      type: 'POST',
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      url: '/substitutions',
      data: {substitutions: coursesToSub},
      dataType: 'json'
    });
  }//end of if
}


function setSelectChange(){
  //2nd Action*/ '[id^=selectSub_]'
  $('select.sub-select').on('change', function(){
    var id = $(this).attr("id").toString();
    console.log(id.split('_')[1]);
    var index = id.split('_')[1];
    coursesToSub[index].sub_id = this.value;
    console.log(coursesToSub);
  });
}

/*Helper functions*/
function createSubstituteDropDown(el){
  /*
    CREATE a formgroup element dynamically.
    That has a dropdown
  */

  var instructor_type = (el.ta_id !== undefined)? "TA" : "Instructor";
  var parent = document.getElementById("sub-modal-body");

  console.log($(parent).data('crsindex'));
  var crsindex = parseInt($(parent).attr('data-crsindex')) + 1;
  $(parent).attr('data-crsindex', crsindex.toString());
  var substitution = document.createElement("div");
    substitution.id = crsindex;
    substitution.classList.add('form-group');
    substitution.classList.add('substitution-group');
    parent.appendChild(substitution);


  var select = document.createElement("select");
    select.id = 'selectSub_' + crsindex;
    select.classList.add('form-control');
    select.classList.add('sub-select');
    select.size = 3;
    select.required = true;
    substitution.appendChild(select);

  var default_option = document.createElement("option");
    default_option.defaultSelected = true;
    default_option.disabled = true;
    var text= document.createTextNode("Please Select a replacement");
    default_option.appendChild(text);
    select.appendChild(default_option);

  var label = document.createElement("label");
    label.htmlFor =  document.getElementById('selectSub_' + crsindex).id;
    text= document.createTextNode(el.course_id + "   Intake:  " + el.intake_no + "   Term ID: " + el.term_id + "   " + instructor_type + " Sessions Missed: " + Object.keys(el.cdate).length);
    label.appendChild(text);
    //label.classList.add('col-md-4');
    label.classList.add('control-label');
    substitution.insertBefore(label,substitution.childNodes[0]);

  var deleteButton = document.createElement('button')
    deleteButton.classList.add('btn')
    deleteButton.classList.add('btn-danger')
    deleteButton.classList.add('btn-xs')
    deleteButton.classList.add('float-right')
    deleteButton.classList.add('removeSubstitution')
    let xText = document.createTextNode('x')
    deleteButton.appendChild(xText)
    deleteButton.id = crsindex
    substitution.insertBefore(deleteButton,substitution.childNodes[1]);
    deleteButton.onclick = () => {
      /* Act on the event */
        let id = crsindex
        console.log(`In remove Substitution, removing index ${id}`);
        /* Remove the substitution select div from the modal's dom */
        let nodeToRemove = $(`#${id}.form-group.substitution-group`)
        /* Remove the actual substitution information from the courses to sub */
        coursesToSub.splice(id, 1)
        nodeToRemove.remove()
    }

  var error = document.createElement("p");
  error.classList.add('text-danger');
  error.id = "errorSub_" + crsindex;
  substitution.appendChild(error);

  //ajax call
  var substitutions = getAvailableReplacements(el.instructor_id, el.cdate, instructor_type, el.course_id, crsindex);

  substitutions.done(function(data){
    //iterate over the objects reutnred from the controller
    /*these substitutions are anyone who is available at the date/times this course is missed, and who are not scheduled in a room at those date/times.*/
    if(Object.keys(data.substitutions).length > 0){
      $.each(data.substitutions, function(index, el) {
        var option = document.createElement("option");
          option.value = el.instructor_id;
          text= document.createTextNode(el.first_name);
          option.appendChild(text);
          select.appendChild(option);
      });
   }else{
     //display error
     $('#errorSub_'+crsindex).html('The are currently no instructors available during these dates and times.');
   }
  }).fail(function(e){console.log(e.responseText);});
  //end of ajax call
}//end of function

/*Proceedural stuff*/

$('#searchInstructor').focus(function(event) {
  $("#instructorSelect").show();
});
$('#searchInstructor').keyup(function(event) {
  var filter = $(this).val();
  var regex = new RegExp(filter, 'i');
  console.log(regex);
  $('#instructorSelect option').each(function(){
  if(regex.test($(this).text())){
    $(this).show();
  }else{
    $(this).hide();
  }
  });
});
$('#instructorSelect option').click(function(event) {
  var that = this;
  $("#searchInstructor").val($(that).text().trim());
  $("#modal_instructor_id").val($(that).val());
  $('#instructorSelect').hide();
});



/*1st Action*/
$('div.modal-body input, div.modal-body select').on('change', function(){
  var start_date = $("#modal_substitution_start_date").val();
  var end_date = (isOneDay)? $("#modal_substitution_start_date").val() : $('#modal_substitution_end_date').val();
  var instructor_id = $('#instructorSelect').val();

  //console.log(start_date +'|'+end_date +'|'+ instructor_id);

  /*When the date then the name is selected, get all the am and pm course offerings for this instructor for those times*/
  if(instructor_id!==undefined && start_date && end_date){

    var courses_promise = getCoursesInRange(start_date,end_date,instructor_id);
    courses_promise.done(function(data){
        //remove and dynamically created substitution form groups
        $('.substitution-group').remove();
        $('#sub-modal-body').attr('data-crsindex', '-1');
        $('#end_date_error').empty();
        coursesToSub = [];
        //offerings is a collection in the array of data.
        console.log(data.offerings);
        //iterate over the objects reutnred from the controller
        if(data.offerings){
            if(Object.keys(data.offerings).length > 0){
               $.each(data.offerings, function(index, el) {
                      coursesToSub.push(el);
                      createSubstituteDropDown(el);
                });
              setSelectChange();
            }else{
             $('#end_date_error').html('The instructor or TA will not miss classes during this time.');
            }
          }else if(data.error){
            $('#end_date_error').html(data.error[0].toString());
          }
      }).fail(function(e){console.log(e.responseText);});
  }//end of if
});



/*3rd Action : Submit*/
$('#sub_modal_save').click(function(e){
  e.preventDefault();
  //look at all the fields and make sure they have a value;

  function empty(){
    var selects = $('select.sub-select');
    for (var i = 0; i < selects.length; i++) {
      var el =$(selects[i]);
      console.log(el);
      console.log(el.val());
      if(el.val() === undefined || el.val() === "" || el.val() === null){
          var id = el.attr('id').toString().split('_')[1];
          $('#errorSub_' +id).html("Please select a substitution");
          return true;
      }
    }
    return false;
  }

  if(empty()){return;}
  var promise = postSubstitutions();
  promise.done(function(data){
    if(data.error){
      console.log(data.error);

    }else if(data.success){
        console.log(data.success[0]);
        location.reload();
    }
  }).fail(function(e){console.log(e.responseText);});
});

/*close button for the modal : reset everything*/
$('#addNewSubstitutionModal > div > div > div.modal-footer > button').click(function(e){
  $(this).closest('form').find("input[type=text], input[type=date]").val("");
  $('#instructorSelect').hide();
  $('.substitution-group').remove();
  $('#sub-modal-body').attr('data-crsindex', '-1');
  $('#end_date_error').empty();
  coursesToSub = [];
});

/*Delete by id*/
$('button.delete').click(function(event) {
  /* Act on the event */
  var promise = deleteById($(this).attr('id'));
  promise.done(function(){
    console.log('deleted successfully');
    location.reload();
  }).fail(function(e){console.log(e.responseText);});
});

/*Remove Substitution by id*/
$('button.removeSubstitution').click(function(event) {
  /* Act on the event */
  let id = $(this).attr('id')
  console.log(`In remove Substitution, removing index ${id}`);
  /* Remove the substitution select div from the modal's dom */
  let nodeToRemove = $(`#${id}.form-group.substitution-group`)
  nodeToRemove.remove()
  /* Remove the actual substitution information from the courses to sub */
  coursesToSub.splice(id, 1)
  $('select.sub-select').each(function(el, idx){
      console.log(`in delete : start id ${el.id}`)
      el.id = `selectSub_${idx}`
      console.log(`to ${el.id}`)
  })
  console.log(JSON.stringify.coursesToSub)
});

/* Show and hide the end date items */

$('#modal_substitution_isOneDay:checkbox').change(function(){
  if(this.checked){
    /* hide the the end date portion and set the end date to the start date */
    console.log('This is checked');
    $('#modal_substitution_end_date_div').hide()
    isOneDay = true
  }else{
    /*  show the end date portion and set the end date to nothing */
    console.log('This is not checked');
    $('#modal_substitution_end_date_div').show()
    isOneDay = false
  }
})

/* Listen for change to modal body dom elements */

$('div.modal-body').on("DOMNodeRemoved", function(e){
    function resetIndexes() {
      $('.form-group.substitution-group').each(function(i,el){
        $(el).attr('id', i)
        $('select.form-control.sub-select', el).attr('id',`selectSub_${i}`)
        $('label.control-label', el).attr('for', `selectSub_${i}`)
        $('button.removeSubstitution', el).attr('id', i)
        $('p.text-danger', el).attr('id', `errorSub_${i}`)
      })
    }
    setTimeout(() => {
      resetIndexes()
    }, 1000);
});