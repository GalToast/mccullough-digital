( () => {
	document.documentElement.classList.add( 'has-js' );
	// Configuration constants
	const CONFIG = {
		MAX_LOGO_ROTATE: 15, // Maximum rotation angle for 3D tilt effect
		HEADER_RESIZE_DEBOUNCE: 150, // Debounce delay for resize events (ms)
	};

	const init = () => {
		const isAutomatedEnvironment =
			typeof window !== 'undefined' &&
			typeof window.navigator !== 'undefined' &&
			!! window.navigator.webdriver;

		const getScrollPosition = () => {
			if ( typeof window.scrollY === 'number' ) {
				return window.scrollY;
			}

			if ( typeof window.pageYOffset === 'number' ) {
				return window.pageYOffset;
			}

			const docEl = document.documentElement;
			if ( docEl && typeof docEl.scrollTop === 'number' ) {
				return docEl.scrollTop;
			}

			const bodyEl = document.body;
			if ( bodyEl && typeof bodyEl.scrollTop === 'number' ) {
				return bodyEl.scrollTop;
			}

			return 0;
		};

		const normalizeTemplateStructure = () => {
			const mainEl = document.querySelector( 'main' );

			if ( mainEl && ! mainEl.classList.contains( 'site-content' ) ) {
				mainEl.classList.add( 'site-content' );

				if (
					document.body.classList.contains( 'blog' ) ||
					document.body.classList.contains( 'archive' )
				) {
					mainEl.classList.add( 'blog-archive' );
				}
			}

			if ( document.querySelector( '.blog-hero' ) ) {
				return;
			}

			if (
				! document.body.classList.contains( 'blog' ) &&
				! document.body.classList.contains( 'archive' )
			) {
				return;
			}

			const queryContainer = mainEl
				? mainEl.querySelector( '.wp-block-query' )
				: null;

			if ( ! queryContainer ) {
				return;
			}

			const candidateHero = Array.from( queryContainer.children ).find(
				( child ) =>
					child &&
					child.classList &&
					child.classList.contains( 'wp-block-group' )
			);

			if ( ! candidateHero ) {
				return;
			}

			candidateHero.classList.add( 'blog-hero' );

			const innerGroup = Array.from( candidateHero.children ).find(
				( child ) =>
					child &&
					child.classList &&
					child.classList.contains( 'wp-block-group' )
			);

			if ( innerGroup ) {
				innerGroup.classList.add( 'blog-hero__inner' );
			}
		};

		normalizeTemplateStructure();

		const initCategoryPillsActiveState = () => {
			const links = document.querySelectorAll(
				'.category-pills .wp-block-button__link'
			);

			if ( ! links.length ) {
				return;
			}

			const normalizePath = ( input ) => {
				if ( typeof input !== 'string' ) {
					return null;
				}

				const trimmed = input.trim();

				if ( ! trimmed.length ) {
					return null;
				}

				try {
					const url = new URL( trimmed, window.location.origin );
					const cleanedPath = url.pathname.replace( /\/+$/, '' );
					return cleanedPath || '/';
				} catch ( error ) {
					if ( trimmed.startsWith( '/' ) ) {
						return trimmed.replace( /\/+$/, '' ) || '/';
					}

					return null;
				}
			};

			const currentPath =
				normalizePath( window.location.pathname ) || '/';
			let matchedLink = null;

			links.forEach( ( link ) => {
				link.removeAttribute( 'aria-current' );

				const pill = link.closest( '.category-pill' );
				if ( pill ) {
					pill.classList.remove( 'is-active' );
				}

				const linkPath = normalizePath( link.getAttribute( 'href' ) );

				if (
					! matchedLink &&
					linkPath &&
					( linkPath === currentPath ||
						( linkPath !== '/' &&
							currentPath.startsWith( linkPath ) ) )
				) {
					matchedLink = link;
				}
			} );

			const activeLink = matchedLink || links[ 0 ];

			if ( ! activeLink ) {
				return;
			}

			activeLink.setAttribute( 'aria-current', 'page' );

			const activePill = activeLink.closest( '.category-pill' );

			if ( activePill ) {
				activePill.classList.add( 'is-active' );
			}
		};

		const initBlogPostCards = () => {
			const cards = document.querySelectorAll(
				'.post-card[data-card-link]'
			);

			if ( ! cards.length ) {
				return;
			}

			const interactiveSelector =
				'a, button, input, textarea, select, details, summary, [role="button"], [role="link"], [data-prevent-card-click]';

			const isInteractiveTarget = ( target ) => {
				return (
					target instanceof Element &&
					typeof target.closest === 'function' &&
					target.closest( interactiveSelector )
				);
			};

			cards.forEach( ( card ) => {
				if ( card.dataset.postCardEnhanced === 'true' ) {
					return;
				}

				const href = card.dataset.cardLink;

				if ( typeof href !== 'string' ) {
					return;
				}

				const destination = href.trim();

				if ( ! destination.length ) {
					return;
				}

				card.dataset.postCardEnhanced = 'true';
				card.classList.add( 'post-card--enhanced' );

				const navigate = () => {
					window.location.href = destination;
				};

				card.addEventListener( 'click', ( event ) => {
					if ( event.defaultPrevented ) {
						return;
					}

					if ( event.button !== 0 ) {
						return;
					}

					if (
						event.metaKey ||
						event.ctrlKey ||
						event.shiftKey ||
						event.altKey
					) {
						return;
					}

					if ( isInteractiveTarget( event.target ) ) {
						return;
					}

					event.preventDefault();
					navigate();
				} );

				card.addEventListener( 'keydown', ( event ) => {
					if ( event.defaultPrevented ) {
						return;
					}

					const isEnter = event.key === 'Enter';
					const isSpace =
						event.key === ' ' || event.key === 'Spacebar';

					if ( ! isEnter && ! isSpace ) {
						return;
					}

					const focusTarget = document.activeElement;

					if (
						focusTarget &&
						focusTarget !== card &&
						isInteractiveTarget( focusTarget )
					) {
						return;
					}

					if ( isEnter ) {
						event.preventDefault();
						navigate();
						return;
					}

					if ( isSpace ) {
						event.preventDefault();
					}
				} );

				card.addEventListener( 'keyup', ( event ) => {
					if (
						event.defaultPrevented ||
						( event.key !== ' ' && event.key !== 'Spacebar' )
					) {
						return;
					}

					if ( isInteractiveTarget( event.target ) ) {
						return;
					}

					navigate();
				} );
			} );
		};

		const watchBlogCards = () => {
			const cardsContainer = document.querySelector(
				'.blog-grid .cards'
			);

			if (
				! cardsContainer ||
				typeof window.MutationObserver === 'undefined'
			) {
				return;
			}

			const observer = new window.MutationObserver(
				( mutationsList ) => {
					for ( const mutation of mutationsList ) {
						if (
							mutation.type === 'childList' &&
							mutation.addedNodes.length
						) {
							initBlogPostCards();
							break;
						}
					}
				}
			);

			observer.observe( cardsContainer, {
				childList: true,
			} );
		};

		const initFaqAccordions = () => {
			const faqSections = document.querySelectorAll(
				'.mcd-home__faq-items, #faq .faq-grid, [data-faq-accordion]'
			);

			if ( ! faqSections.length ) {
				return;
			}

			const detailsSupported =
				'open' in document.createElement( 'details' );
			let idCounter = 0;

			const ensureId = ( node, prefix ) => {
				if ( ! node ) {
					return '';
				}

				const existing =
					typeof node.id === 'string' && node.id.trim().length > 0
						? node.id.trim()
						: null;

				if ( existing ) {
					return existing;
				}

				idCounter += 1;
				const generated = `${ prefix }-${ idCounter }`;
				node.id = generated;
				return generated;
			};

			faqSections.forEach( ( section ) => {
				if ( section.dataset.faqEnhanced === 'true' ) {
					return;
				}

				section.dataset.faqEnhanced = 'true';

				const detailItems = section.querySelectorAll( 'details' );

				if ( ! detailItems.length ) {
					return;
				}

				const controllers = [];
				const closeSiblings = ( currentDetails ) => {
					controllers.forEach( ( controller ) => {
						if (
							! controller ||
							controller.details === currentDetails
						) {
							return;
						}

						if ( controller.details.hasAttribute( 'open' ) ) {
							controller.details.removeAttribute( 'open' );
						}

						if ( ! detailsSupported && controller.content ) {
							controller.content.hidden = true;
						}

						if ( controller.summary ) {
							controller.summary.setAttribute(
								'aria-expanded',
								'false'
							);
						}

						if (
							controller.icon &&
							typeof controller.icon.textContent === 'string'
						) {
							controller.icon.textContent = '+';
						}
					} );
				};

				detailItems.forEach( ( details ) => {
					const summary = details.querySelector( 'summary' );
					const content = details.querySelector(
						'.faq-body, .mcd-home__faq-body, .faq__body'
					);

					if ( ! summary || ! content ) {
						return;
					}

					const summaryId = ensureId( summary, 'faq-trigger' );
					const panelId = ensureId( content, 'faq-panel' );

					summary.setAttribute( 'aria-controls', panelId );
					summary.setAttribute(
						'aria-expanded',
						details.hasAttribute( 'open' ) ? 'true' : 'false'
					);
					content.setAttribute( 'role', 'region' );
					content.setAttribute( 'aria-labelledby', summaryId );

					const icon =
						summary.querySelector( '[data-faq-icon]' ) ||
						summary.querySelector( 'span[aria-hidden="true"]' );

					const updateState = () => {
						const isOpen = details.hasAttribute( 'open' );
						summary.setAttribute(
							'aria-expanded',
							isOpen ? 'true' : 'false'
						);

						if ( icon && typeof icon.textContent === 'string' ) {
							icon.textContent = isOpen ? '-' : '+';
						}
					};

					updateState();

					controllers.push( {
						details,
						summary,
						content,
						icon,
						updateState,
					} );

					if ( detailsSupported ) {
						details.addEventListener( 'toggle', () => {
							updateState();

							if ( details.open ) {
								closeSiblings( details );
							}
						} );
					} else {
						const toggleFallback = ( event ) => {
							event.preventDefault();
							const isOpen = details.hasAttribute( 'open' );

							if ( isOpen ) {
								details.removeAttribute( 'open' );
								content.hidden = true;
							} else {
								details.setAttribute( 'open', 'open' );
								content.hidden = false;
								closeSiblings( details );
							}

							updateState();
						};

						content.hidden = ! details.hasAttribute( 'open' );
						summary.addEventListener( 'click', toggleFallback );
						summary.addEventListener( 'keydown', ( event ) => {
							if ( event.key === 'Enter' || event.key === ' ' ) {
								event.preventDefault();
								summary.click();
							}
						} );
					}
				} );
			} );
		};

		initCategoryPillsActiveState();
		initBlogPostCards();
		watchBlogCards();
		document.addEventListener(
			'mcd:blogLoopUpdated',
			initBlogPostCards
		);
		initFaqAccordions();

		const resolveQueryFlag = ( queryKey ) => {
			if ( typeof window === 'undefined' ) {
				return false;
			}

			try {
				const params = new URLSearchParams( window.location.search );
				return params.has( queryKey );
			} catch ( error ) {
				return false;
			}
		};

		const htmlElement = document.documentElement;
		const respectAllowMotionFlag = resolveQueryFlag( 'allow-motion' );
		const respectDisableMotionFlag = resolveQueryFlag( 'disable-motion' );

		let shouldDisableMotion =
			htmlElement.hasAttribute( 'data-automation-disable-motion' ) ||
			respectDisableMotionFlag ||
			( isAutomatedEnvironment && ! respectAllowMotionFlag );

		if ( respectAllowMotionFlag ) {
			shouldDisableMotion = false;
		}

		if ( shouldDisableMotion ) {
			htmlElement.setAttribute( 'data-automation-disable-motion', 'true' );

			if (
				document.head &&
				! document.head.querySelector(
					'[data-automation-disable-animations]'
				)
			) {
				const disableMotion = document.createElement( 'style' );
				disableMotion.setAttribute(
					'data-automation-disable-animations',
					'true'
				);
				disableMotion.textContent =
					`html[data-automation-disable-motion] *,` +
					`html[data-automation-disable-motion] *::before,` +
					`html[data-automation-disable-motion] *::after{` +
					`animation:none!important;transition:none!important;}`;
				document.head.appendChild( disableMotion );
			}

			window.addEventListener( 'pageshow', ( event ) => {
				if ( event.persisted ) {
					window.location.reload();
				}
			} );
		} else if (
			htmlElement.hasAttribute( 'data-automation-disable-motion' )
		) {
			htmlElement.removeAttribute( 'data-automation-disable-motion' );
		}

		const createMotionPreferenceQuery = () => {
			const noop = () => {};
			const hasMatchMedia = typeof window.matchMedia === 'function';
			const mediaQuery = hasMatchMedia
				? window.matchMedia( '(prefers-reduced-motion: reduce)' )
				: null;
			const createProxy = ( matches ) => {
				if ( ! mediaQuery ) {
					return {
						matches,
						addEventListener: noop,
						removeEventListener: noop,
						addListener: noop,
						removeListener: noop,
					};
				}

				const forward = ( method ) =>
					typeof mediaQuery[ method ] === 'function'
						? ( ...args ) => mediaQuery[ method ]( ...args )
						: noop;

				return {
					matches,
					addEventListener: forward( 'addEventListener' ),
					removeEventListener: forward( 'removeEventListener' ),
					addListener: forward( 'addListener' ),
					removeListener: forward( 'removeListener' ),
				};
			};

			if ( shouldDisableMotion ) {
				return createProxy( true );
			}

			if ( respectAllowMotionFlag ) {
				return createProxy( false );
			}

			if ( mediaQuery ) {
				return mediaQuery;
			}

			return createProxy( false );
		};

		const prefersReducedMotion = createMotionPreferenceQuery();

		const initRevealAnimations = () => {
			const revealElements = Array.from(
				document.querySelectorAll( '.reveal' )
			);

			if ( ! revealElements.length ) {
				return;
			}

			const revealImmediately = ( element ) => {
				if ( element && ! element.classList.contains( 'in' ) ) {
					element.classList.add( 'in' );
				}
			};

			const revealAll = () => {
				revealElements.forEach( revealImmediately );
			};

			if (
				prefersReducedMotion.matches ||
				typeof window.IntersectionObserver !== 'function'
			) {
				revealAll();
				return;
			}

			const observer = new window.IntersectionObserver(
				( entries, obs ) => {
					entries.forEach( ( entry ) => {
						if ( entry.isIntersecting ) {
							revealImmediately( entry.target );
							obs.unobserve( entry.target );
						}
					} );
				},
				{
					root: null,
					rootMargin: '0px 0px -10% 0px',
					threshold: 0.2,
				}
			);

			revealElements.forEach( ( element ) =>
				observer.observe( element )
			);

			const handleMotionPreferenceChange = ( event ) => {
				if ( event.matches ) {
					observer.disconnect();
					revealAll();
				}
			};

			if ( typeof prefersReducedMotion.addEventListener === 'function' ) {
				prefersReducedMotion.addEventListener(
					'change',
					handleMotionPreferenceChange,
					{ passive: true }
				);
			} else if (
				typeof prefersReducedMotion.addListener === 'function'
			) {
				prefersReducedMotion.addListener(
					handleMotionPreferenceChange
				);
			}

			window.addEventListener(
				'beforeunload',
				() => {
					if (
						typeof prefersReducedMotion.removeEventListener ===
						'function'
					) {
						prefersReducedMotion.removeEventListener(
							'change',
							handleMotionPreferenceChange
						);
					} else if (
						typeof prefersReducedMotion.removeListener ===
						'function'
					) {
						prefersReducedMotion.removeListener(
							handleMotionPreferenceChange
						);
					}

					observer.disconnect();
				},
				{ once: true }
			);
		};

		const handleMenuStateChangeCallbacks = [];

		const registerMenuStateCallback = ( callback ) => {
			if ( typeof callback === 'function' ) {
				handleMenuStateChangeCallbacks.push( callback );
			}
		};

		const dispatchMenuState = ( isOpen ) => {
			handleMenuStateChangeCallbacks.forEach( ( callback ) =>
				callback( isOpen )
			);
		};

		const initBlockMenu = ( menuToggle, navBlock ) => {
			if ( ! menuToggle || ! navBlock ) {
				return;
			}

			// Track menu state ourselves - don't rely on class checking at click time
			let menuIsOpen = false;
			let justClosedMenu = false; // Flag to prevent WordPress from reopening

			// Get the responsive container once - we'll use it throughout
			const responsiveContainer = navBlock.querySelector(
				'.wp-block-navigation__responsive-container'
			);

			const syncToggleState = () => {
				const isOpen = navBlock.classList.contains( 'is-menu-open' );

				menuToggle.classList.toggle( 'is-active', isOpen );

				// Update our tracked state
				menuIsOpen = isOpen;

				// Add class to header for neon sweep effect (check if header exists first)
				const headerElement = document.querySelector(
					'#masthead.site-header'
				);
				if ( headerElement ) {
					headerElement.classList.toggle( 'has-modal-open', isOpen );
				}

				dispatchMenuState( isOpen );
			};

			syncToggleState();

			const observer = new window.MutationObserver( syncToggleState );
			observer.observe( navBlock, {
				attributes: true,
				attributeFilter: [ 'class' ],
			} );

			// ALSO observe the responsive container since THAT'S where WordPress adds is-menu-open!
			if ( responsiveContainer ) {
				const containerObserver = new window.MutationObserver( () => {
					const containerIsOpen =
						responsiveContainer.classList.contains(
							'is-menu-open'
						);

					// If we just closed the menu, don't let WordPress reopen it
					if ( justClosedMenu && containerIsOpen ) {
						// Force close it again
						setTimeout( () => {
							responsiveContainer.classList.remove(
								'is-menu-open'
							);
							navBlock.classList.remove( 'is-menu-open' );
							menuToggle.classList.remove( 'is-active' );
						}, 0 );
						return;
					}

					menuIsOpen = containerIsOpen;
				} );
				containerObserver.observe( responsiveContainer, {
					attributes: true,
					attributeFilter: [ 'class' ],
				} );
			}

			const closeMenu = () => {
				// Directly remove the open classes
				navBlock.classList.remove( 'is-menu-open' );
				if ( responsiveContainer ) {
					responsiveContainer.classList.remove( 'is-menu-open' );
				}
				menuToggle.classList.remove( 'is-active' );

				// Re-enable body scroll
				document.body.style.overflow = '';

				// Update our tracked state
				menuIsOpen = false;
			};

			// Use pointerdown instead of click to fire BEFORE WordPress handlers
			menuToggle.addEventListener(
				'pointerdown',
				( e ) => {
					if ( menuIsOpen ) {
						e.preventDefault();
						e.stopPropagation();
						e.stopImmediatePropagation();
						justClosedMenu = true; // Set flag
						closeMenu();
						// Clear flag after a short delay
						setTimeout( () => {
							justClosedMenu = false;
						}, 100 );
					}
				},
				{ capture: true, passive: false }
			);

			// ALSO add mousedown as backup
			menuToggle.addEventListener(
				'mousedown',
				( e ) => {
					if ( menuIsOpen ) {
						e.preventDefault();
						e.stopPropagation();
						e.stopImmediatePropagation();
						closeMenu();
					}
				},
				{ capture: true, passive: false }
			);

			// ALSO add click as backup
			menuToggle.addEventListener(
				'click',
				( e ) => {
					// If we just closed the menu, block WordPress from reopening
					if ( justClosedMenu ) {
						e.preventDefault();
						e.stopPropagation();
						e.stopImmediatePropagation();
						return false;
					}

					if ( menuIsOpen ) {
						e.preventDefault();
						e.stopPropagation();
						e.stopImmediatePropagation();
						closeMenu();
					}
				},
				{ capture: true, passive: false }
			);

			// Nuclear option: Listen at document level to catch ALL clicks
			document.addEventListener(
				'pointerdown',
				( e ) => {
					// Check if the click is on our button or its children
					if (
						e.target === menuToggle ||
						menuToggle.contains( e.target )
					) {
						if ( menuIsOpen ) {
							e.preventDefault();
							e.stopPropagation();
							e.stopImmediatePropagation();
							justClosedMenu = true; // Set flag here too!
							closeMenu();
							// Clear flag after enough time for click to fire
							setTimeout( () => {
								justClosedMenu = false;
							}, 300 );
						}
					}
				},
				{ capture: true, passive: false }
			);

			// Also close menu when clicking the backdrop
			if ( responsiveContainer ) {
				responsiveContainer.addEventListener( 'click', ( e ) => {
					// Only close if clicking the backdrop itself, not menu items
					if ( e.target === responsiveContainer ) {
						closeMenu();
					}
				} );
			}

			// Close menu when clicking navigation links
			navBlock
				.querySelectorAll( '.wp-block-navigation-item a' )
				.forEach( ( link ) => {
					link.addEventListener( 'click', () => {
						if ( menuIsOpen ) {
							closeMenu();
						}
					} );
				} );
		};

		const blockMenuToggle = document.querySelector(
			'.site-header .wp-block-navigation__responsive-container-open'
		);
		const navBlock = document.querySelector(
			'.site-header .wp-block-navigation'
		);
		const navContainer = document.querySelector(
			'.site-header .main-navigation'
		);

		const ensureNavLink = ( label, href, { position = 'end' } = {} ) => {
			const targetNav = navContainer || navBlock;

			if ( ! targetNav ) {
				return;
			}

			const anchors = Array.from( targetNav.querySelectorAll( 'a' ) );

			const normalizePath = ( url ) => {
				try {
					const parsed = new window.URL(
						url,
						window.location.origin
					);
					const path = parsed.pathname || '/';
					if ( '/' === path ) {
						return '/';
					}
					return path.replace( /\/+$/, '/' );
				} catch ( error ) {
					return url;
				}
			};

			const desiredPath = normalizePath( href );

			const existingLink = anchors.find( ( anchor ) => {
				const attrHref = anchor.getAttribute( 'href' ) || '';
				return normalizePath( attrHref ) === desiredPath;
			} );

			if ( existingLink ) {
				if ( existingLink.textContent.trim() !== label ) {
					existingLink.textContent = label;
				}
				existingLink.setAttribute( 'aria-label', label );
				return;
			}

			const navList =
				targetNav.querySelector( '.wp-block-navigation__container' ) ||
				targetNav.querySelector( 'ul' ) ||
				targetNav;
			const listItem = document.createElement( 'li' );

			listItem.className =
				'wp-block-navigation-item wp-block-navigation-link';

			const blogAnchor = document.createElement( 'a' );

			blogAnchor.className =
				'wp-block-navigation-item__content';
			blogAnchor.href = href;
			blogAnchor.textContent = label;
			blogAnchor.setAttribute( 'aria-label', label );

			listItem.appendChild( blogAnchor );
			if ( 'start' === position && navList.firstChild ) {
				navList.insertBefore( listItem, navList.firstChild );
			} else {
				navList.appendChild( listItem );
			}
		};

		ensureNavLink( 'Home', '/', { position: 'start' } );
		ensureNavLink( 'Blog', '/blog/' );
		ensureNavLink( 'About Us', '/about-us/' );

		// Initialize menu only if both elements exist
		if ( blockMenuToggle && navBlock ) {
			initBlockMenu( blockMenuToggle, navBlock );
		}

		const ensureHeaderBackdrop = ( headerElement ) => {
			if ( ! headerElement ) {
				return null;
			}

			const existingBackdrop = headerElement.querySelector(
				'.site-header__backdrop'
			);

			if ( existingBackdrop ) {
				return existingBackdrop;
			}

			const backdrop = document.createElement( 'div' );
			backdrop.className = 'site-header__backdrop';
			backdrop.setAttribute( 'aria-hidden', 'true' );

			[
				'stars stars--header',
				'stars2 stars--header',
				'stars3 stars--header',
			].forEach( ( className ) => {
				const layer = document.createElement( 'div' );
				layer.className = className;
				backdrop.appendChild( layer );
			} );

			const firstElement = headerElement.firstElementChild;
			if ( firstElement ) {
				headerElement.insertBefore( backdrop, firstElement );
			} else {
				headerElement.appendChild( backdrop );
			}

			return backdrop;
		};

		const header = document.querySelector( '#masthead.site-header' );
		const headerBackdrop = ensureHeaderBackdrop( header );

		const restartBackdropAnimations = () => {
			if ( ! headerBackdrop ) {
				return;
			}

			const layers = headerBackdrop.querySelectorAll(
				'.stars, .stars2, .stars3'
			);

			if ( ! layers.length ) {
				return;
			}

			layers.forEach( ( layer ) => {
				layer.style.animation = 'none';
			} );

			void headerBackdrop.offsetWidth;

			layers.forEach( ( layer ) => {
				layer.style.animation = '';
			} );
		};

		if ( headerBackdrop ) {
			restartBackdropAnimations();
		}

		if ( header ) {
			let lastY = window.scrollY;
			let ticking = false;
			let headerHeight = header.offsetHeight;
			let resizeTimeout;
			let isMenuOpen = false;
			let isFocusWithin = false;
			let headerResizeObserver = null;
			let adminBarResizeObserver = null;
			let observedAdminBar = null;
			const rootElement = document.documentElement;
			const rootStyle = rootElement && rootElement.style;
			const bodyElement = document.body;

			const setHeaderHiddenState = ( isHidden ) => {
				if ( ! bodyElement ) {
					return;
				}

				bodyElement.classList.toggle( 'mcd-header-hidden', isHidden );
			};

			const showHeader = () => {
				header.classList.remove( 'hide' );
				setHeaderHiddenState( false );
				if ( headerBackdrop ) {
					headerBackdrop.classList.remove( 'is-hidden' );
					restartBackdropAnimations();
				}
			};

			const hideHeader = () => {
				header.classList.add( 'hide' );
				setHeaderHiddenState( true );
				if ( headerBackdrop ) {
					headerBackdrop.classList.add( 'is-hidden' );
				}
			};

			const initScrollProgress = () => {
				const progressBar = document.querySelector( '.site-progress' );
				const progressFill = progressBar
					? progressBar.querySelector( '.site-progress__fill' )
					: null;
				const progressStatus = document.querySelector(
					'.site-progress__status'
				);

				if ( ! progressBar || ! progressFill || ! progressStatus ) {
					if ( rootStyle ) {
						rootStyle.setProperty( '--mcd-progress-height', '0px' );
					}
					return null;
				}

				const body = document.body;
				const skipBodyClasses = [
					'error404',
					'search',
					'wp-admin',
					'page-template-page-contact',
				];
				const shouldSkip =
					body &&
					skipBodyClasses.some( ( cls ) =>
						body.classList.contains( cls )
					);

				if ( shouldSkip ) {
					progressBar.classList.add( 'is-hidden' );
					if ( rootStyle ) {
						rootStyle.setProperty( '--mcd-progress-height', '0px' );
					}
					return null;
				}

				const computedRootStyles = rootElement
					? window.getComputedStyle( rootElement )
					: null;
				const defaultHeight = computedRootStyles
					? (
							computedRootStyles.getPropertyValue(
								'--mcd-progress-height'
							) || '4px'
					  ).trim()
					: '4px';
				const progressHeight = defaultHeight || '4px';
				const PROGRESS_SCROLL_THRESHOLD = 160;

				let maxScrollable = 0;
				let isVisible = false;
				let progressCurrent = 0;
				let progressTarget = 0;
				let rafId = null;

				const clamp01 = ( value ) =>
					Math.min( Math.max( value, 0 ), 1 );

				const cancelAnimation = () => {
					if ( rafId ) {
						window.cancelAnimationFrame( rafId );
						rafId = null;
					}
				};

				const updateAccessibleStatus = ( value ) => {
					const percent = Math.round( clamp01( value ) * 100 );
					progressStatus.setAttribute(
						'aria-valuenow',
						String( percent )
					);
					progressStatus.textContent = `${ percent }% of page complete`;
					return percent;
				};

				const applyWidth = ( value ) => {
					const percent = updateAccessibleStatus( value );
					progressFill.style.width = `${ percent }%`;
				};

				const animationStep = () => {
					rafId = null;
					const delta = progressTarget - progressCurrent;

					if ( Math.abs( delta ) <= 0.001 ) {
						progressCurrent = progressTarget;
						applyWidth( progressCurrent );
						return;
					}

					progressCurrent += delta * 0.18;
					applyWidth( progressCurrent );
					rafId = window.requestAnimationFrame( animationStep );
				};

				const setProgress = ( value, { immediate = false } = {} ) => {
					const clamped = clamp01( value );
					progressTarget = clamped;

					if ( immediate || prefersReducedMotion.matches ) {
						cancelAnimation();
						progressCurrent = clamped;
						applyWidth( progressCurrent );
						return;
					}

					if ( ! rafId ) {
						rafId = window.requestAnimationFrame( animationStep );
					}
				};

				const setVisibility = ( shouldShow ) => {
					if ( shouldShow === isVisible ) {
						return;
					}

					isVisible = shouldShow;
					progressBar.classList.toggle( 'is-visible', shouldShow );
					progressBar.classList.toggle( 'is-hidden', ! shouldShow );

					if ( rootStyle ) {
						rootStyle.setProperty(
							'--mcd-progress-height',
							shouldShow ? progressHeight : '0px'
						);
					}

					if ( ! shouldShow ) {
						cancelAnimation();
						setProgress( 0, { immediate: true } );
					}
				};

				const refresh = ( { immediate = false } = {} ) => {
					const doc = document.documentElement;
					const bodyEl = document.body;
					const scrollHeight = Math.max(
						doc ? doc.scrollHeight : 0,
						bodyEl ? bodyEl.scrollHeight : 0
					);

					const viewportHeight =
						window.innerHeight ||
						( doc ? doc.clientHeight : 0 ) ||
						0;
					maxScrollable = Math.max(
						scrollHeight - viewportHeight,
						0
					);

					const shouldShow =
						maxScrollable > PROGRESS_SCROLL_THRESHOLD;
					setVisibility( shouldShow );

					if ( shouldShow ) {
						const currentScroll = getScrollPosition();
						const ratio =
							maxScrollable > 0
								? currentScroll / maxScrollable
								: 0;
						setProgress( ratio, { immediate } );
					}
				};

				applyWidth( 0 );
				refresh( { immediate: true } );

				return {
					update: ( scrollY, { immediate = false } = {} ) => {
						if ( ! isVisible || maxScrollable <= 0 ) {
							return;
						}

						const ratio = scrollY / maxScrollable;
						setProgress( ratio, { immediate } );
					},
					refresh,
					syncMotionPreference: () => {
						setProgress( progressTarget, { immediate: true } );
					},
					destroy: cancelAnimation,
				};
			};

			const scrollProgress = initScrollProgress();

			const setHeaderOffset = () => {
				headerHeight = header.offsetHeight;

				const adminBar = document.querySelector( '#wpadminbar' );
				const adminBarHeight = adminBar ? adminBar.offsetHeight : 0;

				if ( rootStyle ) {
					const roundedHeight = Math.max(
						Math.round( headerHeight ),
						0
					);
					const roundedAdminHeight = Math.max(
						Math.round( adminBarHeight ),
						0
					);
					rootStyle.setProperty(
						'--mcd-header-offset',
						`${ roundedHeight }px`
					);
					rootStyle.setProperty(
						'--mcd-admin-bar-offset',
						`${ roundedAdminHeight }px`
					);
				}
			};

			const syncAdminBarObserver = () => {
				if ( typeof window.ResizeObserver !== 'function' ) {
					return;
				}

				const adminBar = document.querySelector( '#wpadminbar' );

				if ( ! adminBarResizeObserver ) {
					adminBarResizeObserver = new window.ResizeObserver( () => {
						setHeaderOffset();
						if ( scrollProgress ) {
							scrollProgress.refresh( { immediate: true } );
						}
					} );
				}

				if ( adminBar === observedAdminBar ) {
					return;
				}

				if ( observedAdminBar ) {
					adminBarResizeObserver.unobserve( observedAdminBar );
				}

				if ( adminBar ) {
					adminBarResizeObserver.observe( adminBar );
				}

				observedAdminBar = adminBar || null;
			};

			// Register callback only after navBlock has been checked
			if ( navBlock ) {
				registerMenuStateCallback( ( open ) => {
					isMenuOpen = open;
					if ( isMenuOpen ) {
						showHeader();
					}
					setHeaderOffset();
					syncAdminBarObserver();
					if ( scrollProgress ) {
						scrollProgress.refresh( { immediate: true } );
					}
				} );

				// Dispatch initial state
				dispatchMenuState(
					navBlock.classList.contains( 'is-menu-open' )
				);
			}

			setHeaderOffset();
			syncAdminBarObserver();
			window.addEventListener( 'load', () => {
				setHeaderOffset();
				syncAdminBarObserver();
				if ( scrollProgress ) {
					scrollProgress.refresh( { immediate: true } );
				}
			} );

			const updateHeaderVisibility = () => {
				const y = getScrollPosition();

				if ( scrollProgress ) {
					scrollProgress.update( y, {
						immediate: prefersReducedMotion.matches,
					} );
				}

				if (
					prefersReducedMotion.matches ||
					isMenuOpen ||
					isFocusWithin
				) {
					showHeader();
					lastY = y;
					ticking = false;
					return;
				}

				if ( y > headerHeight && y > lastY ) {
					hideHeader();
				} else if ( y < lastY ) {
					showHeader();
				}

				lastY = y;
				ticking = false;
			};

			const refreshHeaderMetrics = ( { immediate = false } = {} ) => {
				setHeaderOffset();
				syncAdminBarObserver();
				if ( scrollProgress ) {
					scrollProgress.refresh( { immediate } );
				}
				updateHeaderVisibility();
			};

			const handleScroll = () => {
				if ( ! ticking ) {
					window.requestAnimationFrame( updateHeaderVisibility );
					ticking = true;
				}
			};

			const handleResize = () => {
				clearTimeout( resizeTimeout );
				resizeTimeout = setTimeout( () => {
					refreshHeaderMetrics( { immediate: true } );
				}, CONFIG.HEADER_RESIZE_DEBOUNCE );
			};

			window.addEventListener( 'scroll', handleScroll, {
				passive: true,
			} );
			window.addEventListener( 'resize', handleResize, {
				passive: true,
			} );

			if ( typeof window.ResizeObserver === 'function' ) {
				headerResizeObserver = new window.ResizeObserver( () => {
					setHeaderOffset();
					syncAdminBarObserver();
					if ( scrollProgress ) {
						scrollProgress.refresh( { immediate: true } );
					}
				} );
				headerResizeObserver.observe( header );
			}

			let handleFontLoad = null;

			if (
				document.fonts &&
				typeof document.fonts.addEventListener === 'function'
			) {
				handleFontLoad = () => {
					refreshHeaderMetrics( { immediate: true } );
				};
				document.fonts.addEventListener(
					'loadingdone',
					handleFontLoad
				);
			}

			window.addEventListener( 'pageshow', ( event ) => {
				if ( ! event.persisted ) {
					return;
				}

				if ( isAutomatedEnvironment ) {
					window.location.reload();
					return;
				}

				refreshHeaderMetrics( { immediate: true } );
			} );

			document.addEventListener( 'visibilitychange', () => {
				if ( document.visibilityState === 'visible' ) {
					restartBackdropAnimations();
				}
			} );

			window.addEventListener( 'unload', () => {
				if ( headerResizeObserver ) {
					headerResizeObserver.disconnect();
				}

				if ( adminBarResizeObserver ) {
					adminBarResizeObserver.disconnect();
				}

				if (
					document.fonts &&
					typeof document.fonts.removeEventListener === 'function' &&
					handleFontLoad
				) {
					document.fonts.removeEventListener(
						'loadingdone',
						handleFontLoad
					);
				}

				if ( scrollProgress ) {
					scrollProgress.destroy();
				}
			} );

			header.addEventListener( 'focusin', () => {
				isFocusWithin = true;
				showHeader();
			} );

			header.addEventListener( 'focusout', ( event ) => {
				if ( ! header.contains( event.relatedTarget ) ) {
					isFocusWithin = false;
					updateHeaderVisibility();
				}
			} );

			const onMotionPreferenceChange = () => {
				if ( prefersReducedMotion.matches ) {
					showHeader();
				} else {
					updateHeaderVisibility();
				}

				if ( scrollProgress ) {
					scrollProgress.syncMotionPreference();
				}
			};

			if ( typeof prefersReducedMotion.addEventListener === 'function' ) {
				prefersReducedMotion.addEventListener(
					'change',
					onMotionPreferenceChange
				);
			} else if (
				typeof prefersReducedMotion.addListener === 'function'
			) {
				prefersReducedMotion.addListener( onMotionPreferenceChange );
			}

			updateHeaderVisibility();
		}

		// Logo 3D tilt effect
		const logoContainer = document.querySelector( '.site-branding' );
		if ( logoContainer ) {
			const logoLink = logoContainer.querySelector( '.custom-logo-link' );
			if ( logoLink ) {
				let animationFrameId = null;

				const shouldAnimate = () => ! prefersReducedMotion.matches;

				const resetLogo = () => {
					logoLink.style.transform =
						'perspective(1000px) rotateX(0deg) rotateY(0deg)';
				};

				logoContainer.addEventListener( 'mousemove', ( e ) => {
					if ( ! shouldAnimate() ) {
						resetLogo();
						return;
					}

					window.cancelAnimationFrame( animationFrameId );
					animationFrameId = window.requestAnimationFrame( () => {
						const rect = logoContainer.getBoundingClientRect();
						const x = e.clientX - rect.left;
						const y = e.clientY - rect.top;
						const { width, height } = rect;
						const rotateY =
							CONFIG.MAX_LOGO_ROTATE *
							( ( x - width / 2 ) / ( width / 2 ) );
						const rotateX =
							-CONFIG.MAX_LOGO_ROTATE *
							( ( y - height / 2 ) / ( height / 2 ) );
						logoLink.style.transform = `perspective(1000px) rotateX(${ rotateX }deg) rotateY(${ rotateY }deg)`;
					} );
				} );

				logoContainer.addEventListener( 'mouseleave', () => {
					window.cancelAnimationFrame( animationFrameId );
					animationFrameId = window.requestAnimationFrame( () => {
						resetLogo();
					} );
				} );

				const handleMotionPreferenceChange = () => {
					if ( ! shouldAnimate() ) {
						resetLogo();
					}
				};

				if (
					typeof prefersReducedMotion.addEventListener === 'function'
				) {
					prefersReducedMotion.addEventListener(
						'change',
						handleMotionPreferenceChange
					);
				} else if (
					typeof prefersReducedMotion.addListener === 'function'
				) {
					prefersReducedMotion.addListener(
						handleMotionPreferenceChange
					);
				}
			}
		}

		const initBlogHeroGlitch = () => {
			const blogHero = document.querySelector( '.blog-hero' );

			if ( ! blogHero ) {
				return;
			}

			const title = blogHero.querySelector( '.blog-hero__title' );

			if ( ! title ) {
				return;
			}

			if ( title.dataset.blogHeroGlitchInitialized === 'true' ) {
				return;
			}

			title.dataset.blogHeroGlitchInitialized = 'true';

			const prepareHeadline = () => {
				let textWrapper = title.querySelector(
					'.blog-hero__title-text'
				);

				if ( ! textWrapper ) {
					textWrapper = document.createElement( 'span' );
					textWrapper.classList.add( 'blog-hero__title-text' );

					const children = Array.from( title.childNodes );

					children.forEach( ( node ) => {
						if ( node === textWrapper ) {
							return;
						}

						textWrapper.appendChild( node );
					} );

					title.appendChild( textWrapper );
				}

				const rawText = textWrapper.textContent
					? textWrapper.textContent.trim()
					: '';

				if (
					! rawText ||
					textWrapper.dataset.blogHeroTitleAnimated === 'true'
				) {
					return;
				}

				textWrapper.dataset.blogHeroTitleAnimated = 'true';

				let srText = title.querySelector(
					'.blog-hero__title-text--sr'
				);

				if ( ! srText ) {
					srText = textWrapper.cloneNode( true );
					srText.classList.add(
						'blog-hero__title-text--sr',
						'screen-reader-text'
					);
					srText.setAttribute( 'data-blog-hero-sr-text', 'true' );
					srText.setAttribute( 'aria-hidden', 'false' );
					srText.setAttribute( 'role', 'text' );

					srText.querySelectorAll( '[id]' ).forEach( ( node ) => {
						node.removeAttribute( 'id' );
					} );

					title.insertBefore( srText, textWrapper );
				}

				textWrapper.setAttribute( 'aria-hidden', 'true' );
				textWrapper.classList.add( 'blog-hero__title-text--visual' );

				if (
					typeof document.createTreeWalker !== 'function' ||
					typeof window.NodeFilter === 'undefined'
				) {
					return;
				}

				const walker = document.createTreeWalker(
					textWrapper,
					window.NodeFilter.SHOW_TEXT,
					null,
					false
				);

				const textNodes = [];

				while ( walker.nextNode() ) {
					textNodes.push( walker.currentNode );
				}

				textNodes.forEach( ( node ) => {
					const text = node.textContent;

					if ( ! text ) {
						return;
					}

					const fragment = document.createDocumentFragment();
					let currentWord = null;

					const flushWord = () => {
						if ( currentWord && currentWord.childNodes.length ) {
							fragment.appendChild( currentWord );
						}

						currentWord = null;
					};

					const normalizedText = text.replace( /\r\n/g, '\n' );

					for ( const char of normalizedText ) {
						if ( char === '\n' || char === '\r' ) {
							flushWord();
							fragment.appendChild(
								document.createElement( 'br' )
							);
							continue;
						}

						if ( char.trim() === '' ) {
							flushWord();
							fragment.appendChild(
								document.createTextNode( char )
							);
							continue;
						}

						if ( ! currentWord ) {
							currentWord = document.createElement( 'span' );
							currentWord.classList.add( 'blog-hero__word' );
						}

						const letter = document.createElement( 'span' );
						letter.classList.add( 'blog-hero__letter' );
						letter.dataset.char = char;
						letter.textContent = char;
						letter.setAttribute( 'aria-hidden', 'true' );

						currentWord.appendChild( letter );
					}

					flushWord();

					if ( node.parentNode ) {
						node.parentNode.replaceChild( fragment, node );
					}
				} );
			};

			prepareHeadline();

			const applyMotionPreference = () => {
				blogHero.classList.toggle(
					'is-reduced-motion',
					prefersReducedMotion.matches
				);
			};

			applyMotionPreference();

			const motionListener = () => {
				applyMotionPreference();
			};

			const hasModernMotionListener =
				typeof prefersReducedMotion.addEventListener === 'function';
			const hasLegacyMotionListener =
				! hasModernMotionListener &&
				typeof prefersReducedMotion.addListener === 'function';

			if ( hasModernMotionListener ) {
				prefersReducedMotion.addEventListener(
					'change',
					motionListener
				);
			} else if ( hasLegacyMotionListener ) {
				prefersReducedMotion.addListener( motionListener );
			}

			window.addEventListener(
				'unload',
				() => {
					if ( hasModernMotionListener ) {
						prefersReducedMotion.removeEventListener(
							'change',
							motionListener
						);
					} else if ( hasLegacyMotionListener ) {
						prefersReducedMotion.removeListener( motionListener );
					}
				},
				{ once: true }
			);
		};

		initRevealAnimations();
		initBlogHeroGlitch();
	};

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init, { once: true } );
	} else {
		init();
	}
} )();

