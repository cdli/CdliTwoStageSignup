CREATE TABLE user_signup_email_verification
(
    request_key VARCHAR(32) NOT NULL,
    email_address VARCHAR(255) NOT NULL,
    request_time DATETIME NOT NULL,
    PRIMARY KEY(request_key),
    UNIQUE(email_address)
) ENGINE=InnoDB;

