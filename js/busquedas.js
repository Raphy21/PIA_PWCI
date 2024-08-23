const data = [
    { title: "Producto 1", description: "Descripción del producto 1", image: "recursos/camisa.webp" },
    { title: "Producto 2", description: "Descripción del producto 2", image: "recursos/zapato.png" },
    { title: "Producto 3", description: "Descripción del producto 3", image: "recursos/celular.jpg" },
    { title: "Producto 4", description: "Descripción del producto 4", image: "recursos/camisa.webp" },
    { title: "Producto 5", description: "Descripción del producto 5", image: "recursos/zapato.png" },
    { title: "Producto 6", description: "Descripción del producto 6", image: "recursos/celular.jpg" },
];

const cardContainer = document.getElementById('card-container');
const noResultsMessage = document.getElementById('no-results');
const cardsPerPage = 4;
let currentPage = 0;

//Evento de carga de la página
$(document).ready(function() {

    //Configuración de otros eventos
    $('#btnAtras').click(function() { previousPage(); });
    $('#btnSiguiente').click(function() { nextPage(); });

    // Llama a la función showCards con un índice de inicio específico (por ejemplo, 0)
    showCards(0);
});

function showCards(startIndex) {
    cardContainer.innerHTML = '';
    const data = JSON.parse(localStorage.getItem('respuestasArray')); // Recuperar datos del localStorage
    if (!data || data.length === 0) {
        cardContainer.innerHTML = '<p>No hay datos disponibles.</p>';
        return;
    }
    const endIndex = Math.min(startIndex + cardsPerPage, data.length);
    for (let i = startIndex; i < endIndex; i++) {
        const { id, nombre, descripcion, imagen } = data[i];
        const card = document.createElement('div');
        card.classList.add('card');
        card.setAttribute('data-id', id); // Almacenar el id en un atributo de datos
        card.innerHTML = `
            <img src="data:image/png;base64,${imagen}" alt="${nombre}">
            <h2>${nombre}</h2>
            <p>${descripcion}</p>
            <button id="enlaceProducto">Ver producto</button>
        `;
        cardContainer.appendChild(card);
    }

    // Asignar evento de clic a los botones de "Ver producto"
    document.querySelectorAll('#enlaceProducto').forEach(button => {
        button.addEventListener('click', function() {
            verProducto.call(button); // Usar call para pasar el contexto correcto
        });
    });
}

function verProducto(){
    const productoId = this.parentElement.getAttribute('data-id'); // Obtener el id del atributo de datos del contenedor padre
    localStorage.setItem('productoId', productoId);
    window.location.href = "paginaProducto.html";    
}

function showNoResultsMessage() {
    noResultsMessage.style.display = 'block';
}

function previousPage() {
    currentPage = Math.max(0, currentPage - 1);
    showCards(currentPage * cardsPerPage);
}

function nextPage() {
    currentPage = Math.min(Math.ceil(data.length / cardsPerPage) - 1, currentPage + 1);
    showCards(currentPage * cardsPerPage);
}

// Verificar si hay datos para mostrar
if (data.length === 0) {
    showNoResultsMessage();
} else {
    showCards(0);
}
