const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');
const themeToggle = document.getElementById('trilho');
const pageBody = document.body;

registerBtn?.addEventListener('click', () => {
  container?.classList.add('active');
});

loginBtn?.addEventListener('click', () => {
  container?.classList.remove('active');
});

function applyTheme(isLight) {
  themeToggle?.classList.toggle('light', isLight);
  pageBody?.classList.toggle('light', isLight);
}

function getSavedTheme() {
  try {
    return window.localStorage?.getItem('adota-pet-theme') ?? null;
  } catch {
    return null;
  }
}

function saveTheme(value) {
  try {
    window.localStorage?.setItem('adota-pet-theme', value);
  } catch {
    // O tema continua funcionando mesmo quando o navegador bloqueia o armazenamento.
  }
}

applyTheme(getSavedTheme() === 'light');

themeToggle?.addEventListener('click', () => {
  const isLight = !pageBody.classList.contains('light');
  applyTheme(isLight);
  saveTheme(isLight ? 'light' : 'dark');
});
