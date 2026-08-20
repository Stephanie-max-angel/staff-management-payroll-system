    //JavaScript code for header movement
let lastScroll = 0;
const header = document.getElementById("header");
window.addEventListener("scroll", () => {
    let currentScroll = window.pageYOffset;

    if(currentScroll > lastScroll){
        header.style.top = "-80px";
    }
    else {
            header.style.top = "0";
    }
    lastScroll = currentScroll;
})

//  Dropdown button code
 
const menuIcon = document.querySelector(".menu-icon");
const dropdownMenu = document.querySelector(".dropdown-menu");

menuIcon.addEventListener("click", () => {
    dropdownMenu.classList.toggle("show-menu");
});

