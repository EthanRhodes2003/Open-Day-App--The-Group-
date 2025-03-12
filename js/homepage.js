// Toggle Mobile Menu
document.querySelector('.menu-toggle').addEventListener('click', () => {
  document.querySelector('.nav-links').classList.toggle('active');
});

// Close menu if clicking outside of navigation
document.addEventListener('click', (e) => {
  if (!e.target.closest('nav')) {
      document.querySelector('.nav-links').classList.remove('active');
  }
});

// Add transition delays for menu items
document.querySelectorAll('.nav-links li').forEach((item, index) => {
  item.style.transitionDelay = `${index * 0.1}s`;
});

// Smooth fade in effect when sections appear in viewport
const observerOptions = {
  threshold: 0.1  // Trigger when 10% of section is visible
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
      if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
          observer.unobserve(entry.target); // Stop observing once visible
      }
  });
}, observerOptions);

// Apply observer to all sections
document.querySelectorAll('section').forEach(section => {
  section.style.opacity = '0';
  section.style.transform = 'translateY(20px)';
  observer.observe(section);
});
