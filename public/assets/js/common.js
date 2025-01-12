const pw = document.getElementById("password");
const conf = document.getElementById("confpassword");
const defaultForm = document.querySelectorAll("form.default")[0] ?? null;

function evalError(inputId, errorMessage, isError){
    input = document.getElementById(inputId) ?? null;
    span = document.getElementById(inputId+"-error") ?? null;

    if(input !== null && input.getAttribute("data-overrideErrors") != "true"){
        isError ? input.classList.add('form-input-error') : input.classList.remove('form-input-error');
    }
    if(span !== null && input.getAttribute("data-overrideErrors") != "true"){
        span.textContent = isError ? errorMessage : "";
    }
    return;
}

//Gets all form inputs and if they exist add an event listener
if (defaultForm != null) {
    document.querySelectorAll('input, textarea').forEach(field => {
        if (field.getAttribute("required") != null) {
            field.addEventListener('blur', () => {
                field.value.trim() === '' ? evalError(field.id, "This field is required.", true) : evalError(field.id, null, false);
            });
        }
    });
}

document.querySelectorAll("input#password, input#confpassword").forEach(field => {
    field.addEventListener("keyup", (e) => {
        if(e.key == "Tab"){
            return;
        }
        if(pw.value.trim() !== '' && pw.value.length < 6){
            evalError(pw.id, "Password has to be atleast 6 characters long", true);
            pw.setAttribute("data-overrideErrors", "true");
        } else {
            evalError(pw.id, "", false);
            pw.setAttribute("data-overrideErrors", "false");
        }
        if(conf.value.trim() !== '' && conf.value != pw.value) {
            evalError(conf.id, "Passwords has to match", true);
            conf.setAttribute("data-overrideErrors", "true");
        } else {
            evalError(conf.id, "", false);
            conf.setAttribute("data-overrideErrors", "false");
        }
    })
})

const file = document.querySelector("input#avatar") ?? new EventTarget;
const cont = document.getElementById("file-upload-container") ?? new EventTarget;

cont.addEventListener("click", (e) => {
    file.click();
})

file.addEventListener("change", (e) => {
    document.getElementById('file-name').textContent = file.files[0] ? file.files[0].name : '';
})