// Mobile Menu - Add loading state when clicking nav items
( () => {
	const setAltWhenEmpty = ( img, fallbackText ) => {
		if ( ! img ) {
			return;
		}

		const currentAlt = img.getAttribute( 'alt' );

		if ( typeof currentAlt === 'string' && currentAlt.trim().length > 0 ) {
			return;
		}

		if (
			typeof fallbackText === 'string' &&
			fallbackText.trim().length > 0
		) {
			img.setAttribute( 'alt', fallbackText.trim() );
			return;
		}

		// Provide a generic but descriptive fallback so the image isn't announced as "graphic".
		img.setAttribute( 'alt', 'Illustration for the highlighted service' );
	};

	const collectHeadingText = ( root ) => {
		if ( ! root ) {
			return '';
		}

		const heading = root.querySelector( 'h1, h2, h3, h4, h5, h6' );
		if ( heading && heading.textContent ) {
			return heading.textContent.trim();
		}

		const paragraph = root.querySelector( 'p' );
		return paragraph && paragraph.textContent
			? paragraph.textContent.trim()
			: '';
	};

	const applyFallbackAltText = () => {
		const serviceColumns = document.querySelectorAll(
			'.services-section-v2 .wp-block-column, .service-card-v2'
		);
		serviceColumns.forEach( ( column ) => {
			const image = column.querySelector( 'img' );
			const fallback = collectHeadingText( column );
			setAltWhenEmpty( image, fallback );
		} );

		const caseStudyCards = document.querySelectorAll(
			'.case-study-card, .case-study-spotlight'
		);
		caseStudyCards.forEach( ( card ) => {
			const image = card.querySelector( 'img' );
			const fallback = collectHeadingText( card );
			setAltWhenEmpty( image, fallback || 'Case study visual detail' );
		} );
	};

	document.addEventListener( 'DOMContentLoaded', () => {
		applyFallbackAltText();
	} );
} )();

