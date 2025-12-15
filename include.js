document.addEventListener("DOMContentLoaded", function () {
    // Load header
    fetch("header.html")
        .then(response => response.text())
        .then(data => {
            document.getElementById("header").innerHTML = data;

            // Set active link
            const currentPage = window.location.pathname.split("/").pop();
            document.querySelectorAll(".nav-link, .dropdown-item").forEach(link => {
                if (link.getAttribute("href") === currentPage) {
                    link.classList.add("active");
                    // Highlight parent dropdown if inside one
                    const parentDropdown = link.closest(".dropdown");
                    if (parentDropdown) {
                        const toggle = parentDropdown.querySelector(".nav-link.dropdown-toggle");
                        if (toggle) toggle.classList.add("active");
                    }
                }
            });
        });

    // Load footer
    fetch("footer.html")
        .then(response => response.text())
        .then(data => {
            document.getElementById("footer").innerHTML = data;
        });
        
});
