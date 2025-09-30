/**
 * Hero Neon Button - React Mount Point
 * 
 * Mounts the React-based neon jelly button component into the hero block
 */
import { createRoot } from 'react-dom/client';
import { InteractiveNeonButton } from './components/NeonJellyButton.jsx';

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', () => {
  // Find all mount points for the neon button
  const mountPoints = document.querySelectorAll('.hero-neon-button-mount');
  
  mountPoints.forEach((container) => {
    // Get button configuration from data attributes
    const buttonText = container.dataset.buttonText || 'Start a project';
    const buttonLink = container.dataset.buttonLink || '#';
    
    // Handle click - navigate to link
    const handleClick = () => {
      if (buttonLink && buttonLink !== '#') {
        window.location.href = buttonLink;
      }
    };
    
    // Create React root and render
    const root = createRoot(container);
    root.render(
      <InteractiveNeonButton 
        label={buttonText}
        onClick={handleClick}
      />
    );
  });
});
