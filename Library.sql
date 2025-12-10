
-- USER TABLE

CREATE TABLE `User` (
    User_ID INT PRIMARY KEY,
    Name VARCHAR(50) NOT NULL,
    Role ENUM('Student', 'Teacher', 'Librarian', 'Staff') NOT NULL,
    Email VARCHAR(50),
    Contact VARCHAR(15),
    Username VARCHAR(50) UNIQUE NOT NULL,
    PasswordHash VARCHAR(50) NOT NULL
);


-- BOOK TABLE

CREATE TABLE Book (
    Book_ID INT PRIMARY KEY,
    Title VARCHAR(50) NOT NULL,
    Author VARCHAR(50),
    ISBN VARCHAR(50),
    Copies_Total INT NOT NULL,
    Copies_Available INT NOT NULL,
    Status VARCHAR(50),
    Archived BOOLEAN DEFAULT FALSE,
    Category VARCHAR(50),
    Publisher VARCHAR(50),
    Year INT,
    Location VARCHAR(50)
);


-- SEMESTER TABLE

CREATE TABLE Semester (
    Semester_ID INT PRIMARY KEY,
    Term_Name VARCHAR(50) NOT NULL,
    Start_Date DATE NOT NULL,
    End_Date DATE NOT NULL,
    Student_Borrow_Limit INT DEFAULT 3
);

-- BORROW TABLE

CREATE TABLE Borrow (
    Borrow_ID INT PRIMARY KEY,
    User_ID INT,
    Book_ID INT,
    Semester_ID INT,
    Borrow_Date DATE NOT NULL,
    Due_Date DATE NOT NULL,
    Return_Date DATE,
    Status ENUM('Borrowed','Returned','Overdue','Lost') NOT NULL,
    Renewal_Count INT DEFAULT 0,
    Processed_By INT,
    CONSTRAINT fk_borrow_user FOREIGN KEY (User_ID) REFERENCES `User`(User_ID),
    CONSTRAINT fk_borrow_book FOREIGN KEY (Book_ID) REFERENCES Book(Book_ID),
    CONSTRAINT fk_borrow_semester FOREIGN KEY (Semester_ID) REFERENCES Semester(Semester_ID),
    CONSTRAINT fk_borrow_processed FOREIGN KEY (Processed_By) REFERENCES `User`(User_ID)
);


-- RESERVATION TABLE

CREATE TABLE Reservation (
    Reservation_ID INT PRIMARY KEY,
    User_ID INT,
    Book_ID INT,
    Reservation_Date DATE NOT NULL,
    Expiry_Date DATE NOT NULL,
    Status VARCHAR(50) NOT NULL,
    Fulfilled_By_Borrow_ID INT,
    CONSTRAINT fk_reservation_user FOREIGN KEY (User_ID) REFERENCES `User`(User_ID),
    CONSTRAINT fk_reservation_book FOREIGN KEY (Book_ID) REFERENCES Book(Book_ID),
    CONSTRAINT fk_reservation_fulfilled FOREIGN KEY (Fulfilled_By_Borrow_ID) REFERENCES Borrow(Borrow_ID)
);


-- BORROWING RECORD TABLE

CREATE TABLE Borrowing_Record (
    Record_ID INT PRIMARY KEY,
    Borrow_ID INT,
    Action_Timestamp DATETIME NOT NULL,
    Action_Type ENUM('Borrowed','Renewed','OverdueFlagged','Cleared') NOT NULL,
    Notes VARCHAR(50),
    Changed_By INT,
    CONSTRAINT fk_record_borrow FOREIGN KEY (Borrow_ID) REFERENCES Borrow(Borrow_ID),
    CONSTRAINT fk_record_changed FOREIGN KEY (Changed_By) REFERENCES `User`(User_ID)
);


-- PENALTY TABLE

CREATE TABLE Penalty (
    Penalty_ID INT PRIMARY KEY,
    Borrow_ID INT,
    User_ID INT,
    Amount DECIMAL(10,2) NOT NULL,
    Reason VARCHAR(50),
    Issued_Date DATE NOT NULL,
    Paid_Date DATE,
    Status VARCHAR(50) NOT NULL,
    CONSTRAINT fk_penalty_borrow FOREIGN KEY (Borrow_ID) REFERENCES Borrow(Borrow_ID),
    CONSTRAINT fk_penalty_user FOREIGN KEY (User_ID) REFERENCES `User`(User_ID)
);


-- PAYMENT TABLE

CREATE TABLE Payment (
    Payment_ID INT PRIMARY KEY,
    User_ID INT,
    Penalty_ID INT,
    Reservation_ID INT,
    Record_ID INT,
    Amount DECIMAL(10,2) NOT NULL,
    Payment_Date DATE NOT NULL,
    Method VARCHAR(50) NOT NULL,
    Status VARCHAR(50) NOT NULL,
    CONSTRAINT fk_payment_user FOREIGN KEY (User_ID) REFERENCES `User`(User_ID),
    CONSTRAINT fk_payment_penalty FOREIGN KEY (Penalty_ID) REFERENCES Penalty(Penalty_ID),
    CONSTRAINT fk_payment_reservation FOREIGN KEY (Reservation_ID) REFERENCES Reservation(Reservation_ID),
    CONSTRAINT fk_payment_record FOREIGN KEY (Record_ID) REFERENCES Borrowing_Record(Record_ID)
);
