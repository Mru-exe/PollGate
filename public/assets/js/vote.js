const form = document.getElementById('vote-form');
const submit = form.querySelector('button[type="submit"]');

form.addEventListener('change', () => {
    if (form.checkValidity()) {
        submit.disabled = false;
        submit.setAttribute("data-state", "enabled");
    } else {
        submit.disabled = true;
        submit.setAttribute("data-state", "disabled");
    }
});
