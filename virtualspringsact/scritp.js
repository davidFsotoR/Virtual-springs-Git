            // Datos de espacios ocupados
            const ocupados = [1, 3, 7]; // Espacios ocupados, los números corresponden a los IDs de los espacios
    
            // Generar espacios de parqueo
            const parkingLot = document.getElementById('parkingLot');
            const totalSpaces = 10; // Número total de espacios
            let selectedSpace = null;
    
            for (let i = 1; i <= totalSpaces; i++) {
                const space = document.createElement('div');
                space.classList.add('parking-space');
                space.textContent = `A ${i}`;
                space.dataset.spaceId = i;
    
                // Marcar como ocupado si está en la lista
                if (ocupados.includes(i)) {
                    space.classList.add('occupied');
                    space.setAttribute('data-occupied', 'true');
                }
    
                // Agregar evento de clic para seleccionar espacio
                space.addEventListener('click', function() {
                    if (this.getAttribute('data-occupied') === 'true') return; // No seleccionar si está ocupado
    
                    // Deseleccionar espacio anterior
                    if (selectedSpace) {
                        selectedSpace.classList.remove('selected');
                    }
    
                    // Seleccionar nuevo espacio
                    this.classList.add('selected');
                    selectedSpace = this;
    
                    // Asignar el valor del espacio seleccionado al campo oculto
                    document.getElementById('espacioSeleccionado').value = this.dataset.spaceId;
                });
    
                parkingLot.appendChild(space);
            }