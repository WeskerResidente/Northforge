const initFeaturedCardsModal = () => {
	const featuredSection = document.querySelector('.home-featured');

	if (!featuredSection || featuredSection.dataset.modalReady === 'true') {
		return;
	}

	const cards = featuredSection.querySelectorAll('.home-featured__card');
	let sourceCard = null;
	let modal = null;
	let closeTimeout = null;
	let modalWasHovered = false;

	const removeModal = () => {
		window.clearTimeout(closeTimeout);
		sourceCard = null;
		modalWasHovered = false;

		if (modal) {
			modal.remove();
			modal = null;
		}

		document.body.classList.remove('has-home-featured-modal');
	};

	const queueRemoveModal = () => {
		window.clearTimeout(closeTimeout);
		closeTimeout = window.setTimeout(removeModal, 140);
	};

	const keepModalOpen = () => {
		window.clearTimeout(closeTimeout);
	};

	const buildModal = (card) => {
		const overlay = document.createElement('div');
		const modalCard = card.cloneNode(true);
		const title = modalCard.querySelector('.home-featured__card-title');
		const description = modalCard.querySelector('.home-featured__description');
		const proposedBy = modalCard.querySelector('.home-featured__meta > div:last-child .home-featured__value');

		overlay.className = 'home-featured-modal';
		modalCard.classList.add('home-featured-modal__card');
		modalCard.removeAttribute('style');

		if (title && card.dataset.modalTitle) {
			title.textContent = card.dataset.modalTitle;
		}

		if (description && card.dataset.modalDescription) {
			description.textContent = card.dataset.modalDescription;
		}

		if (proposedBy && card.dataset.modalProposedBy) {
			proposedBy.textContent = card.dataset.modalProposedBy;
		}

		overlay.appendChild(modalCard);
		document.body.appendChild(overlay);

		overlay.addEventListener('click', (event) => {
			if (event.target === overlay) {
				removeModal();
			}
		});

		modalCard.addEventListener('mouseenter', () => {
			modalWasHovered = true;
			keepModalOpen();
		});

		modalCard.addEventListener('mouseleave', () => {
			if (modalWasHovered) {
				queueRemoveModal();
			}
		});

		return overlay;
	};

	const openModal = (card) => {
		keepModalOpen();

		if (sourceCard === card && modal) {
			return;
		}

		if (modal) {
			modal.remove();
		}

		sourceCard = card;
		modal = buildModal(card);
		document.body.classList.add('has-home-featured-modal');
	};

	cards.forEach((card) => {
		card.addEventListener('mouseenter', () => openModal(card));
		card.addEventListener('focusin', () => openModal(card));
		card.addEventListener('focusout', (event) => {
			if (!card.contains(event.relatedTarget)) {
				queueRemoveModal();
			}
		});
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			removeModal();
		}
	});

	featuredSection.dataset.modalReady = 'true';
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initFeaturedCardsModal);
} else {
	initFeaturedCardsModal();
}

document.addEventListener('turbo:load', initFeaturedCardsModal);
