document.getElementById("logout-anchor").addEventListener("click", event => {
    logout();
});

async function logout(){
    let url = "http://zwa.toad.cz/~kindlma7/PollGate/src/functions/logout.php"
    const req = new XMLHttpRequest();

    req.onload = (e) => {
        console.log("response: " + e);
    }

    let body = {
        "name": "test"
    }
    
    console.log("triying to log out");
    req.open("POST", url);
    req.setRequestHeader('Content-Type', "application/json");
    req.send(JSON.stringify(body));
}