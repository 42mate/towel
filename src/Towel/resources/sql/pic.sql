CREATE TABLE pic (
                id BIGINT NOT NULL AUTO_INCREMENT,
                pic VARCHAR(255) NOT NULL,
                created INT NOT NULL,
                object_id BIGINT NOT NULL,
                object_type VARCHAR(50) NOT NULL,
                PRIMARY KEY (id)
);