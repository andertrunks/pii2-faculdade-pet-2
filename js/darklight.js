const themeToggle = document.getElementById('trilho');
const pageBody = document.body;
const themedSections = document.querySelectorAll('.home');

function applyTheme(isLight) {
  themeToggle?.classList.toggle('light', isLight);
  pageBody?.classList.toggle('light', isLight);
  themedSections.forEach((section) => section.classList.toggle('light', isLight));
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
