CREATE TABLE location (
                id BIGINT NOT NULL AUTO_INCREMENT ,
                location VARCHAR(255) NOT NULL,
                PRIMARY KEY (id)
);

CREATE TABLE app_user (
                id BIGINT NOT NULL AUTO_INCREMENT ,
                fbu BIGINT NOT NULL,
                username VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                password VARCHAR(100) NOT NULL,
                fb_location_id BIGINT NOT NULL,
                fb_location VARCHAR(255) NOT NULL,
                PRIMARY KEY (id)
);


ALTER TABLE app_user ADD CONSTRAINT location_user_fk
FOREIGN KEY (fb_location_id)
REFERENCES location (id)
ON DELETE RESTRICT
ON UPDATE CASCADE;