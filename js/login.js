const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');
let trilho = document.getElementById('trilho')
let body = document.querySelector('body')
let home= document.querySelector('.home')

registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});

trilho.addEventListener('click', () => {
    trilho.classList.toggle('light')
    body.classList.toggle('light')
    home.classList.toggle('light')
});