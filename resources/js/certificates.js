

// js para preencher automaticamente os campos do formulario do email na blade participants/view-edit-participants.blade.php
document.addEventListener("DOMContentLoaded", () => {
    const emailButtons = document.querySelectorAll("[data-cert-email]");

    if (emailButtons.length) {
        emailButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                const participantId = btn.dataset.participantId;
                const eventId = btn.dataset.eventId;

                const form = document.getElementById("certificate-email-form");
                form.querySelector("[name='participant_id']").value = participantId;
                form.querySelector("[name='event_id']").value = eventId;

                form.submit();
            });
        });
    }
});

// js para criar o menu dropdown dinamico de participantes ligados a um evento já selecionado na view custom.blade.php
document.addEventListener('DOMContentLoaded', () => {
    const eventSelect = document.getElementById('event_id');
    const participantSelect = document.getElementById('participant_id');

    // Parse the JSON from the data attribute
    const participantsByEvent = JSON.parse(eventSelect.dataset.participants);

    eventSelect.addEventListener('change', () => {
        const eventId = eventSelect.value;

        // Reset participants dropdown
        participantSelect.innerHTML = '<option value="">Selecione um participante</option>';

        if (!eventId || !participantsByEvent[eventId]) return;

        participantsByEvent[eventId].forEach(p => {
            const option = document.createElement('option');
            option.value = p.id;
            option.textContent = p.name;
            participantSelect.appendChild(option);
        });
    });
});







