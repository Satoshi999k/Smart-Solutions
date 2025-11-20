// Function to toggle dropdown
function toggleDropdown() {
    var profileDropdownContent = document.querySelector(".profile-dropdown-content");
    if (profileDropdownContent.style.display === "block") {
        profileDropdownContent.style.display = "none";
    } else {
        profileDropdownContent.style.display = "block";
    }
}

// Close the dropdown if the user clicks anywhere outside of it
window.onclick = function (event) {
    // Check if the click was outside the dropdown button or content
    if (!event.target.closest(".profile-dropdown")) {
        var dropdowns = document.querySelectorAll('.profile-dropdown-content');
        for (var i = 0; i < dropdowns.length; i++) {
            dropdowns[i].style.display = 'none';
        }
    }
};

// Debugging: Add console messages to track actions
console.log("JavaScript is loaded and running!");

document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM fully loaded and parsed");
    var dropdownButton = document.querySelector(".profile-dropdown button");
    if (dropdownButton) {
        console.log("Dropdown button found!");
        dropdownButton.addEventListener("click", toggleDropdown);
    } else {
        console.error("Dropdown button not found. Check your HTML structure!");
    }
});

