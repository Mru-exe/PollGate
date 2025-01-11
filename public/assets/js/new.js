class Option {
    constructor() {
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'option-wrapper';

        this.input = document.createElement('input');
        this.input.type = 'text';
        this.input.name = 'option[]';
        this.input.required = true;

        this.button = document.createElement('button');
        this.button.type = 'button';
        this.button.className = 'btn bg-red';
        this.button.innerHTML = '<i class="fa-solid fa-trash"></i>';
        this.button.addEventListener("click", (e) => {
            list.removeChild(this.wrapper);
        })

        this.wrapper.appendChild(this.input);
        this.wrapper.appendChild(this.button);
    }

    getElement() {
        return this.wrapper;
    }
}

const newBtn = document.getElementById("new-option") ?? new EventTarget;
const list = document.getElementById("option-list");

newBtn.addEventListener("click", (e) => {
    opt = new Option();
    list.append(opt.getElement());
})

function addItem() {   
    const itemList = document.getElementById('item-list');
    
    // Create new list item with editable input
    const wrapper = document.createElement('div');
    wrapper.class = '';
    const textInput = document.createElement('input');
    textInput.type = 'text';
    textInput.value = "";

    wrapper.appendChild(textInput);
    itemList.appendChild(wrapper);
}