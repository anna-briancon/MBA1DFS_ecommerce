## Installation
* Cloner le Repo
* Installer composer ( https://getcomposer.org )
* Executer `php composer.phar install`
* Configurer la connexion vers la base de données: `DATABASE_URL="mysql://root:@127.0.0.1:3306/ecommerce"`
* Créer la base de données: `php bin/console doctrine:database:create`
* Mettre à jour le schéma DB: `php bin/console doctrine:migrations:migrate`
* Insérer trois produits tests en DB: `INSERT INTO produit (nom, prix) VALUES
  ('T-Shirt Dev', 19.99),
  ('Casquette PHP', 14.99),
  ('Mug Codeur', 9.99);`
* Démarrer le serveur `symfony:server:start`
* Accéder à `http://localhost:8000/`

## Tester l'application
* Ajouter un produit au panier.
* Voir le total du panier.
* Supprimer un produit du panier.

## Pour executer les tests unitaires
* Installer PHP Unit `php composer.phar require --dev symfony/test-pack`
* Pour lancer les tests que vous aurez créés: `php bin/phpunit`
