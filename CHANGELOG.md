# Changelog

Toutes les évolutions notables du projet LCD Tracking sont documentées ici.

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

## 🎨 Phase UX (prochaine discussion)

- Intégration logo
- Refactor visuel global
- Harmonisation badges / steps
- Amélioration feedback mobile
- UX upload photo (plus fluide)

---

## 🧱 Phase Stabilisation

- Statut confirmé / verrouillage intervention
- Auto-save brouillon
- Séparation logique photos / infos
- Nettoyage contrôleurs (refactor léger)

---

## 📩 Phase Communication

- Activation checkbox urgence
- Envoi email automatique
- WhatsApp si configuré
- Historique des échanges

---

# 💬 Conclusion

Vous êtes actuellement à un stade :

- 👉 Architecture propre
- 👉 Domaine métier clair
- 👉 Flux complet propriétaire / intervenant
- 👉 Aucune dette critique
- 👉 Design non finalisé
- 👉 Prêt pour phase UX
