<div id="modal-overlay" class="modal-overlay">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">

    <!-- FORMULAIRE -->
    <div id="form-state">
      <div class="modal-header">
        <div>
          <div class="modal-hotel-name" id="modal-title">Chambre</div>
          <div class="modal-hotel-sub"  id="modal-sub"></div>
        </div>
        <button class="modal-close" onclick="closeModal()" aria-label="Fermer">✕</button>
      </div>

      <div class="modal-body">
        <form id="reservation-form" onsubmit="inscrireReservation(event)">
          <input type="hidden" id="f-chambre-id"     name="chambre_id" value="" />
          <input type="hidden" id="f-prix-nuit"       name="prix_nuit"  value="" />
          <input type="hidden" id="f-montant-total"   name="montant_total" value="" />

          <div class="form-row">
            <div class="form-group">
              <label for="f-nom">Nom *</label>
              <input type="text" id="f-nom" name="nom" placeholder="Dupont" autocomplete="family-name" />
              <span class="form-error"></span>
            </div>
            <div class="form-group">
              <label for="f-prenom">Prénom *</label>
              <input type="text" id="f-prenom" name="prenom" placeholder="Marie" autocomplete="given-name" />
              <span class="form-error"></span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group full">
              <label for="f-email">Adresse e-mail *</label>
              <input type="email" id="f-email" name="email" placeholder="marie@email.com" autocomplete="email" />
              <span class="form-error"></span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="f-telephone">Téléphone</label>
              <input type="tel" id="f-telephone" name="telephone" placeholder="+33 6 12 34 56 78" />
            </div>
            <div class="form-group">
              <label for="f-guests-modal">Personnes</label>
              <select id="f-guests-modal" name="nb_personnes">
                <option value="1">1 personne</option>
                <option value="2" selected>2 personnes</option>
                <option value="3">3 personnes</option>
                <option value="4">4 personnes</option>
                <option value="5">5+ personnes</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="f-checkin">Arrivée *</label>
              <input type="date" id="f-checkin" name="date_arrivee" />
              <span class="form-error"></span>
            </div>
            <div class="form-group">
              <label for="f-checkout">Départ *</label>
              <input type="date" id="f-checkout" name="date_depart" />
              <span class="form-error"></span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group full">
              <label for="f-paiement">Mode de paiement</label>
              <select id="f-paiement" name="paiement">
                <option value="carte">Carte bancaire</option>
                <option value="virement">Virement bancaire</option>
                <option value="paypal">PayPal</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group full">
              <label for="f-notes">Demandes spéciales (optionnel)</label>
              <textarea id="f-notes" name="notes" placeholder="Ex : chambre non-fumeur, lit bébé, arrivée tardive…"></textarea>
            </div>
          </div>

          <div class="price-summary" id="price-summary">
            <div class="price-row"><span>Sélectionnez vos dates</span><span>—</span></div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button class="btn-cancel" onclick="closeModal()">Annuler</button>
        <button type="submit" class="btn-confirm">Confirmer la réservation</button>
      </div>
    </div>

    <!-- SUCCÈS -->
    <div class="success-state" id="success-state">
      <div class="success-icon">✓</div>
      <h3>Réservation confirmée !</h3>
      <p>Votre réservation a bien été enregistrée. Un e-mail de confirmation vous sera envoyé sous peu. Merci de votre confiance.</p>
    </div>

  </div>
</div>
