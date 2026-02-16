# Changelog

Toutes les évolutions notables du projet LCD Tracking sont documentées ici.

---

# [0.5.0] — Prestations, Infinite Scroll & UX Stabilisation (Beta)

## ➕ Ajouts

- Page **Prestations (Owner)**
  - Liste paginée des interventions
  - Filtres par :
    - Date début
    - Date fin
    - Logement
    - Statut (conforme / non conforme)
  - Méthode GET (URL partageable)
- Infinite scroll via Turbo Stream
- Pagination progressive (append et non replace)
- Scroll automatique avec IntersectionObserver
- Bouton fallback "Charger plus"

## 🎨 Améliorations UX

- Refonte cartes interventions
- Alignement responsive des actions (mobile / desktop)
- Lightbox photos custom (sans plugin externe)
- Bouton copie lien intervenant (Stimulus clipboard)
- Checkboxes style "button toggle" pour intervenant
- Uniformisation textarea (bordure comme select)
- Amélioration feedback visuel mobile

## 🔧 Corrections

- Correction pagination Turbo (append correct)
- Gestion correcte des query params page
- Stabilisation contrôleur infinite-pager
- Correction accès Doctrine (createdBy au lieu de worker)
- Correction affichage badge / alignement desktop

---

# [0.4.0] — Onboarding & Gestion Propriétaire

## ➕ Ajouts

- Inscription propriétaire
- Vérification email
- Authenticator personnalisé
- Onboarding en 3 étapes
- CRUD logements
- CRUD intervenants
- Recherche intervenant par téléphone
- Liaison intervenant existant
- Assignation intervenant → logement
- Cascade suppression logement → interventions → photos
- Dashboard KPI sur 14 jours glissants

## 🔧 Améliorations

- Sécurité renforcée Owner / Worker
- 404 strict si accès non autorisé
- Normalisation du `PropertyType`
- Uniformisation namespace `Owner`

---

# [0.3.0] — Boucle Intervenant Complète

## ➕ Ajouts

- Accès par token sécurisé
- Création automatique d’intervention
- Formulaire intervention complet
- Conformité calculée automatiquement
- Upload photos (max 10)
- Suppression photo
- Protection CSRF

---

# [0.2.0] — Structure Domaine

## ➕ Ajouts

- Entities : Property / Worker / Intervention
- Relations Doctrine
- Fixtures Faker
- Repository methods personnalisées
- Repository KPI

---

# [0.1.0] — Initialisation

## ➕ Ajouts

- Setup Symfony
- Authentification classique
- Structure de base
- CSS initial mobile-first

---

# 🧭 Roadmap Mise à Jour

## 🎨 Phase UX Finalisation

- Ajustement design global
- Uniformisation composants
- Amélioration expérience mobile terrain
- Optimisation upload photo mobile

---

## 🔒 Phase Stabilisation

- Verrouillage intervention confirmée
- Auto-save brouillon
- Nettoyage contrôleurs
- Tests fonctionnels

---

## 📩 Phase Communication

- Activation mode urgence
- Notifications email
- Intégration WhatsApp (optionnelle)
- Historique des échanges

---

# 💬 Conclusion

Le projet est maintenant :

- ✅ Fonctionnel propriétaire / intervenant
- ✅ Infinite scroll opérationnel
- ✅ UX mobile cohérente
- ✅ Structure stable
- 🚀 Prêt pour déploiement Beta
