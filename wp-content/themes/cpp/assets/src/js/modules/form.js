// ------------------------------------
//
// Actions
//
// ------------------------------------

const phoneRules = {
  minlength: 10,
  maxlength: 15,
  regx: /^(?=.*[0-9])[- +()0-9]+$/,
};

const emailRules = {
  minlength: 4,
  email : /^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i,
};

(function($) {

  class Form {

    constructor($el) {

      this.$form = $el;
      this.$phone = this.$form.find('input[name="phone"]');
      this.$email = this.$form.find('input[name="email"]');
      this.initValidator();
    }

    initValidator() {

      this.updateValidator();
      this.$form.validate();
      this.addRules();

      if (this.$form.find('fieldset').length >= 2) {

        this.steppedForm();
      }


      this.$form.submit((e) => {

        if (!this.$form.valid()) return; 

        let $btn = this.$form.find('.-submit button');

        if ($btn.hasClass('disabled')) {
          e.preventDefault(); 
          return;
        }
        else $btn.addClass('disabled');

        if (this.isSignupForm()) {
          e.preventDefault();
          this.processSignUp();
        }

      })

    }

    isSignupForm () {
      return this.$form.hasClass('form-signup');
    } 

    addRules() {

      this.$email.rules( 'add', emailRules);
      if (this.$phone.length && this.$phone.attr('required')) this.$phone.rules( 'add', phoneRules);

      if (this.$phone.length && this.$phone.attr('required')) this.$phone.rules( 'add', phoneRules);

      this.$phone.on('change paste keyup', function() {

        if ($(this).val() == '' && !$(this).attr('required')) {
          this.$phone.rules( "remove");
        } else {
          this.$phone.rules( "add", phoneRules);
        }

      });
    }

    updateValidator() {
      if (!$.validator) return;

      $.validator.addMethod("regx", function(value, element, regexpr) {          
        return regexpr.test(value);
      }, "Please enter a valid phone number.");

      $.validator.addMethod("email", function(value, element, regexpr) {          
        return regexpr.test(value);
      }, "Please enter a valid email address.");
    }

    steppedForm() {

      this.$form.find('.-next .button').click((e) => {

        if ($(e.target).parent().hasClass('-submit')) return;
        let $current = $(e.target).closest('fieldset');

        // Falidate Fieldset Fields
        let valid = true;

        $current.find('input').each(function() {
          $(this).valid();

          if (!$(this).valid()) {
            valid = false;
          }
        })

        if (!valid) {
          return;
        }

        $current.addClass('hidden');
        $current.next().addClass('visible');
      });
    }

    processSignUp() {

      $.ajax({
        method: "POST", 
        data: this.$form.serialize(), 
        url: '/api/subscribe',
        success: (response) => {
          this.formSuccess(response);
        },
        fail: (response) => {
          this.formFail(response);
        },
      });
    }

    formSuccess(response) {

      let $wrapper = this.$form.closest('.form-wrapper');
      $wrapper.css({'min-height' :$wrapper.height()});

      if (!response.success) this.formFail();
      else {
        this.$form.fadeOut(300);
        this.$form.next('.success').delay(500).fadeIn('visible');
      }
    }

    formFail() {
      let $btn = this.$form.find('.-submit button');
      
      if ($btn.hasClass('disabled')) return; 
      else $btn.addClass('disabled');
    }
  
  }

  module.exports = Form;

})(jQuery);