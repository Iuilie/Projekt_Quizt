let navbar = document.querySelector('.header .flex .navbar');

document.querySelector('#menu-btn').onclick = () => {
   navbar.classList.toggle('active');
   searchForm.classList.remove('active');
   profile.classList.remove('active');
   console.log("Menu button clicked");
}

let profile = document.querySelector('.header .flex .profile');

document.querySelector('#user-btn').onclick = () => {
   profile.classList.toggle('active');
   searchForm.classList.remove('active');
   navbar.classList.remove('active');
   console.log("User button clicked");
}

let searchForm = document.querySelector('.header .flex .search-form');

document.querySelector('#search-btn').onclick = () => {
   searchForm.classList.toggle('active');
   navbar.classList.remove('active');
   profile.classList.remove('active');
   console.log("Search button clicked");
}

window.onscroll = () => {
   profile.classList.remove('active');
   navbar.classList.remove('active');
   searchForm.classList.remove('active');
   console.log("Window scrolled");
}

document.querySelectorAll('.content-150').forEach(content => {
   if (content.innerHTML.length > 150) content.innerHTML = content.innerHTML.slice(0, 150);
   console.log("Content trimmed");
});
