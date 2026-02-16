# LCD Tracking

Application web destinée à formaliser et tracer les validations clés de fin de séjour et de ménage pour les locations courte durée.

---

## 🎯 Objectif

👉 Fournir une **trace factuelle, horodatée et simple** entre propriétaires et intervenants,  
sans friction ni complexité inutile.

---

# 🚀 MVP Fonctionnel Actuel

## 👤 Rôles

### Propriétaire (Owner)

- Inscription classique (email / mot de passe)
- Vérification email (non bloquante)
- Authentification sécurisée
- Dashboard avec KPI
- Gestion des logements
- Gestion des intervenants
- Assignation intervenant ↔ logement
- Parcours onboarding guidé
- Liste des interventions avec filtres avancés
- Pagination infinie (infinite scroll via Turbo)

---

### 👷 Intervenant (Worker)

- Accès via lien sécurisé par token :  
  `/w/{token}`
- Aucun compte requis
- Interface mobile-first
- Création automatique de l’intervention du jour
- Modification autorisée tant que non confirmée
- Upload photo mobile (caméra native)

---

# 🧭 Onboarding Propriétaire

Après inscription :

1. Ajouter un logement  
2. Ajouter ou lier un intervenant  
3. Assigner l’intervenant à un logement  

### Statuts dynamiques :

- ✅ Fait  
- ⚠️ À faire  
- 🔒 Bloqué  
- 🟢 Onboarding terminé  

---

# 🏠 Logements (Property)

- Un logement appartient à un propriétaire
- Assignation possible à un intervenant
- Lien sécurisé copiable pour accès intervenant (fonction copier)
- Suppression en cascade :
  - Interventions
  - Photos liées

CRUD complet côté propriétaire.

---

# 👷 Intervenants (Worker)

- Création manuelle
- Recherche par téléphone
- Liaison à un propriétaire existant
- Un intervenant peut travailler pour plusieurs propriétaires
- Suppression volontairement désactivée (évite incohérences multi-propriétaires)

Accès via :
/w/{accessToken}

Token invalide ⇒ 404

---

# 📝 Interventions

## 📌 Règles métier

- 1 intervention maximum par logement / jour
- Date métier basée sur Europe/Paris
- Création automatique au premier accès
- Modifiable tant que non confirmée
- Historique limité (suppression des interventions > 6 mois)

---

## 📋 Données saisies

### Sortie voyageurs

- À l’heure (oui / non / vide)
- Consignes respectées (oui / non / vide)
- Commentaire libre

### Ménage

Checklist :

- Lit fait
- Sol propre
- Salle de bain OK
- Cuisine OK
- Linge changé

Commentaire ménage libre.

---

## ✅ Conformité

Une intervention est conforme uniquement si **tous les checks ménage sont validés**.

La partie sortie voyageurs n’impacte pas la conformité.

---

# 📷 Photos

- Maximum 10 photos par intervention
- Upload mobile-first (caméra native)
- Stockage local
- Suppression possible
- Suppression automatique si intervention ou logement supprimé
- Visualisation avec lightbox custom (sans plugin externe)

---

# 📊 Dashboard Propriétaire

KPI sur 14 jours glissants :

- Nombre de logements
- Nombre d’interventions
- Nombre d’interventions non conformes
- Dernière intervention par logement

Liste des interventions :

- Filtres GET :
  - Date début
  - Date fin
  - Logement
  - Statut (conforme / non conforme)
- Infinite scroll (Turbo Stream + Stimulus)

Accès protégé : `ROLE_OWNER`

---

# 🔐 Sécurité

- CSRF sur tous les formulaires
- Token sécurisé pour accès intervenant
- Vérification stricte Owner / Worker
- 404 systématique en cas d’accès non autorisé
- Cascade Doctrine cohérente
- Données cloisonnées par propriétaire

---

# 🧪 Fixtures

- Faker
- Données réalistes
- Historique glissant

---

# 🧱 Stack Technique

- Symfony 8
- Doctrine ORM
- Twig
- Stimulus
- Turbo (Hotwire)
- SCSS custom (mobile-first)
- SQLite / MySQL

---

# 📝 Philosophie du Projet

- Simplicité terrain
- Mobile-first
- Zéro friction intervenant
- Pas de sur-engineering
- Refactor uniquement quand nécessaire
- MVP orienté usage réel

---

# ✅ État Actuel

- ✅ Boucle intervenant complète
- ✅ Boucle propriétaire fonctionnelle
- ✅ Onboarding opérationnel
- ✅ Registration + vérification email
- ✅ Gestion logements + intervenants
- ✅ Assignation fonctionnelle
- ✅ Infinite scroll opérationnel
- ✅ Upload & preview photo mobile
- ✅ Sécurité stable

---

# 🧪 Version Actuelle

🚧 **Beta privée – Phase test terrain**

Objectif :  
Valider le flux réel avec 1–2 propriétaires pilotes avant ouverture élargie.

---

## 🎯 Prochaines étapes

- 🔹 Mise en ligne bêta
- 🔹 Tests réels terrain
- 🔹 Optimisations UX fines
- 🔹 Amélioration design
- 🔹 Monitoring erreurs

---

## 📌 Licence

Projet privé – Tous droits réservés.