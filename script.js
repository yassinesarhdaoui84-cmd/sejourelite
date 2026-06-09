// ============================================================
//  SéjourÉlite — Logiciel de Gestion de Réservation (main.js)
// ============================================================

/**
 * Ouvre la fenêtre modale de réservation et configure les données de la chambre
 */
function openModal(id, titre, localisation, prixNuit) {
    const modal = document.getElementById('modal-overlay');
    if (modal) {
        // Reset l'affichage interne (au cas où une ancienne réservation était en succès)
        const formState = document.getElementById('form-state');
        const successState = document.getElementById('success-state');
        if (formState) formState.style.display = 'block';
        if (successState) successState.style.display = 'none';

        // Ouvre l'overlay avec la classe d'origine du CSS
        modal.classList.add('active');

        // Met à jour les textes de la modale
        const titleEl = document.getElementById('modal-title');
        const subEl = document.getElementById('modal-sub');
        if (titleEl) titleEl.innerText = titre;
        if (subEl) subEl.innerText = localisation;

        // Assigne les valeurs masquées du formulaire
        if (document.getElementById('f-chambre-id')) document.getElementById('f-chambre-id').value = id;
        if (document.getElementById('f-prix-nuit')) {
            document.getElementById('f-prix-nuit').value = prixNuit;
            recalcPrice(); // Relancer le calcul initial
        }
    } else {
        alert("Erreur : La modale 'modal-overlay' n'existe pas dans le code HTML.");
    }
}

/**
 * Ferme la fenêtre modale de réservation
 */
function closeModal() {
    const modal = document.getElementById('modal-overlay');
    if (modal) {
        modal.classList.remove('active');
    }
}

/**
 * Calcule dynamiquement le prix total selon les dates choisies
 */
function recalcPrice() {
    const prixNuitInput = document.getElementById('f-prix-nuit');
    const prixNuit = parseFloat(prixNuitInput ? prixNuitInput.value : 0);

    const checkinInput = document.getElementById('f-checkin');
    const checkoutInput = document.getElementById('f-checkout');
    const summaryEl = document.getElementById('price-summary');

    if (!summaryEl || !prixNuit) return;

    const cin = checkinInput ? checkinInput.value : null;
    const cout = checkoutInput ? checkoutInput.value : null;

    let nuits = 1;
    if (cin && cout) {
        const diff = Math.round((new Date(cout) - new Date(cin)) / 86400000);
        if (diff > 0) nuits = diff;
    }

    const sousTotal = prixNuit * nuits;
    const frais = 20; // Frais de service fixes du CSS
    const total = sousTotal + frais;

    summaryEl.innerHTML = `
        <div class="price-row"><span>${nuits} nuit${nuits > 1 ? 's' : ''} × ${prixNuit} €</span><span>${sousTotal} €</span></div>
        <div class="price-row"><span>Frais de service</span><span>${frais} €</span></div>
        <div class="price-row total"><span>Total</span><span>${total} €</span></div>
    `;

    const totalHidden = document.getElementById('f-montant-total');
    if (totalHidden) totalHidden.value = total;
}

/**
 * Valide les champs requis du formulaire avant envoi
 */
function validateForm() {
    let valid = true;
    const requiredFields = [
        { id: 'f-nom', msg: 'Le nom est requis.' },
        { id: 'f-prenom', msg: 'Le prénom est requis.' },
        { id: 'f-email', msg: 'L\'adresse e-mail est requise.' },
        { id: 'f-checkin', msg: 'La date d\'arrivée est requise.' },
        { id: 'f-checkout', msg: 'La date de départ est requise.' }
    ];

    requiredFields.forEach(field => {
        const el = document.getElementById(field.id);
        const group = el ? el.closest('.form-group') : null;
        const errEl = group ? group.querySelector('.form-error') : null;

        if (!el || !el.value.trim()) {
            valid = false;
            if (group) group.classList.add('has-error');
            if (errEl) errEl.textContent = field.msg;
        } else {
            if (group) group.classList.remove('has-error');
            if (errEl) errEl.textContent = '';
        }
    });

    // Vérification cohérence des dates
    const cin = document.getElementById('f-checkin') ? .value;
    const cout = document.getElementById('f-checkout') ? .value;
    if (cin && cout && new Date(cout) <= new Date(cin)) {
        valid = false;
        const checkoutEl = document.getElementById('f-checkout');
        const group = checkoutEl ? checkoutEl.closest('.form-group') : null;
        const errEl = group ? group.querySelector('.form-error') : null;
        if (group) group.classList.add('has-error');
        if (errEl) errEl.textContent = 'La date de départ doit être après la date d\'arrivée.';
    }

    return valid;
}

/**
 * Envoie les données de réservation au serveur via AJAX (déclenché par le bouton Confirmer)
 */
function submitReservation() {
    if (!validateForm()) return;

    const form = document.getElementById('reservation-form');
    if (!form) return;

    const formData = new FormData(form);

    fetch('/hotel/pages/reserver.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Erreur de communication réseau.');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Masque le formulaire et affiche l'interface de succès native
                const formState = document.getElementById('form-state');
                const successState = document.getElementById('success-state');
                if (formState) formState.style.display = 'none';
                if (successState) successState.style.display = 'flex';
                form.reset();
            } else {
                alert('Erreur : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Impossible de joindre le système de réservation MAMP.');
        });
}

/**
 * Gestion du module de recherche de la page principale
 */
function handleSearch() {
    const arrival = document.getElementById('arrival') ? .value;
    const departure = document.getElementById('departure') ? .value;
    const guests = document.getElementById('guests') ? .value || 2;

    if (!arrival || !departure) {
        alert('Veuillez sélectionner vos dates d\'arrivée et de départ.');
        return;
    }
    window.location.href = '/hotel/pages/chambres.php?arrivee=' + arrival + '&depart=' + departure + '&personnes=' + guests;
}

// Configuration des événements dynamiques au chargement du site
document.addEventListener('DOMContentLoaded', function() {
    // Bloquer les réservations dans le passé
    const today = new Date().toISOString().split('T')[0];
    ['arrival', 'departure', 'f-checkin', 'f-checkout'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.min = today;
    });

    // Écouter les modifications de dates pour recalculer le tarif global
    document.addEventListener('change', function(e) {
        if (e.target.id === 'f-checkin' || e.target.id === 'f-checkout') {
            recalcPrice();
        }
    });
});
// Ce bloc est exécuté dès que le serveur MAMP répond "success"
if (data.success) {
    const formState = document.getElementById('form-state');
    const successState = document.getElementById('success-state');

    if (formState && successState) {
        formState.style.display = 'none'; // Cache les champs de saisie
        successState.classList.add('visible'); // Déclenche l'affichage du ✓
    }
}