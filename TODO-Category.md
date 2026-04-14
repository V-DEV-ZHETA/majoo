# Add Category to Product Form

## Steps:
- [x] Step 1: php artisan make:migration create_categories_table
- [x] Step 2: Edit migration to add name field
- [x] Step 3: php artisan make:migration add_category_id_to_products_table
- [x] Step 4: Edit migration to add foreign key
- [x] Step 5: php artisan migrate
- [x] Step 6: Create app/Models/Category.php
- [x] Step 7: Update app/Models/Product.php with $fillable and belongsTo
- [x] Step 8: Uncomment category Select in ProductResource
- [x] Step 9: php artisan filament:cache-components
- [x] Step 10: Complete - Category field now works in Product form (add categories first)

