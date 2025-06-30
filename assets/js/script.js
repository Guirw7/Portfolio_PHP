// Navigation mobile
document.addEventListener('DOMContentLoaded', function() {
    // Toggle navigation mobile
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
        });
    }
    
    // Fermer le menu mobile au clic sur un lien
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
            }
        });
    });
    
    // Animation au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observer les éléments à animer
    const elementsToAnimate = document.querySelectorAll('.feature-card, .portfolio-card, .stat-card');
    elementsToAnimate.forEach(el => observer.observe(el));
    
    // Confirmation de suppression
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Êtes-vous sûr de vouloir supprimer cet élément ?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
    
    // Prévisualisation d'image
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = document.querySelector('.image-preview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.className = 'image-preview';
                        input.parentNode.appendChild(preview);
                    }
                    preview.innerHTML = `<img src="${e.target.result}" alt="Aperçu" style="max-width: 200px; max-height: 200px; border-radius: 8px; margin-top: 10px;">`;
                };
                reader.readAsDataURL(file);
            }
        });
    });
    
    // Auto-resize des textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
    
    // Validation des formulaires côté client
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('error');
                    isValid = false;
                } else {
                    field.classList.remove('error');
                }
            });
            
            // Validation email
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(field => {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (field.value && !emailRegex.test(field.value)) {
                    field.classList.add('error');
                    isValid = false;
                } else {
                    field.classList.remove('error');
                }
            });
            
            // Validation des mots de passe
            const passwordField = form.querySelector('input[name="password"]');
            const confirmPasswordField = form.querySelector('input[name="confirm_password"]');
            
            if (passwordField && confirmPasswordField) {
                if (passwordField.value !== confirmPasswordField.value) {
                    confirmPasswordField.classList.add('error');
                    isValid = false;
                } else {
                    confirmPasswordField.classList.remove('error');
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                
                // Scroll vers le premier champ en erreur
                const firstError = form.querySelector('.error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
        
        // Retirer la classe error lors de la saisie
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('error');
            });
        });
    });
    
    // Messages flash auto-hide
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
    
    // Lazy loading des images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => imageObserver.observe(img));
    }
    
    // Recherche en temps réel
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        let searchTimeout;
        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = this.value.toLowerCase();
            
            searchTimeout = setTimeout(() => {
                const searchableElements = document.querySelectorAll('.searchable');
                searchableElements.forEach(element => {
                    const text = element.textContent.toLowerCase();
                    if (text.includes(searchTerm) || searchTerm === '') {
                        element.style.display = '';
                    } else {
                        element.style.display = 'none';
                    }
                });
            }, 300);
        });
    });
});

// Fonctions utilitaires
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">&times;</button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Copié dans le presse-papiers !', 'success');
    }).catch(() => {
        showNotification('Erreur lors de la copie', 'error');
    });
}

// Gestion des uploads de fichiers
function handleFileUpload(input, callback) {
    const files = input.files;
    if (files.length > 0) {
        const file = files[0];
        
        // Vérification de la taille (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            showNotification('Le fichier est trop volumineux (max 2MB)', 'error');
            input.value = '';
            return;
        }
        
        // Vérification du type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            showNotification('Format de fichier non supporté', 'error');
            input.value = '';
            return;
        }
        
        if (callback) {
            callback(file);
        }
    }
}

// Dark mode toggle (bonus)
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
}

// Initialiser le dark mode au chargement
if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark-mode');
}

// Modal pour les projets
function openModal(title, desc, img, link) {
    const modal = document.getElementById('projectModal');
    const modalImg = document.getElementById('modalImage');
    const modalLink = document.getElementById('modalLink');
    const modalNoLink = document.getElementById('modalNoLink');

    // Reset classes
    modal.classList.remove('fade-out');
    modal.classList.add('active');
    modal.style.display = 'flex';

    // Contenu
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalDescription').innerHTML = desc;

    if (img) {
        modalImg.style.display = 'block';
        modalImg.src = img;
    } else {
        modalImg.style.display = 'none';
    }

    if (link && link !== '#') {
        modalLink.style.display = 'inline-block';
        modalLink.href = link;
        modalLink.textContent = 'Voir le projet complet';
        modalNoLink.style.display = 'none';
    } else {
        modalLink.style.display = 'none';
        modalNoLink.style.display = 'inline-block';
    }
}

// Fermer la modal
function closeModal() {
    const modal = document.getElementById('projectModal');

    // Laisse la modal visible pour jouer l'animation
    modal.classList.add('fade-out');
    modal.classList.remove('active');

    // Attend la fin de l'animation avant de cacher
    setTimeout(() => {
        modal.style.display = 'none';
        modal.classList.remove('fade-out');
    }, 300); // doit correspondre à la durée de l'animation CSS
}


// Liaison sur les cartes
document.querySelectorAll('.portfolio-card').forEach(card => {
    card.addEventListener('click', () => {
        const title = card.getAttribute('data-title');
        const desc = card.getAttribute('data-description');
        const img = card.getAttribute('data-image');
        const link = card.getAttribute('data-link');
        openModal(title, desc, img, link);
    });
});

// Gestion de la modal au chargement du document

document.addEventListener('DOMContentLoaded', function () {
    const modalOverlay = document.getElementById('projectModal');
    const modalContent = modalOverlay.querySelector('.project-modal');

    // Fermeture par clic en dehors de la modal
    modalOverlay.addEventListener('click', function (e) {
        if (!modalContent.contains(e.target)) {
            closeModal();
        }
    });

    // Fermeture par touche Échap
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
});

// Fonction pour basculer entre la vue en table et en cartes
function toggleView() {
    const viewInput = document.getElementById('viewInput');
    viewInput.value = viewInput.value === 'table' ? 'cards' : 'table';
    document.getElementById('filterForm').submit();
}

// Zoom image profil
document.querySelectorAll('.profile-preview').forEach(img => {
    img.addEventListener('click', () => {
        const modal = document.getElementById('imageModal');
        const zoomedImg = document.getElementById('zoomedImage');
        zoomedImg.src = img.src;
        modal.style.display = 'flex';
    });
});

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
}

// Ferme aussi avec Échap ou clic extérieur
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeImageModal();
});
document.getElementById('imageModal').addEventListener('click', e => {
    if (e.target.id === 'imageModal') closeImageModal();
});


// Gestion des projets
function openProjectModal(project) {
    document.getElementById("modalTitle").textContent = project.title;
    document.getElementById("modalDescription").textContent = project.description || "Aucune description";
    document.getElementById("modalImage").src = project.image ? `../uploads/${project.image}` : '';
    document.getElementById("modalImage").style.display = project.image ? 'block' : 'none';

    // Modifier / Supprimer
    document.getElementById("editLink").href = `edit-project.php?id=${project.id}`;
    document.getElementById("deleteLink").href = `delete-project.php?id=${project.id}`;

    // Lien externe
    if (project.link) {
        document.getElementById("modalLink").href = project.link;
        document.getElementById("modalLink").style.display = 'inline-block';
        document.getElementById("modalNoLink").style.display = 'none';
    } else {
        document.getElementById("modalLink").style.display = 'none';
        document.getElementById("modalNoLink").style.display = 'inline-block';
    }

    document.getElementById("projectModal").style.display = "flex";
}

function openImageModal(src) {
    document.getElementById('imageModalContent').src = src;
    document.getElementById('imageModal').style.display = 'flex';
}


