CREATE TABLE myTable(id integer primary key, name varchar(30));

CREATE TABLE post(
  id integer primary key,
  title varchar(100),
  body text,
  category_id integer,
  FOREIGN KEY(category_id) REFERENCES category(id)
);

CREATE TABLE category(
  id integer primary key,
  name varchar(100)
);

CREATE TABLE tag(
  id integer primary key,
  name varchar(100)
);

CREATE TABLE post_tag(
  id integer primary key,
  post_id integer,
  tag_id integer,
  FOREIGN KEY(post_id) REFERENCES post(id),
  FOREIGN KEY(tag_id) REFERENCES tag(id)
);