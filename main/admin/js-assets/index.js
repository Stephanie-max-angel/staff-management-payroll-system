

//  Dropdown button code
 
const menuIcon = document.querySelector(".menu-icon");
const dropdownMenu = document.querySelector(".dropdown-menu");

menuIcon.addEventListener("click", () => {
    dropdownMenu.classList.toggle("show-menu");
});

