CREATE TABLE machines
(
id SERIAL PRIMARY KEY NOT NULL,
serial bigint NOT NULL
);
CREATE UNIQUE INDEX machines_serial_index ON machines (serial);
CREATE TABLE machines_options
(
machine_id int NOT NULL,
firmware VARCHAR(32),
connect_freq int NOT NULL
);
CREATE INDEX machines_options_machine_id_index ON machines_options (machine_id);
ALTER TABLE machines_options ADD FOREIGN KEY (machine_id) REFERENCES machines (id);
CREATE TABLE machines_options_set
(
machine_id int NOT NULL,
connect_freq int
);
CREATE INDEX machines_options_set_mahcine_id_index ON machines_options_set (machine_id);
ALTER TABLE machines_options_set ADD FOREIGN KEY (machine_id) REFERENCES machines (id);

INSERT INTO machines (serial) VALUES (123456789012345);
INSERT INTO machines_options (machine_id, firmware, connect_freq) VALUES (1, '1.01a', 5);
INSERT INTO machines_options_set (machine_id, connect_freq) VALUES (1, 10);