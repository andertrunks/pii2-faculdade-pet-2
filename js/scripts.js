const prevButton = document.getElementById('prev');
const nextButton = document.getElementById('next');
const container = document.querySelector('.container');
const items = container?.querySelectorAll('.list .item') ?? [];
const indicator = document.querySelector('.indicators');
const dots = indicator?.querySelectorAll('ul li') ?? [];
const list = container?.querySelector('.list');
const themeToggle = document.getElementById('trilho');
const pageBody = document.body;
const themedSections = document.querySelectorAll('.home');

let active = 0;
const lastPosition = items.length - 1;

function setSlider() {
  if (!container || !indicator || items.length === 0 || dots.length === 0) {
    return;
  }

  container.querySelector('.list .item.active')?.classList.remove('active');
  indicator.querySelector('ul li.active')?.classList.remove('active');
  dots[active]?.classList.add('active');

  const number = indicator.querySelector('.number');
  if (number) {
    number.textContent = String(active + 1).padStart(2, '0');
  }
}

if (nextButton && list && items.length > 0) {
  nextButton.addEventListener('click', () => {
    list.style.setProperty('--calculation', '1');
    active = active + 1 > lastPosition ? 0 : active + 1;
    setSlider();
    items[active]?.classList.add('active');
  });
}

if (prevButton && list && items.length > 0) {
  prevButton.addEventListener('click', () => {
    list.style.setProperty('--calculation', '-1');
    active = active - 1 < 0 ? lastPosition : active - 1;
    setSlider();
    items[active]?.classList.add('active');
  });
}

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
