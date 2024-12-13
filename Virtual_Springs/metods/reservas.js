document.addEventListener('DOMContentLoaded', () => {
    const parkingSpaces = document.querySelectorAll('.parking-space, .bike-parking');
    const selectedSpaceInput = document.getElementById('espacioSeleccionado');
    const selectedSpaceVisible = document.getElementById('espacioSeleccionadoVisible');

    parkingSpaces.forEach(button => {
        button.addEventListener('click', event => {
            event.preventDefault(); 

            // Quitar la selección previa
            parkingSpaces.forEach(space => {
                space.classList.remove('occupied');
                space.style.backgroundColor = ''; // Resetear colores
                space.style.color = '';
            });

            // Seleccionar el nuevo espacio
            button.classList.add('occupied');
            button.style.backgroundColor = 'black';
            button.style.color = 'white';
        });
    });
});
