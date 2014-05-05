CREATE TABLE post (
                id BIGINT NOT NULL AUTO_INCREMENT ,
                title VARCHAR(255) NOT NULL,
                description LONGTEXT NOT NULL,
                created INT NOT NULL,
                user_id BIGINT NOT NULL,
                status CHAR(1) NOT NULL,
                fb_location_id BIGINT NOT NULL,
                fb_location VARCHAR(255) NOT NULL,
                post_type VARCHAR(50) NOT NULL,
                PRIMARY KEY (id)
);

CREATE TABLE comment (
                id BIGINT NOT NULL AUTO_INCREMENT ,
                user_id BIGINT NOT NULL,
                username VARCHAR(100) NOT NULL,
                post_id BIGINT NOT NULL,
                created INT NOT NULL,
                status CHAR(1) NOT NULL,
                comment VARCHAR(500) NOT NULL,
                PRIMARY KEY (id)
);

ALTER TABLE post ADD CONSTRAINT location_post_fk
FOREIGN KEY (fb_location_id)
REFERENCES location (id)
ON DELETE RESTRICT
ON UPDATE CASCADE;

ALTER TABLE post ADD CONSTRAINT user_post_fk
FOREIGN KEY (user_id)
REFERENCES app_user (id)
ON DELETE CASCADE
ON UPDATE CASCADE;

ALTER TABLE comment ADD CONSTRAINT post_comment_fk
FOREIGN KEY (post_id)
REFERENCES post (id)
ON DELETE CASCADE
ON UPDATE CASCADE;

ALTER TABLE comment ADD CONSTRAINT post_comment_fk_2
FOREIGN KEY (user_id)
REFERENCES app_user (id)
ON DELETE CASCADE
ON UPDATE CASCADE;