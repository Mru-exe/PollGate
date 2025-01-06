let timer = null;
function delayedCheckUsername() {
    if(timer != null) {
        clearTimeout(timer);
        timer = null;
    }
    timer = setTimeout(checkUsername, 500);
}


function checkUsername() {
    username = document.getElementById("username").value;
    if (username != "" && username.length >= 3) { 
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "api/checkUsername.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200 && xhr.responseText == '{"available":false}') {
                document.getElementById("form-error-message").textContent = "Username already exists";
            } else {
                document.getElementById("form-error-message").textContent = "";
            }
        }
        xhr.send(`username=${username}`);
    }
    else {
        document.getElementById("form-error-message").textContent = "";
    }
}

document.getElementById("username").addEventListener("keyup", (e) => {
    delayedCheckUsername();
});