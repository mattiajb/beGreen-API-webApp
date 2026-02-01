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
    cars.forEach(car => {
        car.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('car-id', car.getAttribute('data-id'));
            e.dataTransfer.setData('car-brand', car.getAttribute('data-brand'));
            e.dataTransfer.setData('car-model', car.getAttribute('data-model'));
            e.dataTransfer.setData('car-price', car.getAttribute('data-price'));
            e.dataTransfer.setData('car-image', car.getAttribute('data-image'));
        });
        car.addEventListener('dragend', () => {
            car.style.opacity = '1';
        });
    });
    if (cartZone) {
        cartZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
            cartZone.classList.add('drag-over');
        });

        cartZone.addEventListener('dragleave', () => {
            cartZone.classList.remove('drag-over');
        });
        cartZone.addEventListener('drop', (e) => {
            e.preventDefault();
            cartZone.classList.remove('drag-over');
            const isLogged = cartZone.getAttribute('data-logged') === 'true';
            if (!isLogged) {
                alert("Devi effettuare l'accesso per aggiungere auto al preventivo.");
                return;
            }
            const carId = e.dataTransfer.getData('car-id');
            if (carId) {
                const carObj = {
                    id: carId,
                    brand: e.dataTransfer.getData('car-brand'),
                    model: e.dataTransfer.getData('car-model'),
                    price: e.dataTransfer.getData('car-price'),
                    image: e.dataTransfer.getData('car-image')
                };
                addToCart(carObj);
            }
        });
    }

    function addToCart(car) {
        if (emptyMsg) emptyMsg.style.display = 'none';
        const li = document.createElement('li');
        li.className = 'cart-item';
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
        updateTotal(priceNum);
    }
    window.removeItem = function (btn, price) {
        const item = btn.closest('.cart-item');
        item.remove();
        updateTotal(-price);
        if (cartList.children.length === 0) {
            if (emptyMsg) emptyMsg.style.display = 'block';
        }
    };

    function updateTotal(amount) {
        cartTotal += amount;
        if (cartTotal < 0) cartTotal = 0;

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

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });

    if (quoteForm) {
        quoteForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            alert(`Richiesta inviata con successo da ${email}! Ti contatteremo al più presto.`);
            modal.classList.remove('active');
            quoteForm.reset();
            cartList.innerHTML = '';
            cartTotal = 0;
            updateTotal(0);
            if (emptyMsg) emptyMsg.style.display = 'block';
        });
    }
});