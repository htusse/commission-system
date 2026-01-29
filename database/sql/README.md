# Database Import Instructions

## Steps to import the database:

1. Place your `nxm_assessment_2023.sql` file in this directory
2. Run the following command to import the database:

```bash
./vendor/bin/sail mysql nxm_assessment_2023 < database/sql/nxm_assessment_2023.sql
```

Or manually:

```bash
./vendor/bin/sail exec mariadb mysql -u sail -ppassword nxm_assessment_2023 < database/sql/nxm_assessment_2023.sql
```

## Expected Tables:
Based on the requirements, the database should contain:
- `users` - Contains both Customers and Distributors
- `orders` - Contains order information
- `order_items` - Contains items within each order
- `referrals` or similar - Tracks who referred whom

After placing the SQL file here, we'll analyze the schema and relationships.
