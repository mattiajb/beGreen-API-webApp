document.addEventListener('DOMContentLoaded', () => {
    const cars = document.querySelectorAll('.car-card');
    const cartZone = document.getElementById('cart-zone');
    const cartList = document.getElementById('cart-items-list');
    const emptyMsg = document.getElementById('empty-msg');
    const totalPriceEl = document.getElementById('total-price');
    const requestBtn = document.getElementById('btn-request-quote'); // Potrebbe essere null se guest
    const modal = document.getElementById('quote-modal');
    const closeModal = document.querySelector('.close-modal');
    const quoteForm = document.getElementById('quote-form');

    let cartTotal = 0;

    // --- DRAG AND DROP LOGIC ---

    // 1. Configurazione elementi trascinabili (Auto)
    cars.forEach(car => {
        car.addEventListener('dragstart', (e) => {
            // Raccogli i dati dall'attributo data-car
            const carData = car.getAttribute('data-car');
            e.dataTransfer.setData('application/json', carData);
            e.dataTransfer.effectAllowed = 'copy';
            car.style.opacity = '0.5'; // Feedback visivo
        });

        car.addEventListener('dragend', () => {
            car.style.opacity = '1';
        });
    });

    // 2. Configurazione zona di rilascio (Carrello)
    if (cartZone) {
        cartZone.addEventListener('dragover', (e) => {
            e.preventDefault(); // Necessario per permettere il drop
            e.dataTransfer.dropEffect = 'copy';
            cartZone.classList.add('drag-over');
        });

        cartZone.addEventListener('dragleave', () => {
            cartZone.classList.remove('drag-over');
        });

        cartZone.addEventListener('drop', (e) => {
            e.preventDefault();
            cartZone.classList.remove('drag-over');
            
            const data = e.dataTransfer.getData('application/json');
            if (data) {
                const carObj = JSON.parse(data);
                addToCart(carObj);
            }
        });
    }

    // --- CART LOGIC ---

    function addToCart(car) {
        // Rimuovi messaggio vuoto se presente
        if (emptyMsg) emptyMsg.style.display = 'none';

        // Crea elemento HTML per il carrello
        const li = document.createElement('li');
        li.className = 'cart-item';
        
        // Formatta prezzo
        const priceNum = parseFloat(car.price);
        const formattedPrice = new Intl.NumberFormat('it-IT', { style: 'currency', currency: 'EUR' }).format(priceNum);

        li.innerHTML = `
            <img src="${car.image}" alt="${car.model}">
            <div class="cart-item-details">
                <strong>${car.brand} ${car.model}</strong><br>
                ${formattedPrice}
            </div>
            <button class="remove-btn" onclick="removeItem(this, ${priceNum})"><i class="fa-solid fa-trash"></i></button>
        `;

        cartList.appendChild(li);

        // Aggiorna totale
        updateTotal(priceNum);
    }

    // Funzione globale per essere chiamata dall'onclick inline
    window.removeItem = function(btn, price) {
        const item = btn.closest('.cart-item');
        item.remove();
        updateTotal(-price);

        // Se carrello vuoto, rimostra messaggio
        if (cartList.children.length === 0) {
            if (emptyMsg) emptyMsg.style.display = 'block';
        }
    };

    function updateTotal(amount) {
        cartTotal += amount;
        if (cartTotal < 0) cartTotal = 0; // Prevenzione errori float
        
        totalPriceEl.textContent = new Intl.NumberFormat('it-IT', { 
            style: 'currency', 
            currency: 'EUR' 
        }).format(cartTotal);
    }

    // --- MODAL & FORM LOGIC ---

    if (requestBtn) {
        requestBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (cartList.children.length === 0) {
                alert("Il carrello è vuoto! Trascina un'auto prima di chiedere un preventivo.");
                return;
            }
            modal.classList.add('active');
        });
    }

    if (closeModal) {
        closeModal.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    }

    // Chiudi cliccando fuori dal modale
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });

    if (quoteForm) {
        quoteForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Simulazione invio
            const email = document.getElementById('email').value;
            // Qui si farebbe una chiamata AJAX al server per inviare la mail
            
            // Alert richiesto
            alert(`Preventivo inviato con successo da ${email}! Ti contatteremo presto.`);
            
            modal.classList.remove('active');
            quoteForm.reset();
            
            // Opzionale: svuota carrello
            cartList.innerHTML = '';
            cartTotal = 0;
            updateTotal(0);
            if(emptyMsg) emptyMsg.style.display = 'block';
        });
    }
});