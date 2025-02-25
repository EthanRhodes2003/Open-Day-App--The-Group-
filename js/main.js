document.querySelector('.menu-toggle').addEventListener('click', () => {
    document.querySelector('.nav-links').classList.toggle('active');
  });
  
  document.addEventListener('click', (e) => {
    if (!e.target.closest('nav')) {
      document.querySelector('.nav-links').classList.remove('active');
    }
  });
  
  document.querySelectorAll('.nav-links li').forEach((item, index) => {
    item.style.transitionDelay = `${index * 0.1}s`;
  });
  
  const observerOptions = {
    threshold: 0.1
  };
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);
  
  document.querySelectorAll('section').forEach(section => {
    section.style.opacity = '0';
    section.style.transform = 'translateY(20px)';
    observer.observe(section);
  });
  