# Schema Recommendations for Vendor Filtering

## Current Analysis
Looking at your `shopping.sql` schema:
- The `products` table **does not have a `user_id` column** to track ownership
- Brands and categories are global, not vendor-specific
- No vendor role exists in the system

## Recommended Approach

### Option 1: **Add vendor_id to products table** (RECOMMENDED)
This is the cleanest approach without modifying brands/categories tables:

```sql
ALTER TABLE `products` ADD COLUMN `vendor_id` int(11) DEFAULT NULL;
ALTER TABLE `products` ADD KEY `vendor_id` (`vendor_id`);
ALTER TABLE `products` ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`vendor_id`) REFERENCES `customer` (`customer_id`);
```

**Why this works:**
- Products belong to vendors
- When fetching brands/categories for a vendor, filter through their products
- Brands/categories remain global (shared catalog)
- Admins see all brands/categories
- Vendors see only brands/categories they use in their products

**Implementation:**
```sql
-- Get brands for a vendor (through their products)
SELECT DISTINCT b.brand_id, b.brand_name 
FROM brands b
JOIN products p ON p.product_brand = b.brand_id
WHERE p.vendor_id = :vendor_id;

-- Get categories for a vendor (through their products)
SELECT DISTINCT c.cat_id, c.cat_name 
FROM categories c
JOIN products p ON p.product_cat = c.cat_id
WHERE p.vendor_id = :vendor_id;
```

### Option 2: Vendor-specific brands/categories
If you want vendors to create their own brands/categories:

```sql
ALTER TABLE `brands` ADD COLUMN `vendor_id` int(11) DEFAULT NULL;
ALTER TABLE `categories` ADD COLUMN `vendor_id` int(11) DEFAULT NULL;
```

**This is NOT recommended because:**
- Creates duplicate brand/category data
- Reduces catalog consistency
- Makes global reporting difficult
- Admin loses global control

## Implementation Plan

1. **Add `vendor_id` to products table**
2. **Add vendor role to user_role enum** (1=user, 2=admin, 3=vendor)
3. **Update product management** to assign vendor_id
4. **Filter brands/categories** through products ownership

## Benefits of Recommended Approach

✅ Global catalog (brands/categories shared)
✅ Vendor isolation (vendors only see what they use)
✅ Easy to add new vendors
✅ Admin has full control
✅ Scalable architecture
✅ No duplicate data

