function addCar() {
    const container = document.getElementById('cars-container');
    const index = container.children.length;
    const html = `
        <div class="car-row">
            <input type="text" name="cars[${index}][number]" placeholder="Номер*">
            <input type="text" name="cars[${index}][driver_name]" placeholder="Имя водителя*">
            <button type="button" onclick="removeCar(this)">Удалить</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function removeCar(button) {
    button.parentElement.remove();
}