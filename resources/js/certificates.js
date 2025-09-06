

// js para preencher automaticamente os campos do formulario do email na blade participants/view-edit-participants.blade.php
document.addEventListener("DOMContentLoaded", () => {
    const emailButtons = document.querySelectorAll("[data-cert-email]");

    if (emailButtons.length) {
        emailButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                const name = btn.dataset.participantName;
                const email = btn.dataset.participantEmail;
                const course = "Curso X"; // or fetch dynamically
                const date = new Date().toISOString().slice(0, 10); // today

                const form = document.getElementById("certificate-email-form");
                form.querySelector("[name='name']").value = name;
                form.querySelector("[name='email']").value = email;
                form.querySelector("[name='course']").value = course;
                form.querySelector("[name='date']").value = date;

                form.submit();
            });
        });
    }
});



// js para preencher automaticamente os campos do formulario do email com os valores dos campos do formulario do certificado (na blade certificates/custom.blade.php)
document.addEventListener("DOMContentLoaded", () => {
    const downloadForm = document.querySelector('form[action*="certificates.download.custom"]');
    const emailForm = document.querySelector('form[action*="certificates.send.custom"]');

    if (downloadForm && emailForm) {
        // Sync all inputs by their name
        downloadForm.querySelectorAll("input").forEach(input => {
            input.addEventListener("input", () => {
                const target = emailForm.querySelector(`[name="${input.name}"]`);
                if (target) {
                    target.value = input.value;
                }
            });
        });
    }
});