document.addEventListener( 'DOMContentLoaded', function () {
	const mobileOnly = window.matchMedia( '(max-width: 960px)' );
	if ( ! mobileOnly.matches ) {
		return;
	}

	const mobileNavLinks = document.querySelectorAll(
		'.wp-block-navigation__responsive-container .wp-block-navigation-item__content'
	);

	mobileNavLinks.forEach( ( link ) => {
		link.addEventListener( 'click', function ( e ) {
			if ( this.getAttribute( 'aria-current' ) === 'page' ) {
				return;
			}

			this.classList.add( 'is-loading' );

			e.preventDefault();
			const href = this.getAttribute( 'href' );

			setTimeout( () => {
				window.location.href = href;
			}, 300 );
		} );
	} );
} );

document.addEventListener( 'DOMContentLoaded', () => {
	const originHost = window.location.host;

	document.querySelectorAll( 'a[href^="http"]' ).forEach( ( link ) => {
		let url;

		try {
			url = new URL( link.getAttribute( 'href' ), window.location.origin );
		} catch ( error ) {
			return;
		}

		if ( url.host !== originHost ) {
			if ( ! link.hasAttribute( 'target' ) ) {
				link.setAttribute( 'target', '_blank' );
			}

			const rel = link.getAttribute( 'rel' ) || '';
			if ( ! rel.includes( 'noopener' ) ) {
				link.setAttribute(
					'rel',
					`${ rel } noopener noreferrer`.trim().replace( /\s+/g, ' ' )
				);
			}
		}
	} );
} );
