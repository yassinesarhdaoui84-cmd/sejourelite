<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <div class="logo">Séjour<span>Élite</span></div>
      <p>L'excellence hôtelière à portée de clic.</p>
    </div>
    <div class="footer-links">
      <h4>Navigation</h4>
      <a href="/hotel/index.php">Accueil</a>
      <a href="/hotel/pages/chambres.php">Chambres</a>
      <a href="/hotel/pages/contact.php">Contact</a>
    </div>
    <div class="footer-links">
      <h4>Légal</h4>
      <a href="#">Mentions légales</a>
      <a href="#">Politique de confidentialité</a>
      <a href="#">CGU</a>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© <?= date('Y') ?> <strong>SéjourÉlite</strong> — Projet de Fin d'Études</p>
  </div>
</footer>
<script>
// ============================================================
//  SéjourÉlite — Gestion JavaScript Finale et Stable
// ============================================================

// 1. Fonction d'ouverture globale appelée par vos boutons onclick
function openModal(id, titre, localisation, prixNuit) {
    const modal = document.getElementById('modal-overlay');
    if (modal) {
        // Ajout de la classe d'origine attendue par votre style.css
        modal.classList.add('active');

        // Pré-remplissage des champs du formulaire de la modale
        if (document.getElementById('modalChambreId')) {
            document.getElementById('modalChambreId').value = id;
        }
        
        // Stockage du prix pour le calcul automatique des nuits
        const inputPrix = document.getElementById('f-prix-nuit');
        if (inputPrix) {
            inputPrix.value = prixNuit;
            if (typeof recalcPrice === 'function') recalcPrice();
        }
    } else {
        alert("Erreur : L'élément HTML 'modal-overlay' est introuvable sur cette page.");
    }
}

// 2. Fonction de fermeture liée à votre bouton d'annulation
function closeModal() {
    const modal = document.getElementById('modal-overlay');
    if (modal) {
        modal.classList.remove('active');
    }
}

// 3. Calcul dynamique du prix total dans la modale
function recalcPrice() {
    const prixNuitInput = document.getElementById('f-prix-nuit');
    const prixNuit = parseFloat(prixNuitInput ? prixNuitInput.value : 0);
    
    const checkinInput = document.getElementById('f-checkin');
    const checkoutInput = document.getElementById('f-checkout');
    const summaryEl = document.getElementById('price-summary');
    
    if (!summaryEl || !prixNuit) return;

    let nuits = 1;
    if (checkinInput && checkoutInput && checkinInput.value && checkoutInput.value) {
        const diff = Math.round((new Date(checkoutInput.value) - new Date(checkinInput.value)) / 86400000);
        if (diff > 0) nuits = diff;
    }

    const sousTotal = prixNuit * nuits;
    const frais = 20;
    const total = sousTotal + frais;

    summaryEl.innerHTML = `
        <div class="price-row"><span>${nuits} nuit${nuits > 1 ? 's' : ''} × ${prixNuit} €</span><span>${sousTotal} €</span></div>
        <div class="price-row"><span>Frais de service</span><span>${frais} €</span></div>
        <div class="price-row total"><span>Total</span><span>${total} €</span></div>
    `;
    
    const totalHidden = document.getElementById('f-montant-total');
    if (totalHidden) totalHidden.value = total;
}

// 4. Initialisation des événements au chargement complet du DOM
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des dates minimales (interdire de réserver dans le passé)
    const today = new Date().toISOString().split('T')[0];
    ['arrival', 'departure', 'f-checkin', 'f-checkout'].forEach(function(id) {
        const el = document.getElementById(id);
        if (el) el.min = today;
    });

    // Écouter les changements de date dans la modale pour recalculer le prix
    document.addEventListener('change', function(e) {
        if (e.target.id === 'f-checkin' || e.target.id === 'f-checkout') {
            recalcPrice();
        }
    });

    // Intercepter la soumission du formulaire de réservation (Traitement AJAX)
    const form = document.querySelector('#modal-overlay form');
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Empêcher le rechargement brut de la page PHP

            const formData = new FormData(form);
            fetch('/hotel/pages/reserver.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Erreur réseau d\'envoi');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Masquer le formulaire et afficher l'état de succès natif du CSS
                    const formState = document.getElementById('form-state');
                    const successState = document.querySelector('.success-state');
                    
                    if (formState && successState) {
                        formState.style.display = 'none';
                        successState.classList.add('visible');
                    } else {
                        alert(data.message);
                        closeModal();
                    }
                    form.reset();
                } else {
                    alert('Erreur : ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur AJAX:', error);
                alert('Impossible de valider la réservation sur le serveur MAMP.');
            });
        });
    }
});
</script>

</body>
</html>