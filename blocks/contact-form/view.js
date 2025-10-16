const settings = window.mcdContactFormSettings || {};
const endpoint = settings.endpoint;
const formNonce = settings.nonce || '';
const successEvent = settings.successEvent || 'mcd_contact_success';
const errorEvent = settings.errorEvent || 'mcd_contact_error';

const sendAnalyticsEvent = ( eventName, detail = {} ) => {
	if ( window.dataLayer && typeof window.dataLayer.push === 'function' ) {
		window.dataLayer.push( { event: eventName, ...detail } );
	}

	if ( typeof window.gtag === 'function' ) {
		window.gtag( 'event', eventName, detail );
	}
};

const REQUIRED_FIELD_CONFIG = {
	name: {
		message: 'Add your name so we know who to address.',
	},
	email: {
		message: 'Share a valid email so we can reply with your estimate.',
	},
	goals: {
		message:
			'Tell us what to launch or improve so we can scope it correctly.',
	},
};

const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

const SECTION_REQUIREMENTS = {
	contact: [ 'name', 'email' ],
	project: [ 'goals' ],
	timeline: [],
};

const getFieldWrapper = ( field ) =>
	field ? field.closest( '.mcd-contact-form__field' ) : null;

const getErrorContainer = ( field ) => {
	const wrapper = getFieldWrapper( field );
	return wrapper ? wrapper.querySelector( '.mcd-contact-form__error' ) : null;
};

const setFieldError = ( field, message ) => {
	if ( ! field ) {
		return;
	}

	const wrapper = getFieldWrapper( field );
	if ( wrapper ) {
		wrapper.classList.add( 'mcd-contact-form__field--invalid' );
	}

	field.setAttribute( 'aria-invalid', 'true' );

	const errorNode = getErrorContainer( field );
	if ( errorNode ) {
		errorNode.textContent = message;
	}
};

const clearFieldError = ( field ) => {
	if ( ! field ) {
		return;
	}

	const wrapper = getFieldWrapper( field );
	if ( wrapper ) {
		wrapper.classList.remove( 'mcd-contact-form__field--invalid' );
	}

	field.removeAttribute( 'aria-invalid' );

	const errorNode = getErrorContainer( field );
	if ( errorNode ) {
		errorNode.textContent = '';
	}
};

const clearFormErrors = ( form ) => {
	form.querySelectorAll( '.mcd-contact-form__field--invalid' ).forEach(
		( wrapper ) => {
			wrapper.classList.remove( 'mcd-contact-form__field--invalid' );
		}
	);

	form.querySelectorAll(
		'input[aria-invalid], textarea[aria-invalid]'
	).forEach( ( input ) => {
		input.removeAttribute( 'aria-invalid' );
	} );

	form.querySelectorAll( '.mcd-contact-form__error' ).forEach(
		( errorNode ) => {
			errorNode.textContent = '';
		}
	);
};

const updateSubmitState = ( form ) => {
	const button = form.querySelector( 'button[type="submit"]' );
	if ( ! button ) {
		return;
	}

	if ( form.classList.contains( 'is-loading' ) ) {
		button.disabled = true;
		return;
	}

	const requiredFields = Object.keys( REQUIRED_FIELD_CONFIG );
	const isComplete = requiredFields.every( ( name ) => {
		const field = form.querySelector( `[name="${ name }"]` );
		if ( ! field ) {
			return false;
		}
		const value =
			typeof field.value === 'string'
				? field.value.trim()
				: field.value;

		if ( ! value ) {
			return false;
		}

		if ( name === 'email' && ! EMAIL_REGEX.test( value ) ) {
			return false;
		}

		return true;
	} );

	button.disabled = ! isComplete;
};

