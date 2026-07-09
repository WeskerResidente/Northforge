const initInventoryManagement = () => {
	const modal = document.querySelector('#inventory-product-modal');
	const addButton = document.querySelector('[data-inventory-product-add]');
	const productsBody = document.querySelector('[data-inventory-products-body]');
	const rowTemplate = document.querySelector('#inventory-product-row-template');
	const tabButtons = document.querySelectorAll('[data-inventory-tab]');
	const panels = document.querySelectorAll('[data-inventory-panel]');
	const movementToggles = document.querySelectorAll('[data-inventory-movement-toggle]');

	if (!modal || modal.dataset.inventoryReady === 'true') {
		return;
	}

	const fields = {
		name: modal.querySelector('[data-inventory-modal-field="name"]'),
		reference: modal.querySelector('[data-inventory-modal-field="reference"]'),
		description: modal.querySelector('[data-inventory-modal-field="description"]'),
		categories: modal.querySelector('[data-inventory-modal-field="categories"]'),
		condition: modal.querySelector('[data-inventory-modal-field="condition"]'),
		unitPrice: modal.querySelector('[data-inventory-modal-field="unitPrice"]'),
		quantity: modal.querySelector('[data-inventory-modal-field="quantity"]'),
	};
	const form = modal.querySelector('form');
	const saveButton = modal.querySelector('.inventory-modal__save');
	const deleteButton = modal.querySelector('[data-inventory-modal-delete]');
	const feedback = modal.querySelector('[data-inventory-modal-feedback]');
	const modalTitle = modal.querySelector('[data-inventory-modal-title]');
	const modalSubtitle = modal.querySelector('[data-inventory-modal-subtitle]');
	const createUrl = form?.dataset.createUrl || '';
	let currentRow = null;
	let modalMode = 'edit';

	const setSelectValue = (select, value) => {
		if (!select) {
			return;
		}

		const existingOption = Array.from(select.options).find((option) => option.value === value);

		if (!existingOption && value !== '') {
			select.add(new Option(value, value));
		}

		select.value = value;
	};

	const resetFeedback = () => {
		feedback.textContent = '';
		feedback.classList.remove('inventory-modal__feedback--success');
	};

	const showModal = () => {
		modal.classList.add('is-open');
		modal.setAttribute('aria-hidden', 'false');
		document.body.classList.add('inventory-modal-open');
		resetFeedback();
		fields.name.focus();
	};

	const openEditModal = (row) => {
		modalMode = 'edit';
		currentRow = row;
		modalTitle.textContent = 'Modifier le produit';
		modalSubtitle.textContent = 'Modifiez les informations du produit ou supprimez-le';
		saveButton.textContent = 'Enregistrer';
		deleteButton.hidden = false;
		fields.name.value = row.dataset.name || '';
		fields.reference.value = row.dataset.reference || '';
		fields.description.value = row.dataset.description || '';
		fields.unitPrice.value = row.dataset.unitPrice || '';
		fields.quantity.value = row.dataset.quantity || '';
		setSelectValue(fields.categories, (row.dataset.categories || '').split(',')[0].trim());
		setSelectValue(fields.condition, row.dataset.condition || '');
		showModal();
	};

	const openCreateModal = () => {
		modalMode = 'create';
		currentRow = null;
		modalTitle.textContent = 'Ajouter un nouveau produit';
		modalSubtitle.textContent = 'Saisissez les informations du produit manuellement';
		saveButton.textContent = 'Ajouter';
		deleteButton.hidden = true;
		fields.name.value = '';
		fields.reference.value = 'Auto';
		fields.description.value = '';
		fields.unitPrice.value = '';
		fields.quantity.value = '';
		setSelectValue(fields.categories, 'Emballage');
		setSelectValue(fields.condition, 'excellent');
		showModal();
	};

	const closeModal = () => {
		modal.classList.remove('is-open');
		modal.setAttribute('aria-hidden', 'true');
		document.body.classList.remove('inventory-modal-open');
		currentRow = null;
		modalMode = 'edit';
	};

	const formatConditionClass = (condition) => `inventory-quality inventory-quality--${condition.toLowerCase().replace(/\s+/g, '-')}`;

	const updateTotals = (totals) => {
		const totalProducts = document.querySelector('[data-inventory-total-products]');
		const totalMovements = document.querySelector('[data-inventory-total-movements]');
		const totalUnits = document.querySelector('[data-inventory-total-units]');
		const totalValue = document.querySelector('[data-inventory-total-value]');
		const listCount = document.querySelector('[data-inventory-list-count]');

		if (totalProducts) totalProducts.textContent = totals.products;
		if (totalMovements) totalMovements.textContent = totals.products;
		if (totalUnits) totalUnits.textContent = totals.units;
		if (totalValue) totalValue.textContent = totals.stockValue;
		if (listCount) listCount.textContent = totals.products;
	};

	const fillRow = (row, product) => {
		row.dataset.name = product.name;
		row.dataset.reference = product.reference;
		row.dataset.description = product.description;
		row.dataset.categories = product.categories.join(', ');
		row.dataset.condition = product.condition;
		row.dataset.unitPrice = product.unitPrice;
		row.dataset.quantity = product.quantity;
		row.dataset.updateUrl = product.updateUrl;

		row.querySelector('[data-product-reference]').textContent = product.reference;
		row.querySelector('[data-product-name]').textContent = product.name;
		row.querySelector('[data-product-description]').textContent = product.description;
		row.querySelector('[data-product-categories]').innerHTML = product.categories.join('<br>');
		row.querySelector('[data-product-quantity]').textContent = product.quantity;
		row.querySelector('[data-product-unit-price]').textContent = product.unitPriceFormatted;
		row.querySelector('[data-product-stock-value]').textContent = product.stockValue;

		const condition = row.querySelector('[data-product-condition]');
		condition.textContent = product.condition;
		condition.className = formatConditionClass(product.condition);
	};

	const bindRow = (row) => {
		row.addEventListener('click', () => openEditModal(row));
		row.addEventListener('keydown', (event) => {
			if (event.key === 'Enter' || event.key === ' ') {
				event.preventDefault();
				openEditModal(row);
			}
		});
	};

	const appendRow = (product) => {
		if (!rowTemplate || !productsBody) {
			window.location.reload();
			return;
		}

		const row = rowTemplate.content.firstElementChild.cloneNode(true);
		fillRow(row, product);
		bindRow(row);
		productsBody.appendChild(row);
	};

	const getSubmitUrl = () => (modalMode === 'create' ? createUrl : currentRow?.dataset.updateUrl);

	const saveCurrentProduct = async () => {
		const submitUrl = getSubmitUrl();

		if (!submitUrl) {
			feedback.textContent = 'Impossible de sauvegarder ce produit.';
			return;
		}

		saveButton.disabled = true;
		feedback.textContent = 'Enregistrement...';
		feedback.classList.remove('inventory-modal__feedback--success');

		try {
			const response = await fetch(submitUrl, {
				method: 'POST',
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					name: fields.name.value,
					description: fields.description.value,
					category: fields.categories.value,
					condition: fields.condition.value,
					unitPrice: fields.unitPrice.value,
					quantity: fields.quantity.value,
				}),
			});
			const data = await response.json();

			if (!response.ok || !data.success) {
				throw new Error(data.message || 'Impossible de sauvegarder ce produit.');
			}

			if (modalMode === 'create') {
				appendRow(data.product);
			} else {
				fillRow(currentRow, data.product);
			}

			updateTotals(data.totals);
			feedback.textContent = modalMode === 'create' ? 'Produit ajoute.' : 'Produit mis a jour.';
			feedback.classList.add('inventory-modal__feedback--success');
			window.setTimeout(closeModal, 450);
		} catch (error) {
			feedback.textContent = error.message || 'Impossible de sauvegarder ce produit.';
		} finally {
			saveButton.disabled = false;
		}
	};

	document.querySelectorAll('[data-inventory-product-row]').forEach(bindRow);
	addButton?.addEventListener('click', openCreateModal);

	tabButtons.forEach((button) => {
		button.addEventListener('click', () => {
			const targetPanel = button.dataset.inventoryTab;

			tabButtons.forEach((tabButton) => {
				tabButton.classList.toggle('is-active', tabButton === button);
			});

			panels.forEach((panel) => {
				panel.hidden = panel.dataset.inventoryPanel !== targetPanel;
			});
		});
	});

	movementToggles.forEach((toggle) => {
		toggle.addEventListener('click', () => {
			const movement = toggle.closest('[data-inventory-movement]');
			const details = movement?.querySelector('[data-inventory-movement-details]');
			const isOpen = !movement?.classList.contains('is-open');

			movement?.classList.toggle('is-open', isOpen);
			toggle.setAttribute('aria-expanded', String(isOpen));
			toggle.querySelector('span').textContent = isOpen ? 'v' : '>';

			if (details) {
				details.hidden = !isOpen;
			}
		});
	});

	modal.querySelectorAll('[data-inventory-modal-close]').forEach((button) => {
		button.addEventListener('click', closeModal);
	});

	form?.addEventListener('submit', (event) => {
		event.preventDefault();
		saveCurrentProduct();
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && modal.classList.contains('is-open')) {
			closeModal();
		}
	});

	modal.dataset.inventoryReady = 'true';
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initInventoryManagement);
} else {
	initInventoryManagement();
}

document.addEventListener('turbo:load', initInventoryManagement);
