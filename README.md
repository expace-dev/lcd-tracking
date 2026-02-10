# LCD Tracking

Application web destinée à formaliser et tracer les validations clés de fin de séjour et de ménage
pour les locations courte durée.

Objectif principal :  
👉 fournir une **trace factuelle, horodatée et simple** entre propriétaires et intervenants,
sans friction ni complexité inutile.

---

## 🎯 Périmètre actuel (MVP fonctionnel)

### Rôles
- **Propriétaire (Owner)**
  - Accès via authentification classique (email / mot de passe)
- **Intervenant (Worker)**
  - Accès via **lien sécurisé par token**
  - Aucun compte, aucune authentification lourde

---

### Logements (Property)
- Un logement appartient à **un propriétaire**
- Un logement est assigné à **un seul intervenant** (pour l’instant)
- Suppression d’un logement ⇒ suppression en cascade des interventions associées

---

### Interventions (Intervention)
- **1 intervention maximum par logement et par jour**
- Date métier basée sur `Europe/Paris`
- Création automatique lors du premier accès de l’intervenant
- L’intervention reste **modifiable tant qu’elle n’est pas confirmée**

#### Données saisies
**Sortie voyageurs**
- Voyageurs sortis à l’heure (oui / non / vide)
- Consignes respectées (oui / non / vide)
- Commentaire libre (optionnel)

**Ménage**
- Checklist (non obligatoire) :
  - Lit fait
  - Sol propre
  - Salle de bain OK
  - Cuisine OK
  - Linge changé
- Commentaire ménage (optionnel)

#### Conformité
- Une intervention est **conforme** uniquement si **tous les checks ménage sont validés**
- La partie “sortie voyageurs” n’impacte pas la conformité
- En cas de non-conformité → badge visuel côté propriétaire (orange)

---

### Photos
- Jusqu’à **10 photos par intervention**
- Upload depuis appareil (mobile first)
- Stockage local (`/public/uploads/interventions/{id}`)
- Suppression possible par l’intervenant
- Suppression automatique si l’intervention ou le logement est supprimé

---

## 🔐 Sécurité

- Accès intervenant via URL :
- Token invalide ⇒ 404
- Un intervenant ne peut accéder **qu’à ses propres interventions**
- Aucune donnée sensible exposée côté intervenant
- CSRF actif sur formulaires

---

## 📊 Vue propriétaire (en cours)

- Dashboard protégé (`/owner/dashboard`)
- KPI sur **14 jours glissants** :
- Nombre de logements
- Nombre d’interventions
- Nombre d’interventions non conformes
- Dernière intervention par logement (tri par date)

---

## 🧪 Données de test
- Fixtures uniques avec Faker
- Données réalistes :
- propriétaires
- intervenants
- logements
- interventions sur ~14 jours

---

## 🚧 Points volontairement différés / à reprendre plus tard

Ces points sont **connus et assumés**, mais non bloquants pour le MVP :

### Workflow intervention
- Confirmation définitive / verrouillage
- Gestion avancée du statut (`draft`, `confirmed`)
- Autosave du brouillon
- Séparation éventuelle du formulaire (infos / photos)

### Communication
- Envoi d’alerte “urgence” au propriétaire :
- email (par défaut)
- WhatsApp si configuré
- Checkbox “demande urgente” déjà prévue côté intervention

### Évolutions futures
- Conciergerie (plusieurs intervenants par logement)
- Application mobile dédiée (l’app actuelle est responsive)
- Refactor upload photo (AJAX / UX améliorée)
- Webpack / assets pipeline (CSS actuel volontairement simple)

---

## 🧱 Stack technique

- Symfony 8
- Doctrine ORM
- Twig
- SQLite / MySQL (selon environnement)
- CSS custom (sans framework, mobile-first)

---

## 📝 Philosophie du projet

- Simplicité > exhaustivité
- Fait pour le terrain (mobile first)
- Zéro friction pour l’intervenant
- Trace factuelle avant tout
- Refactorisation prévue **quand elle apporte de la valeur**

---

## ✅ État actuel

👉 **Boucle intervenant complète et fonctionnelle**  
👉 **Base propriétaire solide**  
👉 **Aucune dette technique bloquante**

Le projet est prêt pour itérations fonctionnelles et retours terrain.

## 🧭 Roadmap (indicative)

### Phase 1 — MVP terrain (EN COURS)
- ✅ Accès intervenant par lien token
- ✅ Création / édition d’une intervention
- ✅ Checklist ménage + conformité
- ✅ Ajout / suppression de photos (max 10)
- ✅ Dashboard propriétaire (KPI simples)
- ✅ Données de test (fixtures)

### Phase 2 — Stabilisation & UX
- 🔒 Confirmation définitive d’intervention (verrouillage)
- 💾 Sauvegarde automatique du brouillon
- 🧭 Amélioration navigation mobile (retours, scroll, feedback)
- 🖼️ UX upload photo (AJAX / retour immédiat)
- 🧼 Séparation logique “infos / photos”

### Phase 3 — Communication & alertes
- 📩 Envoi email automatique au propriétaire
- ⚠️ Checkbox “demande urgente”
- 📱 Envoi WhatsApp si configuré côté propriétaire
- 🗂️ Historique des échanges liés à une intervention

### Phase 4 — Multi-intervenants / conciergerie
- 👥 Plusieurs intervenants par logement
- 🏢 Mode conciergerie (permissions spécifiques)
- 📊 Reporting avancé

### Phase 5 — App mobile
- 📱 Application mobile dédiée (intervenant uniquement)
- 🔔 Notifications push