const initSectionProgress = ( form ) => {
	const sections = Array.from(
		form.querySelectorAll( '.mcd-contact-form__section[data-section]' )
	);
	const steps = Array.from(
		form.querySelectorAll(
			'.mcd-contact-form__progress-step[data-section-target]'
		)
	);

	if ( ! sections.length || ! steps.length ) {
		return {
			markAllComplete: () => {},
			reset: () => {},
			refresh: () => {},
		};
	}

	const getSectionKey = ( section ) =>
		( section && section.dataset && section.dataset.section ) || '';

	const getInputsForSection = ( section ) =>
		Array.from(
			section.querySelectorAll( 'input, textarea, select' )
		);

	const hasInputValue = ( input ) => {
		if ( input.type === 'checkbox' || input.type === 'radio' ) {
			return !! input.checked;
		}

		if ( typeof input.value === 'string' ) {
			return input.value.trim() !== '';
		}

		return !! input.value;
	};

	const isSectionStarted = ( section ) =>
		getInputsForSection( section ).some( hasInputValue );

	const isSectionComplete = ( section ) => {
		const key = getSectionKey( section );
		const requiredFields = SECTION_REQUIREMENTS[ key ] || [];

		if ( ! requiredFields.length ) {
			return isSectionStarted( section );
		}

		return requiredFields.every( ( name ) => {
			const field = form.querySelector( `[name="${ name }"]` );
			if ( ! field ) {
				return false;
			}
			const result = validateFieldValue( field );
			return !! result.valid;
		} );
	};

	const findStep = ( section ) => {
		if ( ! section || ! section.dataset ) {
			return null;
		}

		return (
			steps.find(
				( step ) =>
					step.dataset.sectionTarget === section.dataset.section
			) || null
		);
	};

	let activeIndex = -1;

	const refreshStates = () => {
		const statuses = sections.map( ( sectionNode ) => {
			const step = findStep( sectionNode );
			return {
				sectionNode,
				step,
				started: isSectionStarted( sectionNode ),
				complete: isSectionComplete( sectionNode ),
			};
		} );

		statuses.forEach( ( state, index ) => {
			const isActive = index === activeIndex;
			const isComplete = state.complete;
			const started = state.started && ! isComplete;
			const previousComplete =
				index === 0
					? true
					: statuses
							.slice( 0, index )
							.every( ( prevState ) => prevState.complete );
			const isReady =
				index === 0
					? isActive || state.started
					: previousComplete && ! isComplete;
			const readyAndIdle = isReady && ! isActive && ! isComplete;

			if ( state.sectionNode ) {
				state.sectionNode.classList.toggle( 'is-active', isActive );
				state.sectionNode.classList.toggle(
					'is-complete',
					isComplete
				);
				state.sectionNode.classList.toggle(
					'is-started',
					started && ! isActive
				);
				state.sectionNode.classList.toggle(
					'is-ready',
					readyAndIdle
				);
			}

			if ( state.step ) {
				state.step.classList.toggle( 'is-active', isActive );
				state.step.classList.toggle( 'is-complete', isComplete );
				state.step.classList.toggle(
					'is-started',
					started && ! isActive
				);
				state.step.classList.toggle( 'is-ready', readyAndIdle );
			}
		} );
	};

	const activateSection = ( section ) => {
		if ( ! section ) {
			activeIndex = -1;
			refreshStates();
			return;
		}

		const index = sections.indexOf( section );
		activeIndex = index >= 0 ? index : -1;
		refreshStates();
	};

	refreshStates();

	form.addEventListener( 'focusin', ( event ) => {
		const section = event.target.closest( '.mcd-contact-form__section' );
		if ( section && sections.includes( section ) ) {
			activateSection( section );
		}
	} );

	return {
		markAllComplete: () => {
			steps.forEach( ( step ) => {
				step.classList.add( 'is-complete' );
				step.classList.remove( 'is-active', 'is-ready', 'is-started' );
			} );
			sections.forEach( ( sectionNode ) => {
				sectionNode.classList.add( 'is-complete' );
				sectionNode.classList.remove(
					'is-active',
					'is-ready',
					'is-started'
				);
			} );
		},
		reset: () => {
			activeIndex = -1;
			refreshStates();
		},
		refresh: refreshStates,
	};
};

const serializeForm = ( form ) => {
	const formData = new FormData( form );
	const payload = {};

	formData.forEach( ( rawValue, key ) => {
		const value = typeof rawValue === 'string' ? rawValue.trim() : rawValue;

		if ( key.endsWith( '[]' ) ) {
			const normalizedKey = key.slice( 0, -2 );
			if ( ! Array.isArray( payload[ normalizedKey ] ) ) {
				payload[ normalizedKey ] = [];
			}
			if ( value !== '' ) {
				payload[ normalizedKey ].push( value );
			}
			return;
		}

		if ( value === 'on' ) {
			payload[ key ] = true;
			return;
		}

		payload[ key ] = value;
	} );

	return payload;
};

const validateFieldValue = ( field ) => {
	if ( ! field ) {
		return { valid: true };
	}

	const name = field.getAttribute( 'name' );
	const value =
		typeof field.value === 'string' ? field.value.trim() : field.value;

	if ( ! value ) {
		const fallback =
			REQUIRED_FIELD_CONFIG[ name ]?.message || 'This field is required.';
		return { valid: false, message: fallback };
	}

	if ( field.type === 'email' && value && ! EMAIL_REGEX.test( value ) ) {
		return {
			valid: false,
			message: 'Use a valid email address (e.g., name@business.com).',
		};
	}

	return { valid: true };
};

const validateForm = ( form ) => {
	let firstInvalidField = null;

	Object.keys( REQUIRED_FIELD_CONFIG ).forEach( ( name ) => {
		const field = form.querySelector( `[name="${ name }"]` );
		if ( ! field ) {
			return;
		}

		const result = validateFieldValue( field );
		if ( result.valid ) {
			clearFieldError( field );
			return;
		}

		setFieldError( field, result.message );
		if ( ! firstInvalidField ) {
			firstInvalidField = field;
		}
	} );

	if ( firstInvalidField ) {
		try {
			firstInvalidField.focus( { preventScroll: true } );
		} catch ( error ) {
			firstInvalidField.focus();
		}

		return {
			valid: false,
			message: 'Please fix the highlighted fields before sending.',
		};
	}

	return { valid: true };
};

