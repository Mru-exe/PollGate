//usernameAjax.js depends on common.js !
let timer = null;
document.getElementById("username").addEventListener("keyup", (e) => {
    if(timer != null) {
        clearTimeout(timer);
        timer = null;
    }
    timer = setTimeout(checkUsername, 500);
});

function checkUsername() {
    let username = document.getElementById("username");
    let errMsg = document.getElementById("username-error");
    if (username.value != "" && username.value.length >= 3 && username.value != username.getAttribute("data-currentUsername")){ 
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "api/checkUsername.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200 && xhr.responseText == '{"available":false}') {
                evalError("username", "Username is already taken", 1)
                username.setAttribute("data-overrideErrors", "true");
            } else {
                username.setAttribute("data-overrideErrors", "false");
                evalError("username", "", 0)
            }
        }
        xhr.send(`username=${username.value}`);
    }else if (username.value == username.getAttribute("data-currentUsername")) {
        username.setAttribute("data-overrideErrors", "false");
        evalError("username", "", 0)
    }else{
        errMsg.textContent = "";
    }
}
