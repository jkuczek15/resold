require([
    "jquery",
    "mage/mage",
    "mage/validation",
    "vue"
], ($, mage, validation, Vue) => {
    // initialize a new Vue app
    var app = new Vue({
      el: '#app',
      data: {
        categories: [],
        subcategories: [],
        lowestcategories: [],
        num_images: 9,
        local_global_error: false,
        image_error: false,
        form_data: {}
      }
    });

    // add custom validation to our form
    addCustomValidation();

    // initial api requests
    $.get('/api/categories', (data) => app.categories = data);

    // initialize validation
    $('#sell_form').mage('validation', {});

    // category change function
    $('#category_select').change(function() {
      let category = this.value;
      if(category == ''){
        app.subcategories = [];
        $('.subcategory').hide();
        $('.lowestcategory').hide();
      }else{
        app.subcategories = app.categories[category];
        $('#subcategory_select').val('');
        $('#lowestcategory_select').val('');
        $('.lowestcategory').hide();
        $('.subcategory').show();
      }
    });

    // subcategory change function
    $('#subcategory_select').change(function(){
      let subcategory = this.value;
      if(subcategory == ''){
        app.lowestcategories = [];
        $('.lowestcategory').hide();
      }else{
        app.lowestcategories = app.subcategories[subcategory];

        if(isNaN(app.lowestcategories)){
          $('#lowestcategory_select').val('');
          $('.lowestcategory').show();
        }
      }
    });

    // image change function
    $('.image_input, #image_-1').change(function(event){
      //$('.images > .control > .mage-error').hide();
      let id = Number.parseInt((this.id.split('_'))[0]);
      $(`#image_${id}-error`).hide();
      $(`#image_${id+0}`).show();
    });

   // after everything loads, add our click handler
   $(window).load(() => {
       $('#sell_form').submit((event) => {
          let is_valid = $('#sell_form').validation('isValid');

          if(is_valid){
            // we have valid form data
            // make a request to upload our product
            $.post('/api/product', app.form_data, (response) => {
              console.log(response);
            });
          }// end if form valid

          event.preventDefault();
      });
      $('#submit').prop('disabled', false);
   });

   /**************************************************
   ******************* FUNCTIONS *********************
   **************************************************/
   function addCustomValidation() {
     $.validator.addMethod(
         'validate-local-global',
         function (value) {
             // custom validation
             if(!document.getElementById('local').checked && !document.getElementById('global').checked){
               app.local_global_error = true;
               return false;
             }
             app.local_global_error = false;
             return true;
         }
     );
   };
});