const resetFormState = ( form ) => {
	form.classList.remove( 'is-loading', 'has-error', 'has-success' );
	const button = form.querySelector( 'button[type="submit"]' );
	if ( button ) {
		const originalText =
			form.dataset.buttonText || button.dataset.buttonText;
		if ( originalText ) {
			const textNode = button.querySelector(
				'.mcd-contact-form__submit-text'
			);
			if ( textNode ) {
				textNode.textContent = originalText;
			}
		}
	}
	clearFormErrors( form );
	updateSubmitState( form );
};

const setLoadingState = ( form ) => {
	form.classList.remove( 'has-error', 'has-success' );
	form.classList.add( 'is-loading' );
	const button = form.querySelector( 'button[type="submit"]' );
	if ( button ) {
		button.disabled = true;
		const textNode = button.querySelector(
			'.mcd-contact-form__submit-text'
		);
		if ( textNode ) {
			button.dataset.buttonText = textNode.textContent;
			textNode.textContent = settings.loadingText || 'Sending...';
		}
	}
};

const showNotice = ( form, type, message ) => {
	const notice = form.querySelector( '.mcd-contact-form__notice' );
	if ( ! notice ) {
		return;
	}

	notice.textContent = message;
	notice.classList.remove( 'is-success', 'is-error' );
	if ( type === 'error' ) {
		notice.setAttribute( 'aria-live', 'assertive' );
		notice.setAttribute( 'role', 'alert' );
	} else {
		notice.setAttribute( 'aria-live', 'polite' );
		notice.setAttribute( 'role', 'status' );
	}

	if ( type === 'success' ) {
		notice.classList.add( 'is-success' );
		form.classList.remove( 'has-error' );
		form.classList.add( 'has-success' );
	} else if ( type === 'error' ) {
		notice.classList.add( 'is-error' );
		form.classList.remove( 'has-success' );
		form.classList.add( 'has-error' );
	}
};

const attachHandlers = () => {
	if ( ! endpoint ) {
		return;
	}

	const forms = document.querySelectorAll( '.mcd-contact-form' );

	forms.forEach( ( form ) => {
		const progress = initSectionProgress( form );

		form.addEventListener( 'input', ( event ) => {
			const field = event.target;
			if ( ! field || ! field.name ) {
				updateSubmitState( form );
				if ( progress && typeof progress.refresh === 'function' ) {
					progress.refresh();
				}
				return;
			}

			if ( REQUIRED_FIELD_CONFIG[ field.name ] ) {
				const result = validateFieldValue( field );
				if ( result.valid ) {
					clearFieldError( field );
				}
			}
			updateSubmitState( form );
			if ( progress && typeof progress.refresh === 'function' ) {
				progress.refresh();
			}
		} );

		form.addEventListener(
			'blur',
			( event ) => {
				const field = event.target;
				if (
					! field ||
					! field.name ||
					! REQUIRED_FIELD_CONFIG[ field.name ]
				) {
					return;
				}

				const result = validateFieldValue( field );
				if ( result.valid ) {
					clearFieldError( field );
				} else {
					setFieldError( field, result.message );
				}
				updateSubmitState( form );
				if ( progress && typeof progress.refresh === 'function' ) {
					progress.refresh();
				}
			},
			true
		);

		form.addEventListener( 'submit', async ( event ) => {
			event.preventDefault();

			const validation = validateForm( form );
			if ( ! validation.valid ) {
				showNotice( form, 'error', validation.message );
				form.classList.add( 'has-error' );
				updateSubmitState( form );
				return;
			}

			setLoadingState( form );

			try {
				const payload = serializeForm( form );
				if ( formNonce ) {
					payload.nonce = formNonce;
				}
				const response = await window.fetch( endpoint, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify( payload ),
				} );

				if ( ! response.ok ) {
					throw new Error( 'Request failed' );
				}

				const data = await response.json();
				const successMessage =
					( form.dataset && form.dataset.successMessage ) ||
					settings.successMessage ||
					'Thanks! We will be in touch shortly.';

				showNotice( form, 'success', data?.message || successMessage );
				form.classList.add( 'has-success' );
				form.reset();
				resetFormState( form );
				if ( progress && typeof progress.reset === 'function' ) {
					progress.reset();
				}
				sendAnalyticsEvent( successEvent, { status: 'success' } );
				updateSubmitState( form );
			} catch ( error ) {
				resetFormState( form );
				showNotice(
					form,
					'error',
					settings.errorMessage ||
						'Something went wrong. Please email hello@mccullough.digital and we will follow up.'
				);
				sendAnalyticsEvent( errorEvent, { status: 'error' } );
				updateSubmitState( form );
			}
		} );

		updateSubmitState( form );
		if ( progress && typeof progress.refresh === 'function' ) {
			progress.refresh();
		}
	} );
};

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', attachHandlers );
} else {
	attachHandlers();
}
