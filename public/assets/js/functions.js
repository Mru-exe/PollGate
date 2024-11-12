document.getElementById("logout-anchor").addEventListener("click", event => {
    logout();
});

async function logout(){
    let url = "https://zwa.toad.cz/~kindlma7/PollGate/src/api/logout.php"
    const req = new XMLHttpRequest();
    req.open("GET", url);
    req.send()
}