CREATE TABLE IF NOT EXISTS /*_*/user_page_views (
	user_id INT(5) UNSIGNED NOT NULL,
	page_id INT(8) UNSIGNED NOT NULL,
	hits INT(10) UNSIGNED NOT NULL,
	last TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY ( user_id, page_id )
);

CREATE OR REPLACE VIEW /*_*/user_page_hits AS SELECT
	u.user_name AS user_name,
	u.user_real_name AS user_real_name,
	p.page_namespace AS page_namespace,
	p.page_title AS page_title,
	v.hits AS hits,
	v.last AS last,
	u.user_id AS user_id
FROM (/*_*/user u JOIN /*_*/page p) JOIN /*_*/user_page_views v
WHERE u.user_id = v.user_id AND p.page_id = v.page_id
ORDER BY v.last DESC;

CREATE TABLE IF NOT EXISTS /*_*/user_page_history (
	user_id INT(5) UNSIGNED NOT NULL,
	user_name VARCHAR(255) NOT NULL,
	page_title VARCHAR(255) NOT NULL,
	last TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);