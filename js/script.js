// toggle class active
const navbarNav = document.querySelector(".navbar-nav");
// ketika hamburger menu di klik
document.querySelector("#hamburger-menu").onclick = () => {
  navbarNav.classList.toggle("active");
};

// klik diluar sidebar untuk menghilangkan nav
const hamburger = document.querySelector("#hamburger-menu");

document.addEventListener("click", function (e) {
  if (!hamburger.contains(e.target) && !navbarNav.contains(e.target)) {
    navbarNav.classList.remove("active");
  }
});

// toggle active menu
document.querySelectorAll(".read-more-link").forEach((link) => {
  link.addEventListener("click", (e) => {
    e.preventDefault();
    e.target.closest(".menu-card").classList.add("show-full-desc");
  });
});

document.querySelectorAll(".read-less-link").forEach((link) => {
  link.addEventListener("click", (e) => {
    e.preventDefault();
    e.target.closest(".menu-card").classList.remove("show-full-desc");
  });
});

// toggle dark and light mode
function setDarkMode(isDark) {
  const body = document.body;
  const sunBtn = document.getElementById("sun-btn");
  const moonBtn = document.getElementById("moon-btn");

  if (isDark) {
    body.classList.add("dark-mode");
    sunBtn.style.display = "inline-block";
    moonBtn.style.display = "none";
    localStorage.setItem("theme", "dark");
  } else {
    body.classList.remove("dark-mode");
    sunBtn.style.display = "none";
    moonBtn.style.display = "inline-block";
    localStorage.setItem("theme", "light");
  }
}

// Saat halaman dimuat, cek preferensi tema
window.addEventListener("DOMContentLoaded", () => {
  const savedTheme = localStorage.getItem("theme");
  setDarkMode(savedTheme === "dark");
});

// scroll bar
// document.addEventListener('DOMContentLoaded', function() {
//   const container = document.querySelector('.menu-cards');
//   const prevBtn = document.querySelector('.scroll-prev');
//   const nextBtn = document.querySelector('.scroll-next');

//   function updateButtons() {
//     prevBtn.disabled = container.scrollLeft <= 0;
//     nextBtn.disabled = container.scrollLeft >=
//       container.scrollWidth - container.clientWidth - 1;
//   }

//   prevBtn.addEventListener('click', () => {
//     container.scrollBy({
//       left: -300,
//       behavior: 'smooth'
//     });
//   });

//   nextBtn.addEventListener('click', () => {
//     container.scrollBy({
//       left: 300,
//       behavior: 'smooth'
//     });
//   });

//   container.addEventListener('scroll', updateButtons);
//   window.addEventListener('resize', updateButtons);
//   updateButtons();
// });
