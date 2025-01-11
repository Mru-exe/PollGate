const bar = document.getElementById("search-bar");
const btn = document.getElementById("search-btn");

const prev = document.getElementById("search-pgn-prev");
const next = document.getElementById("search-pgn-next");

let lastQuery = "";
let offset = 0;

function shortenString(str) {
    if (str.length > 50) {
        return str.substring(0, 50) + "...?";
    }
    return str;
}


//LISTENERS
bar.addEventListener("keydown", (e) => {
    if(e.key == "Enter" && e.target.value != lastQuery){
        e.preventDefault();
        offset = 0;
        searchPolls(e.target.value, offset, (result) => {
            renderResults(result, offset);
        });
    }
});

btn.addEventListener("click", () => {
    let bar = document.getElementById("search-bar").value;
    if(lastQuery != bar){
        offset = 0;
        searchPolls(bar, offset, (result) => {
            renderResults(result, offset);
        });
    }
});

next.addEventListener("click", (e) => {
    if(next.getAttribute("data-state") == "enabled"){
        searchPolls(lastQuery, offset+3, (data) => {
            renderResults(data, offset);
        });
    }
});

prev.addEventListener("click", (e) => {
    if (prev.getAttribute("data-state") == "enabled") {
        searchPolls(lastQuery, offset-3, (data) => {
            renderResults(data, offset);
        });
    }
});

function searchPolls(query, callOffset, callback){
    lastQuery = query;
    var xhr = new XMLHttpRequest();
    offset = callOffset;
    xhr.open("GET", "api/searchPolls.php?query="+query+"&offset="+callOffset, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                data = JSON.parse(xhr.response);
                callback(data,);
            } else {
                callback([{"error": true, "message": "Something went wrong, try again..."}]);
            }
        }
    }
    xhr.send();
}

class Result {
    constructor(data, logged) {
        this.data = data;
        this.logged = logged;
        this.element = this.createElement();
    }

    getButton(){
        return `<span><a class="btn bg-blue" href="vote.php?pid=${this.data.id}"><i class="fa-solid fa-arrow-up-right-from-square"></i>View</a></span>`;
    }

    createElement() {
        const div = document.createElement("div");
        div.className = "result-item";
        div.innerHTML = `
            <span class="div-title">${this.data.title}</span>
            <span class="poll-preview">${shortenString(this.data.question)}</span>
            <span class="btn-container">${this.getButton()}</span>
        `;
        return div;
    }

    getElement() {
        return this.element;
    }
}

function renderResults(obj, offset) {
    console.log("Rendering results with offset:", offset);
    console.log("count", obj.count);

    const resultsContainer = document.getElementById("search-results");
    resultsContainer.innerHTML = ""; // Clear previous results
    const pgnContainer = document.getElementById("pgn-n");
    
    let pages = Math.ceil(obj.count / 3);
    pgnContainer.innerHTML = "";

    //render pagination buttons
    for (let index = 1; index <= pages; index++) {
        page = document.createElement("a");
        page.addEventListener("click", (e) => {
            nextOffset = parseInt(e.target.getAttribute("data-offset"));
            searchPolls(lastQuery, nextOffset, (data) => {
                renderResults(data, nextOffset);
            });
        })
        page.className = "pgn-n-item";
        page.setAttribute("data-offset", 3*(index-1));
        if(offset == 3*(index-1)){
            page.setAttribute("data-index", "current");
        }
        page.textContent = `${index}`;
        pgnContainer.appendChild(page);
    }

    if (obj.error) {
        console.log(obj.message);
        return;
    }


    //enable/disable next/prev buttons accordingly
    if(offset < Math.floor(obj.count/3)*3 && obj.count > 3){
        next.setAttribute("data-state", "enabled");
    } else {
        next.setAttribute("data-state", "disabled");
    }

    if(offset > 0 && obj.count > 3){
        prev.setAttribute("data-state", "enabled");
    } else {
        prev.setAttribute("data-state", "disabled");
    }

    //render results
    obj.results.forEach(element => {
        const result = new Result(element, obj.logged);
        resultsContainer.appendChild(result.getElement());
    });
}