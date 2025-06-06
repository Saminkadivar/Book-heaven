// Graph
var ctx = document.getElementById("myChart");

var myChart = new Chart(ctx, {
    type: "line",
    data: {
        labels: [
            "Sunday",
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
        ],
        datasets: [
            {
                data: [15339, 21345, 18483, 24003, 23489, 24092, 12034],
                lineTension: 0,
                backgroundColor: "transparent",
                borderColor: "#007bff",
                borderWidth: 4,
                pointBackgroundColor: "#007bff",
            },
        ],
    },
    options: {
        scales: {
            yAxes: [
                {
                    ticks: {
                        beginAtZero: false,
                    },
                },
            ],
        },
        legend: {
            display: false,
        },
    },
});


/*log in page */

document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    const emailField = document.getElementById("email");
    const passwordField = document.getElementById("password");
    const emailError = document.getElementById("emailError");
    const passwordError = document.getElementById("passwordError");
    const togglePassword = document.querySelector(".toggle-password");

    // Email Validation
    emailField.addEventListener("input", function () {
        if (emailField.value.trim() === "") {
            emailError.classList.remove("d-none");
        } else {
            emailError.classList.add("d-none");
        }
    });

    // Password Validation
    passwordField.addEventListener("input", function () {
        if (passwordField.value.trim() === "") {
            passwordError.classList.remove("d-none");
        } else {
            passwordError.classList.add("d-none");
        }
    });

    // Form Submission Validation
    loginForm.addEventListener("submit", function (e) {
        let valid = true;

        if (emailField.value.trim() === "") {
            emailError.classList.remove("d-none");
            valid = false;
        }

        if (passwordField.value.trim() === "") {
            passwordError.classList.remove("d-none");
            valid = false;
        }

        if (!valid) {
            e.preventDefault(); // Prevent form submission if invalid
        }
    });

    // Password Toggle
    togglePassword.addEventListener("click", function () {
        if (passwordField.type === "password") {
            passwordField.type = "text";
            togglePassword.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            passwordField.type = "password";
            togglePassword.innerHTML = '<i class="fas fa-eye"></i>';
        }
    });
});
