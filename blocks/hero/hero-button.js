import React from 'react';
import { createRoot } from 'react-dom/client';
import { InteractiveNeonButton } from './components/NeonJellyButton.jsx';

const MOUNT_SELECTOR = '.hero-neon-button-mount';
const ROOT_CLASS = 'hero-neon-button-react-root';
const HYDRATED_CLASS = 'hero-neon-button-mount--hydrated';

const preferReducedMotionQuery =
  typeof window !== 'undefined' && typeof window.matchMedia === 'function'
    ? window.matchMedia('(prefers-reduced-motion: reduce)')
    : null;

const getReducedMotion = () => preferReducedMotionQuery?.matches ?? false;

const motionHandlers = new WeakMap();
const reactRoots = new WeakMap();

const ensureRoot = (container) => {
  let host = container.querySelector(`:scope > .${ROOT_CLASS}`);

  if (!host) {
    host = document.createElement('div');
    host.className = ROOT_CLASS;
    container.appendChild(host);
  }

  return host;
};

const hideFallback = (fallbackEl) => {
  if (!fallbackEl) {
    return;
  }

  fallbackEl.setAttribute('aria-hidden', 'true');
  fallbackEl.setAttribute('tabindex', '-1');
  fallbackEl.classList.add('hero-neon-button-fallback--hidden');
};

const mountButton = (container) => {
  if (!container || container.dataset.heroReactMounted === 'true') {
    return;
  }

  const fallback = container.querySelector('[data-hero-fallback]');
  const fallbackLabelNode = fallback?.querySelector('.hero-neon-button-fallback__label') || fallback;

  const label = (container.dataset.buttonText || fallbackLabelNode?.textContent || '').trim() || 'Start a project';
  const candidateHref = (container.dataset.buttonLink || '').trim();
  const fallbackHref = fallback instanceof HTMLAnchorElement ? (fallback.getAttribute('href') || '').trim() : '';
  const href = [candidateHref, fallbackHref].find((value) => value && value !== '#') || '';
  const candidateTarget = (container.dataset.buttonTarget || '').trim();
  const fallbackTarget =
    fallback instanceof HTMLAnchorElement ? (fallback.getAttribute('target') || '').trim() : '';
  const target = [candidateTarget, fallbackTarget].find((value) => value) || '';
  const candidateRel = (container.dataset.buttonRel || '').trim();
  const fallbackRel =
    fallback instanceof HTMLAnchorElement ? (fallback.getAttribute('rel') || '').trim() : '';
  const rel = [candidateRel, fallbackRel].find((value) => value) || '';

  const rootNode = ensureRoot(container);
  const root = createRoot(rootNode);
  reactRoots.set(container, root);

  const render = (reducedMotion) => {
    root.render(
      <InteractiveNeonButton
        label={label}
        href={href || undefined}
        target={target || undefined}
        rel={rel || undefined}
        onClick={
          href
            ? undefined
            : () => {
                if (!fallback) {
                  return;
                }

                try {
                  if (typeof fallback.click === 'function') {
                    fallback.click();
                  } else {
                    fallback.dispatchEvent(new Event('click', { bubbles: true }));
                  }
                } catch (error) {
                  // eslint-disable-next-line no-console
                  console.error('Unable to trigger hero CTA fallback activation', error);
                }
              }
        }
        strobe={!reducedMotion}
        showOrbiters={!reducedMotion}
        showPointerTrail={!reducedMotion}
        showBurst={!reducedMotion}
      />
    );
  };

  render(getReducedMotion());

  if (preferReducedMotionQuery?.addEventListener || preferReducedMotionQuery?.addListener) {
    const handler = (event) => render(event.matches);

    if (preferReducedMotionQuery.addEventListener) {
      preferReducedMotionQuery.addEventListener('change', handler);
    } else {
      preferReducedMotionQuery.addListener(handler);
    }

    motionHandlers.set(container, handler);
  }

  hideFallback(fallback);
  container.dataset.heroReactMounted = 'true';
  container.classList.add(HYDRATED_CLASS);
};

const cleanupContainer = (container) => {
  const fallback = typeof container.querySelector === 'function' ? container.querySelector('[data-hero-fallback]') : null;

  if (fallback) {
    fallback.removeAttribute('aria-hidden');
    fallback.removeAttribute('tabindex');
    fallback.classList.remove('hero-neon-button-fallback--hidden');
  }

  const handler = motionHandlers.get(container);
  const root = reactRoots.get(container);

  if (root) {
    root.unmount();
    reactRoots.delete(container);
  }

  if (!handler || !preferReducedMotionQuery) {
    container.classList.remove(HYDRATED_CLASS);
    delete container.dataset.heroReactMounted;
    return;
  }

  if (preferReducedMotionQuery.removeEventListener) {
    preferReducedMotionQuery.removeEventListener('change', handler);
  } else if (preferReducedMotionQuery.removeListener) {
    preferReducedMotionQuery.removeListener(handler);
  }

  motionHandlers.delete(container);
  container.classList.remove(HYDRATED_CLASS);
  delete container.dataset.heroReactMounted;
};

const mountAllButtons = (root = document) => {
  const containers = root.querySelectorAll ? root.querySelectorAll(MOUNT_SELECTOR) : [];
  containers.forEach(mountButton);
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => mountAllButtons());
} else {
  mountAllButtons();
}

const observer = new MutationObserver((mutations) => {
  mutations.forEach((mutation) => {
    mutation.addedNodes.forEach((node) => {
      if (!(node instanceof Element)) {
        return;
      }

      if (node.matches(MOUNT_SELECTOR)) {
        mountButton(node);
      }

      node.querySelectorAll?.(MOUNT_SELECTOR).forEach(mountButton);
    });

    mutation.removedNodes.forEach((node) => {
      if (!(node instanceof Element)) {
        return;
      }

      const removedContainers = [];

      if (node.matches(MOUNT_SELECTOR)) {
        removedContainers.push(node);
      }

      node.querySelectorAll?.(MOUNT_SELECTOR).forEach((el) => removedContainers.push(el));

      removedContainers.forEach(cleanupContainer);
    });
  });
});

if (document.body) {
  observer.observe(document.body, { childList: true, subtree: true });
} else {
  document.addEventListener('DOMContentLoaded', () => {
    observer.observe(document.body, { childList: true, subtree: true });
  });
}
