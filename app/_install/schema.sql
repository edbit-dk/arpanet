-- Users -------------------------------------------------------

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email varchar(255) NOT NULL UNIQUE,
    username varchar(255) NOT NULL UNIQUE,
    password varchar(255) NOT NULL,
    first_name varchar(255) NOT NULL,
    last_name varchar(255) NOT NULL,
    active boolean DEFAULT true,
    level_id integer NOT NULL DEFAULT 0,
    xp integer NOT NULL DEFAULT 0,
    rep varchar(255) NOT NULL,
    last_login datetime,
    created_at datetime,
    updated_at datetime
);

-- Servers -------------------------------------------------------

CREATE TABLE servers (
    id SERIAL PRIMARY KEY,
    admin_id integer NOT NULL DEFAULT 0,
    admin_pass varchar(255) NOT NULL,
    name varchar(255) NOT NULL,
    active boolean DEFAULT true,
    location varchar(255) NOT NULL,
    nodes json,
    level_id integer NOT NULL DEFAULT 0,
    created_at datetime
);

-- Server Accounts -------------------------------------------------------

CREATE TABLE server_accounts (
    id SERIAL PRIMARY KEY,
    user_id integer NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    server_id integer NOT NULL REFERENCES servers(id) ON DELETE CASCADE
);

-- Server Logs -------------------------------------------------------

CREATE TABLE logs (
    id SERIAL PRIMARY KEY,
    server_id integer NOT NULL REFERENCES servers(id) ON DELETE CASCADE,
    info text,
    ip varchar(255),
    code varchar(255),
    type varchar(255)
);

-- Help manuals -------------------------------------------------------

CREATE TABLE help (
    id SERIAL PRIMARY KEY,
    cmd varchar(255),
    opt varchar(255),
    params varchar(255),
    auth boolean DEFAULT false
);

-- Levels -------------------------------------------------------

CREATE TABLE levels (
    id SERIAL PRIMARY KEY,
    rep varchar(255),
    xp_req integer NOT NULL DEFAULT 0,
    xp_reward integer NOT NULL DEFAULT 0,
    skill_1 integer NOT NULL DEFAULT 4,
    skill_2 integer NOT NULL DEFAULT 5
);
