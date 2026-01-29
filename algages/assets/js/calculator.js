document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.service-checkbox');
    const totalDisplay = document.getElementById('running-total');
    const proceedBtn = document.getElementById('proceed-btn');
    const modal = document.getElementById('submission-modal');
    const closeBtn = document.querySelector('.close');
    const summaryList = document.getElementById('summary-services');
    const modalTotal = document.getElementById('modal-total');
    const servicesInput = document.getElementById('selected-services-input');
    const totalInput = document.getElementById('total-fee-input');

    function updateTotal() {
        let total = 0;
        let selectedCount = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) {
                total += parseFloat(cb.dataset.price);
                selectedCount++;
            }
        });
        totalDisplay.textContent = total.toFixed(2);
        proceedBtn.disabled = selectedCount === 0;
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateTotal);
    });

    proceedBtn.addEventListener('click', () => {
        let total = 0;
        summaryList.innerHTML = '';
        let selectedIds = [];

        checkboxes.forEach(cb => {
            if (cb.checked) {
                const title = cb.closest('.service-item').querySelector('.service-title').textContent;
                const price = parseFloat(cb.dataset.price);
                total += price;
                selectedIds.push(cb.value);

                const li = document.createElement('li');
                li.textContent = `${title} - $${price.toFixed(2)}`;
                summaryList.appendChild(li);
            }
        });

        modalTotal.textContent = total.toFixed(2);
        totalInput.value = total.toFixed(2);
        servicesInput.value = selectedIds.join(',');
        modal.style.display = 'block';
    });

    closeBtn.onclick = () => {
        modal.style.display = 'none';
    };

    window.onclick = (event) => {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };
});
