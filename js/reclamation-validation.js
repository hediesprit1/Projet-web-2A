document.addEventListener('DOMContentLoaded', function() {
    const MIN_CHARS = 10; // Nombre minimum de caractères requis
    
    /**
     * Initialise la validation pour un champ textarea spécifique
     * @param {HTMLTextAreaElement} textField - Le champ textarea à valider
     */
    function initFieldValidation(textField) {
        if (!textField) return;
        
        // Validation en temps réel lors de la frappe
        textField.addEventListener('input', function() {
            const charCount = this.value.trim().length;
            
            // Créer ou trouver l'élément de feedback
            let feedbackEl = this.nextElementSibling;
            if (!feedbackEl || !feedbackEl.classList.contains('char-count-feedback')) {
                feedbackEl = document.createElement('div');
                feedbackEl.className = 'char-count-feedback mt-1 small';
                this.parentNode.insertBefore(feedbackEl, this.nextSibling);
            }
            
            // Mettre à jour le message de feedback selon le nombre de caractères
            if (charCount < MIN_CHARS) {
                feedbackEl.textContent = `Il manque ${MIN_CHARS - charCount} caractère(s). Minimum: ${MIN_CHARS}.`;
                feedbackEl.className = 'char-count-feedback mt-1 small text-danger';
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            } else {
                feedbackEl.textContent = `Longueur valide (${charCount} caractères)`;
                feedbackEl.className = 'char-count-feedback mt-1 small text-success';
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
        
        // Déclencher l'événement input pour initialiser le compteur
        const inputEvent = new Event('input');
        textField.dispatchEvent(inputEvent);
    }
    
    /**
     * Initialise la validation pour un formulaire spécifique
     * @param {HTMLFormElement} form - Le formulaire à valider
     * @param {string} fieldSelector - Le sélecteur CSS pour trouver le champ textarea
     */
    function initFormValidation(form, fieldSelector) {
        if (!form) return;
        
        const textField = form.querySelector(fieldSelector);
        if (!textField) return;
        
        // Initialiser la validation du champ
        initFieldValidation(textField);
        
        // Validation à la soumission
        form.addEventListener('submit', function(event) {
            const charCount = textField.value.trim().length;
            
            if (charCount < MIN_CHARS) {
                event.preventDefault(); // Empêcher la soumission du formulaire
                alert(`Le message doit contenir au moins ${MIN_CHARS} caractères.`);
                textField.focus();
            }
        });
    }
    
    // Initialiser tous les formulaires de validation des réclamations
    const validateForms = document.querySelectorAll('form[action*="backoffice_reclamation_validate"]');
    validateForms.forEach(form => {
        initFormValidation(form, 'textarea[name="validation_message"]');
    });
    
    // Initialiser tous les formulaires de réponse
    const responseForms = document.querySelectorAll('form[action*="backoffice_response_create"]');
    responseForms.forEach(form => {
        initFormValidation(form, 'textarea[name="description"]');
    });
}); 