let trilho = document.getElementById('trilho')
let body = document.querySelector('body')
let home= document.querySelector('.home')

trilho.addEventListener('click', () => {
    trilho.classList.toggle('light')
    body.classList.toggle('light')
    home.classList.toggle('light')
});