document.addEventListener('DOMContentLoaded', () => {
    const appointmentForm = document.getElementById('appointmentForm');
    const successMessage = document.getElementById('successMessage');
    const newAppointmentBtn = document.getElementById('newAppointmentBtn');
    const dateInput = document.getElementById('date');

    // Evitar que seleccionen fechas pasadas en el calendario
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);

    // Número de WhatsApp de la clínica
    const numeroWhatsApp = "529624505235"; 

    // Manejar el envío del formulario
    appointmentForm.addEventListener('submit', (e) => {
        e.preventDefault(); 

        // 1. Capturar los valores de los inputs
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const department = document.getElementById('department').value;
        const date = document.getElementById('date').value;
        const time = document.getElementById('time').value;

        // 2. Crear objeto con la nueva cita
        const nuevaCita = { name, email, phone, department, date, time };

        // 3. Guardar en el LocalStorage para que aparezca en citas.html
        let citas = JSON.parse(localStorage.getItem('citasClinica')) || [];
        citas.push(nuevaCita);
        localStorage.setItem('citasClinica', JSON.stringify(citas));

        // 4. Crear el texto del mensaje para WhatsApp
        const mensaje = `Hola, quiero agendar una cita en Clínica Dental Sonrisa:%0A` +
                        `*Nombre:* ${name}%0A` +
                        `*Correo:* ${email}%0A` +
                        `*Teléfono:* ${phone}%0A` +
                        `*Especialidad:* ${department}%0A` +
                        `*Fecha:* ${date}%0A` +
                        `*Hora:* ${time}`;

        const urlWhatsApp = `https://wa.me/${numeroWhatsApp}?text=${mensaje}`;

        // 5. Ocultar formulario y mostrar mensaje de éxito en la web
        appointmentForm.classList.add('hidden');
        successMessage.classList.remove('hidden');

        // Abrir WhatsApp automáticamente
        setTimeout(() => {
            window.open(urlWhatsApp, '_blank');
        }, 1000);
    });

    // Botón para agendar otra cita
    newAppointmentBtn.addEventListener('click', () => {
        appointmentForm.reset(); 
        successMessage.classList.add('hidden');
        appointmentForm.classList.remove('hidden');
    });
});
