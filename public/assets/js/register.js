
(function () {
    "use strict";

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
        if (input.getAttribute('name') === 'email') {
            return input.value.trim().match(/^([a-zA-Z0-9_\-.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(]?)$/) != null;
        }
        if (input.getAttribute('id') === 'pass-check') {
            return input.value.trim() !== '' && input.value.trim() === document.getElementsByName('pass')[0].value;
        }

        return input.value.trim() !== '';
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