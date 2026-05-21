CREATE TABLE articles (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug         VARCHAR(200) NOT NULL,
    title        VARCHAR(200) NOT NULL,
    description  VARCHAR(500) NULL,
    body         MEDIUMTEXT NOT NULL,
    image_path   VARCHAR(255) NULL,
    views        INT UNSIGNED NOT NULL DEFAULT 0,
    published_at DATETIME NOT NULL,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_articles_slug (slug),
    KEY idx_articles_published_at (published_at),
    KEY idx_articles_views (views)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
