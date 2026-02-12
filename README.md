LCD Tracking

Application web destinée à formaliser et tracer les validations clés de fin de séjour et de ménage pour les locations courte durée.

Objectif principal :
👉 fournir une trace factuelle, horodatée et simple entre propriétaires et intervenants,
sans friction ni complexité inutile.

🎯 MVP fonctionnel actuel
👤 Rôles
Propriétaire (Owner)

Inscription classique (email / mot de passe)

Vérification email (non bloquante)

Authentification sécurisée

Dashboard avec KPI

Gestion des logements

Gestion des intervenants

Parcours onboarding guidé

Intervenant (Worker)

Accès via lien sécurisé par token (/w/{token})

Aucun compte

Interface mobile-first

Création automatique d’intervention du jour

Modification autorisée tant que non confirmée

🧭 Onboarding propriétaire

Après inscription :

Ajouter un logement

Ajouter ou lier un intervenant

Assigner l’intervenant à un logement

Statuts dynamiques :

À faire

Bloqué

Fait

Onboarding terminé

🏠 Logements (Property)

Un logement appartient à un propriétaire

Assignation possible à un intervenant

Suppression en cascade :

interventions

photos liées

CRUD complet côté propriétaire.

👷 Intervenants (Worker)

Création manuelle

Recherche par téléphone

Liaison à un propriétaire existant

Un intervenant peut travailler pour plusieurs propriétaires

Suppression volontairement désactivée (évite incohérences multi-propriétaires)

Accès via :

/w/{accessToken}


Token invalide ⇒ 404

📝 Interventions
Règles métier

1 intervention max par logement / jour

Date métier Europe/Paris

Création automatique au premier accès

Modifiable tant que non confirmée

Données saisies
Sortie voyageurs

À l’heure (oui / non / vide)

Consignes respectées (oui / non / vide)

Commentaire libre

Ménage

Checklist :

Lit fait

Sol propre

Salle de bain OK

Cuisine OK

Linge changé

Commentaire ménage

Conformité

Conforme si :

Tous les checks ménage validés

La sortie voyageurs n’impacte pas la conformité.

📷 Photos

Max 10 par intervention

Upload mobile

Stockage local

Suppression possible

Suppression automatique si intervention/logement supprimé

📊 Dashboard propriétaire

KPI sur 14 jours glissants :

Nombre de logements

Nombre d’interventions

Nombre de non-conformités

Dernière intervention par logement

Accès protégé (ROLE_OWNER)

🔐 Sécurité

CSRF sur tous les formulaires

Token sécurisé pour accès intervenant

Vérification stricte owner / worker

404 systématique si tentative d’accès non autorisé

Cascade DB cohérente

🧪 Fixtures

Faker

Données réalistes

14 jours d’historique

🧱 Stack

Symfony 8

Doctrine ORM

Twig

CSS custom mobile-first

SQLite / MySQL

📝 Philosophie

Simplicité terrain

Mobile-first

Zéro friction intervenant

Refactor uniquement quand nécessaire

MVP orienté usage réel

🚀 État actuel

✅ Boucle intervenant complète
✅ Boucle propriétaire fonctionnelle
✅ Onboarding opérationnel
✅ Registration + vérification email
✅ Gestion logements + intervenants
✅ Assignation fonctionnelle
✅ Sécurité stable

Projet prêt pour phase UX.