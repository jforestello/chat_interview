
(function () {
    "use strict";


    /*==================================================================
    [ Validate ]*/
    const input = document.querySelectorAll('.validate-input .input100');
    document.getElementById('login-form').onsubmit = () => {
        let check = true;

        for (let i=0; i<input.length;i++) {
            if (validate(input[i]) === false) {
                showValidate(input[i]);
                check = false;
            }
        }

        return check;
    };

    input.forEach((el) => {
        el.onfocus = () => hideValidate(el);
    });

    function validate (input) {
        if(input.getAttribute('name') === 'email') {
            if(input.value.trim().match(/^([a-zA-Z0-9_\-.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(]?)$/) == null) {
                return false;
            }
        }
        else {
            if(input.value.trim() === ''){
                return false;
            }
        }

        return true;
    }

    function showValidate(input) {
        const thisAlert = input.closest('div');

        thisAlert.classList.add('alert-validate');
    }

    function hideValidate(input) {
        const thisAlert = input.closest('div');

        thisAlert.classList.remove('alert-validate');
    }
})();