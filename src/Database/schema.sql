-- ============================================
-- Clothing Shop DB Schema
-- Generated from schema.dbml
-- Engine: InnoDB | Charset: utf8mb4
-- ============================================

CREATE DATABASE IF NOT EXISTS shop_project
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE shop_project;

-- -------------------------------------------
-- Categories (self-referencing for nesting)
-- -------------------------------------------
CREATE TABLE categories (
  id         INT           AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(255)  NOT NULL,
  parent_id  INT           NULL,
  created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (parent_id) REFERENCES categories(id)
    ON DELETE SET NULL
    ON UPDATE CASCADE,

  INDEX idx_parent_id (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- Products
-- -------------------------------------------
CREATE TABLE products (
  id          INT           AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(255)  NOT NULL,
  description TEXT          NULL,
  brand       VARCHAR(255)  NULL,
  base_price  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  is_active   TINYINT(1)    NOT NULL DEFAULT 1,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  INDEX idx_is_active (is_active),
  INDEX idx_brand (brand)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- Product <-> Category (many-to-many)
-- -------------------------------------------
CREATE TABLE product_categories (
  product_id  INT NOT NULL,
  category_id INT NOT NULL,

  PRIMARY KEY (product_id, category_id),

  FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  FOREIGN KEY (category_id) REFERENCES categories(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- Tags
-- -------------------------------------------
CREATE TABLE tags (
  id   INT          AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- Product <-> Tag (many-to-many)
-- -------------------------------------------
CREATE TABLE product_tags (
  product_id INT NOT NULL,
  tag_id     INT NOT NULL,

  PRIMARY KEY (product_id, tag_id),

  FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  FOREIGN KEY (tag_id) REFERENCES tags(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- Product Variants (size, color, stock)
-- -------------------------------------------
CREATE TABLE product_variants (
  id         INT           AUTO_INCREMENT PRIMARY KEY,
  product_id INT           NOT NULL,
  sku        VARCHAR(100)  NOT NULL UNIQUE,
  price      DECIMAL(10,2) NOT NULL,
  stock_qty  INT           NOT NULL DEFAULT 0,
  attributes JSON          NULL,
  image_url  VARCHAR(500)  NULL,
  created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  INDEX idx_product_id (product_id),
  INDEX idx_sku (sku)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- Customers
-- -------------------------------------------
CREATE TABLE customers (
  id            INT          AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(255) NOT NULL,
  email         VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- Addresses
-- -------------------------------------------
CREATE TABLE addresses (
  id          INT          AUTO_INCREMENT PRIMARY KEY,
  customer_id INT          NOT NULL,
  line1       VARCHAR(255) NOT NULL,
  line2       VARCHAR(255) NULL,
  city        VARCHAR(100) NOT NULL,
  postal_code VARCHAR(20)  NULL,
  country     VARCHAR(100) NOT NULL,
  phone       VARCHAR(30)  NULL,
  is_default  TINYINT(1)   NOT NULL DEFAULT 0,

  FOREIGN KEY (customer_id) REFERENCES customers(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  INDEX idx_customer_id (customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- Carts (supports guest via session_id)
-- -------------------------------------------
CREATE TABLE carts (
  id          INT       AUTO_INCREMENT PRIMARY KEY,
  customer_id INT       NULL,
  session_id  VARCHAR(255) NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (customer_id) REFERENCES customers(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  INDEX idx_customer_id (customer_id),
  INDEX idx_session_id (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- Cart Items
-- -------------------------------------------
CREATE TABLE cart_items (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  cart_id    INT NOT NULL,
  variant_id INT NOT NULL,
  quantity   INT NOT NULL DEFAULT 1,

  FOREIGN KEY (cart_id) REFERENCES carts(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  FOREIGN KEY (variant_id) REFERENCES product_variants(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  INDEX idx_cart_id (cart_id),
  UNIQUE KEY uq_cart_variant (cart_id, variant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- Orders
-- -------------------------------------------
CREATE TABLE orders (
  id          INT           AUTO_INCREMENT PRIMARY KEY,
  customer_id INT           NOT NULL,
  status      VARCHAR(50)   NOT NULL DEFAULT 'pending',
  total       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (customer_id) REFERENCES customers(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  INDEX idx_customer_id (customer_id),
  INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- Order Items (price copied at purchase time)
-- -------------------------------------------
CREATE TABLE order_items (
  id                INT           AUTO_INCREMENT PRIMARY KEY,
  order_id          INT           NOT NULL,
  variant_id        INT           NOT NULL,
  quantity          INT           NOT NULL,
  price_at_purchase DECIMAL(10,2) NOT NULL,

  FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  FOREIGN KEY (variant_id) REFERENCES product_variants(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  INDEX idx_order_id (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- Discounts (coupon codes)
-- -------------------------------------------
CREATE TABLE discounts (
  id               INT           AUTO_INCREMENT PRIMARY KEY,
  code             VARCHAR(100)  NOT NULL UNIQUE,
  type             VARCHAR(20)   NOT NULL DEFAULT 'fixed',
  value            DECIMAL(10,2) NOT NULL,
  min_order_amount DECIMAL(10,2) NULL,
  usage_limit      INT           NULL,
  times_used       INT           NOT NULL DEFAULT 0,
  starts_at        TIMESTAMP     NULL,
  expires_at       TIMESTAMP     NULL,
  is_active        TINYINT(1)    NOT NULL DEFAULT 1,

  INDEX idx_code (code),
  INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- Discount Usage (prevents code abuse)
-- -------------------------------------------
CREATE TABLE discount_usage (
  id          INT       AUTO_INCREMENT PRIMARY KEY,
  discount_id INT       NOT NULL,
  customer_id INT       NULL,
  order_id    INT       NOT NULL,
  used_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (discount_id) REFERENCES discounts(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  FOREIGN KEY (customer_id) REFERENCES customers(id)
    ON DELETE SET NULL
    ON UPDATE CASCADE,

  FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  INDEX idx_discount_id (discount_id),
  INDEX idx_customer_id (customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- Order Discounts (applied discounts snapshot)
-- -------------------------------------------
CREATE TABLE order_discounts (
  id           INT           AUTO_INCREMENT PRIMARY KEY,
  order_id     INT           NOT NULL,
  discount_id  INT           NOT NULL,
  amount_saved DECIMAL(10,2) NOT NULL DEFAULT 0.00,

  FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  FOREIGN KEY (discount_id) REFERENCES discounts(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  INDEX idx_order_id (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
