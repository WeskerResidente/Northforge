const initRegisterForm = () => {
	const form = document.querySelector('#registration-form');

	if (!form || form.dataset.registerReady === 'true') {
		return;
	}

	const emailField = form.querySelector('[name="register[email]"], input[type="email"]');
	const siretField = form.querySelector('[name="register[siret]"]');
	const passwordField = form.querySelector('[name="register[plainPassword][first]"]');
	const confirmField = form.querySelector('[name="register[plainPassword][second]"]');
	const tagFields = form.querySelectorAll('[name="register[tags][]"]');
	const submitButton = form.querySelector('#register-btn');
	const checkEmailUrl = form.dataset.checkEmailUrl || '/register/check-email';
	const checkSiretUrl = form.dataset.checkSiretUrl || '/register/check-siret';

	if (!emailField || !siretField || !passwordField || !confirmField || !submitButton) {
		return;
	}

	const availability = {
		email: {
			checking: false,
			message: '',
			valid: null,
			value: '',
		},
		siret: {
			checking: false,
			message: '',
			valid: null,
			value: '',
		},
	};

	const getFeedbackBox = (field) => {
		let box = field.closest('.register-form__field').querySelector('.form-error');

		if (!box) {
			box = document.createElement('div');
			box.className = 'form-error';
			field.closest('.register-form__field').appendChild(box);
		}

		return box;
	};

	const setFieldFeedback = (field, message = '', type = 'error') => {
		const box = getFeedbackBox(field);
		const hasError = message !== '' && type === 'error';
		const hasSuccess = message !== '' && type === 'success';

		box.textContent = message;
		box.classList.toggle('form-error--info', message !== '' && type === 'info');
		box.classList.toggle('form-error--success', hasSuccess);
		field.classList.toggle('has-error', hasError);
		field.classList.toggle('has-success', hasSuccess);
	};

	const debounce = (callback, delay = 450) => {
		let timeoutId;

		return (...args) => {
			window.clearTimeout(timeoutId);
			timeoutId = window.setTimeout(() => callback(...args), delay);
		};
	};

	const setStrength = (score) => {
		const strength = form.querySelector('.password-strength');
		const meter = form.querySelector('.strength-meter');
		const fill = meter?.querySelector('.strength-meter__fill');

		if (!strength || !meter || !fill) {
			return;
		}

		if (score === 0) {
			strength.textContent = '';
			meter.hidden = false;
			meter.dataset.empty = 'true';
			fill.style.width = '0';
			return;
		}

		meter.hidden = false;
		meter.dataset.empty = 'false';

		if (score <= 2) {
			strength.textContent = 'Mot de passe : faible';
			fill.style.width = '33%';
			fill.dataset.level = 'weak';
			return;
		}

		if (score <= 4) {
			strength.textContent = 'Mot de passe : moyen';
			fill.style.width = '66%';
			fill.dataset.level = 'medium';
			return;
		}

		strength.textContent = 'Mot de passe : fort';
		fill.style.width = '100%';
		fill.dataset.level = 'strong';
	};

	const isEmailFormatValid = () => {
		const value = emailField.value.trim();

		return value !== '' && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
	};

	const isSiretFormatValid = () => {
		const value = siretField.value.trim();

		return value !== '' && /^\d{14}$/.test(value);
	};

	const updateEmailFeedback = () => {
		const value = emailField.value.trim();

		if (value === '') {
			setFieldFeedback(emailField);
			return false;
		}

		if (!isEmailFormatValid()) {
			setFieldFeedback(emailField, 'Format email invalide.');
			return false;
		}

		if (availability.email.checking) {
			setFieldFeedback(emailField, 'Vérification de l’email...', 'info');
			return false;
		}

		if (availability.email.valid === false && availability.email.value === value) {
			setFieldFeedback(emailField, availability.email.message);
			return false;
		}

		if (availability.email.valid === true && availability.email.value === value) {
			setFieldFeedback(emailField, 'Email valide et disponible.', 'success');
			return true;
		}

		setFieldFeedback(emailField);
		return true;
	};

	const updateSiretFeedback = () => {
		const value = siretField.value.trim();

		if (value === '') {
			setFieldFeedback(siretField);
			return false;
		}

		if (!isSiretFormatValid()) {
			setFieldFeedback(siretField, 'Le SIRET doit contenir 14 chiffres.');
			return false;
		}

		if (availability.siret.checking) {
			setFieldFeedback(siretField, 'Vérification du SIRET...', 'info');
			return false;
		}

		if (availability.siret.valid === false && availability.siret.value === value) {
			setFieldFeedback(siretField, availability.siret.message);
			return false;
		}

		if (availability.siret.valid === true && availability.siret.value === value) {
			setFieldFeedback(siretField, 'SIRET valide et disponible.', 'success');
			return true;
		}

		setFieldFeedback(siretField);
		return true;
	};

	const validatePassword = () => {
		const password = passwordField.value;
		const confirm = confirmField.value;
		let score = 0;
		const errors = [];

		if (password.length >= 8) score += 1;
		if (/[A-Z]/.test(password)) score += 1;
		if (/[a-z]/.test(password)) score += 1;
		if (/\d/.test(password)) score += 1;
		if (/[^A-Za-z0-9]/.test(password)) score += 1;

		if (password.length > 0 && password.length < 8) {
			errors.push('Minimum 8 caractères.');
		}

		setStrength(score);
		setFieldFeedback(passwordField, errors.join(' '));

		if (confirm && password !== confirm) {
			setFieldFeedback(confirmField, 'Les mots de passe ne correspondent pas.');
			return false;
		}

		setFieldFeedback(confirmField);

		return password.length >= 8 && password === confirm;
	};

	const validateTags = () => {
		if (tagFields.length === 0) {
			return true;
		}

		return Array.from(tagFields).some((field) => field.checked);
	};

	const validateForm = () => {
		const requiredFields = form.querySelectorAll('input[required], textarea[required]');
		const requiredFieldsFilled = Array.from(requiredFields).every((field) => field.value.trim() !== '');
		const isEmailValid = updateEmailFeedback();
		const isSiretValid = updateSiretFeedback();
		const isPasswordValid = validatePassword();
		const areTagsValid = validateTags();
		const isValid = requiredFieldsFilled
			&& isEmailValid
			&& isSiretValid
			&& isPasswordValid
			&& areTagsValid;

		submitButton.disabled = !isValid;
		submitButton.classList.toggle('is-disabled', !isValid);
	};

	const checkAvailability = async (fieldName, field, endpoint, parameterName, usedMessage) => {
		const value = field.value.trim();
		const state = availability[fieldName];

		state.value = value;
		state.valid = null;

		if ((fieldName === 'email' && !isEmailFormatValid()) || (fieldName === 'siret' && !isSiretFormatValid())) {
			state.checking = false;
			validateForm();
			return;
		}

		state.checking = true;
		validateForm();

		try {
			const response = await fetch(`${endpoint}?${parameterName}=${encodeURIComponent(value)}`, {
				headers: {
					'Accept': 'application/json',
				},
			});
			const data = await response.json();

			if (field.value.trim() !== value) {
				return;
			}

			state.valid = Boolean(data.valid && data.available);
			state.message = state.valid ? '' : usedMessage;
		} catch (error) {
			if (field.value.trim() === value) {
				state.valid = null;
				state.message = '';
			}
		} finally {
			if (field.value.trim() === value) {
				state.checking = false;
				validateForm();
			}
		}
	};

	const checkEmail = debounce(() => {
		checkAvailability('email', emailField, checkEmailUrl, 'email', 'Cet email est déjà utilisé.');
	});

	const checkSiret = debounce(() => {
		checkAvailability('siret', siretField, checkSiretUrl, 'siret', 'Ce numéro de SIRET est déjà utilisé.');
	});

	form.addEventListener('input', validateForm);
	form.addEventListener('change', validateForm);

	emailField.addEventListener('input', () => {
		availability.email.valid = null;
		availability.email.value = emailField.value.trim();
		availability.email.checking = isEmailFormatValid();
		validateForm();
		checkEmail();
	});
	emailField.addEventListener('blur', () => {
		checkAvailability('email', emailField, checkEmailUrl, 'email', 'Cet email est déjà utilisé.');
	});

	siretField.addEventListener('input', () => {
		availability.siret.valid = null;
		availability.siret.value = siretField.value.trim();
		availability.siret.checking = isSiretFormatValid();
		validateForm();
		checkSiret();
	});
	siretField.addEventListener('blur', () => {
		checkAvailability('siret', siretField, checkSiretUrl, 'siret', 'Ce numéro de SIRET est déjà utilisé.');
	});

	passwordField.addEventListener('input', validatePassword);
	confirmField.addEventListener('input', validatePassword);

	validateForm();
	form.dataset.registerReady = 'true';
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initRegisterForm);
} else {
	initRegisterForm();
}

document.addEventListener('turbo:load', initRegisterForm);
