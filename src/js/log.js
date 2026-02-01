function toggleMode(mode) {
    const loginCont = document.getElementById('login-container');
    const regCont = document.getElementById('register-container');
    
    const alerts = document.querySelectorAll('.alert, .alert-error, .alert-success');
    alerts.forEach(el => el.style.display = 'none');
    
    if (mode === 'register') {
        loginCont.style.display = 'none';
        regCont.style.display = 'block';
    } else {
        loginCont.style.display = 'block';
        regCont.style.display = 'none';
    }
}
    
    function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }

    // --- VALIDAZIONE LATO CLIENT ---
    document.addEventListener("DOMContentLoaded", function() {
        // VALIDAZIONE LOGIN
        const loginForm = document.getElementById('login-form');
        if (loginForm) {
            loginForm.addEventListener("submit", function(e) {
                const user = document.getElementById('l-user').value.trim();
                const pass = document.getElementById('l-pass').value;
                if (user === "" || pass === "") {
                    e.preventDefault();
                    alert("Inserisci username e password per accedere.");
                }
            });
        }
        // VALIDAZIONE REGISTRAZIONE
        const regForm = document.getElementById('register-form');
        if (regForm) {
            regForm.addEventListener("submit", function(e) {
                const user = document.getElementById('r-user').value.trim();
                const email = document.getElementById('r-email').value.trim();
                const pass = document.getElementById('r-pass').value;
                const conf = document.getElementById('r-conf').value;
                let errors = [];
                if (user.length < 3) errors.push("L'username deve avere almeno 3 caratteri.");
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) errors.push("Inserisci un indirizzo email valido.");
                if (pass.length < 6) errors.push("La password deve essere di almeno 6 caratteri.");
                if (!/[A-Z]/.test(pass)) errors.push("La password deve contenere almeno una lettera MAIUSCOLA.");
                if (!/[^a-zA-Z0-9]/.test(pass)) errors.push("La password deve contenere almeno un carattere speciale (es. ! @ #).");
                if (pass !== conf) errors.push("Le due password non coincidono.");
                if (errors.length > 0) {
                    e.preventDefault();
                    alert("Impossibile registrarsi:\n- " + errors.join("\n- "));
                }
            });
        }
    });