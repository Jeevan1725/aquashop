USE aquashop;

-- =========================================
-- ROLES
-- =========================================

INSERT INTO roles (name)
VALUES
('customer'),
('manager'),
('admin')
ON DUPLICATE KEY UPDATE
name = VALUES(name);


-- =========================================
-- CATEGORIES
-- =========================================

INSERT INTO categories (name, description)
VALUES
(
    'Freshwater Fish',
    'Colorful and healthy freshwater ornamental fish'
),
(
    'Marine Fish',
    'Beautiful saltwater and marine aquarium fish'
),
(
    'Aquatic Plants',
    'Live plants for freshwater aquariums'
),
(
    'Aquariums',
    'Glass aquariums and fish tanks'
),
(
    'Equipment',
    'Filters, pumps, lights and aquarium equipment'
),
(
    'Fish Food',
    'Food and nutrition products for ornamental fish'
)
ON DUPLICATE KEY UPDATE
description = VALUES(description);


-- =========================================
-- PRODUCTS
-- =========================================

INSERT INTO products
(
    category_id,
    name,
    slug,
    description,
    price,
    stock,
    status
)
VALUES
(
    (SELECT id FROM categories WHERE name = 'Freshwater Fish'),
    'Betta Fish',
    'betta-fish',
    'Beautiful ornamental Betta fish suitable for home aquariums.',
    299.00,
    20,
    'active'
),
(
    (SELECT id FROM categories WHERE name = 'Freshwater Fish'),
    'Guppy Fish',
    'guppy-fish',
    'Colorful freshwater Guppy fish for community aquariums.',
    149.00,
    30,
    'active'
),
(
    (SELECT id FROM categories WHERE name = 'Freshwater Fish'),
    'Goldfish',
    'goldfish',
    'Popular freshwater ornamental Goldfish.',
    199.00,
    25,
    'active'
),
(
    (SELECT id FROM categories WHERE name = 'Marine Fish'),
    'Clownfish',
    'clownfish',
    'Bright marine Clownfish suitable for saltwater aquariums.',
    799.00,
    10,
    'active'
),
(
    (SELECT id FROM categories WHERE name = 'Aquatic Plants'),
    'Java Fern',
    'java-fern',
    'Easy-to-maintain aquatic plant for freshwater aquariums.',
    149.00,
    40,
    'active'
),
(
    (SELECT id FROM categories WHERE name = 'Aquariums'),
    'Nano Aquarium',
    'nano-aquarium',
    'Compact aquarium suitable for small ornamental fish.',
    1499.00,
    8,
    'active'
),
(
    (SELECT id FROM categories WHERE name = 'Equipment'),
    'Aquarium Filter',
    'aquarium-filter',
    'Efficient aquarium filtration system for clean water.',
    899.00,
    15,
    'active'
),
(
    (SELECT id FROM categories WHERE name = 'Fish Food'),
    'Premium Fish Food',
    'premium-fish-food',
    'Nutritional fish food for healthy ornamental fish.',
    249.00,
    50,
    'active'
);
