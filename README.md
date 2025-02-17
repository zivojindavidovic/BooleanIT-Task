Commands, links & explanations:

Application is dockerized so application is running using ./vendor/bin/sail down command.

Run the migration using php artisan migrate.

Run the migration from CSV using php artisan import:product-categories ./storage/app/public/product_categories.csv.

API documentation as been added with possible routes and it is accessible at http://localhost:8088/api/documentation#/.

GET endpoints for fetching categories, products that belong to a category, and all products in the production environment could return a huge amount of items, so pagination has been added as described in the API documentation.

Instead of hard delete, soft delete is implemented. This ensures that required statistics are preserved—items are not permanently removed but are instead soft deleted in the database. They are not shown in the API response, but the item still exists.

Additionally, some extra checks have been added, such as validation before deleting a category to ensure it has no active products, etc.
